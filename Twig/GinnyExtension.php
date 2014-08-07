<?php namespace Foote\Ginny\Twig;
/**
 * This file is part of the Ginny package: https://github.com/crowefoote/ginny
 *
 * (c) Matt Crowe <crowefoote@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Part of my feeble attempt to make the generated code look tidy.
 *
 * This helps a little.
 *
 * @author Matt Crowe <crowefoote@zym.me>
 */
class GinnyExtension extends \Twig_Extension
{

    public function getName()
    {
        return 'ginny_extension';
    }

    public function getFilters()
    {
        return array(
            // ie, "A B" -> "AB"
            new \Twig_SimpleFilter('spaceless', function ($data) {
                    return preg_replace('/\s+/', '', $data);
                },
                ['is_safe' => ['html']]
            ),
            // ie, "A\nB" -> "A B"
            new \Twig_SimpleFilter('inline', function ($data) {
                    return preg_replace('/\s+/', ' ', $data);
                },
                ['is_safe' => ['html']]
            ),
        );
    }

}