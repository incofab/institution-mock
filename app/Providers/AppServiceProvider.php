<?php

namespace App\Providers;

use App\Actions\StartExam;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    //
  }

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    app()->singleton(\App\Helpers\ExamHandler::class);
    app()->bind(StartExam::class, function (Application $app, array $params) {
      return new StartExam($params['exam']);
    });
  }
}
