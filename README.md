Jaxon Library for Symfony
=========================

This package integrates the [Jaxon library](https://github.com/jaxon-php/jaxon-core) into the Symfony framework.

Installation
------------

Add the following lines in the `composer.json` file, and run the `composer update` command.

```json
"require": {
    "jaxon-php/jaxon-symfony": "^4.0"
}
```

Or run the `composer require jaxon-php/jaxon-symfony` command.

Add the following settings in the `config/services.yaml` file, to declare the Jaxon service.

```yaml
services:
    ...
    jaxon.ajax.utils:
        class: Jaxon\Symfony\Utils
    Jaxon\Symfony\Jaxon:
        arguments:
            - '@kernel'
            - '@logger'
            - '@twig'
            - '@=service(service("jaxon.ajax.utils").getSessionService())'
            - '%jaxon%'
imports:
    ...
    - { resource: jaxon.yaml }
```

Create and edit the `config/jaxon.yaml` file to suit the needs of your application.
A sample config file is available [in this repo](https://github.com/jaxon-php/jaxon-symfony/blob/master/config/jaxon.yaml).

This config file by default registers Jaxon classes in the `jaxon/ajax` directory with the `\Jaxon\Ajax` namespace.
Make sure this directory exists, even if it is empty.

The last step is to define a controller action to process Jaxon ajax requests, and insert Jaxon js and css codes in the pages where they are required.

```php
use Jaxon\Symfony\Jaxon;

class DemoController extends AbstractController
{
    /**
     * Process Jaxon ajax requests. This route must be the same that is set in the Jaxon config.
     *
     * @Route("/ajax", name="jaxon.ajax")
     */
    public function jaxon(Jaxon $jaxon)
    {
        if(!$jaxon->canProcessRequest())
        {
            // Jaxon failed to find a plugin to process the request 
            return; // Todo: return an error message
        }

        $jaxon->processRequest();
        return $jaxon->httpResponse();
    }

    /**
     * Insert Jaxon js and css codes in the page.
     *
     * @Route("/", name="homepage")
     */
    public function index(Jaxon $jaxon)
    {
        // Insert Jaxon codes into the page
        return $this->render('demo/index.html.twig', [
            ...
            'jaxonCss' => $jaxon->css(),
            'jaxonJs' => $jaxon->js(),
            'jaxonScript' => $jaxon->script(),
        ]);
    }
}
```

Configuration
------------

The settings in the `config/jaxon.yml` config file are separated into two sections.
The options in the `lib` section are those of the Jaxon core library, while the options in the `app` sections are those of the Symfony application.

The following options can be defined in the `app` section of the config file.

| Name | Description |
|------|---------------|
| directories | An array of directory containing Jaxon application classes |
| views   | An array of directory containing Jaxon application views |
| | | |

By default, the `views` array is empty. Views are rendered from the framework default location.
There's a single entry in the `directories` array with the following values.

| Name | Default value | Description                               |
|------|---------------|-------------------------------------------|
| directory | jaxon/ajax    | The directory of the Jaxon classes        |
| namespace | \Jaxon\Ajax   | The namespace of the Jaxon classes        |
| separator | .             | The separator in Jaxon js class names     |
| protected | empty array   | Prevent Jaxon from exporting some methods |
| |               |                                           |

Usage
-----

### The Jaxon classes

The Jaxon classes can inherit from `\Jaxon\App\CallableClass`.
By default, they are located in the `jaxon/ajax` dir of the Symfony application, and the associated namespace is `\Jaxon\Ajax`.

This is a simple example of a Jaxon class, defined in the `jaxon/Ajax/HelloWorld.php` file.

```php
namespace Jaxon\Ajax;

class HelloWorld extends \Jaxon\App\CallableClass
{
    public function sayHello()
    {
        $this->response->assign('div2', 'innerHTML', 'Hello World!');
        return $this->response;
    }
}
```

### Dependency injection

Services in Symfony can be declared as public or private, and [injected in Jaxon classes](https://www.jaxon-php.org/docs/v3x/advanced/dependency-injection.html).

Since Jaxon uses a container to fetch to the Symfony services that are injected in his classes, by default it will be able to get access only to services declared as public.

A service locator can be defined for Jaxon in the `config/services.yaml` file, in order to provide access to private services.

```yaml
services:
  ...
    jaxon.service_locator:
        public: true
        class: Symfony\Component\DependencyInjection\ServiceLocator
        tags: ['container.service_locator']
        arguments:
            -
                Twig\Environment: '@twig'
```

The service locator must be declared as public, and take all the services that can be passed to Jaxon classes as arguments.
See the [Symfony service locators documentation](https://symfony.com/doc/4.4/service_container/service_subscribers_locators.html).

Contribute
----------

- Issue Tracker: github.com/jaxon-php/jaxon-symfony/issues
- Source Code: github.com/jaxon-php/jaxon-symfony

License
-------

The package is licensed under the BSD license.
