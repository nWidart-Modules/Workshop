<?php namespace Modules\Workshop\Scaffold\Theme\FileTypes;

use Modules\Workshop\Scaffold\Theme\Traits\FindsThemePath;

class GulpFileJs extends BaseFileType implements FileType
{
    use FindsThemePath;

    /**
     * Generate the current file type
     * @return string
     */
    public function generate()
    {
        $stub = $this->finder->get(__DIR__ . '/../stubs/gulpFileJs.stub');

        $this->finder->put($this->themePathForFile($this->options['name'], 'gulpfile.js'), $stub);
    }
}
