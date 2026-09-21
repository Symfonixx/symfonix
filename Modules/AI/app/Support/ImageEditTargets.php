<?php

namespace Modules\AI\Support;

final class ImageEditTargets
{
    /**
     * @return array<string, array{model: class-string, fields: list<string>, disk: string, permission: string}>
     */
    public static function all(): array
    {
        return config('ai.editable_targets', []);
    }

    /**
     * @return list<string>
     */
    public static function names(): array
    {
        return array_keys(self::all());
    }

    /**
     * @return array{model: class-string, fields: list<string>, disk: string, permission: string}|null
     */
    public static function get(string $target): ?array
    {
        return self::all()[$target] ?? null;
    }

    /**
     * Permissions that may generate or apply AI images for any registered target.
     *
     * @return list<string>
     */
    public static function anyPermissions(): array
    {
        $permissions = [];

        foreach (self::all() as $entry) {
            $edit = $entry['permission'] ?? null;

            if (! is_string($edit) || $edit === '') {
                continue;
            }

            $permissions[] = $edit;

            if (str_ends_with($edit, '.edit')) {
                $permissions[] = substr($edit, 0, -5).'.create';
            }
        }

        return array_values(array_unique($permissions));
    }

    public static function allowsField(string $target, string $field): bool
    {
        $entry = self::get($target);

        return $entry !== null && in_array($field, $entry['fields'], true);
    }
}
