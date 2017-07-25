<?php

namespace Greg\AppView;

use Greg\Framework\ServiceProvider;
use PHPUnit\Framework\TestCase;

class ViewServiceProviderTest extends TestCase
{
    public function testCanInstantiate()
    {
        $serviceProvider = new ViewServiceProvider();

        $this->assertInstanceOf(ServiceProvider::class, $serviceProvider);
    }
}