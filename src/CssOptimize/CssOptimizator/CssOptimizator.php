<?php

declare(strict_types=1);

namespace CssOptimizator\CssOptimize\CssOptimizator;

/**
 * CssOptimizator allows to remove from css scope unused selectors accordingly to specified html content.
 *
 * Known issue:
 *  - ':not(.class)' selector does not work properly without tagname
 *  - '*.class' selector does not work properly with * instead of letters
 *
 * @author Dmitriy Ignatiev <thewind05@gmail.com>
 * @license MIT
 */
class CssOptimizator implements CssOptimizatorInterface
{
    private const LEVEL_TOP = '/';
    private const LEVEL_ANY = '//';
    private const LEVEL_SAME = '/../';
    private const XPATH_OR = '|';

    private $xPathErrorCount = 0;
    private $xPathSuccessCount = 0;
    private $selectorsRemoved = 0;
    private $selectorsProcessed = 0;

    /**
     * @inheritdoc
     */
    public function optimize(string $cssContent, string $sourceContent): string
    {
        preg_match_all('/(?<selector>[^\{]{1,})[\,\{]{1}(?<content>[^\}]*)\}/i', (string)preg_replace('/\/\*(?:(?!\*\/).)*\*\//s', '', $cssContent), $selectors);

        $this->selectorsProcessed = count($selectors[0]);

        /**
         * Remove all constructions like:
         * - {% ... %}
         * - {{ ... }}
         * - {# ... #}
         */
        $sourceContent = (string)preg_replace('/\{([\%\#\{]{1})(?:(?![\%\}]{1}\}).)*(?:\1|\})[\}]?/s', '', $sourceContent);

        $doc = new \DOMDocument();
        $errors = libxml_use_internal_errors(true);
        $doc->loadHTML($sourceContent);
        libxml_use_internal_errors($errors);
        $xpath = new \DOMXPath($doc);

        foreach ($selectors[0] as $selectorNum => $selector) {
            $cssSelector = trim(str_replace(PHP_EOL, '', $selectors['selector'][$selectorNum]));
            $xPathQuery = $this->buildXpathQueryFromSelector($cssSelector);

            //echo $cssSelector . ' => ' . $xPathQuery . PHP_EOL;

            $selectorFound = true;

            if (!trim($selectors['content'][$selectorNum])) {
                $selectorFound = false;
            } else {
                try {
                    $xPathResult = $xpath->query($xPathQuery);
                    if ($xPathResult instanceof \DOMNodeList) {
                        $selectorCount = $xPathResult->count();
                        $selectorFound = (bool)$selectorCount;
                        $this->xPathSuccessCount++;
                    }
                } catch (\Exception $e) {
                    $this->xPathErrorCount++;
                }
            }

            if ($selectorFound === false) {
                $cssSelector = (string)preg_replace('/[\(\)\[\]]+/', '\\\\\0', $selectors['selector'][$selectorNum]);
                $cssContent = (string)preg_replace('/(^| |\}?)' . $cssSelector . '[^\w\{]*\{[^\}]*\}/is', '', $cssContent);
                $this->selectorsRemoved++;
            }
        }

        return $cssContent;
    }

    /**
     * @inheritdoc
     */
    public function getXPathErrorCount(): int
    {
        return $this->xPathErrorCount;
    }

    /**
     * @inheritdoc
     */
    public function getXPathSuccessCount(): int
    {
        return $this->xPathSuccessCount;
    }

    /**
     * @inheritdoc
     */
    public function getSelectorsRemoved(): int
    {
        return $this->selectorsRemoved;
    }

    /**
     * @inheritdoc
     */
    public function getSelectorsProcessed(): int
    {
        return $this->selectorsProcessed;
    }

    /**
     * @TODO: implement parsing selectors like:
     *  - [controls]
     *  - [id*="xxx"]
     *  - [class^="yyy"], [attr$="zzz"]
     *
     * @param string $selector
     * @param int $level
     *
     * @return string
     */
    private function buildXpathQueryFromSelector(string $selector, int $level = 0): string
    {
        $query = [];

        $selector = trim($selector);

        $selector = (string)preg_replace('/[\>\~\+\,]+/', ' \\0 ', $selector);
        $selector = (string)preg_replace('/[ ]+/', ' ', $selector);

        $selector = explode(' ', $selector);

        $openBracket = $level === 0 ? '[' : '';
        $closeBracket = $level === 0 ? ']' : '';

        foreach ($selector as $partNum => $part) {
            if (!trim($part)) {
                continue 1;
            }

            if ($part === '>') {
                $query[] = self::LEVEL_TOP;
                continue 1;
            }

            if ($part === '~') {
                $query[] = self::LEVEL_SAME;
                continue 1;
            }

            if ($part === '+') {
                // TODO: implement + operator
            }

            if ($part === ',') {
                $query[] = self::XPATH_OR;
                continue 1;
            }

            if ($level === 0 && ($partNum === 0 || !in_array(end($query), [self::LEVEL_TOP, self::LEVEL_SAME], true))) {
                $query[] = self::LEVEL_ANY;
            }

            /**
             * Parse every selector into parts:
             * - tag (could be empty, if selector starts with . or # symbol)
             * - typeAndValue (sequences of (. or #) and (letter, numbers, dash, underscore))
             * - pseudo (optional pseudo class)
             * - pseudoContent (optional content of pseudo class)
             */
            preg_match_all('/^(?<tag>[a-z]*)(?<typeandvalue>[a-z0-9\.\#\-\_]+)*[\:]?(?<pseudo>[a-z\-]*)(?:[\(]?(?<pseudocontent>[^\)]*)[\)]?)$/im', $part, $subParts);

            if (!count($subParts[0])) {
                $query[] = $part;
                continue 1;
            }

            foreach ($subParts[0] as $subPartNum => $subPart) {
                $hasClassOrId = isset($subParts['typeandvalue'][$subPartNum]) && $subParts['typeandvalue'][$subPartNum] !== '';

                if ($subPartNum === 0) {
                    $query[] = $subParts['tag'][$subPartNum] === '' && $level === 0 && $hasClassOrId ? '*' : $subParts['tag'][$subPartNum];
                }

                if ($hasClassOrId) {
                    preg_match_all('/(?<type>[\.\#]{1})(?<value>[^\.\#]+)/i', $subParts['typeandvalue'][$subPartNum], $typeValues);

                    foreach ($typeValues[0] as $typeValueNum => $typeValue) {
                        $typeValueType = $typeValues['type'][$typeValueNum];
                        $typeValueValue = trim($typeValues['value'][$typeValueNum]);

                        if ($typeValueNum > 0 && $level > 0) {
                            $query[] = ' and ';
                        }

                        if ($typeValueType === '#') {
                            $query[] = $openBracket . '@id="' . $typeValueValue . '"' . $closeBracket;
                        } else if ($typeValueType === '.') {
                            $query[] = $openBracket . 'contains(concat(" ", @class, " "), " ' . $typeValueValue . ' ")' . $closeBracket;
                        }
                    }
                }

                $selectorPseudo = $subParts['pseudo'][$subPartNum];
                $selectorPseudoContent = $subParts['pseudocontent'][$subPartNum];

                if ($selectorPseudo === '') {
                    continue 1;
                }

                switch ($selectorPseudo) {
                    case 'not':
                        $query[] = sprintf('[not%s]', $selectorPseudoContent !== ''
                            ? sprintf('(%s)', $this->buildXpathQueryFromSelector($selectorPseudoContent, $level + 1))
                            : ''
                        );
                        break;

                    case 'first-child':
                        $query[] = $openBracket . '1' . $closeBracket;
                        break;

                    case 'last-child':
                        $query[] = $openBracket . 'last()' . $closeBracket;
                        break;

                    case 'before':
                    case 'after':
                        break;

                    // @TODO: implement nth-child
                    case 'nth-child':
                        break;
                }
            }
        }

        return implode('', $query);
    }
}
