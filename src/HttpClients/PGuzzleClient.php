<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\Helpers\RegexStuff;

class PGuzzleClient extends GuzzleBaseClient
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setRedirects(int $num): PGuzzleClient
    {
        unset($this->baseClient);
        $this->clientOptions['redirect_num'] = $num;
        return $this;
    }

    public function setUserAgent(string $userAgent): PGuzzleClient
    {
        unset($this->baseClient);
        $this->clientOptions['user_agent'] = $userAgent;
        return $this;
    }

    public function addHeaders(array $headers): PGuzzleClient
    {
        if (!empty($headers)) {
            $this->setHeaders(RegexStuff::combineHeaders($headers, $this->clientOptions['headers']));
        }
        return $this;
    }

    public function setHeaders(array $headers): PGuzzleClient
    {
        unset($this->baseClient);
        $this->clientOptions['headers'] = $headers;
        return $this;
    }

    public function cookies(bool $status): PGuzzleClient
    {
        $this->clientOptions['cookies'] = $status;
        unset($this->baseClient);
        return $this;
    }

    public function clearCookies(): PGuzzleClient
    {
        unset($this->baseClient);
        return $this;
    }

}