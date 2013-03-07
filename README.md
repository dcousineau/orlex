Orlex: Organized Silex
===

Orlex attempts to give a mild amount of structure to [Silex](http://github.com/fabpot/Silex) without extending, overwriting, or using any dirty hacks.

Orlex is simply a manager that *scaffolds* your Silex application for you in a way that is maintainable for medium sized projects and larger teams.

Using Orlex is as simple as wrapping your pre-existing Silex application instance with the Orlex manager:

```php
<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';

$app = new \Silex\Application();

$orlex = new \Orlex\Manager($app, [
    'controllers' => [
        __DIR__ . '/app/Controllers',
    ],
]);

$orlex->scaffold()  //Creates routes in $app
      ->run();      //Proxy for $app->run()
```

And then creating controller classes in your controller directories:

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
    public function indexAction() {
        //Do Stuff
    }
    
    public function beforeIndex() {
        //Do Stuff Before
    }

    public function afterIndex() {
        //Do Stuff After
    }

    /**
     * @Route(path="/page/{id}", name="root_page", methods={"GET", "POST"})
     */
    public function pageAction($id) {
        //Do Stuff
    }
}
```

Installation
---

Orlex is provided as a [composer package](http://getcomposer.org/). Simply add:

```json
{
    "require": {
        "dcousineau/orlex": "dev-master"
    }
}
```

Since Orlex is *not currenlty distributed through packagist*, you will need to add a reference to this repository in your `composer.json` file:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/dcousineau/orlex.git"
        }
    ]
}
```

WARNING
---

**Orlex is currently in extremely active development.** The "exterior" API involving creating the Manager and pointing to the directories containing Controller classes should remain unchanged, but the internals will vary drastically.

Custom Annotations
---

Orlex will support custom annotations. If you give your Orlex manager a directory and a namespace it will look for annotations contained there within:

```php
<?php
//...
$orlex = new \Orlex\Manager($app, [
    'controllers' => [
        __DIR__ . '/app/Controllers',
    ],
    'annotations' => [
        __DIR__ => 'app\Annotation',
    ]
]);
//...
```

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

    /**
     * @param string $serviceid
     * @param \Silex\Controller $controller
     * @param \Silex\Application $app
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     * @return mixed
     */
    public function modify($serviceid, Controller $controller, Application $app, \ReflectionClass $class, \ReflectionMethod $method) {
        $controller->before(function() {
            var_dump('From @Test annotation');
        });
    }
}
```

**Expect this interface to possibly change in the future, particularly where the `modify(…)` signature is concerned**.

Internal Behavior
---

Orlex works by scanning controller directories and inspecting annotations on all classes contained therewithin. Each controller class has a [Service Controller](http://silex.sensiolabs.org/doc/providers/service_controller.html) created for it (as seen in the above `modify(…)` example's parameter `$serviceid`).

*Orlex automatically registers the `ServiceControllerServiceProvider` with the specified Silex application.*

Then, each method with a `@Route` annotation is created by performing an `$app->match($path, "$serviceid:$methodName")`. The result of `$app->match(…)` is a Silex Controller, which is then passed in a chain through each annotation that implements the `Orlex\Annotation\RouteModifier` interface, allowing it to chain anything that is required to the Silex controller.

Want To Help?
---

Please do! Just remember this is super early alpha under going "api stress testing" (meaning I'm using this internally on a project and new features are being implemented on demand and going through trial-by-fire).

To-Do
---

* Clean up route compiler, become more DI friendly
* Functional Testing
* Annotation Caching
* Route Caching
* Post to packagist.org
* CLI Scaffolding (similar to Symfony's console command)