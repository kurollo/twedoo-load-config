<?php
/**
 * Created by PhpStorm.
 * User: Houssem Maamria
 * mail.houssem@gmail.com
 * Twitter : @maamriya
 * Date: 02/09/18
 * Time: 01:00
 */

namespace symfony\Twedoo\RoutingInjection;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Yaml\Yaml;

class RouteServiceProvider extends Loader
{

    public $getDirectories = [];
    public $ignoreDir = ['..', '.'];
    public $getBundles = [];

    public function load($resource, $type = null)
    {

        $dir = getcwd().'/app/config';
        $this->getDirectories = Yaml::parse(file_get_contents($dir.'/config.yml'))['parameters']['directories_inside_src'];

        if(is_array($this->getDirectories))
        {
            foreach ($this->getDirectories as $key => $dirName)
            {
                $dir = getcwd().'/src/'.$dirName.'/';
                if (is_dir($dir )) {
                    $this->getBundles[$dir] = preg_grep('/^([^.])/', array_diff(scandir($dir, 1), $this->ignoreDir));
                }
            }
            $this->ignoreDir = array_merge($this->ignoreDir, $this->getDirectories);
        }

        $dir = getcwd().'/src/';
        $this->getBundles[$dir] = preg_grep('/^([^.])/', array_diff(scandir($dir, 1),  $this->ignoreDir));

        $routes = new RouteCollection();

        foreach ($this->getBundles as $path => $bundles)
        {
            foreach ($bundles as $bundle)
            {
                if(file_exists($path.$bundle.'/Resources/config/routing.yml'))
                {
                    $resource = '@'.$bundle.'/Resources/config/routing.yml';
                    $type = 'yaml';
                    $importedRoutes = $this->import($resource, $type);
                    $routes->addCollection($importedRoutes);
                }
            }
        }
        return  $routes;
    }

    public function supports($resource, $type = null)
    {
        return 'routing_injection' === $type;
    }
}

