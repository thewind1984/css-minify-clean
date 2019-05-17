# CSS optimizator

Interface accepts css files and folders / single templates files, then combines provided css files into one, compares it with provided templates, removes unused selectors and minifies final file.  
It supports twig templates also, because `{% %}`, `{{ }}` and `{# #}` will be deleted before check.

[![Latest Stable Version](https://poser.pugx.org/thewind1984/css-optimizator/v/stable.svg)](https://packagist.org/packages/thewind1984/css-optimizator)
[![GitHub license](https://img.shields.io/github/license/thewind1984/css-optimizator.svg)](https://github.com/thewind1984/css-optimizator/blob/master/LICENSE)

## Features
* Chains of CSS selectors like **>**, **~**
* Pseudo-classes (with optional sub-selectors)
* Multiple selectors separated with **, (comma)**

## TODO
* Parsing of chains of CSS selectors with **+** separator
  * `.class + .subclass` 
* Parsing of selectors, which are implemented with quatro brackets
  * `audio[controls]`
  * `[id*="xxx"]`, `[class^="yyy"]`, `[attr$="zzz"]`
* Implementation convertion to xPath for pseudo-classes
  * `nth-child`
* Combining of duplicated selectors
  * `body {margin; 0;} body {color: #000;}` => `body {margin: 0; color: #000;}`
* Unification / optimization the rules inside selector
  * `margin: 0; margin-bottom: 10px;` => `margin: 0 0 10px;`

## Installation through Composer

> composer require thewind1984/css-optimizator

## Usage

> require_once './vendor/autoload.php';  
> $cssOptimizator = new \CssOptimizator\CssOptimize();  
> $cssOptimizator->addCssFile('path/to/assets/file.css');  
> $cssOptimizator->addSourceFile('path/to/templates/page.html');  
> $cssOptimizator->optimize()->minify()->saveContent('path/to/assets/file.min.css');  
