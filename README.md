# Twedoo Load Config inside a Bundle for Symfony 3.*

## Description

Using Symfony modular, simple loading all file yml (configuration and services and routing) from inside each Bundle and inject it in config global.

Installation
------------

 link packagist : https://packagist.org/packages/twedoo/twedoo-load-config



**1.** **Install package :**

  Install with composer run `composer require twedoo/twedoo-load-config` to include this in your project. 
  
  Or add to your composer.json in Symfony project : 

 ```
 required-dev:
 {
    twedoo/twedoo-load-config: '^1.0'
 }
 ```
 and run `composer update`



**2.** **Configuration package :** 

Add in app/AppKernel.php 
```
new symfony\Twedoo\TwedooBundle();
```
Add in app/config/routing.yml

```
app_extra:
    resource: .
    type: routing_injection

```



Documentation
-------------

Wiki : https://github.com/Maamriya/twedoo-load-config/wiki/Install-package-Symfony-Twedoo-Load-Config


This package provider new extension name `twedoo_load`.

* In your Bundle create directory `/src/appBundle/Resources/` it's content all files config  (config.yml, services.yml, routing.yml) inside the directory config see this image:

![description path dir config](https://pli.io/nTrjx.png)

* In your file `appBundle/Resources/config/routing.yml` put all of routing of this Bundle here without touch `app/config/routing.yml`.

* The same thing with `appBundle/Resources/config/services.yml` you can add services of this Bundle.

* But in `appBundle/Resources/config/config.yml` should add `parameters:` and `twedoo_load:` extension then add the parameters like this:

``` 
parameters:
    twedoo_load:
        exemple_key1: exemple_value1
        exemple_key2: exemple_value2
        exemple_key3: exemple_value3
```



License
-------

This package is under the MIT license.
