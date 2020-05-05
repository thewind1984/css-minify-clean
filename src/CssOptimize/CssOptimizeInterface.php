<?php

declare(strict_types=1);

namespace CssOptimizator\CssOptimize;

use CssOptimizator\CssOptimize\Exceptions\CssFileNotFoundException;
use CssOptimizator\CssOptimize\Exceptions\FileIsNotReadableException;
use CssOptimizator\CssOptimize\Exceptions\SourceFileNotFoundException;

interface CssOptimizeInterface
{
    /**
     * Adds new css file by its path to analyze scope.
     *
     * @param string $filePath
     *
     * @throws CssFileNotFoundException
     * @throws FileIsNotReadableException
     *
     * @return CssOptimizeInterface
     */
    public function addCssFile(string $filePath): self;

    /**
     * Adds css content (from text) to analyze scope.
     *
     * @param string $content
     *
     * @return CssOptimizeInterface
     */
    public function addCssContent(string $content): self;

    /**
     * Adds new source folder with optional filter for files.
     *
     * @param string $dirPath
     * @param string|null $filePathFilter
     *
     * @throws SourceFileNotFoundException
     * @throws FileIsNotReadableException
     *
     * @return CssOptimizeInterface
     */
    public function addSourceFolder(string $dirPath, string $filePathFilter = null): self;

    /**
     * Adds specified source file.
     *
     * @param string $filePath
     *
     * @throws SourceFileNotFoundException
     * @throws FileIsNotReadableException
     *
     * @return CssOptimizeInterface
     */
    public function addSourceFile(string $filePath): self;

    /**
     * Adds source (html) content (from text).
     *
     * @param string $content
     *
     * @return CssOptimizeInterface
     */
    public function addSourceContent(string $content): self;

    /**
     * Makes optimization of scope of css files according to source files.
     *
     * @return CssOptimizeInterface
     */
    public function optimize(): self;

    /**
     * Makes minification of css content.
     *
     * @return CssOptimizeInterface
     */
    public function minify(): self;

    /**
     * Returns current css content.
     *
     * @param bool $clean
     *
     * @return string
     */
    public function getCssContent(bool $clean = false): string;

    /**
     * Returns current source.
     *
     * @return string
     */
    public function getSourceContent(): string;

    /**
     * Saves current css content to specified file.
     *
     * @param string $filePath
     *
     * @return CssOptimizeInterface
     */
    public function saveCssContent(string $filePath): self;

    /**
     * Returns length of current css content.
     *
     * @return int
     */
    public function getCssContentLength(): int;

    /**
     * @return \stdClass
     */
    public function getOptimizationStats(): \stdClass;
}
