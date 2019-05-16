<?php

declare(strict_types=1);

namespace CssOptimizator\CssOptimize\CssMinificator;

class CssMinificator implements CssMinificatorInterface
{

    /**
     * @inheritdoc
     */
    public function minify(string $content): string
    {
        // remove comments
        $content = (string)preg_replace('/\/\*(?:(?!\*\/).)*\*\//s', '', $content);

        // remove leading spaces for each line
        $content = (string)preg_replace('/^[ ]+/m', '', $content);

        // remove around spaces near special symbols
        $content = (string)preg_replace('/[ ]*([\{\>\~]+)[ ]*/', '\1', $content);

        // remove line breaks
        $content = str_replace(["\r\n", "\n"], '', $content);

        // replace multiple spaces with single space
        $content = (string)preg_replace('/[ ]+/', ' ', $content);

        // replace extra staff for minification purposes
        $content = str_replace([
            '} {',
            ';}',
            '@import url(',
            ' 0px',
            '; ',
            ': ',
            ', ',
        ], [
            '}{',
            '}',
            PHP_EOL . '@import url(',
            ' 0',
            ';',
            ':',
            ',',
        ], $content);

        // final trimming
        return trim($content);
    }
}
