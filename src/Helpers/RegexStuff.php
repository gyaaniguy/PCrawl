<?php

namespace Gyaaniguy\PCrawl\Helpers;

class RegexStuff
{
    public static function combineHeaders(array $headers, array $headersOriginal): array
    {
        $headersAssoc = self::headerToAssoc($headers);
        $originalHeadersAssoc = self::headerToAssoc($headersOriginal);
        $allHeaders = array_merge($originalHeadersAssoc, $headersAssoc);
        if (!empty($allHeaders)) {
            array_walk($allHeaders, function (&$val, $key) {
                $val = $key . ': ' . $val;
            });
        }
        return $allHeaders;
    }

    public static function headerToAssoc(array $headers): array
    {
        $assocHeaders = [];
        foreach ($headers as $headerStr) {
            if (strstr($headerStr, ':')) {
                if (preg_match('/([^:]+):\s*(.+)/', $headerStr, $matchesHeader)) {
                    $assocHeaders[$matchesHeader[1]] = $matchesHeader[2];
                }
            }
        }
        return $assocHeaders;
    }
}
