<?php

namespace Dwivedianuj9118\ApiStarter\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Dwivedianuj9118\ApiStarter\ApiStarterServiceProvider;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [ApiStarterServiceProvider::class];
    }
}
