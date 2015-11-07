# Static Method Loader

A custom route loader for Symfony's [routing component](http://symfony.com/doc/current/book/routing.html)
that will load routes from a static method.

Written for a [tutorial on custom route loaders](http://christopherdavis.me/blog/symfony-custom-route-loaders.html).

## Usage with Symfony Full Stack

Add the `StaticMethodLoader` to your service configuration and tag it with
`routing.loader`.

```yaml
# services.yml

services:
    app.static_method_loader:
        class: Chrisguitarguy\StaticMethodLoader\StaticMethodLoader
        tags:
            - { name: routing.loader }
```

Then import static method resources:

```yaml
# routing.yml

_other:
    resource: Vendor\Package\ClassName::method
    type: staticmethod
```

## Usage with Only the Routing Component

Pass the `StaticMethodLoader` to your main `Router` class or pass it to a
`DelegatingLoader`.

```php
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Chrisguitarguy\StaticMethodLoader\StaticMethodLoader;

// using only the static method loader
$router = new Router(new StaticMethodLoader(), 'Vendor\Package\ClassName::load', [
    'resource_type' => 'staticmethod',
]);

// or with a DelegatingLoader
$router = new Router(new DelegatingLoader( new LoaderResolver([
    new YamlFileLoader(),
    new StaticMethodLoader(),
])), 'path/to/routing.yml');
```

## License

MIT
