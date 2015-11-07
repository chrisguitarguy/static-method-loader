<?php
/*
 * This file is part of chrisguitarguy/static-method-loader
 *
 * Copyright (c) Christopher Davis <http://christopherdavis.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Chrisguitarguy\StaticMethodLoader\Resolver\Fixtures;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FixtureBundle extends Bundle
{
    public static function loadRoutes()
    {
        // noop
    }
}
