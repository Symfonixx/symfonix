<?php

namespace App\Translation;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;
use Illuminate\Support\Collection;
use RuntimeException;

class JsonFileLoader extends FileLoader
{
    public function __construct(Filesystem $files, array|string $path)
    {
        parent::__construct($files, $path);
    }

    protected function loadPaths(array $paths, $locale, $group)
    {
        return (new Collection($paths))
            ->reduce(function ($output, $path) use ($locale, $group) {
                $phpFile = "{$path}/{$locale}/{$group}.php";

                if ($this->files->exists($phpFile)) {
                    $output = array_replace_recursive($output, $this->files->getRequire($phpFile));
                }

                $jsonFile = "{$path}/{$locale}/{$group}.json";

                if ($this->files->exists($jsonFile)) {
                    $decoded = json_decode($this->files->get($jsonFile), true);

                    if (! is_array($decoded)) {
                        throw new RuntimeException("Translation file [{$jsonFile}] contains an invalid JSON structure.");
                    }

                    $output = array_replace_recursive($output, $decoded);
                }

                return $output;
            }, []);
    }
}
