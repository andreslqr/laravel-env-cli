<?php

namespace Andresdevr\EnvCli;

use Andresdevr\EnvCli\Console\SetEnvVariable;
use Illuminate\Support\ServiceProvider;

class EnvCliServiceProvider extends ServiceProvider
{
	/**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
      
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if($this->app->runningInConsole()) {
            $this->commands([
                SetEnvVariable::class
            ]);
        }
    }
}