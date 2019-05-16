<?php

declare(strict_types=1);

namespace CssOptimizator\CssOptimize;

use CssOptimizator\CssOptimize\CssMinificator\CssMinificator;
use CssOptimizator\CssOptimize\CssOptimizator\CssOptimizator;
use Symfony\Component\Finder\Finder;

/**
 * CssOptimize allows to clean-up and minify css files accordingly to specified templates
 *
 * @author Dmitriy Ignatiev <thewind05@gmail.com>
 * @license MIT
 * @copyright 2019
 * @version 1.0
 */
class CssOptimize implements CssOptimizeInterface
{
    /**
     * @var Finder
     */
    private $finder;

    /**
     * @var array
     */
    private $cssFiles = [];

    /**
     * @var array
     */
    private $sourceFiles = [];

    /**
     * @var string
     */
    private $content = '';

    public function __construct()
    {
        $this->finder = new Finder();
    }

    /**
     * @inheritdoc
     */
    public function addCssFile(string $filePath): CssOptimizeInterface
    {
        $this->cssFiles[] = $filePath;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addSourceFolder(string $dirPath, string $filePathFilter = null): CssOptimizeInterface
    {
        $filesList = $this->finder->files()->in($dirPath)->name($filePathFilter ?? []);

        foreach ($filesList as $file) {
            $this->sourceFiles[] = $file->getRealPath();
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addSourceFile(string $filePath): CssOptimizeInterface
    {
        $this->sourceFiles[] = $filePath;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function optimize(): CssOptimizeInterface
    {
        if (!count($this->cssFiles)) {
            return $this;
        }

        if (!count($this->sourceFiles)) {
            $this->content = '';

            return $this;
        }

        $cssContent = $sourceContent = '';

        array_walk($this->cssFiles, function (string $filePath) use (&$cssContent) {
            if (file_exists($filePath) && is_file($filePath)) {
                $cssContent .= file_get_contents($filePath);
            }
        });

        array_walk($this->sourceFiles, function (string $filePath) use (&$sourceContent) {
            if (file_exists($filePath) && is_file($filePath)) {
                $sourceContent .= file_get_contents($filePath);
            }
        });

        if (trim($cssContent) !== '') {
            $this->content = (new CssOptimizator())->optimize($cssContent, $sourceContent);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function minify(): CssOptimizeInterface
    {
        $this->content = (new CssMinificator())->minify($this->content);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @inheritdoc
     */
    public function saveContent(string $filePath): CssOptimizeInterface
    {
        file_put_contents($filePath, $this->content);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getContentLength(): int
    {
        return mb_strlen($this->content);
    }
}
