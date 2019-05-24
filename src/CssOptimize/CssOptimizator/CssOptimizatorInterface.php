<?php

declare(strict_types=1);

namespace CssOptimizator\CssOptimize\CssOptimizator;

interface CssOptimizatorInterface
{
    /**
     * @param string $cssContent
     * @param string $sourceContent
     *
     * @return string
     */
    public function optimize(string $cssContent, string $sourceContent): string;

    /**
     * @return int
     */
    public function getXPathErrorCount(): int;

    /**
     * @return int
     */
    public function getXPathSuccessCount(): int;

    /**
     * @return int
     */
    public function getSelectorsRemoved(): int;

    /**
     * @return int
     */
    public function getSelectorsProcessed(): int;
}
