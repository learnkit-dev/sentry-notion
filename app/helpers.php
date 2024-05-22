<?php

function extractNumbers($string): ?int {
    preg_match_all('/\d+/', $string, $matches);

    $allMatches = $matches[0] ?? null;

    if (!$allMatches) {
        return null;
    }

    return ((int)$allMatches[0]) ?? null;
}
