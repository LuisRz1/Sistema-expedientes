<?php

namespace App\Services;

class FuzzyMatcherService
{
    /**
     * Find the best matching catalog item.
     * Returns the id of the best match, or null if similarity is below threshold.
     */
    public static function findBestMatch(
        string $input,
        array  $catalog,
        string $nameKey,
        string $idKey,
        int    $threshold = 50
    ): ?int {
        $result = self::score($input, $catalog, $nameKey, $idKey, $threshold);
        return $result['id'];
    }

    /**
     * Return the top N closest catalog items sorted by score descending.
     * Each entry: ['id' => int, 'name' => string, 'score' => float]
     */
    public static function getSuggestions(
        string $input,
        array  $catalog,
        string $nameKey,
        string $idKey,
        int    $limit = 5
    ): array {
        $input = mb_strtoupper(self::normalizeRaw($input));
        if (empty($input)) return [];
        $inputNorm = self::normalize($input);

        $scores = [];
        foreach ($catalog as $item) {
            $name     = mb_strtoupper(self::normalizeRaw(trim($item[$nameKey])));
            $nameNorm = self::normalize($name);

            if ($inputNorm === $nameNorm) {
                $scores[] = ['id' => $item[$idKey], 'name' => $item[$nameKey], 'score' => 100.0];
                continue;
            }

            $containScore = 0;
            if (str_contains($nameNorm, $inputNorm) || str_contains($inputNorm, $nameNorm)) {
                $containScore = 20;
            }

            similar_text($inputNorm, $nameNorm, $percent);
            $maxLen   = max(mb_strlen($inputNorm), mb_strlen($nameNorm), 1);
            $dist     = levenshtein(mb_substr($inputNorm, 0, 255), mb_substr($nameNorm, 0, 255));
            $levScore = (1 - $dist / $maxLen) * 100;
            $score    = ($percent + $levScore) / 2 + $containScore;

            $scores[] = ['id' => $item[$idKey], 'name' => $item[$nameKey], 'score' => round($score, 1)];
        }

        usort($scores, fn($a, $b) => $b['score'] <=> $a['score']);
        return array_slice($scores, 0, $limit);
    }

    /**
     * Internal: compute best match and return ['id' => ?int, 'score' => float, 'name' => ?string]
     */
    private static function score(
        string $input,
        array  $catalog,
        string $nameKey,
        string $idKey,
        int    $threshold
    ): array {
        $input = mb_strtoupper(self::normalizeRaw($input));
        if (empty($input)) {
            return ['id' => null, 'score' => -1, 'name' => null];
        }

        $inputNorm = self::normalize($input);
        $bestId    = null;
        $bestScore = -1;
        $bestName  = null;

        foreach ($catalog as $item) {
            $name     = mb_strtoupper(self::normalizeRaw(trim($item[$nameKey])));
            $nameNorm = self::normalize($name);

            if ($inputNorm === $nameNorm) {
                return ['id' => $item[$idKey], 'score' => 100.0, 'name' => $item[$nameKey]];
            }

            $containScore = 0;
            if (str_contains($nameNorm, $inputNorm) || str_contains($inputNorm, $nameNorm)) {
                $containScore = 20;
            }

            similar_text($inputNorm, $nameNorm, $percent);
            $maxLen   = max(mb_strlen($inputNorm), mb_strlen($nameNorm), 1);
            $dist     = levenshtein(mb_substr($inputNorm, 0, 255), mb_substr($nameNorm, 0, 255));
            $levScore = (1 - $dist / $maxLen) * 100;
            $score    = ($percent + $levScore) / 2 + $containScore;

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestId    = $item[$idKey];
                $bestName  = $item[$nameKey];
            }
        }

        return [
            'id'    => $bestScore >= $threshold ? $bestId : null,
            'score' => $bestScore,
            'name'  => $bestName,
        ];
    }

    /**
     * Strip non-printable / unicode whitespace variants before processing.
     * Handles BOM, non-breaking space (U+00A0), zero-width chars, etc.
     */
    public static function normalizeRaw(string $str): string
    {
        // Remove BOM
        $str = str_replace("\xEF\xBB\xBF", '', $str);
        // Non-breaking space and other unicode spaces → regular space
        $str = preg_replace('/[\x{00A0}\x{200B}\x{FEFF}\x{00AD}]/u', ' ', $str);
        // Collapse all whitespace
        $str = preg_replace('/\s+/', ' ', $str);
        return trim($str);
    }

    /**
     * Normalise a string for fuzzy comparison:
     * - Strip accents/diacritics
     * - Collapse separators (/, -) into a single space
     * - Collapse multiple spaces
     */
    public static function normalize(string $str): string
    {
        $transliterations = [
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u',
            'Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U',
            'ñ'=>'n','Ñ'=>'N','ü'=>'u','Ü'=>'U',
        ];
        $str = strtr($str, $transliterations);
        $str = preg_replace('/[\\/\\-]+/', ' ', $str);
        $str = preg_replace('/\s+/', ' ', $str);
        return trim($str);
    }
}
