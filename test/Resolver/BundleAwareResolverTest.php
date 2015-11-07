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
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class BundleAwareResolverTest extends \PHPUnit_Framework_TestCase
{
    private $kernel, $resolver;

    public function testResolverChecksForNormalClassIfNoPrefixIsFound()
    {
        $this->assertEquals(__CLASS__, $this->resolver->resolve(__CLASS__));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The FixtureBundle (from @FixtureBundle\ClassName) does not exist
     */
    public function testResolverErrorsWhenTheBundleIsNotFound()
    {
        $this->kernel->expects($this->once())
            ->method('getBundle')
            ->with('FixtureBundle', false)
            ->willThrowException(new \InvalidArgumentException('bundle not found'));

        $this->resolver->resolve('@FixtureBundle\\ClassName');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage must be in the format @BundleName\ClassName
     */
    public function testBundleClassNamesMustHaveABundleAndClassName()
    {
        $this->resolver->resolve('@FixtureBundle');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unable to find class DoesNotExist
     */
    public function testResolverErrorsWhenTheClassIsNotFoundInAnyBundles()
    {
        $this->withBundle();

        $this->resolver->resolve('@FixtureBundle\\DoesNotExist');
    }

    public function testValidClassFoundInBundleReturnsResolvedClassName()
    {
        $this->withBundle();

        $class = $this->resolver->resolve('@FixtureBundle\\Loader');

        $this->assertEquals(__NAMESPACE__.'\\Fixtures\\Loader', $class);
    }

    protected function setUp()
    {
        $this->kernel = $this->getMock(KernelInterface::class);
        $this->resolver = new BundleAwareResolver($this->kernel);
    }

    private function withBundle()
    {
        $this->kernel->expects($this->once())
            ->method('getBundle')
            ->with('FixtureBundle', false)
            ->willReturn([
                new Fixtures\FixtureBundle()
            ]);
    }
}
