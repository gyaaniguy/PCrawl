<?php

namespace Gyaaniguy\PCrawl\Loggers;

use Gyaaniguy\PCrawl\Response\PResponse;

use function foo\func;

class ResponseDebug implements InterfaceResponseDebug
{
    public PResponse $res;
    public bool $resFail;

    public array $badStrings;
    private int $goodResponseHttpCode;
    private array $goodStrings;
    private array $goodRegex;
    private array $badRegex;
    private array $expectedHeaders;
    private array $analysis;



    public function getFailDetail()
    {
        // TODO: Implement getFailDetail() method.
    }

    public function setResponse(PResponse $res): ResponseDebug
    {
        $this->res = $res;
        return $this;
    }

    public function appendBadStrings(array $strings): ResponseDebug
    {
        $this->badStrings = array_merge($this->badStrings, $strings);
        return $this;
    }

    public function setBadStrings(array $strings): ResponseDebug
    {
        $this->badStrings = $strings;
        return $this;
    }

    public function unsetBadStrings(): ResponseDebug
    {
        $this->badStrings = [];
        return $this;
    }

    public function setGoodHttpCode(int $int): ResponseDebug
    {
        $this->goodResponseHttpCode = $int;
        return $this;
    }

    public function unsetGoodHttpCode(): ResponseDebug
    {
        $this->goodResponseHttpCode = -1;
        return $this;
    }

    public function appendGoodStrings(array $strings): ResponseDebug
    {
        $this->goodStrings = array_merge($this->goodStrings, $strings);
        return $this;
    }

    public function setGoodStrings(array $strings): ResponseDebug
    {
        $this->goodStrings = $strings;
        return $this;
    }

    public function unsetGoodStrings(): ResponseDebug
    {
        $this->goodStrings = [];
        return $this;
    }

    public function appendGoodRegex(array $strings): ResponseDebug
    {
        $this->goodRegex = array_merge($this->goodRegex, $strings);
        return $this;
    }

    public function setGoodRegex(array $strings): ResponseDebug
    {
        $this->goodRegex = $strings;
        return $this;
    }

    public function unsetGoodRegex(): ResponseDebug
    {
        $this->goodRegex = [];
        return $this;
    }

    public function appendBadRegex(array $strings): ResponseDebug
    {
        $this->badRegex = array_merge($this->badRegex, $strings);
        return $this;
    }

    public function setBadRegex(array $strings): ResponseDebug
    {
        $this->badRegex = $strings;
        return $this;
    }

    public function unsetBadRegex(): ResponseDebug
    {
        $this->badRegex = [];
        return $this;
    }

    public function setContainExpectedHeaders(array $strings): ResponseDebug
    {
        $this->expectedHeaders = $strings;
        return $this;
    }

    public function unsetContainExpectedHeaders(): ResponseDebug
    {
        $this->expectedHeaders = [];
        return $this;
    }

    public function isFail(): bool
    {
        $this->resFail = false;
        $this->compareGoodStrings();
        $this->compareBadStrings();
        $this->compareGoodRegex();
        $this->compareBadRegex();
        $this->compareHttpCode();
        $this->compareHeaders();
        
        return $this->resFail;
    }



    private function criteriaSearchStore($stristr, $str, $key, $successIsGood)
    {
        if ( ($stristr && $successIsGood === false) || (!$stristr && $successIsGood === true)) {
            $this->analysis[$key][$str] = ($successIsGood === true ? ' not ': '').' found';
            $this->resFail = true;
            return false;
        }        
        return true;
    }

    /**
     * @param array $resHeaders
     * @return void
     */
    public function compareHeaders(): void
    {
        if (!empty($this->expectedHeaders)) {
            $resHeaders = $this->res->getResponseHeaders();
            collect($this->expectedHeaders)->every(function ($expectedHeader) use ($resHeaders) {
                $found = collect($resHeaders)->contains(function ($resHeader) use ($expectedHeader) {
                    return stristr($resHeader, $expectedHeader);
                });
                if (!$found) {
                    $this->analysis['expected_header'][$expectedHeader] = ' not found';
                    $this->resFail = true;
                }
            });
        }
    }

    /**
     * @return void
     */
    public function compareHttpCode(): void
    {
        if (!empty($this->goodResponseHttpCode) && $this->res->getHttpCode() != $this->goodResponseHttpCode) {
            $this->analysis['expected_httpcode'][$this->goodResponseHttpCode] = ' not found';
            $this->resFail = true;
        }
    }

    /**
     * @return void
     */
    public function compareBadStrings(): void
    {
        if (!empty($this->badStrings)) {
            collect($this->badStrings)->every(function ($str, $key) {
                $stristr = stristr($this->res->getBody(), $str);
                return $this->criteriaSearchStore($stristr, $str, 'bad_string', false);
            });
        }
    }

    /**
     * @return void
     */
    public function compareGoodStrings(): void
    {
        if (!empty($this->goodStrings)) {            
            collect($this->goodStrings)->every(function ($str, $key) {
                $stristr = stristr($this->res->getBody(), $str);
                return $this->criteriaSearchStore($stristr, $str, 'bad_string', true);
            });
        }
    }

    /**
     * @return void
     */
    public function compareGoodRegex(): void
    {
        if (!empty($this->goodRegex)) {            
            collect($this->goodRegex)->every(function ($str) {
                $stristr = preg_match($str, $this->res->getBody());
                return $this->criteriaSearchStore($stristr, $str, 'good_regex', true);
            });
        }
    }

    /**
     * @return void
     */
    public function compareBadRegex(): void
    {
        if (!empty($this->badRegex)){          
            collect($this->badRegex)->every(function ($str) {
                $stristr = preg_match($str, $this->res->getBody());
                return $this->criteriaSearchStore($stristr, $str, 'bad_regex', false);
            });
        }
    }


}
