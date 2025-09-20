<?php

namespace CrunchzApp\Tests;

use CrunchzApp\CrunchzAppServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            CrunchzAppServiceProvider::class,
        ];
    }
}
