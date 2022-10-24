<?php namespace Gyaaniguy\PCrawl;

/**
*  A sample class
*
*  Use this section to define what this class is doing, the PHPDocumentator will use this
*  to automatically generate an API documentation using this information.
*
*  @author yourname
*/
class PRequest{
    public PRequestOptions $opts ;

    public function __construct()
    {
        $this->opts = new PRequestOptions();
    }


    function get($url = ''): string{
        return 'body';
    }


}