<?php

namespace JohnConde\Authnet;

interface ProcessorInterface
{
    public function process($url, $json);
    public function setResponse($json);
    public function getName();
}