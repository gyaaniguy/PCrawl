<?php

namespace Gyaaniguy\PCrawl\Parsing;

use QueryPath\DOMQuery;

class PParser
{
    private DOMQuery $qp;

    public function __construct(string $body)
    {
        $this->qp = qp($body);
    }


}