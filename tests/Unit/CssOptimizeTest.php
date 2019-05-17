<?php

declare(strict_types=1);

namespace CssOptimizator\Tests\Unit;

use CssOptimizator\CssOptimize\CssOptimize;
use CssOptimizator\CssOptimize\CssOptimizeInterface;
use CssOptimizator\CssOptimize\Exceptions\CssFileNotFoundException;
use CssOptimizator\CssOptimize\Exceptions\SourceFileNotFoundException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

class CssOptimizeTest extends TestCase
{
    /**
     * @var CssOptimizeInterface
     */
    private $cssOptimizeReal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cssOptimizeReal = new CssOptimize();
    }

    /**
     * @test
     */
    public function addCssFileWillAddContentWithoutAnyChanges(): void
    {
        $filePath = __DIR__ . '/../data/css/test.css';
        $this->cssOptimizeReal->addCssFile($filePath);
        $this->assertSame(file_get_contents($filePath), $this->cssOptimizeReal->getCssContent());
    }

    /**
     * @test
     */
    public function addCssFileWillThrowExceptionDueToWrongFilePath(): void
    {
        $filePath = __DIR__ . '/test.css';
        $this->expectExceptionObject(new CssFileNotFoundException(sprintf('CSS file not found (%s)', $filePath)));
        $this->cssOptimizeReal->addCssFile($filePath);
    }

    /**
     * @test
     */
    public function addCssContentTest(): void
    {
        $content = 'p{margin:0;}';
        $this->cssOptimizeReal->addCssContent($content);
        $this->assertSame($content, $this->cssOptimizeReal->getCssContent());
    }

    /**
     * @test
     */
    public function addSourceFileWillAddContentWithoutAnyChanges(): void
    {
        $filePath = __DIR__ . '/../data/templates/page.html';
        $this->cssOptimizeReal->addSourceFile($filePath);
        $this->assertSame(file_get_contents($filePath), $this->cssOptimizeReal->getSourceContent());
    }

    /**
     * @test
     */
    public function addSourceFileWillThrowExceptionDueToWrongFilePath(): void
    {
        $filePath = __DIR__ . '/page.html';
        $this->expectExceptionObject(new SourceFileNotFoundException(sprintf('Source file not found (%s)', $filePath)));
        $this->cssOptimizeReal->addSourceFile($filePath);
    }

    /**
     * @test
     */
    public function addSourceFolderWillAddAllFilesWithoutAnyChanges(): void
    {
        $dirPath = __DIR__ . '/../data/templates';
        $this->cssOptimizeReal->addSourceFolder($dirPath);

        $files = (new Finder())->in($dirPath)->files();
        $expectedContent = '';

        foreach ($files as $file) {
            $expectedContent .= file_get_contents($file->getRealPath());
        }

        $this->assertSame($expectedContent, $this->cssOptimizeReal->getSourceContent());
    }

    /**
     * @test
     */
    public function addSourceFolderWillAddFilesByMaskWithoutAnyChanges(): void
    {
        $dirPath = __DIR__ . '/../data/templates';
        $this->cssOptimizeReal->addSourceFolder($dirPath, 'html.twig');

        $files = (new Finder())->in($dirPath)->name('html.twig')->files();
        $expectedContent = '';

        foreach ($files as $file) {
            $expectedContent .= file_get_contents($file->getRealPath());
        }

        $this->assertSame($expectedContent, $this->cssOptimizeReal->getSourceContent());
    }

    /**
     * @test
     */
    public function addSourceContentTest(): void
    {
        $content = '<div class="cl"><p></p></div>';
        $this->cssOptimizeReal->addSourceContent($content);
        $this->assertSame($content, $this->cssOptimizeReal->getSourceContent());
    }

    /**
     * @test
     * @dataProvider localizedCssContent
     *
     * @param string $cssContent
     * @param int $expectedLength
     */
    public function getCssContentLength(string $cssContent, int $expectedLength): void
    {
        $this->cssOptimizeReal->addCssContent($cssContent);
        $this->assertSame($expectedLength, $this->cssOptimizeReal->getCssContentLength());
    }

    public function localizedCssContent(): \Generator
    {
        yield ['p{color:red;}', 13];
        yield ['p{color:красный;}', 17];
    }
}
