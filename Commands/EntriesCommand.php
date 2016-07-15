<?php

namespace Statamic\Addons\Overload\Commands;

use Statamic\API\File;
use Statamic\API\YAML;
use Statamic\API\Entry;
use Statamic\API\Config;
use Statamic\API\Content;
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

        $collections = Content::collectionNames()->toArray();

        $collection = $this->choice('In which collection would you like them?', $collections);

        $this->makeTheGoodStuff($count, $collection);

        $this->info("Your entries have arrived. Happy testing!");
    }

    public function makeTheGoodStuff($count, $collection)
    {
        $faker = \Faker\Factory::create();

        $this->output->progressStart($count);

        for ($x = 1; $x <= $count; $x++) {

            $entry = Entry::create($faker->slug)
                ->collection($collection)
                ->with(['title' => $faker->catchPhrase, 'content' => $faker->realText(500)])
                ->date()
                ->get();

            $entry->save();

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
