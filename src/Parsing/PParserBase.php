<?php

namespace Gyaaniguy\PCrawl\Parsing;

use QueryPath\CSS\ParseException;
use QueryPath\DOMQuery;
use QueryPath\QueryPath;

class PParserBase
{
    public DOMQuery $qp;
    public DOMQuery $qpXML;
    private string $body;

    public function __construct(string $body, array $options = [])
    {
        $this->body = $body;
        $this->qp = QueryPath::withHTML5($body,'',$options);
    }

    public function setResponse(string $body, array $options = [])
    {
        $this->body = $body;
        $this->qp = QueryPath::withXML($body,'',$options);
    }

    /**
     * @throws ParseException
     */
    public function find(string $query)
    {
        return $this->qp->find($query);
    }

    /**
     * @throws ParseException
     */
    public function xpath(string $query)
    {
        $this->getQuerypathXMLObject();
        return $this->qpXML->xpath($query);
    }

    /**
     * @throws ParseException
     */
    public function children(string $query = null)
    {
        return $this->qp->children($query);
    }

    /**
     * @return void
     */
    public function getQuerypathXMLObject(string $body = ''): void
    {
        if (empty($this->qpXML)) {
            $this->qpXML = QueryPath::withXML($body ?: $this->body);
        }
    }


}
