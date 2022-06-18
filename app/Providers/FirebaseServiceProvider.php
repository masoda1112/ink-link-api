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
            // 'path/to/firebase-private-key' の部分は書き換えてください
            $serviceAccount = ServiceAccount::fromJsonFile('path/to/firebase-private-key');
            return (new Factory())
                ->withServiceAccount($serviceAccount)
                ->create();
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
