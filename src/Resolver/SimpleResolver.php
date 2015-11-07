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

/**
 * A class name resolver that just checks to ensure the class exists.
 *
 * @since   2015-11-07
 */
final class SimpleResolver extends AbstractResolver
{
    /**
     * {@inheritdoc}
     */
    public function resolve($classname)
    {
        return self::assureClassExists($classname);
    }
}
