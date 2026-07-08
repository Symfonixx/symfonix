<?php

namespace Modules\Team\Repositories;

use Illuminate\Support\Collection;
use Modules\Cms\Enums\CmsStatus;
use Modules\Core\Traits\ExceptionHandlerTrait;
use Modules\Core\Traits\FileTrait;
use Modules\Team\Models\Team;

class TeamRepository
{
    use ExceptionHandlerTrait, FileTrait;

    private string $avatarUploadPath = 'teams';

    private string $resumeUploadPath = 'teams/resumes';

    public function all(array $columns = ['*']): Collection
    {
        return Team::all($columns);
    }

    public function find($id): ?Team
    {
        return Team::find($id);
    }

    public function store(array $data): mixed
    {
        return $this->execute(function () use ($data) {
            $teamData = $this->prepareTeamData($data);
            Team::create($teamData);
            session()->flushMessage(true);
        });
    }

    private function prepareTeamData(array $data, ?string $existingImage = null): array
    {
        $path = $this->handleImageUpload($data, $existingImage);
        $resumePath = $this->handleResumeUpload($data, null);
        $translations = buildTranslations([
            'name' => $data['name'] ?? '',
            'position' => $data['position'] ?? '',
        ], wantsAutoTranslate($data));

        return array_merge($data, $translations, [
            'avatar' => $path,
            'resume' => $resumePath,
            'status' => $data['status'] instanceof CmsStatus ? $data['status']->value : $data['status'],
        ]);
    }

    private function prepareTeamUpdateData(array $data, Team $team): array
    {
        $path = $this->handleImageUpload($data, $team->avatar);
        $resumePath = $this->handleResumeUpload($data, $team->resume);
        $translations = buildTranslations([
            'name' => $data['name'] ?? '',
            'position' => $data['position'] ?? '',
        ], wantsAutoTranslate($data), [
            'name' => $team->getTranslations('name'),
            'position' => $team->getTranslations('position'),
        ]);

        return array_merge($data, $translations, [
            'avatar' => $path,
            'resume' => $resumePath,
            'status' => $data['status'] instanceof CmsStatus ? $data['status']->value : $data['status'],
        ]);
    }

    private function handleImageUpload(array $data, ?string $existingImage = null): ?string
    {
        return $data['avatar']
            ? $this->upload($data['avatar'], $this->avatarUploadPath, null, $existingImage)
            : $existingImage;
    }

    private function handleResumeUpload(array $data, ?string $existingResume = null): ?string
    {
        return $data['resume']
            ? $this->upload($data['resume'], $this->resumeUploadPath, null, $existingResume)
            : $existingResume;
    }

    public function update(array $data, Team $team): mixed
    {
        return $this->execute(function () use ($data, $team) {
            $teamData = $this->prepareTeamUpdateData($data, $team);
            $team->update($teamData);
            session()->flushMessage(true);

            return true;
        });
    }

    public function deleteMulti(array $ids): ?bool
    {
        return $this->execute(function () use ($ids) {
            $images = Team::whereIn('id', $ids)->pluck('avatar')->filter()->toArray();
            $resumes = Team::whereIn('id', $ids)->pluck('resume')->filter()->toArray();
            Team::destroy($ids);
            $this->deleteFile($images);
            $this->deleteFile($resumes);
            session()->flushMessage(true);

            return true;
        });
    }
}
