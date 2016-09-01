<?php

namespace Statamic\Addons\Overload\Commands;

use Statamic\API\File;
use Statamic\API\YAML;
use Statamic\API\Entry;
use Statamic\API\Config;
use Statamic\API\Content;
use Statamic\API\Collection;
use Illuminate\Console\Command;

class EntriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'overload:entries
                            {folder? : Name of the collection to generate entries in. If left blank you will be asked.}
                            {count? : How many entries to generate.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate placeholder entries interactively.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $count = ($this->argument('count'))
            ? $this->argument('count')
            : $this->ask('How many entries do you want?');

        $collections = Collection::handles();

        $collection = $this->choice('In which collection would you like them?', $collections);

        $this->makeTheGoodStuff($count, $collection);

        $this->info("Your entries have arrived. Happy testing!");
    }

    /**
     * Make the good stuff
     *
     * @param int $count
     * @param string $collection_name
     */
    public function makeTheGoodStuff($count, $collection_name)
    {
        $faker = \Faker\Factory::create();

        $collection = Collection::whereHandle($collection_name);

        $this->output->progressStart($count);

        // Disable search auto indexing to prevent overhead especially if using an API-based driver like Algolia.
        Config::set('search.auto_index', false);

        for ($x = 1; $x <= $count; $x++) {
            $entry = Entry::create($faker->slug)
                ->collection($collection_name)
                ->with(['title' => $faker->catchPhrase, 'content' => $faker->realText(500)]);

            if ($collection->order() === 'date') {
                $entry->date();
            } elseif ($collection->order() === 'number') {
                $entry->order($x);
            }

            $entry->ensureId();
            $entry->save();

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
