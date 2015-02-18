<?php

class CurlWrapperTest extends PHPUnit_Framework_TestCase
{
    public function testCurlWrapperGetName()
    {
        $wrapper = new \JohnConde\Authnet\CurlWrapper();

        $this->assertEquals('JohnConde\Authnet\CurlWrapper', $wrapper->getName());
    }
}