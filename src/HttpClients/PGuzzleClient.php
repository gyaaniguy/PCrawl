<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\Helpers\RegexStuff;

class PPGuzzleClient extends PGuzzleBaseClient
{
    public function __construct()
    {
        parent::__construct();
        $this->clientOptions['timeout'] = 60;
    }

    public function setRedirects(int $num): PPGuzzleClient
    {
        unset($this->baseClient);
        $this->clientOptions['redirect_num'] = $num;
        return $this;
    }

    public function setUserAgent(string $userAgent): PPGuzzleClient
    {
        unset($this->baseClient);
        $this->clientOptions['user_agent'] = $userAgent;
        return $this;
    }

    public function addHeaders(array $headers): PPGuzzleClient
    {
        if (!empty($headers)) {
            $this->setHeaders(RegexStuff::combineHeaders($headers, $this->clientOptions['headers']));
        }
        return $this;
    }

    public function setHeaders(array $headers): PPGuzzleClient
    {
        unset($this->baseClient);
        $this->clientOptions['headers'] = $headers;
        return $this;
    }

    public function cookies(bool $status): PPGuzzleClient
    {
        $this->clientOptions['cookies'] = $status;
        unset($this->baseClient);
        return $this;
    }

    public function setConnectTimeout(int $timeout): PPGuzzleClient
    {
        $this->clientOptions['connect_timeout'] = $timeout;
        unset($this->baseClient);
        return $this;
    }

    public function clearCookies(): PPGuzzleClient
    {
        unset($this->baseClient);
        return $this;
    }
}
