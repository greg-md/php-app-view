<?php

namespace Greg\AppView;

use Greg\AppInstaller\Application;
use Greg\AppView\Events\LoadViewerEvent;
use Greg\Framework\ServiceProvider;
use Greg\View\ViewBladeCompiler;
use Greg\View\Viewer;
use PHPUnit\Framework\TestCase;

class ViewServiceProviderTest extends TestCase
{
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
}
