<?php

namespace OnePlusOne\CMWQuery;

use Filament\PluginServiceProvider;
use Filament\Events\ServingFilament;
use OnePlusOne\CMWQuery\Widgets\CMWWidget;

class CMWServiceProvider  extends PluginServiceProvider
{

    protected array $widgets = [
        CMWWidget::class,
    ];

    public function configurePackage(Package $package): void
    {
        $package
            ->name('cmw-query')
            ->hasConfigFile()
            ->hasTranslations()
        ;
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