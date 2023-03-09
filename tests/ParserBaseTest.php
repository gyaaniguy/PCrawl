<?php

namespace Gyaaniguy\PCrawl\Parsing;

use PHPUnit\Framework\TestCase;
use QueryPath\DOMQuery;

class ParserBaseTest extends TestCase
{
    private string $body = '<html><body><a href="http://www.google.com">Google</a><div id="parent" class=" max-w-xs mx-auto  bg-white rounded-xl flex items-center space-x-4 p-6">         <div class="shrink-0">            <img class="h-8 w-8 faceimg" src="Facebook_f_logo_(2021).svg.png" alt="ChitChat Logo">        <a href="http://www.bing.com">Bing</a></div>        <div>           <div class="text-xl font-medium text-black">ChitChat</div>          <p class="text-slate-500">You have a new message!</p>        </div>  <div class="text-xl font-medium text-black">china</div>    </div></body></html>';

    public function testFind()
    {
        $pParserBase = $this->makePParserBase();
        $links = $pParserBase->find('a');
        $this->assertInstanceOf(ParserBase::class, $pParserBase);
        $this->assertInstanceOf(DOMQuery::class, $links);
        $this->assertEquals(2, count($links));
    }

    public function makePParserBase(string $body = ''): ParserBase
    {
        return new ParserBase($body ?: $this->body);
    }

    public function testNotGettingMissingElements()
    {
        $pParserBase = $this->makePParserBase();
        $missing = $pParserBase->find('b');
        $this->assertInstanceOf(DOMQuery::class, $missing);
        $this->assertInstanceOf(ParserBase::class, $pParserBase);
        $this->assertEquals(0, count($missing));
    }

    public function testXpath()
    {
        $body = '<html lang="en"><body><a href="http://www.google.com">Google</a></body></html>';
        $pParserBase = $this->makePParserBase($body);
        $links = $pParserBase->xpath('//a');
        $this->assertInstanceOf(ParserBase::class, $pParserBase);
        $this->assertInstanceOf(DOMQuery::class, $links);

        $this->assertEquals(1, count($links));
    }

    public function testChildren()
    {
        $pParserBase = $this->makePParserBase();
        $divs = $pParserBase->find('div[@id="parent"]')->children();
        $this->assertInstanceOf(DOMQuery::class, $divs);
        $this->assertEquals(3, count($divs));
    }
}
