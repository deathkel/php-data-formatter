<?php
namespace Deathel\DataFormatter\Laravel\Provider;

use Illuminate\Support\ServiceProvider;
use Deathel\DataFormatter\Laravel\Console\MakeFormatterCommand;

class DataFormatterProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerMakeFormatterCommand();
    }

    /**
     * Register a command
     */
    protected function registerMakeFormatterCommand(){
        $this->app->singleton('make:formatter', function () {
            return new MakeFormatterCommand();
        });
    }
}