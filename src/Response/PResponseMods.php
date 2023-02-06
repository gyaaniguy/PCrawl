<?php

namespace Gyaaniguy\PCrawl\Response;

class PResponseMods
{
    public PResponse $pRes;

    public function __construct(PResponse $pRes)
    {
        $this->pRes = $pRes;
    }

    public function toAbsoluteUrls(): PResponseMods
    {
        return $this;
    }
    public function addNikhil(): PResponseMods
    {
        $this->pRes->setBody($this->pRes->getBody()."nikhil");
        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function tidy(): PResponseMods
    {
        if (!class_exists('tidy')) {
            throw new \Exception("Tidy not installed");
        }
        $tidy = new Tidy();
        
        $config = array(
            'indent' => true,
            'output-xhtml' => true,
            'wrap' => 200
        );
        $tidy->parseString($this->pRes->getBody(), $config, 'utf8');
        $tidy->cleanRepair();
        $this->pRes->setBody($tidy->value);
        return $this;
    }


    public function modBody(array $callbacks)
    {
        foreach ($callbacks as $middleware) {
            $this->pRes->setBody($middleware($this->pRes->getBody()));
        }
    }
}