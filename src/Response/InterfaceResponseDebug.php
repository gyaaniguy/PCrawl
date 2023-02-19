<?php

namespace Gyaaniguy\PCrawl\Response;

interface InterfaceResponseDebug
{
    public function isFail();

    public function getFailDetail();
}