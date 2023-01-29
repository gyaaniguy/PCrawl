<?php

namespace Gyaaniguy\PCrawl\Loggers;
use Psr\Log\AbstractLogger;

class RequestLog extends AbstractLogger
{
    
    /**
     * Interpolates context values into the message placeholders.
     */
    private function interpolate ($message, array $context ) {
        if (is_array ($message)) {
            return $message;
        }
        // build a replacement array with braces around the context keys
        $replace = array ();
        foreach ($context as $key => $val) {
            if (is_object ($val) && get_class ($val) === 'DateTime') {
                $val = $val->format ('Y-m-d H:i:s');
            } elseif (is_object ($val)) {
                $val = json_encode ($val);
            } elseif (is_array ($val)) {
                $val = json_encode ($val);
            } elseif (is_resource ($val)) {
                $val = (string) $val;
            }elseif ($key === 'exception' && $val instanceof \Exception) {
                $val = $val->getMessage();
            }
            $replace['{' . $key . '}'] = $val;
        }

        // interpolate replacement values into the the message and return
        return strtr ($message, $replace);
    }

    public function log($level, $message, array $context = array())
    {
        
    }
}