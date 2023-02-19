<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\Response\PResponse;
use InvalidArgumentException;

class CurlCustomClient extends CurlBaseClient
{
    protected array $customClientOptions = [] ;
    public function __construct()
    {
        parent::__construct();
        if (!empty($this->customClientOptions)) {
            $this->setCustomOptions($this->customClientOptions);
        }
    }

    public function setCustomOptions(array $customClientOptions): void
    {
        $this->curlInitIf();
        curl_setopt_array($this->ch, $customClientOptions);
    }
}