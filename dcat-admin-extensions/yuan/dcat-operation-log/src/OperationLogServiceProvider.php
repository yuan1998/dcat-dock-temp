<?php

namespace Weiaibaicai\OperationLog;

use Dcat\Admin\Extend\ServiceProvider;
use Dcat\Admin\Admin;
use Illuminate\Console\Scheduling\Schedule;
use Weiaibaicai\OperationLog\Console\Commands\OperationLogClean;
use Weiaibaicai\OperationLog\Http\Middleware\OperationLogMiddleware;


class OperationLogServiceProvider extends ServiceProvider
{
    protected $middleware = [
        'middle' => [
            OperationLogMiddleware::class,
        ],
    ];

    public function init()
    {
        $this->commands([
            OperationLogClean::class,
        ]);
        $this->publishes([
            __DIR__ . '/../config/operation-log.php' => config_path('operation-log.php'),
        ]);
        if (static::setting('enable_remove')) {
            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);
                $days = static::setting('remove_day', 30);
                if (is_numeric($days)) {
                    $schedule->command("admin:operation-log-clean --days=$days")->daily();
                }
            });
        }

        parent::init();
    }


    public function settingForm()
    {
//        dd(config('admin.extensions.dcat_operation_log.close_routes'));
        return new Setting($this);
    }
}
