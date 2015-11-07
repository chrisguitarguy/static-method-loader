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

/**
 * Resolve a class name string to a valid class name.
 *
 * @since   0.1
 */
interface ClassNameResolver
{
    /**
     * Resolve a class name to something that can be used with the `StaticMethodLoader`.
     * This may mean just doing a simple `class_exists` check.
     *
     * @param   string $name The class name
     * @throws  InvalidArgumentException if the class name is invalid
     * @return  string The resolved class name
     */
    public function resolve($className);
}
