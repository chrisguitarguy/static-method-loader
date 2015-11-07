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

use Chrisguitarguy\StaticMethodLoader\ClassNameResolver;
use Chrisguitarguy\StaticMethodLoader\Exception\ClassDoesNotExist;

/**
 * ABC for resolvers.
 *
 * @since   0.1
 */
abstract class AbstractResolver implements ClassNameResolver
{
    protected static function assureClassExists($classname)
    {
        if (!class_exists($classname)) {
            throw ClassDoesNotExist::fromClass($classname);
        }

        return $classname;
    }
}
