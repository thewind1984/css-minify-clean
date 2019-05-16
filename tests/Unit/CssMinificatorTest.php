<?php

declare(strict_types=1);

namespace CssOptimizator\Tests\Unit;

use CssOptimizator\CssOptimize\CssMinificator\CssMinificator;
use CssOptimizator\CssOptimize\CssMinificator\CssMinificatorInterface;
use PHPUnit\Framework\TestCase;

final class CssMinificatorTest extends TestCase
{
    /**
     * @var CssMinificatorInterface
     */
    private $cssMinificator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cssMinificator = new CssMinificator();
    }

    /**
     * @test
     * @dataProvider minifyCssCases
     *
     * @param string $plainCss
     * @param string $minifiedCss
     */
    public function minifyTest(string $plainCss, string $minifiedCss): void
    {
        $this->assertSame($minifiedCss, $this->cssMinificator->minify($plainCss));
    }

    /**
     * @return \Generator
     */
    public function minifyCssCases(): \Generator
    {
        yield ['.class {color: red;}', '.class{color:red}'];
        yield ['/* comment */' . PHP_EOL . ' #id > .class {display: block; margin: 0px;}', '#id>.class{display:block;margin:0}'];
        yield ['.class1,  .class1-extra {font-size:  0px;}' . PHP_EOL . '.class2 {color: red;} ', '.class1,.class1-extra{font-size:0}.class2{color:red}'];
    }
}
