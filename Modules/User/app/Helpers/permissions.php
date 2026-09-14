<?php

use Modules\User\Support\PermissionCatalog;

if (! function_exists('permission_keys')) {
    /**
     * @param  string|list<string>  $tabOrPrefix
     * @return list<string>
     */
    function permission_keys(string|array $tabOrPrefix): array
    {
        return PermissionCatalog::keysFor($tabOrPrefix);
    }
}

if (! function_exists('permission_section_keys')) {
    /**
     * @return list<string>
     */
    function permission_section_keys(string $section): array
    {
        return PermissionCatalog::keysForSection($section);
    }
}
