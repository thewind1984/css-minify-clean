<?php

declare(strict_types=1);

namespace CssOptimizator\CssOptimize\CssMinificator;

interface CssMinificatorInterface
{
    /**
     * @param string $content
     *
     * @return string
     */
    public function minify(string $content): string;
}
