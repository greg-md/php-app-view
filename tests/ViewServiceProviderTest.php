<?php

namespace Greg\AppView;

use Greg\AppInstaller\Application;
use Greg\AppView\Events\LoadViewerEvent;
use Greg\Framework\ServiceProvider;
use Greg\Support\Dir;
use Greg\View\ViewBladeCompiler;
use Greg\View\Viewer;
use PHPUnit\Framework\TestCase;

class ViewServiceProviderTest extends TestCase
{
    private $rootPath = __DIR__ . '/app';

    protected function setUp()
    {
        Dir::make($this->rootPath);

        Dir::make($this->rootPath . '/app');
        Dir::make($this->rootPath . '/build-deploy');
        Dir::make($this->rootPath . '/config');
        Dir::make($this->rootPath . '/public');
        Dir::make($this->rootPath . '/resources');
        Dir::make($this->rootPath . '/storage');
    }

    protected function tearDown()
    {
        Dir::unlink($this->rootPath);
    }

    public function testCanInstantiate()
    {
        $serviceProvider = new ViewServiceProvider();

        $this->assertInstanceOf(ServiceProvider::class, $serviceProvider);
    }

    public function testCanGetName()
    {
        $serviceProvider = new ViewServiceProvider();

        $this->assertEquals('greg-view', $serviceProvider->name());
    }

    public function testCanBoot()
    {
        $serviceProvider = new ViewServiceProvider();

        $app = new Application([
            'viewer' => [
                'paths' => __DIR__,

                'compilers' => [
                    [
                        'extension'         => '.blade.php',
                        'type'              => 'blade',
                        'compilation_path'  => __DIR__,
                    ],
                ],
            ],
        ]);

        $serviceProvider->boot($app);

        $app->listen(LoadViewerEvent::class, function () {
        });

        /** @var Viewer $viewer */
        $viewer = $app->get(Viewer::class);

        $this->assertInstanceOf(Viewer::class, $viewer);

        $this->assertArraySubset([__DIR__], $viewer->getPaths());

        $this->assertArraySubset(['.blade.php'], $viewer->getCompilersExtensions());

        /** @var ViewBladeCompiler $bladeCompiler */
        $bladeCompiler = $viewer->getCompiler('.blade.php');

        $this->assertInstanceOf(ViewBladeCompiler::class, $bladeCompiler);

        $this->assertEquals(__DIR__, $bladeCompiler->compilationPath());
    }
    
    public function testCanThrowExceptionIfExtensionIsNotDefined()
    {
        $serviceProvider = new ViewServiceProvider();

        $app = new Application([
            'viewer' => [
                'paths' => __DIR__,

                'compilers' => [
                    [
                        'type'              => 'blade',
                        'compilation_path'  => __DIR__,
                    ],
                ],
            ],
        ]);

        $this->expectException(\Exception::class);

        $serviceProvider->boot($app);

        $app->get(Viewer::class);
    }

    public function testCanThrowExceptionIfCompilationPathIsNotDefined()
    {
        $serviceProvider = new ViewServiceProvider();

        $app = new Application([
            'viewer' => [
                'paths' => __DIR__,

                'compilers' => [
                    [
                        'extension'         => '.blade.php',
                        'type'              => 'blade',
                    ],
                ],
            ],
        ]);

        $this->expectException(\Exception::class);

        $serviceProvider->boot($app);

        /** @var Viewer $viewer */
        $viewer = $app->get(Viewer::class);

        $viewer->getCompiler('.blade.php');
    }

    public function testCanThrowExceptionIfUndefinedCompiler()
    {
        $serviceProvider = new ViewServiceProvider();

        $app = new Application([
            'viewer' => [
                'paths' => __DIR__,

                'compilers' => [
                    [
                        'extension'         => '.blade.php',
                        'type'              => 'undefined',
                        'compilation_path'  => __DIR__,
                    ],
                ],
            ],
        ]);

        $this->expectException(\Exception::class);

        $serviceProvider->boot($app);

        /** @var Viewer $viewer */
        $viewer = $app->get(Viewer::class);

        $viewer->getCompiler('.blade.php');
    }

    public function testCanInstall()
    {
        $serviceProvider = new ViewServiceProvider();

        $app = new Application();

        $app->configure(__DIR__ . '/app');

        $serviceProvider->install($app);

        $this->assertFileExists(__DIR__ . '/app/config/viewer.php');

        $this->assertDirectoryExists(__DIR__ . '/app/resources/views');
    }

    public function testCanUninstall()
    {
        $serviceProvider = new ViewServiceProvider();

        $app = new Application();

        $app->configure(__DIR__ . '/app');

        file_put_contents(__DIR__ . '/app/config/viewer.php', '');

        Dir::make(__DIR__ . '/app/resources/views');

        $serviceProvider->uninstall($app);

        $this->assertFileNotExists(__DIR__ . '/app/config/viewer.php');

        $this->assertDirectoryNotExists(__DIR__ . '/app/resources/views');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Application
     */
    private function mockApplication()
    {
        return $this->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
