<?php
namespace flexycms\FlexyCacheBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FlexyCacheBundle extends Bundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}