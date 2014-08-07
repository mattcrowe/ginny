<?php namespace Foote\Ginny\Twig;
/**
 * This file is part of the Ginny package: https://github.com/crowefoote/ginny
 *
 * (c) Matt Crowe <crowefoote@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ICanBoogie\Inflector;

/**
 * @author Matt Crowe <crowefoote@zym.me>
 */
class InflectorExtension extends \Twig_Extension
{

    public function getName()
    {
        return 'inflector_extension';
    }

    public function getFilters()
    {
        return array(
            // ie, "ABC" -> "abc"
            new \Twig_SimpleFilter('low', function ($key) {
                    return strtolower($key);
                },
                ['is_safe' => ['html']]
            ),
            // ie, "abc" -> "ABC"
            new \Twig_SimpleFilter('up', function ($key) {
                    return strtoupper($key);
                },
                ['is_safe' => ['html']]
            ),
            // ie, "things" -> "thing"
            new \Twig_SimpleFilter('singularize', function ($key) {
                    return Inflector::get()->singularize($key);
                },
                ['is_safe' => ['html']]
            ),
            // ie, "thing" -> "things"
            new \Twig_SimpleFilter('pluralize', function ($key) {
                    return Inflector::get()->pluralize($key);
                },
                ['is_safe' => ['html']]
            ),
            // ie, "one_big_thing" -> "OneBigThing"
            new \Twig_SimpleFilter('camelize', function ($key) {
                    return Inflector::get()->camelize($key);
                },
                ['is_safe' => ['html']]
            ),
            // ie, "one_big_thing" -> "oneBigThing"
            new \Twig_SimpleFilter('variableize', function ($key) {
                    return Inflector::get()->camelize($key, true);
                },
                ['is_safe' => ['html']]
            ),
            // ie, "oneBigThing" -> "one_big_thing"
            new \Twig_SimpleFilter('underscore', function ($key) {
                    return Inflector::get()->underscore($key);
                },
                ['is_safe' => ['html']]
            ),
            // ie, "one_big_thing" -> "One big thing"
            new \Twig_SimpleFilter('humanize', function ($key) {
                    return Inflector::get()->humanize($key);
                },
                ['is_safe' => ['html']]
            ),
        );
    }

}