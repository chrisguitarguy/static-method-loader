<?php
/*
 * This file is part of chrisguitarguy/static-method-loader
 *
 * Copyright (c) Christopher Davis <http://christopherdavis.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chrisguitarguy\StaticMethodLoader\Resolver;

use Symfony\Component\HttpKernel\KernelInterface;
use Chrisguitarguy\StaticMethodLoader\Exception\InvalidArgumentException;

/**
 * A resolver that looks for an `@` sign at the begininging of a class name
 * and, if present, parses the class name as if its coming from a bundle.
 *
 * @since   2015-11-07
 */
final class BundleAwareResolver extends AbstractResolver
{
    const PREFIX = '@';

    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($classname)
    {
        if (!$this->isBundle($classname)) {
            return self::assureClassExists($classname);
        }

        if (count($parts = explode('\\', substr($classname, 1), 2)) !== 2) {
            throw new InvalidArgumentException(sprintf(
                'Bundle based resources must be in the format @BundleName\\ClassName, got %s',
                $classname
            ));
        }

        list($bundleName, $className) = $parts;

        try {
            $bundles = $this->kernel->getBundle($bundleName, false);
        } catch (\InvalidArgumentException $e) {
            throw new InvalidArgumentException(sprintf(
                'The %s (from %s) does not exist or is not enabled in your kernel',
                $bundleName,
                $classname
            ), 0, $e);
        }

        return $this->locateClass($bundles, $className);
    }

    private function locateClass(array $bundles, $className)
    {
        $invalid = [];
        foreach ($bundles as $bundle) {
            $try = $bundle->getNamespace().'\\'.$className;
            if (class_exists($try)) {
                return $try;
            }
            $invalid[] = $bundle->getName();
        }

        throw new InvalidArgumentException(sprintf(
            'Unable to find class %s in bundles %s',
            $className,
            implode(', ', $invalid)
        ));
    }

    private function isBundle($classname)
    {
        return !$classname || self::PREFIX === $classname[0];
    }
}
