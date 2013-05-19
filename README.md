Orlex: Organized Silex
===
[![Build Status](https://api.travis-ci.org/dcousineau/orlex.png?branch=master)](https://travis-ci.org/dcousineau/orlex)

Orlex attempts to give a mild amount of structure to [Silex](http://github.com/fabpot/Silex) without extending, overwriting, or using any dirty hacks.

Setting up Orlex is as simple as registering a service provider and pointing it to a directory of controllers:

```php
<?php
$app = new \Silex\Application();

$app->register(new \Orlex\ServiceProvider(),[
    'orlex.controller.dirs' => [
        __DIR__ . '/app/Controllers',
    ],
]);

$app->run();
```

And then actually creating a controller:

```php
<?php
namespace app\Controllers;

use Orlex\Annotation\Route;
use Orlex\Annotation\Before;
use Orlex\Annotation\After;

/**
 * @Route(path="/")
 */
class IndexController {
    /**
     * @Route(path="/", name="root")
     * @Before("beforeIndex")
     * @After("afterIndex")
     */
    public function indexAction() { /* Do Stuff */ }
    
    public function beforeIndex() { /* Do Stuff Before */ }
    public function afterIndex() { /* Do Stuff After */ }

    /**
     * @Route(path="/page/{id}", name="root_page", methods={"GET", "POST"})
     */
    public function pageAction($id) { /* Do Stuff With $id */ }
}
```

Installation
---

Orlex is provided as a [composer package](http://getcomposer.org/) and requires PHP 5.4 and up. To use Orlex, simply add:

```json
{
    "require": {
        "dcousineau/orlex": "dev-master"
    }
}
```

Controller Traits
---

Since Orlex seeks to be an "Organized Silex", it provides several convenience traits for your controller classes. These traits include a Twig trait, a Form trait, a Session trait.

To use them, your controller MUST use the `\Orlex\ContainerAwareTrait`. 

> **NOTE:** Any controller classes that use the aforementioned ContainerAwareTrait will have their container automatically set by the route compiler.

For example:

```php
<?php
namespace app\Controllers;

use Orlex\Annotation\Route;
use Orlex\ContainerAwareTrait;
use Orlex\Controller\TwigTrait;

/**
 * @Route(path="/")
 */
class IndexController {
    use ContainerAwareTrait;
    use TwigTrait;
    
    /**
     * @Route(path="/", name="root")
     */
    public function indexAction() {
        return $this->render('index.html.twig', []);
    }
}
```

Obviously each respsective trait will require their companion service provider to be already registered with the Silex application.

Custom Annotations
---

Orlex will support custom annotations. If you give your Orlex manager a directory and a namespace it will look for annotations contained there within:

```php
<?php
//...
$app->register(new \Orlex\ServiceProvider(),[
    'orlex.controller.dirs' => [
        __DIR__ . '/app/Controllers',
    ],
    'orlex.annotation.dirs' => [
        __DIR__ => 'app\Annotation',
    ]
]);
//...
```

> **NOTE:** The path key for annotation autoloading should be the *root directory containing the entire namespace*. If your annotation files are of the namespace `app\Annotation` and your annotation files are in `/path/to/src/app/Annotation`, the proper annotation configuration would be `'/path/to/src' => 'app\Annotation'`

If the annotation implements the `Orlex\Annotation\RouteModifier` interface, Orlex will allow it to alter the internal Silex controller created for that specific route/action:

```php
<?php
namespace app\Annotation;

use Orlex\Annotation\RouteModifier;
use Silex\Controller;
use Silex\Application;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class Test implements RouteModifier {
    public function weight() { return 1; }

    public function modify($serviceid, Controller $controller, Application $app, \ReflectionClass $class, \ReflectionMethod $method) {
        $controller->before(function() {
            var_dump('From @Test annotation');
        });
    }
}
```

For more information on how to define annotations, please see the [Doctrine Annotations documentation](http://docs.doctrine-project.org/projects/doctrine-common/en/latest/reference/annotations.html).

**Expect this interface to possibly change in the future, particularly where the `modify(…)` signature is concerned**.

Annotation Caching
---

Orlex is setup for easy annotation caching via the `Doctrine\Common\Annotations\CachedReader` reader. Simply include a cache directory:

```php
<?php
$app->register(new \Orlex\ServiceProvider(),[
    'orlex.cache.dir' => __DIR__ . '/cache',
    'orlex.controller.dirs' => [
        __DIR__ . '/app/Controllers',
    ],
]);
```

And Orlex will setup a `Doctrine\Common\Cache\FilesystemCache` pointed at that directory and use said cache with the `CachedReader`. Alternatively you can override the `orlex.cache` service in your application container to return a `Doctrine\Common\Cache\Cache` object and it will be used instead.

Internal Behavior
---

Orlex works by scanning controller directories and inspecting annotations on all classes contained therewithin. Each controller class has a [Service Controller](http://silex.sensiolabs.org/doc/providers/service_controller.html) created for it (as seen in the above `modify(…)` example's parameter `$serviceid`).

> **Orlex automatically registers the `ServiceControllerServiceProvider` with the specified Silex application.**

Then, each method with a `@Route` annotation is created by performing an `$app->match($path, "$serviceid:$methodName")`. The result of `$app->match(…)` is a Silex Controller, which is then passed in a chain through each annotation that implements the `Orlex\Annotation\RouteModifier` interface, allowing it to chain anything that is required to the Silex controller.

Want To Help?
---

Please do! Just remember this is early alpha under going "api stress testing" (meaning I'm using this internally on a project and new features are being implemented on demand and going through trial-by-fire).

To-Do
---

* ~~Clean up route compiler, become more DI friendly~~
* ~~Post to packagist.org~~
* ~~Functional Testing~~
* ~~Annotation Caching~~
* Route Caching
* CLI Scaffolding (similar to Symfony's console command)
