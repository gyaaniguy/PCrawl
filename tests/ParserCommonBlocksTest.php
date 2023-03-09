<?php

namespace Gyaaniguy\PCrawl\Parsing;

use PHPUnit\Framework\TestCase;

class ParserCommonBlocksTest extends TestCase
{
    private string $body = '<html><body><a href="http://www.google.com">Google</a><div id="parent" class=" max-w-xs mx-auto  bg-white rounded-xl flex items-center space-x-4 p-6">         <div class="shrink-0">            <img class="h-8 w-8 faceimg" src="Facebook_f_logo_(2021).svg.png" alt="ChitChat Logo">        <a href="http://www.bing.com">Bing</a></div>        <div>           <div class="text-xl font-medium text-black">ChitChat</div>          <p class="text-slate-500">You have a new message!</p>        </div>  <div class="text-xl font-medium text-black">china</div>    </div></body></html>';

    public function testGetAllLinks()
    {
        $parser = $this->makePParserCommonBlocks();
        $links = $parser->getAllLinks();
        $this->assertInstanceOf(ParserCommon::class, $parser);
        $this->assertIsArray($links);
        $this->assertEquals(2, count($links));
        $this->assertArrayHasKey('href', $links[0]);
        $this->assertStringContainsStringIgnoringCase('google', $links[0]['href']);
    }

    public function makePParserCommonBlocks(string $body = ''): ParserCommon
    {
        return new ParserCommon($body ?: $this->body);
    }

    public function testGetAllImages()
    {
        $parser = $this->makePParserCommonBlocks();
        $images = $parser->getAllImages();
        $this->assertInstanceOf(ParserCommon::class, $parser);
        $this->assertIsArray($images);
        $this->assertEquals(1, count($images));
        $this->assertArrayHasKey('src', $images[0]);
        $this->assertStringContainsStringIgnoringCase('Facebook', $images[0]['src']);
    }

    public function testGetAllForms()
    {
        $body = '<html><body><form action="getAddress.php" method="post">
        <label for="fname">First name:</label><br>
        <input type="text" id="fname" name="fname" value="John"><br>
        <label for="lname">Last name:</label><br>
        <input type="text" id="lname" name="lname" value="Doe"><br><br>
        <input type="submit" value="Submit">
        </form><div>fff</div>
        <form action="login.php" method="post">
            <input type="text" id="age" name="age" value="11">
            <input type="submit" value="Submit">
        </form> 
        </body></html>';
        $parser = $this->makePParserCommonBlocks($body);
        $forms = $parser->getAllFormInputDetails();
        $this->assertInstanceOf(ParserCommon::class, $parser);
        $this->assertIsArray($forms);
        $this->assertEquals(2, count($forms));
        $this->assertArrayHasKey('action', $forms[1]);
        $this->assertArrayHasKey('inputs', $forms[1]);
        $this->assertCount(2, $forms[1]['inputs']);
        $this->assertStringContainsStringIgnoringCase('login.php', $forms[1]['action']);
        $this->assertStringContainsStringIgnoringCase('age', $forms[1]['inputs'][0]['name']);
        $this->assertStringContainsStringIgnoringCase('submit', $forms[1]['inputs'][1]['type']);

        $this->assertStringContainsStringIgnoringCase('getaddress', $forms[0]['action']);
        $this->assertStringContainsStringIgnoringCase('john', $forms[0]['inputs'][0]['value']);

        $this->assertArrayHasKey('action', $forms[0]);
        $this->assertArrayHasKey('inputs', $forms[0]);
        $this->assertCount(3, $forms[0]['inputs']);
        $this->assertStringContainsStringIgnoringCase('getaddress.php', $forms[0]['action']);
    }
}
