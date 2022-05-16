<?php

namespace Techenby\TransistorFm\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Statamic\Console\RunsInPlease;
use Statamic\Facades\Collection;
use Statamic\Facades\Site;

class InstallCommand extends Command
{
    use RunsInPlease;

    protected $name = 'tfm:install';
    protected $description = 'Install Transistor FM';

    public function handle()
    {
        $this
            // ->publishBlueprints()
            // ->publishConfigurationFile()
            ->setupCollections();
    }

    // protected function publishBlueprints(): self
    // {
    //     $this->info('Publishing Blueprints');

    //     $this->callSilent('vendor:publish', [
    //         '--tag' => 'transistor-fm-blueprints',
    //     ]);

    //     return $this;
    // }

    // protected function publishConfigurationFile(): self
    // {
    //     $this->info('Publishing Config file');

    //     $this->callSilent('vendor:publish', [
    //         '--tag' => 'transistor-fm-config',
    //     ]);

    //     return $this;
    // }

    protected function setupCollections()
    {
        $siteHandles = Site::all()->map->handle()->toArray();

        if (! Collection::handleExists('episodes')) {
            $this->info('Creating: Episodes');

            Collection::make('episodes')
                ->title(Str::title('episodes'))
                ->pastDateBehavior('public')
                ->futureDateBehavior('private')
                ->sites($siteHandles)
                ->routes('/episodes/{slug}')
                ->save();
        } else {
            $this->warn('Skipping: Episodes');
        }

        return $this;
    }
}
