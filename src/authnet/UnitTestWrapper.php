<?php

namespace JohnConde\Authnet;

class UnitTestWrapper Implements ProcessorInterface
{
    private $json;

    public function process($url, $json)
    {
        return $this->json;
    }

    public function setResponse($json)
    {
        $this->json = $json;
    }

    public function getName()
    {
        return __CLASS__;
    }
} 