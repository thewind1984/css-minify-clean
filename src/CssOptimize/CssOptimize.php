<?php

declare(strict_types=1);

namespace CssOptimizator\CssOptimize;

use CssOptimizator\CssOptimize\CssMinificator\CssMinificator;
use CssOptimizator\CssOptimize\CssOptimizator\CssOptimizator;
use CssOptimizator\CssOptimize\Exceptions\CssFileNotFoundException;
use CssOptimizator\CssOptimize\Exceptions\FileIsNotReadableException;
use CssOptimizator\CssOptimize\Exceptions\SourceFileNotFoundException;
use Symfony\Component\Finder\Finder;

/**
 * CssOptimize allows to clean-up and minify css files accordingly to specified templates
 *
 * @author Dmitriy Ignatiev <thewind05@gmail.com>
 * @license MIT
 */
class CssOptimize implements CssOptimizeInterface
{
    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var CssMinificator
     */
    private $cssMinificator;

    /**
     * @var CssOptimizator
     */
    private $cssOptimizator;

    /**
     * @var string
     */
    private $cssContent = '';

    /**
     * @var string
     */
    private $sourceContent = '';

    public function __construct() {
        $this->finder = new Finder();
        $this->cssMinificator = new CssMinificator();
        $this->cssOptimizator = new CssOptimizator();
    }

    /**
     * @inheritdoc
     */
    public function addCssFile(string $filePath): CssOptimizeInterface
    {
        if (!file_exists($filePath)) {
            throw new CssFileNotFoundException(sprintf('CSS file not found (%s)', $filePath));
        }

        $content = file_get_contents($filePath);

        if ($content === false) {
            throw new FileIsNotReadableException($filePath);
        }

        return $this->addCssContent($content);
    }

    /**
     * @inheritdoc
     */
    public function addCssContent(string $content): CssOptimizeInterface
    {
        $this->cssContent .= $content;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addSourceFolder(string $dirPath, string $filePathFilter = null): CssOptimizeInterface
    {
        $filesList = $this->finder->files()->in($dirPath)->name($filePathFilter ?? []);

        foreach ($filesList as $file) {
            $filePath = $file->getRealPath();

            if (!is_string($filePath)) {
                throw new SourceFileNotFoundException();
            }

            $content = file_get_contents($filePath);

            if ($content === false) {
                throw new FileIsNotReadableException($filePath);
            }

            $this->addSourceContent($content);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addSourceFile(string $filePath): CssOptimizeInterface
    {
        if (!file_exists($filePath)) {
            throw new SourceFileNotFoundException(sprintf('Source file not found (%s)', $filePath));
        }

        $content = file_get_contents($filePath);

        if ($content === false) {
            throw new FileIsNotReadableException($filePath);
        }

        return $this->addSourceContent($content);
    }

    /**
     * @inheritdoc
     */
    public function addSourceContent(string $content): CssOptimizeInterface
    {
        $this->sourceContent .= $content;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function optimize(): CssOptimizeInterface
    {
        if (!trim($this->cssContent)) {
            return $this;
        }

        if (!trim($this->sourceContent)) {
            $this->cssContent = '';

            return $this;
        }

        $this->cssContent = $this->cssOptimizator->optimize($this->cssContent, $this->sourceContent);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function minify(): CssOptimizeInterface
    {
        $this->cssContent = $this->cssMinificator->minify($this->cssContent);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCssContent(): string
    {
        return $this->cssContent;
    }

    /**
     * @inheritdoc
     */
    public function getSourceContent(): string
    {
        return $this->sourceContent;
    }

    /**
     * @inheritdoc
     */
    public function saveCssContent(string $filePath): CssOptimizeInterface
    {
        file_put_contents($filePath, $this->cssContent);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCssContentLength(): int
    {
        return mb_strlen($this->cssContent);
    }

    /**
     * @inheritdoc
     */
    public function getOptimizationStats(): \stdClass
    {
        $result = new \stdClass();
        $result->processed = $this->cssOptimizator->getSelectorsProcessed();
        $result->removed = $this->cssOptimizator->getSelectorsRemoved();

        return $result;
    }
}
