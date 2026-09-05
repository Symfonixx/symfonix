<?php

namespace Modules\Cms\Repositories\Client;

use Config;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Cms\Enums\CmsStatus;
use Modules\Cms\Models\Client;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\Core\Traits\FileTrait;

class ClientModelRepository implements ClientRepository
{
    use ExceptionHandlerTrait, FileTrait;

    private string $logoUploadPath = 'clients';

    public function all(array $columns = ['*']): LengthAwarePaginator
    {
        return Client::select($columns)->ordered()->paginate(Config::get('core.page_size', 10));
    }

    public function find(int $id, array $columns = ['*']): ?Client
    {
        return Client::find($id, $columns);
    }

    public function store(array $data): mixed
    {
        return $this->execute(function () use ($data) {
            Client::create($this->prepareClientData($data));
            session()->flushMessage(true);
        });
    }

    public function update(array $data, Client $client): mixed
    {
        return $this->execute(function () use ($data, $client) {
            $client->update($this->prepareClientUpdateData($data, $client));
            session()->flushMessage(true);

            return true;
        });
    }

    public function deleteMulti(array $ids): ?bool
    {
        return $this->execute(function () use ($ids) {
            $logos = Client::whereIn('id', $ids)->pluck('logo')->filter()->toArray();
            Client::destroy($ids);
            foreach ($logos as $logo) {
                $this->deleteFile($logo);
            }
            session()->flushMessage(true);

            return true;
        });
    }

    private function prepareClientData(array $data): array
    {
        $translations = buildTranslations([
            'name' => $data['name'] ?? '',
        ], wantsAutoTranslate($data));

        return array_merge($data, $translations, [
            'logo' => $this->handleLogoUpload($data),
            'url' => $this->normalizeUrl($data['url'] ?? null),
            'rank' => $data['rank'] ?? 0,
            'status' => $data['status'] instanceof CmsStatus ? $data['status']->value : $data['status'],
        ]);
    }

    private function prepareClientUpdateData(array $data, Client $client): array
    {
        $translations = buildTranslations([
            'name' => $data['name'] ?? '',
        ], wantsAutoTranslate($data), [
            'name' => $client->getTranslations('name'),
        ]);

        return array_merge($data, $translations, [
            'logo' => $this->handleLogoUpload($data, $client->logo),
            'url' => $this->normalizeUrl($data['url'] ?? null),
            'rank' => $data['rank'] ?? $client->rank,
            'status' => $data['status'] instanceof CmsStatus ? $data['status']->value : $data['status'],
        ]);
    }

    private function handleLogoUpload(array $data, ?string $existingLogo = null): ?string
    {
        return ! empty($data['logo'])
            ? $this->upload($data['logo'], $this->logoUploadPath, null, $existingLogo)
            : $existingLogo;
    }

    private function normalizeUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        return $url !== '' ? $url : null;
    }
}
