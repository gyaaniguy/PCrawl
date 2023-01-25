<?php

namespace Gyaaniguy\PCrawl\Response;

use Gyaaniguy\PCrawl\Tidy;

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

    public function tidy(): PResponseMods
    {
//        $config = array(
//            'indent' => true,
//            'output-xhtml' => true,
//            'wrap' => 200
//        );
//        $tidy = new Tidy();
//        $tidy->parseString($this->pRes->getBody(), $config, 'utf8');
//        $tidy->cleanRepair();
//        $this->pRes->setBody($tidy->value);
        return $this;
    }


    public function modBody(array $callbacks)
    {
        foreach ($callbacks as $middleware) {
            $this->pRes->setBody($middleware($this->pRes->getBody()));
        }
    }
}