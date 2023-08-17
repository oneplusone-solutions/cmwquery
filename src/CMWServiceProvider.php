<?php

namespace OnePlusOne\CMWQuery;

use Filament\Events\ServingFilament;
use Filament\PluginServiceProvider;
use OnePlusOne\CMWQuery\Widgets\CMWWidget;
use Spatie\LaravelPackageTools\Package;

class CMWServiceProvider extends PluginServiceProvider
{
    protected array $widgets = [
        CMWWidget::class,
    ];

    public function configurePackage(Package $package): void
    {
        $package
            ->name('cmwquery')
            ->hasConfigFile()
            ->hasTranslations();
    }
    //    /**
    //     * Bootstrap any package services.
    //     *
    //     * @return void
    //     */
    //    public function boot()
    //    {
    //        $this->publishes([
    //            __DIR__.'/../config/cmw-query.php' => config_path('cmw-query.php'),
    //        ]);
    //    }
    //    public function packageConfiguring(Package $package): void
    //    {
    //        Event::listen(ServingFilament::class, [$this, 'sendRequest']);
    //    }
    //
    //    protected function sendRequest(ServingFilament $event): void
    //    {
    //        // ...
    //    }
}
