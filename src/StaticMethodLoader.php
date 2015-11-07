<?php
/*
 * This file is part of chrisguitarguy/static-method-loader
 *
 * Copyright (c) Christopher Davis <http://christopherdavis.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chrisguitarguy\StaticMethodLoader;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Routing\RouteCollection;

/**
 * Load routes from a static method on a class.
 *
 * @since   0.1
 */
final class StaticMethodLoader extends Loader
{
    const TYPE = 'staticmethod';

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type=null)
    {
        return $type === self::TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type=null)
    {
        if (!$this->supports($resource, $type)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s resources are not supported',
                null === $type ? 'NULL' : $type
            ));
        }

        list($class, $method) = self::assureValidResource($resource);

        call_user_func([$class, $method], $routes = new RouteCollection(), $this);

        self::addResources(new \ReflectionClass($class), $routes);

        return $routes;
    }

    private static function addResources(\ReflectionClass $ref, RouteCollection $routes)
    {
        do {
            $routes->addResource(new FileResource($ref->getFileName()));
        } while ($ref = $ref->getParentClass());
    }

    private static function assureValidResource($resource)
    {
        $parts = explode('::', $resource, 2);
        if (count($parts) !== 2) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects resources to be in the format FCQN::methodName, got "%s"',
                __CLASS__,
                $resource
            ));
        }

        if (!class_exists($parts[0])) {
            throw new Exception\LogicException(sprintf('class %s does not exist', $parts[0]));
        }

        if (!is_callable($parts)) {
            throw new Exception\LogicException(sprintf('%s is not callable', $resource));
        }

        return $parts;
    }
}
