<?php namespace Modules\Workshop\Events\Handlers;

use Illuminate\Filesystem\Filesystem;
use Modules\Workshop\Events\ModuleHasBeenDeleted;

/**
 * Class RemoveModuleData
 * @package Modules\Workshop\Events\Handlers
 */
class RemoveModuleData
{
    /**
     * @var string
     */
    private $moduleName;

    /**
     * @var Filesystem
     */
    private $finder;

    /**
     * @param Filesystem $finder
     */
    public function __construct(Filesystem $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @param ModuleHasBeenDeleted $event
     */
    public function handle(ModuleHasBeenDeleted $event)
    {
        $this->moduleName = $event->module->getLowerName();
        $this->deleteAssets();
        $this->deleteModuleFolder();
    }

    /**
     * Deletes the asset folder
     */
    public function deleteAssets()
    {
        $assetsDirectory = public_path() . '/modules/' . $this->moduleName;
        $this->finder->delete($assetsDirectory);
    }

    /**
     * Deletes the module folder
     */
    public function deleteModuleFolder()
    {
        $moduleDirectory = app_path() . '/Modules/' . ucfirst($this->moduleName);
        $this->finder->delete($moduleDirectory);
    }
}
