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
}
