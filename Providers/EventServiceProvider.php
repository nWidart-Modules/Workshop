<?php namespace Modules\Workshop\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package Modules\Workshop\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $listen = [
        'Modules\Workshop\Events\ModuleHasBeenDeleted' => [
            'Modules\Workshop\Events\Handlers\RemoveModuleData',
        ]
    ];
}
