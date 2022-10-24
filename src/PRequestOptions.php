<?php namespace Gyaaniguy\PCrawl;

/**
*  A sample class
*
*  Use this section to define what this class is doing, the PHPDocumentator will use this
*  to automatically generate an API documentation using this information.
*
*  @author yourname
*/
class PRequestOptions{

    protected $useTidy = false;
    protected $sleepBetween = 100; //ms

    /**
     * @param int $sleepBetween
     * @return PRequestOptions
     */
    public function setSleepBetween(int $sleepBetween): PRequestOptions
    {
        $this->sleepBetween = $sleepBetween;
        return $this;
    }

    /**
     * @param bool $useTidy
     * @return PRequestOptions
     */
    public function setUseTidy(bool $useTidy): PRequestOptions
    {
        $this->useTidy = $useTidy;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUseTidy(): bool
    {
        return $this->useTidy;
    }

    /**
     * @return int
     */
    public function getSleepBetween(): int
    {
        return $this->sleepBetween;
    }


}