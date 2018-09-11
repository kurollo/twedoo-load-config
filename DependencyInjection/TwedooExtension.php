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
    public $getBundles = [];
    public $ignoreDir = ['..', '.'];
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $dirGlobal = $container->getParameter('kernel.project_dir');
        $dirInSrc  = $container->getParameter('directories_inside_src');

        if(is_array($dirInSrc))
        {
            foreach ($dirInSrc as $key => $dirName)
            {
                $dir = $dirGlobal.'/src/'.$dirName.'/';
                if (is_dir($dir )) {
                    $this->getBundles[$dir] = preg_grep('/^([^.])/', array_diff(scandir($dir, 1), $this->ignoreDir));
                }
            }
            $this->ignoreDir = array_merge($this->ignoreDir, $dirInSrc);
        }

        $dir = $dirGlobal.'/src/';
        $this->getBundles[$dir] = preg_grep('/^([^.])/', array_diff(scandir($dir, 1),  $this->ignoreDir));

        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../services/')
        );
        $loader->load('services.yml');

        foreach ($this->getBundles as $path => $bundles)
        {
            foreach ($bundles as $bundle)
            {
                $getFileLocator = $path.$bundle.'/Resources/config';
                
                if (!is_dir($getFileLocator ))
                    continue;


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