<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\Helpers\RegexStuff;

class GuzzleClient extends GuzzleBaseClient
{
    public function __construct()
    {
        parent::__construct();
        $this->clientOptions['timeout'] = 60;
    }

    public function setRedirects(int $num): GuzzleClient
    {
        unset($this->baseClient);
        $this->clientOptions['redirect_num'] = $num;
        return $this;
    }

    public function setUserAgent(string $userAgent): GuzzleClient
    {
        unset($this->baseClient);
        $this->clientOptions['user_agent'] = $userAgent;
        return $this;
    }

    public function addHeaders(array $headers): GuzzleClient
    {
        if (!empty($headers)) {
            $this->setHeaders(RegexStuff::combineHeaders($headers, $this->clientOptions['headers']));
        }
        return $this;
    }

    public function setHeaders(array $headers): GuzzleClient
    {
        unset($this->baseClient);
        $this->clientOptions['headers'] = $headers;
        return $this;
    }

    public function cookies(bool $status): GuzzleClient
    {
        $this->clientOptions['cookies'] = $status;
        unset($this->baseClient);
        return $this;
    }

    public function setConnectTimeout(int $timeout): GuzzleClient
    {
        $this->clientOptions['connect_timeout'] = $timeout;
        unset($this->baseClient);
        return $this;
    }

    public function setTimeout(int $timeout): GuzzleClient
    {
        $this->clientOptions['timeout'] = $timeout;
        unset($this->baseClient);
        return $this;
    }

    public function clearCookies(): GuzzleClient
    {
        unset($this->baseClient);
        return $this;
    }


    /**
     * Set proxy to be used by the client
     * @param string $proxyPort - host:port - '1.1.1.1:8080' OR 'bestproxy:8080'
     * @param string $proxyAuth - username:password optional
     * @return $this
     */
    public function setProxy(string $proxyPort, string $proxyAuth = ''): GuzzleClient
    {
        $this->clientOptions['proxyPort'] = $proxyPort;
        if (!empty($proxyAuth)) {
            $this->clientOptions['proxyAuth'] = $proxyAuth;
        }
        return $this;
    }
}
