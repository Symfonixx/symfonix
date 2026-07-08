<?php

namespace Modules\Base\Services;

use Illuminate\Support\Collection;
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
                    'type' => str_contains($filename, '_auto_') ? 'auto' : 'manual',
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

    protected function assertSafeFilename(string $filename): void
    {
        if ($filename === '' || str_contains($filename, '..') || str_contains($filename, '/') || str_contains($filename, '\\') || ! str_ends_with(strtolower($filename), '.zip')) {
            throw new RuntimeException('Invalid backup filename.');
        }
    }
}
