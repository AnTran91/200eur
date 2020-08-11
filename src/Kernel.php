<?php

/*
 * This file is the core of Symfony.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    /**
     * Get the cache directory
     *
     * @return string Cache directory
     */
    public function getCacheDir()
    {
        return $this->getProjectDir().'/var/cache/'.$this->environment;
    }

    /**
     * Get the log directory
     *
     * @return string Log directory
     */
    public function getLogDir()
    {
        return $this->getProjectDir().'/var/log';
    }

    /**
     * {@inheritDoc}
     */
    public function registerBundles()
    {
        $contents = require  $this->getProjectDir().'/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }
    /**
     * {@inheritDoc}
     */
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $container->addResource(new FileResource($this->getProjectDir().'/config/bundles.php'));
        // Feel free to remove the "container.autowiring.strict_mode" parameter
        // if you are using symfony/dependency-injection 4.0+ as it's the default behavior
        $container->setParameter('container.autowiring.strict_mode', true);
        $container->setParameter('container.dumper.inline_class_loader', true);

        $confDir = $this->getProjectDir().'/config';

        $loader->load($confDir.'/{parameters}/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{parameters}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');

        $loader->load($confDir.'/{packages}/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{packages}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');

        $loader->load($confDir.'/{services}/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
    }

    /**
     * {@inheritDoc}
     */
    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $confDir = $this->getProjectDir().'/config';

        $routes->import($confDir.'/{routes}/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}'.self::CONFIG_EXTS, '/', 'glob');
    }
}
