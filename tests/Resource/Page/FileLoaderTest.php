<?php

namespace Madapaja\TwigModule\Resource\Page;

use Madapaja\TwigModule\TwigFileLoaderTestModule;
use Madapaja\TwigModule\TwigRenderer;
use PHPUnit_Framework_TestCase;
use Ray\Di\Injector;

class FileLoaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Injector
     */
    private $injector;

    public function setUp()
    {
        $this->injector = new Injector(new TwigFileLoaderTestModule([$_ENV['TEST_DIR'] . '/Resource/']));
    }

    public function testRenderer()
    {
        $ro = $this->injector->getInstance(Index::class);
        $prop = (new \ReflectionClass($ro))->getProperty('renderer');
        $prop->setAccessible(true);

        $this->assertInstanceOf(TwigRenderer::class, $prop->getValue($ro));
    }

    public function testIndex()
    {
        $ro = $this->injector->getInstance(Index::class);

        $this->assertSame('Hello, BEAR.Sunday!', (string) $ro->onGet());
    }

    public function testIndexWithArg()
    {
        $ro = $this->injector->getInstance(Index::class);

        $this->assertSame('Hello, Madapaja!', (string) $ro->onGet('Madapaja'));
    }

    /**
     * @expectedException \Madapaja\TwigModule\Exception\TemplateNotFound
     */
    public function testTemplateNotFoundException()
    {
        $ro = $this->injector->getInstance(NoTemplate::class);
        $prop = (new \ReflectionClass($ro))->getProperty('renderer');
        $prop->setAccessible(true);
        $prop->getValue($ro)->render($ro);
    }

    public function testNoViewWhenCode301()
    {
        $ro = $this->injector->getInstance(NoTemplate::class);
        $ro->code = 303;
        $prop = (new \ReflectionClass($ro))->getProperty('renderer');
        $prop->setAccessible(true);
        $view = $prop->getValue($ro)->render($ro);
        $this->assertSame('', $view);
    }

    public function testPage()
    {
        $ro = $this->injector->getInstance(Page::class);

        $this->assertSame('<!DOCTYPE html><html><head><title>Page</title><body>Hello, BEAR.Sunday!</body></html>', (string) $ro->onGet());
    }

    public function testIndexTemplateWithoutPaths()
    {
        $injector = new Injector(new TwigFileLoaderTestModule([]));
        $ro = $injector->getInstance(Index::class);

        $this->assertSame('Hello, BEAR.Sunday!', (string) $ro->onGet());
    }
}
