<?php

namespace Statamic\Addons\Overload\Commands;

use Statamic\API\File;
use Statamic\API\Asset;
use Faker\Factory as Faker;
use Illuminate\Console\Command;
use Statamic\API\AssetContainer;
use Statamic\Contracts\Assets\AssetFolder;
use Statamic\Contracts\Assets\AssetContainer as Container;

class AssetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'overload:assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate placeholder assets interactively.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $count = $this->ask('How many assets do you want?');

        $container = $this->getContainer();

        $folder = $this->getFolder($container);

        $this->makeTheGoodStuff($count, $folder);

        $this->info("Your entries have arrived. Happy testing!");
    }

    private function getContainer()
    {
        $ids = AssetContainer::all()->map(function ($container) {
            return "{$container->title()} (ID: {$container->id()})";
        })->values()->all();

        $chosen = $this->choice('In which container would you like them?', $ids);

        preg_match('/ID: (.*)\)$/', $chosen, $matches);
        $id = $matches[1];

        return AssetContainer::find($id);
    }

    private function getFolder(Container $container)
    {
        $chosen = $this->choice('In which folder would you like them?', $container->folders()->keys()->all());

        return $container->folder($chosen);
    }

    private function makeTheGoodStuff($count, AssetFolder $folder)
    {
        $faker = Faker::create();

        $this->output->progressStart($count);

        for ($x = 1; $x <= $count; $x++) {
            $id = $faker->uuid;
            $file = "{$faker->slug}.txt";

            $asset = Asset::create($id)
                ->container($folder->container()->handle())
                ->folder($folder->path())
                ->file($file)
                ->get();

            $folder->addAsset($id, $asset);

            $asset->disk()->put($asset->path(), $faker->sentence);

            $this->output->progressAdvance();
        }

        $folder->save();

        $this->output->progressFinish();
    }
}
