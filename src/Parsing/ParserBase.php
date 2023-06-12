<?php

namespace Gyaaniguy\PCrawl\Parsing;

use QueryPath\CSS\ParseException;
use QueryPath\DOMQuery;
use QueryPath\QueryPath;

class ParserBase
{
    public DOMQuery $querypath;
    public DOMQuery $qpXML;
    protected string $body = '';

    public function __construct(string $body = '', array $options = [])
    {
        if (!empty($body)) {
            $this->body = $body;
            $this->querypath = QueryPath::withHTML($body, '', $options);
        }
    }

    public function setResponse(string $body, array $options = []): ParserBase
    {
        $this->body = $body;
        $this->querypath = QueryPath::withHTML5($body, '', $options);
        return $this;
    }

    /**
     * @throws ParseException
     */
    public function find(string $query)
    {
        $this->isBodyEmpty();
        return $this->querypath->find($query);
    }

    public function xpathObj(string $query)
    {
        return $this->querypath->xpath($query);
    }
    /**
     * @throws ParseException
     */
    public function xpathText(string $query)
    {
        return $this->xpathObj($query)->eq(0)->text();
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

    /**
     * @throws ParseException
     */
    public function children(string $query = null)
    {
        $this->isBodyEmpty();
        return $this->querypath->children($query);
    }

    /**
     * @throws ParseException
     */
    private function isBodyEmpty()
    {
        if (empty($this->querypath)) {
            throw new ParseException('Body is empty');
        }
    }
}
