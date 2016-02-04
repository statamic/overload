<?php

namespace Statamic\Addons\Overload\Commands;

use Statamic\API\File;
use Statamic\API\YAML;
use Statamic\API\Config;
use Statamic\API\Content;
use Illuminate\Console\Command;

class UsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'overload:users
                            {count? : How many files to generate.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate placeholder users interactively.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $count = ($this->argument('count'))
            ? $this->argument('count')
            : $this->ask('How many users do you want?');

        $this->makeTheGoodStuff($count);

        $this->info("Your users have arrived. Happy testing!");
    }

    public function makeTheGoodStuff($count)
    {
        $faker = \Faker\Factory::create();

        $this->output->progressStart($count);

        for ($x = 1; $x <= $count; $x++) {
            $content = YAML::dump([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->email
            ], $faker->realText(500));

            File::put(users_path().$faker->unique()->username.'.yaml', $content);

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
