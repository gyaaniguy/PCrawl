<?php

namespace Gyaaniguy\PCrawl\Response;

use Closure;

class PResponseDebug implements InterfaceResponseDebug
{
    public PResponse $res;
    public bool $resFail;

    public array $mustNotExistStrings;
    private int $mustBeHttpCode;
    private array $mustNotExistHttpCodes;
    private array $mustExistStrings;
    private array $mustExistRegex;
    private array $mustNotExistRegex;
    private array $expectedHeaders;
    private array $analysis;
    private array $customFailCallbacks;


    /**
     * Shows which of the criteria's added caused a match.
     * @return array
     */
    public function getFailDetail(): array
    {
        return $this->analysis;
    }

    public function setResponse(PResponse $res): PResponseDebug
    {
        $this->res = $res;
        return $this;
    }

    public function appendToMustNotExistStrings(array $strings): PResponseDebug
    {
        $this->mustNotExistStrings = array_merge($this->mustNotExistStrings, $strings);
        return $this;
    }

    public function setMustNotExistStrings(array $strings): PResponseDebug
    {
        $this->mustNotExistStrings = $strings;
        return $this;
    }

    public function unsetMustNotExistStrings(): PResponseDebug
    {
        $this->mustNotExistStrings = [];
        return $this;
    }

    public function setMustNotExistHttpCodes(array $httpCodes): PResponseDebug
    {
        $this->mustNotExistHttpCodes = $httpCodes;
        return $this;
    }

    public function unsetMustNotExistHttpCodes(): PResponseDebug
    {
        $this->mustNotExistHttpCodes = [];
        return $this;
    }

    public function setMustBeHttpCode(int $int): PResponseDebug
    {
        $this->mustBeHttpCode = $int;
        return $this;
    }

    public function unsetGoodHttpCode(): PResponseDebug
    {
        $this->mustBeHttpCode = -1;
        return $this;
    }

    public function appendToMustExistStrings(array $strings): PResponseDebug
    {
        $this->mustExistStrings = array_merge($this->mustExistStrings, $strings);
        return $this;
    }

    public function setMustExistStrings(array $strings): PResponseDebug
    {
        $this->mustExistStrings = $strings;
        return $this;
    }

    public function unsetMustExistStrings(): PResponseDebug
    {
        $this->mustExistStrings = [];
        return $this;
    }

    public function appendToMustExistRegex(array $strings): PResponseDebug
    {
        $this->mustExistRegex = array_merge($this->mustExistRegex, $strings);
        return $this;
    }

    public function setMustExistRegex(array $strings): PResponseDebug
    {
        $this->mustExistRegex = $strings;
        return $this;
    }

    public function unsetMustExistRegex(): PResponseDebug
    {
        $this->mustExistRegex = [];
        return $this;
    }

    public function appendToMustNotExistRegex(array $strings): PResponseDebug
    {
        $this->mustNotExistRegex = array_merge($this->mustNotExistRegex, $strings);
        return $this;
    }

    public function setMustNotExistRegex(array $strings): PResponseDebug
    {
        $this->mustNotExistRegex = $strings;
        return $this;
    }

    public function unsetRegexMustNotExist(): PResponseDebug
    {
        $this->mustNotExistRegex = [];
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
        $this->compareMustExistStrings();
        $this->compareMustNotExistStrings();
        $this->compareMustExistRegex();
        $this->compareMustNotExistRegex();
        $this->compareGoodHttpCode();
        $this->compareMustNotExistHttpCodes();
        $this->compareHeaders();
        $this->runCustomFailCallbacks();

        return $this->resFail;
    }

    /**
     * @return void
     */
    private function compareMustExistStrings(): void

    {
        if (!empty($this->mustExistStrings)) {
            array_map(function ($str) {
                $strExistsRes = stristr($this->res->getBody(), $str);
                return $this->compareSetComparisonResult($strExistsRes, $str, 'good_string', true);
            }, $this->mustExistStrings);
        }
    }

    private function compareSetComparisonResult($comparisonFound, $str, $key, $successIsGood): bool
    {
        if (($comparisonFound && $successIsGood === false) || (!$comparisonFound && $successIsGood === true)) {
            $this->analysis[$key][$str] = ($successIsGood === true ? ' not ' : '') . ' found';
            $this->resFail = true;
            return false;
        }
        return true;
    }

    /**
     * @return void
     */
    private function compareMustNotExistStrings(): void
    {
        if (!empty($this->mustNotExistStrings)) {
            array_map(function ($str) {
                $strExistsRes = stristr($this->res->getBody(), $str);
                return $this->compareSetComparisonResult($strExistsRes, $str, 'bad_string', false);
            }, $this->mustNotExistStrings);
        }
    }

    /**
     * @return void
     */
    private function compareMustExistRegex(): void
    {
        if (!empty($this->mustExistRegex)) {
            array_map(function ($str) {
                $strExistsRes = preg_match($str, $this->res->getBody());
                return $this->compareSetComparisonResult($strExistsRes, $str, 'good_regex', true);
            }, $this->mustExistRegex);
        }
    }

    /**
     * @return void
     */
    private function compareMustNotExistRegex(): void
    {
        if (!empty($this->mustNotExistRegex)) {
            array_map(function ($str) {
                $strExistsRes = preg_match($str, $this->res->getBody());
                return $this->compareSetComparisonResult($strExistsRes, $str, 'bad_regex', false);
            }, $this->mustNotExistRegex);
        }
    }

    /**
     * @return void
     */
    private function compareGoodHttpCode(): void
    {
        if (!empty($this->mustBeHttpCode) && $this->mustBeHttpCode != -1 && $this->res->getHttpCode(
            ) != $this->mustBeHttpCode) {
            $this->analysis['expected_httpcode'][$this->mustBeHttpCode] = ' not found';
            $this->resFail = true;
        }
    }

    /**
     * @return void
     */
    private function compareMustNotExistHttpCodes(): void
    {
        if (!empty($this->mustNotExistHttpCodes)) {
            array_map(function ($httpCode) {
                return $this->compareSetComparisonResult(
                    $this->res->getHttpCode() === $httpCode,
                    $httpCode,
                    'bad_http_code',
                    false
                );
            }, $this->mustNotExistHttpCodes);
        }
    }

    /**
     * @return void
     */
    private function compareHeaders(): void
    {
        if (!empty($this->expectedHeaders)) {
            $resHeaders = $this->res->getResponseHeaders();
            array_map(function ($expectedHeader) use ($resHeaders) {
                array_map(function ($resHeader) use ($expectedHeader, &$found) {
                    if (stristr($resHeader, $expectedHeader)) {
                        $found = true;
                    }
                }, $resHeaders);
                
              
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
