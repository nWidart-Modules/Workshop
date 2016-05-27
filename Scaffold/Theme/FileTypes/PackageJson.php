<?php namespace Modules\Workshop\Scaffold\Theme\FileTypes;

use Modules\Workshop\Scaffold\Theme\Traits\FindsThemePath;

class PackageJson extends BaseFileType implements FileType
{
    use FindsThemePath;

    /**
     * Generate the current file type
     * @return string
     */
    public function generate()
    {
        $stub = $this->finder->get(__DIR__ . '/../stubs/packageJson.stub');

        $stub = $this->replaceContentInStub($stub);

        $this->finder->put($this->themePathForFile($this->options['name'], 'package.json'), $stub);
    }

    public function replaceContentInStub($stub)
    {
        return str_replace(
            [
                '{{gulp-version}}',
                '{{elixir-version}}',
            ],
            [
                $this->options['gulp'],
                $this->options['elixir'],
            ],
            $stub
        );
    }
}
