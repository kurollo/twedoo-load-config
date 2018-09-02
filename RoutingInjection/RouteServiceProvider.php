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

class RouteServiceProvider extends Loader
{

    public function load($resource, $type = null)
    {
        $dir = getcwd().'/src/';

        $getBundles = preg_grep('/^([^.])/', array_diff(scandir($dir, 1), array('..', '.')));
        $routes = new RouteCollection();
        foreach ($getBundles as $bundle)
        {
            if(file_exists($dir.$bundle.'/Resources/config/routing.yml'))
            {
                $resource = '@'.$bundle.'/Resources/config/routing.yml';
                $type = 'yaml';
                $importedRoutes = $this->import($resource, $type);
                $routes->addCollection($importedRoutes);
            }
        }
        return  $routes;
    }

    public function supports($resource, $type = null)
    {
        return 'routing_injection' === $type;
    }
}

