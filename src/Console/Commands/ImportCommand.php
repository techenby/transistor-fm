<?php

namespace Techenby\TransistorFm\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Statamic\Console\RunsInPlease;
use Statamic\Facades\Collection;
use Statamic\Facades\Entry;

class ImportCommand extends Command
{
    use RunsInPlease;

    protected $name = 'tfm:import';
    protected $description = 'Import Episodes from Transistor FM';

    public function handle()
    {
        $collection = Collection::findByHandle('episodes');
        if ($collection !== null) {
            $this->info('Importing Episodes');

            $response = Http::transistor()->get('episodes');

            foreach($response->json()['data'] as $data) {
                $entry = Entry::whereCollection('episodes')->where('transistor_id', $data['id'])->first();

                if ($entry !== null) {
                    $this->updateEpisode($entry, $data);
                } else {
                    $this->createEpisode($data, $collection);
                }
            }

            $this->info('Importing Episodes finished');
        }
    }

    private function createEpisode($data, $collection)
    {
        $episode = $this->formatData($data);

        $entry = Entry::make()->collection($collection)->data($episode);

        if ($episode['status'] === 'published') {
            $entry->published();
        }

        $entry->save();
    }

    private function formatData($data)
    {
        $episode = $data['attributes'];
        $episode['transistor_id'] = $data['id'];
        $episode['slug'] = Str::slug($episode['title']);
        $episode['transistor_created_at'] = $episode['created_at'];
        $episode['transistor_updated_at'] = $episode['updated_at'];

        return $episode;
    }

    private function updateEpisode($entry, $data)
    {
        if ($entry->transistor_updated_at !== $data['attributes']['updated_at']) {
            $entry->update($this->formatData($data));
        }
    }
}
