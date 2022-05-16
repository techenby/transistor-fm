<?php

namespace Techenby\TransistorFm;

use Illuminate\Support\Facades\Http;
use Statamic\Facades\CP\Nav;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $commands = [
        Console\Commands\ImportCommand::class,
        Console\Commands\InstallCommand::class,
    ];

    protected $routes = [
        'cp' => __DIR__.'/../routes/cp.php',
        'web' => __DIR__.'/../routes/web.php',
    ];

    public function bootAddon()
    {
        $this->bootAddonNav();

        Http::macro('transistor', function () {
            return Http::withHeaders([
                'x-api-key' => 'FQLM2MLPnYAD7-6myaq1kg',
            ])->baseUrl('https://api.transistor.fm/v1');
        });
    }

    public function bootAddonNav()
    {
        Nav::extend(function ($nav) {
            $nav->create(__('Episodes'))
                ->section(__('Transistor FM'))
                ->route('collections.show', 'episodes')
                ->can('view transistor-fm episodes')
                ->icon('entries');

            $nav->create(__('Analytics'))
                ->section(__('Transistor FM'))
                ->route('transistor-fm.analytics')
                ->can('view transistor-fm analytics')
                ->icon('charts');

            // Drop any collection items from 'Collections' nav
            $collections = $nav->content('Collections');

            $children = $collections->children()()
                ->reject(function ($child) {
                    return $child->name() === 'Episodes';
                });

            $collections->children(function () use ($children) {
                return $children;
            });
        });

        return $this;
    }
}
