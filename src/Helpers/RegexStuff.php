<?php

namespace Gyaaniguy\PCrawl\Helpers;

class RegexStuff
{
    public static function headerToAssoc(array $headers){
        $assocHeaders = [];
        foreach ($headers as $headerStr) {
            if (strstr($headerStr,':')) {
                if (preg_match('/([^:]+):\s*(.+)/',$headerStr, $matchesHeader)) {
                    $assocHeaders[$matchesHeader[1]] = $matchesHeader[2];
                }
            }
        }
        return $assocHeaders;
    }
    public static function endsWith(string $haystack, string $needle): bool
    {
        if (strlen($needle) === 0) {
            return true;
        }

        return str_ends_with($haystack, $needle);
    }
}