<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(\Kreait\Firebase::class, function () {
            $factory = (new Factory)->withServiceAccount(__DIR__.'/../../ink-link-43c72-firebase-adminsdk-d4m0p-6e5a5457e9.json');
            $database = $factory->createDatabase();
            // 'path/to/firebase-private-key' の部分は書き換えてください
            // $serviceAccount = ServiceAccount::fromJsonFile('../../ink-link-43c72-firebase-adminsdk-d4m0p-6e5a5457e9.json');
        });
    }

    /**
     * @return array
     */
    public function provides(): array
    {
        return [\Kreait\Firebase::class];
    }
}
