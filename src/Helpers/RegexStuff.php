<?php

namespace Gyaaniguy\PCrawl\Helpers;

class RegexStuff
{
    public static function headerToAssoc(array $headers): array
    {
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
}