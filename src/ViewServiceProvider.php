<?php

namespace Greg\AppView;

use Greg\AppInstaller\Application;
use Greg\AppInstaller\Events\ConfigAddEvent;
use Greg\AppInstaller\Events\ConfigRemoveEvent;
use Greg\AppInstaller\Events\ResourceAddEvent;
use Greg\AppInstaller\Events\ResourceRemoveEvent;
use Greg\AppView\Events\LoadViewerEvent;
use Greg\Framework\ServiceProvider;
use Greg\View\ViewBladeCompiler;
use Greg\View\Viewer;

class ViewServiceProvider implements ServiceProvider
{
    const EXTENSION_BLADE = 'blade';

    private const CONFIG_NAME = 'viewer';

    private const RESOURCE_NAME = 'views';

    private $app;

    public function name(): string
    {
        return 'greg-view';
    }

    public function boot(Application $app)
    {
        $this->app = $app;

        $app->inject(Viewer::class, function () use ($app) {
            $viewer = new Viewer(...(array) $this->config('paths'));

            foreach ((array) $this->config('compilers') as $compiler) {
                if (!($extension = $compiler['extension'] ?? null)) {
                    throw new \Exception('Undefined extension for compiler.');
                }

                $viewer->addExtension($extension, function () use ($extension, $compiler) {
                    $type = $compiler['type'] ?? null;

                    if ($type === self::EXTENSION_BLADE) {
                        if (!($compilationPath = $compiler['compilation_path'] ?? null)) {
                            throw new \Exception('Undefined compilation path for `' . $extension . '` extension.');
                        }

                        return new ViewBladeCompiler($compilationPath);
                    }

                    throw new \Exception('Unsupported compiler type `' . $type . '` for `' . $extension . '` extension.');
                });
            }

            $app->event(new LoadViewerEvent($viewer));

            return $viewer;
        });
    }

    public function install(Application $app)
    {
        $app->event(new ConfigAddEvent(__DIR__ . '/../config/config.php', self::CONFIG_NAME));

        $app->event(new ResourceAddEvent(__DIR__ . '/../resources/views', self::RESOURCE_NAME));
    }

    public function uninstall(Application $app)
    {
        $app->event(new ResourceRemoveEvent(self::RESOURCE_NAME));

        $app->event(new ConfigRemoveEvent(self::CONFIG_NAME));
    }

    private function config($name)
    {
        return $this->app()->config(self::CONFIG_NAME . '.' . $name);
    }

    private function app(): Application
    {
        return $this->app;
    }
}
