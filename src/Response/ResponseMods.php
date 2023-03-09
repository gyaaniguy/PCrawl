<?php

namespace Gyaaniguy\PCrawl\Response;

use Exception;
use Tidy;

class ResponseMods
{
    public Response $pRes;

    public function __construct(Response $pRes)
    {
        $this->pRes = $pRes;
    }

    /**
     * Cleans html through the tidy extension.
     * @return $this
     * @throws Exception
     */
    public function tidy(): ResponseMods
    {
        if (!extension_loaded('tidy')) {
            throw new Exception("Tidy not installed");
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


    /**
     * Modifying the body of the response using callbacks
     * @param array $callbacks
     * @return void
     */
    public function modBody(array $callbacks)
    {
        foreach ($callbacks as $middleware) {
            $this->pRes->setBody($middleware($this->pRes->getBody()));
        }
    }
}
