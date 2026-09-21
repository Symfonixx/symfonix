<?php

namespace Modules\Cms\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

final class RankConstraint
{
    /**
     * @param  class-string<Model>  $modelClass
     */
    public static function minRank(string $modelClass, ?int $exceptId = null): int
    {
        $query = $modelClass::query();

        if ($exceptId !== null) {
            $query->where('id', '!=', $exceptId);
        }

        return max((int) $query->max('rank'), 1);
    }

    public static function rejectIfBelow(int $rank, int $minRank): ?RedirectResponse
    {
        if ($rank >= $minRank) {
            return null;
        }

        return back()
            ->withErrors(['rank' => __('Rank must be at least :min', ['min' => $minRank])])
            ->withInput();
    }
}
