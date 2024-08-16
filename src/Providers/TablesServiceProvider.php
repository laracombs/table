<?php

namespace LaraCombs\Table\Providers;

use Composer\InstalledVersions;
use Illuminate\Support\ServiceProvider;
use LaraCombs\Table\Commands\TableMakeCommand;
use Illuminate\Foundation\Console\AboutCommand;

class TablesServiceProvider extends ServiceProvider
{
    /**
     * The name of the composer package.
     */
    protected string $packageName = 'laracombs/table';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/laracombs-table.php',
            'laracombs-table'
        );
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->loadJsonTranslationsFrom(__DIR__ . '/../../lang');
        $this->loadJsonTranslationsFrom(lang_path('vendor/laracombs-table'));

        $this->publishes([
            __DIR__ . '/../../config/laracombs-table.php' => config_path('laracombs-table.php'),
        ], 'laracombs-table-config');

        $this->publishes([
            __DIR__ . '/../../stubs' => base_path('stubs/laracombs/table'),
        ], 'laracombs-table-stubs');

        $this->publishes([
            __DIR__ . '/../../lang' => base_path('lang/vendor/laracombs-table'),
        ], 'laracombs-table-translations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                TableMakeCommand::class,
            ]);
        }

        $this->aboutCommand();
    }

    /**
     * Add additional data to the output of the 'about' command.
     */
    protected function aboutCommand(): void
    {
        AboutCommand::add($this->packageName, fn () => [
            'Version' => $this->getPackageVersion(),
            'Source' => 'https://github.com/' . $this->packageName,
        ]);
    }

    protected function getPackageVersion(): string
    {
        if (InstalledVersions::isInstalled($this->packageName)) {
            if ($version = InstalledVersions::getVersion($this->packageName)) {
                return $version;
            }
        }

        return 'unknown';
    }
}
