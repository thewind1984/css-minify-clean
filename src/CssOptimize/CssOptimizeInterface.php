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
    public function getContent(): string;

    /**
     * Saves current css content to specified file.
     *
     * @param string $filePath
     *
     * @return CssOptimizeInterface
     */
    public function saveContent(string $filePath): self;

    /**
     * Returns length of current css content.
     *
     * @return int
     */
    public function getContentLength(): int;
}
