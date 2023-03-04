<?php

namespace Gyaaniguy\PCrawl\HttpClients;

class PCurlClient extends CurlBaseClient
{
    public function __construct()
    {
        parent::__construct();
        $this->setClientOptions();
    }


    /**
     * Sets the default client options. Intended to make easily clients extendable.
     * Is automatically called and used if the base $clientOptions is set.
     * @return void
     */
    private function setClientOptions()
    {
        if (!empty($this->clientOptions)) {
            foreach ($this->clientOptions as $optionName => $value) {
                if (is_string($optionName) && $optionName == 'user_agent') {
                    $this->setUserAgent($value);
                }
                if (is_string($optionName) && $optionName == 'headers') {
                    $this->setHeaders($value);
                }
                if (is_string($optionName) && $optionName == 'redirect_num') {
                    $this->setRedirects($value);
                }
                if (is_string($optionName) && $optionName == 'cookies') {
                    $this->cookies($value);
                }
            }
        }
    }

    public function setUserAgent(string $userAgent): PCurlClient
    {
        $this->curlInitIf();
        $this->clientOptions['user_agent'] = $userAgent;
        curl_setopt($this->ch, CURLOPT_USERAGENT, $userAgent);
        return $this;
    }

    public function setHeaders(array $headers): PCurlClient
    {
        $this->curlInitIf();
        $this->clientOptions['headers'] = $headers;
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        return $this;
    }


    /**
     * Set the number of redirects allowed.
     * @param int $num
     * @return $this
     */
    public function setRedirects(int $num): PCurlClient
    {
        $this->curlInitIf();
        $this->clientOptions['redirect_num'] = $num;
        curl_setopt($this->ch, CURLOPT_MAXREDIRS, $num);
        return $this;
    }

    public function cookies(bool $enable): PCurlClient
    {
        $this->curlInitIf();
        $this->clientOptions['cookies'] = $enable;
        if ($enable) {
            if (empty($this->cookiePath)) {
                $this->cookiePath = '/tmp/' . uniqid(rand(999, 99999999), true);
            }
            curl_setopt($this->ch, CURLOPT_COOKIEJAR, $this->cookiePath);
            curl_setopt($this->ch, CURLOPT_COOKIEFILE, $this->cookiePath);
            return $this;
        }
        return $this->disableCookies();
    }
    protected function disableCookies(): PCurlClient
    {
        $this->curlInitIf();
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, '');
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, '');
        return $this;
    }
    public function clearCookies(): PCurlClient
    {
        $this->curlInitIf();
        if (!empty($this->cookiePath)) {
            $this->closeConnection();
            if (file_exists($this->cookiePath)) {
                unlink($this->cookiePath);
                $this->setClientOptions();
            }
        }
        return $this;
    }


    public function setConnectTimeout(int $timeout): PCurlClient
    {
        $this->curlInitIf();
        $this->clientOptions['connect_timeout'] = $timeout;
        curl_setopt($this->ch,CURLOPT_CONNECTTIMEOUT, $timeout);
        return $this;
    }
    public function setTimeout(int $timeout): PCurlClient
    {
        $this->curlInitIf();
        $this->clientOptions['timeout'] = $timeout;
        curl_setopt($this->ch,CURLOPT_TIMEOUT, $timeout);
        return $this;
    }
}
