<?php

class UnitTestWrapperTest extends PHPUnit_Framework_TestCase
{
    protected $wrapper;

    protected function setUp()
    {
        $this->wrapper = new \JohnConde\Authnet\UnitTestWrapper();
    }

    public function testResponse()
    {
        $json = '{test}';
        $this->wrapper->setResponse($json);
        $response = $this->wrapper->process(null, $json);

        $this->assertEquals('{test}', $response);
    }

    public function testGetName()
    {
        $this->assertEquals('JohnConde\Authnet\UnitTestWrapper', $this->wrapper->getName());
    }
}