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

class SimpleResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testResolverErrorsWhenTheClassDoesNotExist()
    {
        (new SimpleResolver())->resolve(__NAMESPACE__.'\\DoesNotExist');
    }

    public function testResolverReturnsTheClassNameWhenItExists()
    {
        $this->assertEquals(__CLASS__, (new SimpleResolver())->resolve(__CLASS__));
    }
}
