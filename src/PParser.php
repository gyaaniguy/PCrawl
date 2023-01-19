<?php

namespace Gyaaniguy\PCrawl;

class PParser
{
    private \QueryPath\DOMQuery $qp;

    public function __construct(string $body)
    {
        $this->qp = qp($body);
    }


}