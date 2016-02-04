<?php

namespace Statamic\Addons\Overload\Commands;

use Statamic\API\File;
use Statamic\API\YAML;
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
                            {folder? : Name of the folder to generate content in. If left blank you will be asked.}
                            {count? : How many files to generate.}';

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

        $collections = Content::collectionNames();

        $folder = $this->choice('In which collection would you like them?', $collections);

        $this->makeTheGoodStuff($count, $folder);

        $this->info("Your entries have arrived. Happy testing!");
    }

    public function makeTheGoodStuff($count, $folder)
    {
        $faker = \Faker\Factory::create();
        $extension = Config::get('system.default_extension');

        $this->output->progressStart($count);

        for ($x = 1; $x <= $count; $x++) {
            $content = YAML::dump([
                'title' => $faker->catchPhrase,
                'author' => $faker->name,
                'tags' => $faker->words(3)
            ], $faker->realText(500));

            File::put(content_path('collections/') . $folder . '/' . $faker->date() . '.' . $faker->slug . '.' . $extension, $content);

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
