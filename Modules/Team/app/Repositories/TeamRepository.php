<?php

namespace Modules\Team\Repositories;

use Exception;
use Illuminate\Support\Collection;
use Log;
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
        $transName = [app()->getLocale() => $data['name']];
        $transPosition = [app()->getLocale() => $data['position']];

        foreach (otherLangs() as $lang) {
            try {
                $transName[$lang] = autoGoogleTranslator($lang, $data['name'] ?? '');
                $transPosition[$lang] = autoGoogleTranslator($lang, $data['position'] ?? '');
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }

        return array_merge($data, [
            'avatar' => $path,
            'resume' => $resumePath,
            'name' => $transName,
            'position' => $transPosition,
            // Persist status as enum string into the status column
            'status' => $data['status'] instanceof CmsStatus ? $data['status']->value : $data['status'],
        ]);
    }

    /**
     * Prepare team data for update without auto translation.
     * Only the current locale is updated; other locales are preserved.
     */
    private function prepareTeamUpdateData(array $data, Team $team): array
    {
        $path = $this->handleImageUpload($data, $team->avatar);
        $resumePath = $this->handleResumeUpload($data, $team->resume);

        $locale = app()->getLocale();

        $transName = $team->getTranslations('name');
        $transPosition = $team->getTranslations('position');

        $transName[$locale] = $data['name'] ?? ($transName[$locale] ?? '');
        $transPosition[$locale] = $data['position'] ?? ($transPosition[$locale] ?? '');

        return array_merge($data, [
            'avatar' => $path,
            'resume' => $resumePath,
            'name' => $transName,
            'position' => $transPosition,
            // Persist status as enum string into the status column
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
