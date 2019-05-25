<?php

declare(strict_types=1);

namespace CssOptimizator\Tests\Unit;

use CssOptimizator\CssOptimize\CssOptimizator\CssOptimizator;
use CssOptimizator\CssOptimize\CssOptimizator\CssOptimizatorInterface;
use PHPUnit\Framework\TestCase;

final class CssOptimizatorTest extends TestCase
{
    /**
     * @var CssOptimizatorInterface
     */
    private $cssOptimizator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cssOptimizator = new CssOptimizator();
    }

    /**
     * @test
     * @dataProvider optimizeCssCasesContains
     *
     * @param string $cssContent
     * @param string $htmlContainedSelector
     * @param string $htmlNotContainedSelector
     * @param int $expectedSelectorsProcessed
     * @param int $expectedSelectorsRemoved
     */
    public function optimizeTestAboutHtmlEitherContainsOrNotAppropriateTags(
        string $cssContent,
        string $htmlContainedSelector,
        string $htmlNotContainedSelector,
        int $expectedSelectorsProcessed,
        int $expectedSelectorsRemoved
    ): void {
        $this->assertSame($cssContent, $this->cssOptimizator->optimize($cssContent, $htmlContainedSelector));
        $this->assertSame($expectedSelectorsProcessed, $this->cssOptimizator->getSelectorsProcessed());

        $this->assertSame('', $this->cssOptimizator->optimize($cssContent, $htmlNotContainedSelector));
        $this->assertSame($expectedSelectorsProcessed, $this->cssOptimizator->getSelectorsProcessed());

        $this->assertSame($expectedSelectorsRemoved, $this->cssOptimizator->getSelectorsRemoved());
    }

    /**
     * @test
     * @dataProvider optimizeCssCasesExtra
     *
     * @param string $cssContent
     * @param string $expectedCssContent
     * @param string $htmlContent
     */
    public function optimizeTestWhenCssSelectorIsRemoved(string $cssContent, string $expectedCssContent, string $htmlContent): void
    {
        $this->assertSame($expectedCssContent, $this->cssOptimizator->optimize($cssContent, $htmlContent));
    }

    /**
     * @return \Generator
     */
    public function optimizeCssCasesContains(): \Generator
    {
        yield ['.class{k:v}', '<p class="class"></p>', '<p class="class2"></p>', 1, 1];
        yield ['#id{k:v}', '<p id="id"></p>', '<p id="dida"></p>', 1, 1];
        yield ['.class #id {k:v}', '<p class="class"><span id="id"></span></p>', '<p class="class"><span id="id1"></span></p>', 1, 1];
        yield ['#id > ul li {k:v}', '<div id="id"><ul><li></li></ul>', '<div id="id"><div><ul><li></li></ul></div></div>', 1, 1];
        yield ['.class#with-id > div ~ p.sub_class {k:v}', '<div class="class" id="with-id"><div></div><p class="sub_class"></p></div>', '<div class="class" id="with-id"><div><p class="sub_class"></p></div></div>', 1, 1];
        yield ['b:first-child {k:v}', '<div><b></b></div>', '<p><a></a></p>', 1, 1];
        yield ['b:last-child {k:v}', '<div><b></b></div>', '<p><a></a></p>', 1, 1];
        yield ['a:not(.dd) {k:v}', '<a class="dd1"></a>', '<a class="dd"></a>', 1, 1];
        yield ['a:not(.dd#qq) {k:v}', '<a class="dd1" id="qq"></a>', '<a class="dd" id="qq"></a>', 1, 1];
        yield ['a, b {k:v}', '<b>2</b>', '<p>1</p>', 1, 1];
        yield ['a, p>b {k:v;}', '<a>1</a>', '<p><span><b>22</b></span></p>', 1, 1];
        yield ['a,' . PHP_EOL . 'p>b {k:v;}', '<a>1</a>', '<p><span><b>22</b></span></p>', 1, 1];
    }

    /**
     * @return \Generator
     */
    public function optimizeCssCasesExtra(): \Generator
    {
        yield ['.class { }', '', '<body></body>'];
    }
}
