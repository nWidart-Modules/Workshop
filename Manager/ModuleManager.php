<?php namespace Modules\Workshop\Manager;

use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Pingpong\Modules\Module;
use Symfony\Component\Yaml\Parser;

class ModuleManager
{
    /**
     * @var Module
     */
    private $module;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var PackageInformation
     */
    private $packageVersion;
    /**
     * @var Filesystem
     */
    private $finder;

    /**
     * @param Config $config
     * @param PackageInformation $packageVersion
     * @param Filesystem $finder
     */
    public function __construct(Config $config, PackageInformation $packageVersion, Filesystem $finder)
    {
        $this->module = app('modules');
        $this->config = $config;
        $this->packageVersion = $packageVersion;
        $this->finder = $finder;
    }

    /**
     * Return all modules
     * @return \Illuminate\Support\Collection
     */
    public function all()
    {
        $modules = new Collection($this->module->all());

        foreach ($modules as $module) {
            $moduleName = $module->getName();
            $package = $this->packageVersion->getPackageInfo("asgardcms/$moduleName-module");
            $module->version = isset($package->version) ? $package->version: 'N/A';
            $module->versionUrl = '#';
            if (isset($package->source->url)) {
                $packageUrl = str_replace('.git', '', $package->source->url);
                $module->versionUrl = $packageUrl . '/tree/' . $package->dist->reference;
            }
        }

        return $modules;
    }

    /**
     * Return all the enabled modules
     * @return array
     */
    public function enabled()
    {
        return $this->module->enabled();
    }

    /**
     * Get the core modules that shouldn't be disabled
     * @return array|mixed
     */
    public function getCoreModules()
    {
        $coreModules = $this->config->get('asgard.core.config.CoreModules');
        $coreModules = array_flip($coreModules);

        return $coreModules;
    }

    /**
     * Get the enabled modules, with the module name as the key
     * @return array
     */
    public function getFlippedEnabledModules()
    {
        $enabledModules = $this->module->enabled();

        $enabledModules = array_map(function (Module $module) {
            return $module->getName();
        }, $enabledModules);

        return array_flip($enabledModules);
    }

    /**
     * Disable the given modules
     * @param $enabledModules
     */
    public function disableModules($enabledModules)
    {
        $coreModules = $this->getCoreModules();

        foreach ($enabledModules as $moduleToDisable => $value) {
            if (isset($coreModules[$moduleToDisable])) {
                continue;
            }
            $module = $this->module->get($moduleToDisable);
            $module->disable();
        }
    }

    /**
     * Enable the given modules
     * @param $modules
     */
    public function enableModules($modules)
    {
        foreach ($modules as $moduleToEnable => $value) {
            $module = $this->module->get($moduleToEnable);
            $module->enable();
        }
    }

    /**
     * Delete the given module
     * @param Module $module
     */
    public function deleteModule(Module $module)
    {
        $coreModules = $this->getCoreModules();
        if (!isset($coreModules[$module->getLowerName()])) {
            //TODO: $this->deleteModulePermissionsAndRoles($module->name);
            //$this->deleteModuleAssets($module->getLowerName());
            $this->deleteModuleTables($module->getLowerName());
            $this->module->delete($module);
        }
    }

    /**
     * Delete the given module's tables
     * @param string $moduleName
     */
    private function deleteModuleTables($moduleName)
    {
        $tables = \DB::select('select table_name from information_schema.tables where table_name like "%' . $moduleName . '%"');
        foreach ($tables as $table) {
            \DB::statement("drop table $table->table_name");
        }
    }

    /**
    * Delete given module's assets folder
    * @param string $moduleName
    */

    private function deleteModuleAssets($moduleName)
    {
        $assets = public_path() . '/modules/' . $moduleName;
        $this->finder->deleteDirectory($assets);
    }

    /**
     * Get the changelog for the given module
     * @param Module $module
     * @return array
     */
    public function changelogFor(Module $module)
    {
        $path = $module->getPath() . '/changelog.yml';
        if (! $this->finder->isFile($path)) {
            return [];
        }

        $yamlParser = new Parser();

        return $yamlParser->parse(file_get_contents($path));
    }
}
