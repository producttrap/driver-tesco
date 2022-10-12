<?php

declare(strict_types=1);

namespace ProductTrap\tesco\Tests;

use ProductTrap\ProductTrapServiceProvider;
use ProductTrap\Tesco\TescoServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [ProductTrapServiceProvider::class, TescoServiceProvider::class];
    }
}
