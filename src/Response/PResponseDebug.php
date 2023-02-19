<?php

namespace Gyaaniguy\PCrawl\Response;

use Closure;

class PResponseDebug implements InterfaceResponseDebug
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
    private array $customFailCallbacks;


    public function getFailDetail()
    {
        return $this->analysis;
        // TODO: Implement getFailDetail() method.
    }

    public function setResponse(PResponse $res): PResponseDebug
    {
        $this->res = $res;
        return $this;
    }

    public function appendBadStrings(array $strings): PResponseDebug
    {
        $this->badStrings = array_merge($this->badStrings, $strings);
        return $this;
    }

    public function setBadStrings(array $strings): PResponseDebug
    {
        $this->badStrings = $strings;
        return $this;
    }

    public function unsetBadStrings(): PResponseDebug
    {
        $this->badStrings = [];
        return $this;
    }

    public function setGoodHttpCode(int $int): PResponseDebug
    {
        $this->goodResponseHttpCode = $int;
        return $this;
    }

    public function unsetGoodHttpCode(): PResponseDebug
    {
        $this->goodResponseHttpCode = -1;
        return $this;
    }

    public function appendGoodStrings(array $strings): PResponseDebug
    {
        $this->goodStrings = array_merge($this->goodStrings, $strings);
        return $this;
    }

    public function setGoodStrings(array $strings): PResponseDebug
    {
        $this->goodStrings = $strings;
        return $this;
    }

    public function unsetGoodStrings(): PResponseDebug
    {
        $this->goodStrings = [];
        return $this;
    }

    public function appendGoodRegex(array $strings): PResponseDebug
    {
        $this->goodRegex = array_merge($this->goodRegex, $strings);
        return $this;
    }

    public function setGoodRegex(array $strings): PResponseDebug
    {
        $this->goodRegex = $strings;
        return $this;
    }

    public function unsetGoodRegex(): PResponseDebug
    {
        $this->goodRegex = [];
        return $this;
    }

    public function appendBadRegex(array $strings): PResponseDebug
    {
        $this->badRegex = array_merge($this->badRegex, $strings);
        return $this;
    }

    public function setBadRegex(array $strings): PResponseDebug
    {
        $this->badRegex = $strings;
        return $this;
    }

    public function unsetBadRegex(): PResponseDebug
    {
        $this->badRegex = [];
        return $this;
    }

    public function setContainExpectedHeaders(array $strings): PResponseDebug
    {
        $this->expectedHeaders = $strings;
        return $this;
    }

    public function unsetContainExpectedHeaders(): PResponseDebug
    {
        $this->expectedHeaders = [];
        return $this;
    }

    public function isFail(): bool
    {
        $this->resFail = false;
        $this->analysis = [];
        $this->compareGoodStrings();
        $this->compareBadStrings();
        $this->compareGoodRegex();
        $this->compareBadRegex();
        $this->compareHttpCode();
        $this->compareHeaders();
        $this->runCustomFailCallbacks();

        return $this->resFail;
    }

    /**
     * @return void
     */
    private function compareGoodStrings(): void

    {
        if (!empty($this->goodStrings)) {
            array_map(function ($str) {
                $stristr = stristr($this->res->getBody(), $str);
                return $this->compareSetComparisonResult($stristr, $str, 'good_string', true);
            }, $this->goodStrings);
        }
    }

    private function compareSetComparisonResult($stristr, $str, $key, $successIsGood): bool
    {
        if (($stristr && $successIsGood === false) || (!$stristr && $successIsGood === true)) {
            $this->analysis[$key][$str] = ($successIsGood === true ? ' not ' : '') . ' found';
            $this->resFail = true;
            return false;
        }
        return true;
    }

    /**
     * @return void
     */
    private function compareBadStrings(): void
    {
        if (!empty($this->badStrings)) {
            array_map(function ($str) {
                $stristr = stristr($this->res->getBody(), $str);
                return $this->compareSetComparisonResult($stristr, $str, 'bad_string', false);
            }, $this->badStrings);
        }
    }

    /**
     * @return void
     */
    private function compareGoodRegex(): void
    {
        if (!empty($this->goodRegex)) {
            array_map(function ($str) {
                $stristr = preg_match($str, $this->res->getBody());
                return $this->compareSetComparisonResult($stristr, $str, 'good_regex', true);
            }, $this->goodRegex);
        }
    }

    /**
     * @return void
     */
    private function compareBadRegex(): void
    {
        if (!empty($this->badRegex)) {
            array_map(function ($str) {
                $stristr = preg_match($str, $this->res->getBody());
                return $this->compareSetComparisonResult($stristr, $str, 'bad_regex', false);
            }, $this->badRegex);
        }
    }

    /**
     * @return void
     */
    private function compareHttpCode(): void
    {
        if (!empty($this->goodResponseHttpCode) && $this->goodResponseHttpCode != -1 && $this->res->getHttpCode(
            ) != $this->goodResponseHttpCode) {
            $this->analysis['expected_httpcode'][$this->goodResponseHttpCode] = ' not found';
            $this->resFail = true;
        }
    }

    /**
     * @param array $resHeaders
     * @return void
     */
    private function compareHeaders(): void
    {
        if (!empty($this->expectedHeaders)) {
            $resHeaders = $this->res->getResponseHeaders();
            array_map(function ($expectedHeader) use ($resHeaders) {
                $found = collect($resHeaders)->contains(function ($resHeader) use ($expectedHeader) {
                    return stristr($resHeader, $expectedHeader);
                });
                if (!$found) {
                    $this->analysis['expected_header'][$expectedHeader] = ' not found';
                    $this->resFail = true;
                }
            }, $this->expectedHeaders);
        }
    }

    private function runCustomFailCallbacks()
    {
        if (!empty($this->customFailCallbacks)) {
            foreach ($this->customFailCallbacks as $customFailCallback) {
                if (!$customFailCallback($this->res)) {
                    $this->resFail = true;
                }
            }
        }
    }

    public function setCustomFailCondition(Closure $callbackFunction)
    {
        $this->customFailCallbacks[] = $callbackFunction;
    }
}
