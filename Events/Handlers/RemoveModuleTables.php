<?php namespace Modules\Workshop\Events\Handlers;

use Illuminate\Support\Facades\DB;
use Modules\Workshop\Events\ModuleHasBeenDeleted;

/**
 * Class RemoveModuleData
 * @package Modules\Workshop\Events\Handlers
 */
class RemoveModuleTables
{
    /**
     * @var string
     */
    private $moduleName;

    /**
     * @param ModuleHasBeenDeleted $event
     */
    public function handle(ModuleHasBeenDeleted $event)
    {
        $this->moduleName = $event->module->getLowerName();
        $this->deleteTables();
        $this->cleanMigrationTable();
    }

    /**
     * Delete the module's tables
     */
    private function deleteTables()
    {
        $tables = DB::table('information_schema.tables')->select('table_name')->where('table_name', 'like', $this->moduleName . '%')->get();

        foreach ($tables as $table) {
            DB::statement("drop table $table->table_name");
        }
    }

    /**
     * Remove rows from migration table
     */
    private function cleanMigrationTable()
    {
        DB::table('migrations')->where('migration', 'like', '%' . $this->moduleName . '%')->delete();
    }
}
