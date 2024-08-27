<?php

namespace Amsaid\WorldNewsApi;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Amsaid\WorldNewsApi\Commands\WorldNewsApiCommand;

class WorldNewsApiServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('world-news-api')
            ->hasConfigFile()
            ->hasCommand(WorldNewsApiCommand::class);
    }
}
