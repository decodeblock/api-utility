<?php

namespace Decodeblock\ApiUtility;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Decodeblock\ApiUtility\Commands\ApiUtilityCommand;

class ApiUtilityServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('api-utility');
            // ->hasConfigFile()
            // ->hasViews()
            // ->hasMigration('create_api_utility_table')
            // ->hasCommand(ApiUtilityCommand::class);
    }
}
