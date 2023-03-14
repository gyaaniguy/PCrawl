<?php

namespace Gyaaniguy\PCrawl\Factories;

use Gyaaniguy\PCrawl\Request\Request;

class RequestFactory
{
    // create a request object for this libray as a factory pattern
    public static function create($client = null): Request
    {
        $req = new Request();
        if ($client) {
            $req->setClient($client);
        }
        return $req;
    }
}