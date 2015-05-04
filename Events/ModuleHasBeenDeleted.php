<?php namespace Modules\Workshop\Events;

use Pingpong\Modules\Module;

class ModuleHasBeenDeleted
{
    public $module;

    public function __construct(Module $module)
    {
        $this->module = $module;
    }
}
