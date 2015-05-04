<?php namespace Modules\Workshop\Events\Handlers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Modules\Workshop\Events\ModuleHasBeenDeleted;

/**
 * Class RemoveModuleData
 * @package Modules\Workshop\Events\Handlers
 */

class RemoveModuleData
{
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
        $module = $event->module;
        $this->deleteTables($module->getLowerName());
        $this->deleteAssets($module->getLowerName());
    }

    /**
     * Delete the given module's tables
     * @param string $moduleName
     */
    private function deleteTables($moduleName)
    {
        $tables = DB::table('information_schema.tables')->select('table_name')->where('table_name', 'like', $moduleName . '%')->get();

        foreach ($tables as $table) {
            DB::statement("drop table $table->table_name");
        }

        //clean migration table
        DB::table('migrations')->where('migration', 'like', '%' . $moduleName . '%')->delete();
    }

    /**
     * Delete given module's assets folder
     * @param string $moduleName
     */

    private function deleteAssets($moduleName)
    {
        //set paths
        $assetsDirectory = public_path() . '/modules/' . $moduleName;
        $moduleDirectory = app_path() . '/Modules/' . ucfirst($moduleName);

        //delete assets
        $this->finder->delete($assetsDirectory);
        //delete empty module folder
        $this->finder->delete($moduleDirectory);
    }
}
