<?php

require 'vendor/autoload.php';

class ContainerTest extends PHPUnit_Framework_TestCase
{
    private $container = null;

    public function setUp()
    {
        $this->container = new LittleNinja\Container();
    }

    public function testBindingObject()
    {
        $this->container->bind('foo', 'Bar');

        $this->assertEquals('Bar', $this->container->getBinding('foo')['value']);
    }

    public function testReturnsNullWhenBindingNotFound()
    {
        $this->assertNull($this->container->getBinding('bar'));
    }

    public function testResolveClassReturnsObject()
    {
        $object = app('bar');

        $this->assertInstanceOf('Bar', $object);
    }

    public function testArrayAccessWorks()
    {
        $this->container['qux'] = 'Bar';

        $object = $this->container['qux'];

        $this->assertInstanceOf('Bar', $object);
    }
}

class Foo
{
}

class Bar
{
    public function __construct(Foo $foo)
    {
    }
}
