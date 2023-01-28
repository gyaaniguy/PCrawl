<?php

namespace Gyaaniguy\PCrawl\Loggers;

use GuzzleHttp\Client;
use Gyaaniguy\PCrawl\Response\PResponse;

interface InterfaceResponseDebug
{
    public function isFail();
    public function getFailDetail();
}