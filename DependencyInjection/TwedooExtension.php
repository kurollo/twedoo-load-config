<?php
/**
 * Created by PhpStorm.
 * User: Houssem Maamria
 * mail.houssem@gmail.com
 * Twitter : @maamriya
 * Date: 02/09/18
 * Time: 01:00
 */
namespace symfony\Twedoo\DependencyInjection;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class TwedooExtension extends Extension
{
    public $configs = [];
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $dirGlobal = $container->getParameter('kernel.project_dir');
        $dir = $dirGlobal.'/src/';
        $getBundles = preg_grep('/^([^.])/', array_diff(scandir($dir, 1), array('..', '.')));

        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../services/')
        );
        $loader->load('services.yml');

        foreach ($getBundles as $bundle)
        {
            $getFileLocator = $dir.$bundle.'/Resources/config';
            $loader = new Loader\YamlFileLoader(
                $container,
                new FileLocator($getFileLocator)
            );

            if(file_exists($getFileLocator.'/config.yml'))
            {
                $loader->load('config.yml');
                $this->configs = Yaml::parse(file_get_contents($getFileLocator.'/config.yml'))['parameters']['twedoo_load'];

                foreach ($this->configs as $key => $attribute) {
                    if(is_array($attribute) && strpos($key, '[]') !== false)
                    {
                        foreach ($attribute as $param => $value)
                            $container->setParameter('twedoo_load.'.$key.'.'.$param, $value);
                    }
                    else{
                        $container->setParameter('twedoo_load.'.$key, $attribute);
                    }
                }
            }

            if(file_exists($getFileLocator.'/services.yml'))
                $loader->load('services.yml');
        }
    }

    public function getAlias()
    {
        return 'twedoo';
    }

    public function getNamespace()
    {
        return 'twedoo';

    }

    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/';
    }
}