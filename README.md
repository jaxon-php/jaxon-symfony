Jaxon integration for Symfony
=============================

This package integrates the [Jaxon library](https://github.com/jaxon-php/jaxon-core) into the Symfony framework.

Installation
------------

Add the following lines in the `composer.json` file, and run the `composer update` command.

```json
"require": {
    "jaxon-php/jaxon-symfony": "^5.0"
}
```

Or run the `composer require jaxon-php/jaxon-symfony ^5.0` command.

Add the Jaxon bundle in the `config/bundle.php` file.

```php
return [
    ...

    Jaxon\Symfony\JaxonBundle::class => ['all' => true],
];
```

Create and edit the `packages/config/jaxon.yaml` file to suit the needs of your application.
A sample config file is available [in this repo](https://github.com/jaxon-php/jaxon-symfony/blob/master/config/jaxon.yaml).

Add the following settings in the `config/services.yaml` file, to configure the Jaxon library.

```yaml
imports:
    ...
    - { resource: packages/jaxon.yaml }
```

This config file by default registers Jaxon classes in the `jaxon/ajax` directory with the `\Jaxon\Ajax` namespace.

The Jaxon library must be setup on all pages that need to show Jaxon related content, using an event subscriber for example.

```php
<?php

// src/EventSubscriber/JaxonSubscriber.php
namespace App\EventSubscriber;

use App\Controller\DemoController;
use App\Controller\JaxonController;
use Jaxon\Symfony\App\Jaxon;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

use function is_array;

class JaxonSubscriber implements EventSubscriberInterface
{
    public function __construct(private Jaxon $jaxon)
    {}

    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();

        // when a controller class defines multiple action methods, the controller
        // is returned as [$controllerInstance, 'methodName']
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        // Select the controllers with Jaxon related content.
        if ($controller instanceof JaxonController || $controller instanceof DemoController) {
            $this->jaxon->setup();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
```

Define a controller action to process Jaxon ajax requests.

```php
use Jaxon\Symfony\App\Jaxon;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class JaxonController extends AbstractController
{
    #[Route('jaxon', name: 'jaxon.ajax', methods: ['POST'])]
    public function __invoke(Jaxon $jaxon)
    {
        if(!$jaxon->canProcessRequest())
        {
            return; // Todo: return an error message
        }

        $jaxon->processRequest();
        return $jaxon->httpResponse();
    }
}
```

Insert Jaxon js and css codes in the pages that need to show Jaxon related content, using the `Twig` functions provided by the Jaxon bundle.

```php
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use function Jaxon\rq;

class DemoController extends AbstractController
{
    #[Route('/', name: 'demo.home')]
    public function __invoke()
    {
        // Print the page
        return $this->render('demo/index.html.twig', [
            'pageTitle' => "Symfony Framework",
        ]);
    }
}
```

```php
// templates/demo/index.html.twig

<!-- In page header -->

{{ jxnCss() }}
</head>

<body>

<!-- Page content here -->

</body>

<!-- In page footer -->

{{ jxnJs() }}

{{ jxnScript() }}
```

Configuration
------------

The settings in the `config/package/jaxon.yml` config file are separated into two sections.
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
