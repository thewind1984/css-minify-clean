<?php

declare(strict_types=1);

namespace CssOptimizator\CssOptimize;

interface CssOptimizeInterface
{
    /**
     * Adds new css file by its path to analyze scope.
     *
     * @param string $filePath
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
     * @return CssOptimizeInterface
     */
    public function addSourceFolder(string $dirPath, string $filePathFilter = null): self;

    /**
     * Adds specified source file.
     *
     * @param string $filePath
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
     * @return string
     */
    public function getCssContent(): string;

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
}
