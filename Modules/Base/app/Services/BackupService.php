<?php

namespace Modules\Base\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;
use ZipArchive;

class BackupService
{
    public const DISK = 'local';

    public const DIRECTORY = 'backups';

    public function create(string $type = 'manual'): array
    {
        $disk = Storage::disk(self::DISK);
        $disk->makeDirectory(self::DIRECTORY);

        $timestamp = now()->format('Y-m-d_H-i-s');
        $typeSlug = Str::slug($type) ?: 'manual';
        $filename = "backup_{$typeSlug}_{$timestamp}.zip";
        $relativePath = self::DIRECTORY.'/'.$filename;
        $absolutePath = $disk->path($relativePath);

        $tempDir = storage_path('app/private/backups/tmp_'.Str::random(12));
        File::ensureDirectoryExists($tempDir);

        try {
            $sqlPath = $tempDir.'/database.sql';
            $this->exportDatabase($sqlPath);

            $zip = new ZipArchive;
            if ($zip->open($absolutePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new RuntimeException('Unable to create backup archive.');
            }

            $zip->addFile($sqlPath, 'database.sql');
            $zip->addFromString('manifest.json', json_encode([
                'app' => config('app.name'),
                'type' => $type,
                'created_at' => now()->toIso8601String(),
                'database' => config('database.connections.'.config('database.default').'.database'),
                'connection' => config('database.default'),
            ], JSON_PRETTY_PRINT));

            $this->addPublicStorageFiles($zip);
            $zip->close();

            if (! File::exists($absolutePath)) {
                throw new RuntimeException('Backup archive was not created.');
            }

            return [
                'filename' => $filename,
                'path' => $relativePath,
                'size' => File::size($absolutePath),
                'type' => $type,
                'created_at' => now(),
            ];
        } finally {
            File::deleteDirectory($tempDir);
        }
    }

    public function list(): Collection
    {
        $disk = Storage::disk(self::DISK);

        if (! $disk->exists(self::DIRECTORY)) {
            return collect();
        }

        return collect($disk->files(self::DIRECTORY))
            ->filter(fn (string $path) => str_ends_with(strtolower($path), '.zip'))
            ->map(function (string $path) use ($disk) {
                $filename = basename($path);

                return [
                    'filename' => $filename,
                    'path' => $path,
                    'size' => $disk->size($path),
                    'type' => match (true) {
                        str_contains($filename, '_auto_') => 'auto',
                        str_contains($filename, '_import_') => 'import',
                        default => 'manual',
                    },
                    'created_at' => $disk->lastModified($path),
                ];
            })
            ->sortByDesc('created_at')
            ->values();
    }

    public function absolutePath(string $filename): string
    {
        $this->assertSafeFilename($filename);

        $path = Storage::disk(self::DISK)->path(self::DIRECTORY.'/'.$filename);

        if (! File::exists($path)) {
            throw new RuntimeException('Backup file not found.');
        }

        return $path;
    }

    public function delete(string $filename): void
    {
        $this->assertSafeFilename($filename);

        $relativePath = self::DIRECTORY.'/'.$filename;
        $disk = Storage::disk(self::DISK);

        if ($disk->exists($relativePath)) {
            $disk->delete($relativePath);
        }
    }

    /**
     * Restore database and public storage from an uploaded backup ZIP
     * (same format produced by create()).
     *
     * @return array{filename: string, path: string, size: int}
     */
    public function import(UploadedFile $file): array
    {
        $disk = Storage::disk(self::DISK);
        $disk->makeDirectory(self::DIRECTORY);

        $timestamp = now()->format('Y-m-d_H-i-s');
        $original = pathinfo((string) $file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBase = Str::slug((string) $original) ?: 'import';
        $filename = "backup_import_{$safeBase}_{$timestamp}.zip";
        $relativePath = self::DIRECTORY.'/'.$filename;

        $stored = $file->storeAs(self::DIRECTORY, $filename, self::DISK);
        if (! $stored) {
            throw new RuntimeException('Unable to store uploaded backup.');
        }

        try {
            $this->restoreFromPath($disk->path($relativePath));
        } catch (Throwable $e) {
            $disk->delete($relativePath);
            throw $e;
        }

        return [
            'filename' => $filename,
            'path' => $relativePath,
            'size' => $disk->size($relativePath),
        ];
    }

    /**
     * Restore database and public storage from an existing backup ZIP on disk.
     */
    public function restore(string $filename): void
    {
        $this->restoreFromPath($this->absolutePath($filename));
    }

    public function restoreFromPath(string $absoluteZipPath): void
    {
        if (! File::exists($absoluteZipPath)) {
            throw new RuntimeException('Backup file not found.');
        }

        // Debugbar tries to pretty-print every query; a full SQL dump makes
        // its formatter call preg_replace() with null and crash the request.
        $this->disableDebugbarForRestore();

        $tempDir = storage_path('app/private/backups/tmp_restore_'.Str::random(12));
        File::ensureDirectoryExists($tempDir);

        try {
            $this->extractBackupArchive($absoluteZipPath, $tempDir);

            $sqlPath = $tempDir.'/database.sql';
            if (! File::exists($sqlPath) || File::size($sqlPath) === 0) {
                throw new RuntimeException('Backup archive is missing database.sql.');
            }

            $this->importDatabase($sqlPath);
            $this->restorePublicStorage($tempDir);

            try {
                Artisan::call('cache:clear');
                Artisan::call('view:clear');
                cache()->forget('settings');
            } catch (Throwable) {
                // Cache clear is best-effort after a successful restore.
            }
        } finally {
            File::deleteDirectory($tempDir);
        }
    }

    public function shouldRunAutoBackup(): bool
    {
        $enabled = filter_var(\Modules\Base\Models\Settings::get('auto_backup_enabled', '0'), FILTER_VALIDATE_BOOLEAN);
        if (! $enabled) {
            return false;
        }

        $days = (int) \Modules\Base\Models\Settings::get('auto_backup_interval_days', '7');
        if ($days < 1) {
            return false;
        }

        $lastRun = \Modules\Base\Models\Settings::get('auto_backup_last_run');
        if (! $lastRun) {
            return true;
        }

        try {
            return now()->greaterThanOrEqualTo(
                \Illuminate\Support\Carbon::parse($lastRun)->addDays($days)
            );
        } catch (Throwable) {
            return true;
        }
    }

    public function markAutoBackupRan(): void
    {
        \Modules\Base\Models\Settings::set('auto_backup_last_run', now()->toDateTimeString());
        cache()->forget('settings');
    }

    protected function exportDatabase(string $sqlPath): void
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");

        if (in_array($config['driver'] ?? '', ['mysql', 'mariadb'], true) && $this->dumpWithMysqldump($config, $sqlPath)) {
            return;
        }

        $this->dumpWithPhp($connection, $sqlPath);
    }

    protected function dumpWithMysqldump(array $config, string $sqlPath): bool
    {
        $mysqldump = $this->findMysqldump();
        if (! $mysqldump) {
            return false;
        }

        $host = $config['host'] ?? '127.0.0.1';
        $port = (string) ($config['port'] ?? 3306);
        $user = $config['username'] ?? '';
        $database = $config['database'] ?? '';
        $password = $config['password'] ?? '';

        $command = [
            $mysqldump,
            '--host='.$host,
            '--port='.$port,
            '--user='.$user,
            '--single-transaction',
            '--routines',
            '--triggers',
            '--result-file='.$sqlPath,
            $database,
        ];

        if ($password !== '') {
            array_splice($command, 4, 0, ['--password='.$password]);
        }

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptors, $pipes, null, null, ['bypass_shell' => true]);
        if (! is_resource($process)) {
            return false;
        }

        fclose($pipes[0]);
        stream_get_contents($pipes[1]);
        stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        return $exitCode === 0 && File::exists($sqlPath) && File::size($sqlPath) > 0;
    }

    protected function findMysqldump(): ?string
    {
        $candidates = [
            'mysqldump',
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe',
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/opt/homebrew/bin/mysqldump',
        ];

        foreach ($candidates as $candidate) {
            if ($candidate === 'mysqldump') {
                $which = stripos(PHP_OS, 'WIN') === 0 ? 'where mysqldump' : 'command -v mysqldump';
                $path = trim((string) shell_exec($which));
                if ($path !== '' && File::exists(explode(PHP_EOL, $path)[0])) {
                    return explode(PHP_EOL, $path)[0];
                }

                continue;
            }

            if (File::exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    protected function dumpWithPhp(string $connection, string $sqlPath): void
    {
        $driver = config("database.connections.{$connection}.driver");
        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            throw new RuntimeException('Automatic SQL export currently supports MySQL/MariaDB only.');
        }

        $tables = DB::connection($connection)->select('SHOW TABLES');
        $key = 'Tables_in_'.config("database.connections.{$connection}.database");

        $sql = "-- Symfonix database backup\n";
        $sql .= '-- Generated at: '.now()->toDateTimeString()."\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $tableRow) {
            $table = $tableRow->{$key} ?? array_values((array) $tableRow)[0] ?? null;
            if (! $table) {
                continue;
            }

            $create = DB::connection($connection)->select('SHOW CREATE TABLE `'.str_replace('`', '``', $table).'`');
            $createSql = $create[0]->{'Create Table'} ?? null;
            if (! $createSql) {
                continue;
            }

            $safeTable = str_replace('`', '``', $table);
            $sql .= "DROP TABLE IF EXISTS `{$safeTable}`;\n";
            $sql .= $createSql.";\n\n";

            $rows = DB::connection($connection)->table($table)->get();
            foreach ($rows as $row) {
                $values = collect((array) $row)->map(function ($value) {
                    if ($value === null) {
                        return 'NULL';
                    }

                    return "'".str_replace(["\\", "'"], ["\\\\", "\\'"], (string) $value)."'";
                })->implode(', ');

                $sql .= "INSERT INTO `{$safeTable}` VALUES ({$values});\n";
            }

            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        File::put($sqlPath, $sql);
    }

    protected function addPublicStorageFiles(ZipArchive $zip): void
    {
        $publicRoot = storage_path('app/public');
        if (! File::isDirectory($publicRoot)) {
            return;
        }

        $files = File::allFiles($publicRoot);
        foreach ($files as $file) {
            $relative = 'storage/public/'.$file->getRelativePathname();
            $zip->addFile($file->getPathname(), str_replace('\\', '/', $relative));
        }
    }

    protected function extractBackupArchive(string $absoluteZipPath, string $tempDir): void
    {
        $zip = new ZipArchive;
        if ($zip->open($absoluteZipPath) !== true) {
            throw new RuntimeException('Unable to open backup archive.');
        }

        try {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if ($name === false) {
                    continue;
                }

                $normalized = str_replace('\\', '/', $name);
                if ($normalized === '' || str_ends_with($normalized, '/')) {
                    continue;
                }

                if (str_contains($normalized, '..') || str_starts_with($normalized, '/')) {
                    throw new RuntimeException('Backup archive contains an unsafe path.');
                }

                $allowed = $normalized === 'database.sql'
                    || $normalized === 'manifest.json'
                    || str_starts_with($normalized, 'storage/public/');

                if (! $allowed) {
                    continue;
                }

                $target = $tempDir.'/'.$normalized;
                File::ensureDirectoryExists(dirname($target));

                $stream = $zip->getStream($name);
                if ($stream === false) {
                    throw new RuntimeException('Unable to read a file from the backup archive.');
                }

                $out = fopen($target, 'wb');
                if ($out === false) {
                    fclose($stream);
                    throw new RuntimeException('Unable to extract backup archive.');
                }

                stream_copy_to_stream($stream, $out);
                fclose($stream);
                fclose($out);
            }
        } finally {
            $zip->close();
        }
    }

    protected function importDatabase(string $sqlPath): void
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");
        $driver = $config['driver'] ?? '';

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            throw new RuntimeException('Automatic SQL restore currently supports MySQL/MariaDB only.');
        }

        if ($this->importWithMysqlClient($config, $sqlPath)) {
            DB::purge($connection);
            DB::reconnect($connection);

            return;
        }

        $this->importWithPhp($connection, $sqlPath);
    }

    protected function importWithMysqlClient(array $config, string $sqlPath): bool
    {
        $mysql = $this->findMysqlClient();
        if (! $mysql) {
            return false;
        }

        $host = $config['host'] ?? '127.0.0.1';
        $port = (string) ($config['port'] ?? 3306);
        $user = $config['username'] ?? '';
        $database = $config['database'] ?? '';
        $password = $config['password'] ?? '';

        $command = [
            $mysql,
            '--host='.$host,
            '--port='.$port,
            '--user='.$user,
            $database,
        ];

        if ($password !== '') {
            array_splice($command, 4, 0, ['--password='.$password]);
        }

        $descriptors = [
            0 => ['file', $sqlPath, 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptors, $pipes, null, null, ['bypass_shell' => true]);
        if (! is_resource($process)) {
            return false;
        }

        stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            throw new RuntimeException(
                'Database restore failed'.($stderr ? ': '.trim($stderr) : '.')
            );
        }

        return true;
    }

    protected function findMysqlClient(): ?string
    {
        $candidates = [
            'mysql',
            'C:\\xampp\\mysql\\bin\\mysql.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysql.exe',
            '/usr/bin/mysql',
            '/usr/local/bin/mysql',
            '/opt/homebrew/bin/mysql',
        ];

        foreach ($candidates as $candidate) {
            if ($candidate === 'mysql') {
                $which = stripos(PHP_OS, 'WIN') === 0 ? 'where mysql' : 'command -v mysql';
                $path = trim((string) shell_exec($which));
                if ($path !== '' && File::exists(explode(PHP_EOL, $path)[0])) {
                    return explode(PHP_EOL, $path)[0];
                }

                continue;
            }

            if (File::exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    protected function importWithPhp(string $connection, string $sqlPath): void
    {
        $sql = File::get($sqlPath);
        if ($sql === false || trim($sql) === '') {
            throw new RuntimeException('Backup SQL file is empty.');
        }

        // Use PDO directly so the multi-megabyte dump is not recorded as a
        // single QueryExecuted event (Debugbar cannot format that safely).
        $pdo = DB::connection($connection)->getPdo();

        try {
            $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
            if ($pdo->exec($sql) === false) {
                $error = $pdo->errorInfo();
                throw new RuntimeException('Database restore failed: '.($error[2] ?? 'unknown PDO error'));
            }
        } catch (RuntimeException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new RuntimeException('Database restore failed: '.$e->getMessage(), 0, $e);
        } finally {
            try {
                $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
            } catch (Throwable) {
                // ignore
            }
        }

        DB::purge($connection);
        DB::reconnect($connection);
    }

    protected function disableDebugbarForRestore(): void
    {
        if (! app()->bound('debugbar')) {
            return;
        }

        try {
            app('debugbar')->disable();
        } catch (Throwable) {
            // Debugbar is optional; restore must not depend on it.
        }
    }

    protected function restorePublicStorage(string $tempDir): void
    {
        $sourceRoot = $tempDir.'/storage/public';
        if (! File::isDirectory($sourceRoot)) {
            return;
        }

        $publicRoot = storage_path('app/public');
        File::ensureDirectoryExists($publicRoot);

        $files = File::allFiles($sourceRoot);
        foreach ($files as $file) {
            $relative = str_replace('\\', '/', $file->getRelativePathname());
            if (str_contains($relative, '..')) {
                throw new RuntimeException('Backup storage path is unsafe.');
            }

            $target = $publicRoot.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
            File::ensureDirectoryExists(dirname($target));
            File::copy($file->getPathname(), $target);
        }
    }

    protected function assertSafeFilename(string $filename): void
    {
        if ($filename === '' || str_contains($filename, '..') || str_contains($filename, '/') || str_contains($filename, '\\') || ! str_ends_with(strtolower($filename), '.zip')) {
            throw new RuntimeException('Invalid backup filename.');
        }
    }
}
