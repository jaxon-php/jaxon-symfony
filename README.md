Jaxon integration for Symfony
=============================

This package is an extension to integrate the [Jaxon library](https://github.com/jaxon-php/jaxon-core) into the Symfony framework.
It works with Symfony version 5 or newer.

Installation
------------

Add the following lines in the `composer.json` file, and run the `composer update jaxon-php/` command.

```json
"require": {
    "jaxon-php/jaxon-symfony": "^5.0"
}
```

Add the Jaxon bundle in the `config/bundle.php` file.

```php
return [
    ...

    Jaxon\Symfony\JaxonBundle::class => ['all' => true],
];
```

Configuration
-------------

The library configuration is located in the `packages/config/jaxon.yaml` file.
It must contain both the `app` and `lib` sections defined in the documentation (https://www.jaxon-php.org/docs/v5x/about/configuration.html).

An example is presented in the `config/jaxon.yaml` file of this repo.

Add the following settings in the `config/services.yaml` file, to configure the Jaxon library.

```yaml
imports:
    ...
    - { resource: packages/jaxon.yaml }
```

Add the following settings in the `config/routes.yaml` file, to configure the Jaxon route.

```yaml
jaxon_ajax:
    resource: "@JaxonBundle/config/routes.yaml"
    prefix:   /
```

Routing and listener
--------------------

The extension provides a route and a controller to process Jaxon Ajax requests, as well as a listener on the `kernel.controller` event to bootstrap the Jaxon library.

Dependency injection
--------------------

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

Twig functions
--------------

This extension provides the following Twig functions to insert Jaxon js and css codes in the pages that need to show Jaxon related content.

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

Call factories
--------------

This extension registers the following Blade directives for Jaxon [call factories](https://www.jaxon-php.org/docs/v5x/ui-features/call-factories.html) functions.

> [!NOTE]
> In the following examples, the `rqAppTest` var in the template is set to the value `rq(Demo\Ajax\App\AppTest::class)`.

The `jxnBind` directive attaches a UI component to a DOM node, while the `jxnHtml` directive displays a component HTML code in a view.

```php
    <div class="col-md-12" {{ jxnBind(rqAppTest) }}>
        {{ jxnHtml(rqAppTest) }}
    </div>
```

The `jxnPagination` directive displays pagination links in a view.

```php
    <div class="col-md-12" {{ jxnPagination(rqAppTest) }}>
    </div>
```

The `jxnOn` directive binds an event on a DOM node to a Javascript call defined with a `call factory`.

```php
    <select class="form-control"
        {{ jxnOn('change', rqAppTest.setColor(jq()->val())) }}>
        <option value="black" selected="selected">Black</option>
        <option value="red">Red</option>
        <option value="green">Green</option>
        <option value="blue">Blue</option>
    </select>
```

The `jxnClick` directive is a shortcut to define a handler for the `click` event on a DOM node.

```php
    <button type="button" class="btn btn-primary"
        {{ jxnClick(rqAppTest.sayHello(true)) }}>Click me</button>
```

The `jxnEvent` directive defines a set of events handlers on the children of a DOM nodes, using `jQuery` selectors.

```php
    <div class="row" {{ jxnEvent([
        ['.app-color-choice', 'change', rqAppTest.setColor(jq()->val())]
        ['.ext-color-choice', 'change', rqExtTest.setColor(jq()->val())]
    ]) }}>
        <div class="col-md-12">
            <select class="form-control app-color-choice">
                <option value="black" selected="selected">Black</option>
                <option value="red">Red</option>
                <option value="green">Green</option>
                <option value="blue">Blue</option>
            </select>
        </div>
        <div class="col-md-12">
            <select class="form-control ext-color-choice">
                <option value="black" selected="selected">Black</option>
                <option value="red">Red</option>
                <option value="green">Green</option>
                <option value="blue">Blue</option>
            </select>
        </div>
    </div>
```

The `jxnEvent` directive takes as parameter an array in which each entry is an array with a `jQuery` selector, an event and a `call factory`.

Contribute
----------

- Issue Tracker: github.com/jaxon-php/jaxon-symfony/issues
- Source Code: github.com/jaxon-php/jaxon-symfony

License
-------

The package is licensed under the BSD license.
