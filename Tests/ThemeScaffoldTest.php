<?php namespace Modules\Workshop\Tests;

use Modules\Workshop\Scaffold\Theme\Exceptions\FileTypeNotFoundException;
use Modules\Workshop\Scaffold\Theme\Exceptions\ThemeExistsException;
use Modules\Workshop\Scaffold\Theme\ThemeScaffold;

class ThemeScaffoldTest extends BaseTestCase
{
    public $path = 'Themes';

    /**
     * @var ThemeScaffold
     */
    protected $scaffold;

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $finder;
    /**
     * @var
     */
    protected $testThemeName;
    /**
     * @var
     */
    protected $testThemePath;

    public function setUp()
    {
        parent::setUp();
        $this->finder = $this->app['files'];
        $this->scaffold = $this->app['asgard.theme.scaffold'];
        if (!$this->finder->isDirectory(base_path("Themes"))) {
            $this->finder->makeDirectory(base_path("Themes"));
        }
        $this->testThemeName = 'TestingTheme';
        $this->testThemePath = base_path("Themes/{$this->testThemeName}");
    }

    private function generateFrontendTheme()
    {
        $this->scaffold->setName($this->testThemeName)->forType('frontend')->setVendor('asgardcms')->generate();
    }

    private function generateFrontendThemeWithElixir($elixir, $gulp)
    {
        $this->scaffold->setName($this->testThemeName)->forType('frontend')->setVendor('asgardcms')->withElixir($elixir, $gulp)->generate();
    }

    public function tearDown()
    {
        $this->finder->deleteDirectory($this->testThemePath);
        $this->finder->deleteDirectory(base_path("Themes"));
    }

    /** @test */
    public function it_generates_theme_folder()
    {
        $this->scaffold->setFiles([]);

        $this->generateFrontendTheme();

        $this->assertTrue($this->finder->isDirectory($this->testThemePath));
    }

    /** @test */
    public function it_throws_exception_if_file_type_does_not_exist()
    {
        $this->scaffold->setFiles(['OneTwoThree']);

        $this->setExpectedException(FileTypeNotFoundException::class);

        $this->generateFrontendTheme();
    }

    /** @test */
    public function it_throws_exception_if_theme_exists()
    {
        $this->setExpectedException(ThemeExistsException::class);

        $this->scaffold->setFiles([]);
        $this->generateFrontendTheme();
        $this->generateFrontendTheme();
    }

    /** @test */
    public function it_throws_exception_if_no_name_provided()
    {
        $this->setExpectedException(\InvalidArgumentException::class, 'You must provide a name');

        $this->scaffold->setName('')->forType('frontend')->setVendor('asgardcms')->generate();
    }

    /** @test */
    public function it_throws_exception_if_no_type_provided()
    {
        $this->setExpectedException(\InvalidArgumentException::class, 'You must provide a type');

        $this->scaffold->setName($this->testThemeName)->forType('')->setVendor('asgardcms')->generate();
    }

    /** @test */
    public function it_throws_exception_if_no_vendor_provided()
    {
        $this->setExpectedException(\InvalidArgumentException::class, 'You must provide a vendor name');

        $this->scaffold->setName($this->testThemeName)->forType('frontend')->setVendor('')->generate();
    }

    /** @test */
    public function it_creates_theme_json_file()
    {
        $this->scaffold->setFiles(['themeJson']);

        $this->generateFrontendTheme();

        $this->assertTrue($this->finder->isFile($this->testThemePath . '/theme.json'));
        $this->assertTrue(str_contains($this->finder->get($this->testThemePath . '/theme.json'), '"name": "' . $this->testThemeName . '",'));
        $this->assertTrue(str_contains($this->finder->get($this->testThemePath . '/theme.json'), '"type": "frontend"'));
    }

    /** @test */
    public function it_creates_composer_json_file()
    {
        $this->scaffold->setFiles(['composerJson']);

        $this->generateFrontendTheme();

        $this->assertTrue($this->finder->isFile($this->testThemePath . '/composer.json'));
        $this->assertTrue(str_contains($this->finder->get($this->testThemePath . '/composer.json'), '"name": "asgardcms/TestingTheme-theme",'));
    }

    /** @test */
    public function it_creates_master_blade_layout()
    {
        $this->scaffold->setFiles(['masterBladeLayout']);

        $this->generateFrontendTheme();

        $this->assertTrue($this->finder->isFile($this->testThemePath . '/views/layouts/master.blade.php'));
    }

    /** @test */
    public function it_creates_basic_view()
    {
        $this->scaffold->setFiles(['masterBladeLayout', 'basicView']);

        $this->generateFrontendTheme();

        $this->assertTrue($this->finder->isFile($this->testThemePath . '/views/default.blade.php'));
    }

    /** @test */
    public function it_creates_empty_resources_folder()
    {
        $this->scaffold->setFiles(['resourcesFolder']);

        $this->generateFrontendTheme();

        $this->assertTrue($this->finder->isDirectory($this->testThemePath . '/resources'));
        $this->assertTrue($this->finder->isDirectory($this->testThemePath . '/resources/css'));
        $this->assertTrue($this->finder->isDirectory($this->testThemePath . '/resources/js'));
        $this->assertTrue($this->finder->isDirectory($this->testThemePath . '/resources/images'));
        $this->assertTrue($this->finder->isFile($this->testThemePath . '/resources/.gitignore'));
        $this->assertTrue($this->finder->isFile($this->testThemePath . '/resources/css/.gitignore'));
        $this->assertTrue($this->finder->isFile($this->testThemePath . '/resources/js/.gitignore'));
        $this->assertTrue($this->finder->isFile($this->testThemePath . '/resources/images/.gitignore'));
    }

    /** @test */
    public function it_creates_empty_assets_folder()
    {
        $this->scaffold->setFiles(['assetsFolder']);

        $this->generateFrontendTheme();

        $this->assertTrue($this->finder->isDirectory($this->testThemePath . '/assets'));
        $this->assertTrue($this->finder->isDirectory($this->testThemePath . '/assets/css'));
        $this->assertTrue($this->finder->isDirectory($this->testThemePath . '/assets/js'));
        $this->assertTrue($this->finder->isDirectory($this->testThemePath . '/assets/images'));
        $this->assertTrue($this->finder->isFile($this->testThemePath . '/assets/.gitignore'));
        $this->assertTrue($this->finder->isFile($this->testThemePath . '/assets/css/.gitignore'));
        $this->assertTrue($this->finder->isFile($this->testThemePath . '/assets/js/.gitignore'));
        $this->assertTrue($this->finder->isFile($this->testThemePath . '/assets/images/.gitignore'));
    }

    /**
     * @group elixir
     * @test
     */
    public function it_does_not_create_elixir_files_when_option_is_no()
    {
        $this->generateFrontendTheme();

        $this->assertFalse($this->finder->isFile($this->testThemePath . '/package.json'));
        $this->assertFalse($this->finder->isFile($this->testThemePath . '/gulpfile.js'));
    }

    /**
     * @group elixir
     * @test
     */
    public function it_creates_elixir_files_when_option_is_yes_with_defaults()
    {
        $expectedPackageJsonFile = $this->testThemePath . '/package.json';
        $expectedGulpFile = $this->testThemePath . '/gulpfile.js';

        $this->generateFrontendThemeWithElixir(null, null);

        $this->assertTrue($this->finder->isFile($expectedPackageJsonFile), "expect '" . $expectedPackageJsonFile . "' to be created.");
        $this->assertTrue($this->finder->isFile($expectedGulpFile), "expect " . $expectedGulpFile . " to be created.");

        $this->assertTrue(str_contains($this->finder->get($expectedPackageJsonFile), '"gulp": "*",'));
        $this->assertTrue(str_contains($this->finder->get($expectedPackageJsonFile), '"laravel-elixir": "*"'));
    }

    /**
     * @group elixir
     * @test
     */
    public function it_creates_versioned_elixir_files_when_version_set()
    {
        $expectedPackageJsonFile = $this->testThemePath . '/package.json';
        $expectedGulpFile = $this->testThemePath . '/gulpfile.js';

        $this->generateFrontendThemeWithElixir('5.0.0', '^3.9.1');

        $this->assertTrue($this->finder->isFile($expectedPackageJsonFile), "expect '" . $expectedPackageJsonFile . "' to be created.");
        $this->assertTrue($this->finder->isFile($expectedGulpFile), "expect " . $expectedGulpFile . " to be created.");

        $this->assertTrue(str_contains($this->finder->get($expectedPackageJsonFile), '"gulp": "^3.9.1",'));
        $this->assertTrue(str_contains($this->finder->get($expectedPackageJsonFile), '"laravel-elixir": "5.0.0"'));
    }
}
