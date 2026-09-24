<?php

namespace App\Services;

use App\Models\Perfume;
use Illuminate\Support\Collection;

class ScentFinder
{
    public static function recommendations(Collection $perfumes, ?string $style, ?string $occasion, ?string $gender): Collection
    {
        return $perfumes->map(function (Perfume $perfume) use ($style, $occasion, $gender) {
            $haystack = mb_strtolower(implode(' ', [
                $perfume->name, $perfume->brand, $perfume->description,
            ]));
            $score = 0;
            if ($gender && $perfume->gender === $gender) $score += 4;
            $styleKeyword = ['hoa' => 'hoa', 'go' => 'gỗ', 'vanilla' => 'vanilla', 'tuoi' => 'tươi', 'am' => 'ấm'][$style] ?? null;
            if ($styleKeyword && str_contains($haystack, $styleKeyword)) $score += 5;
            if ($occasion === 'hang-ngay' && str_contains($haystack, 'tươi')) $score += 2;
            if ($occasion === 'hen-ho' && (str_contains($haystack, 'hoa') || str_contains($haystack, 'ngọt'))) $score += 2;
            if ($occasion === 'tiec' && (str_contains($haystack, 'gỗ') || str_contains($haystack, 'hổ phách'))) $score += 2;
            $perfume->match_score = $score;
            return $perfume;
        })->sortByDesc('match_score')->take(6)->values();
    }
}
