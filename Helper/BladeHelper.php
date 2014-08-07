<?php namespace Foote\Ginny\Helper;
/**
 * This file is part of the Ginny package: https://github.com/crowefoote/ginny
 *
 * (c) Matt Crowe <crowefoote@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Foote\Ginny\Map\BaseItem;

/**
 * Blade is a template engine used by the Laravel framework.
 *
 * This class' methods are used to create some common snippets to insert
 * into generated Blade templates.
 *
 * @see \Foote\Ginny\Package\Foote\Twig\FooteExtension
 * @author Matt Crowe <crowefoote@zym.me>
 */
class BladeHelper
{

    /**
     * creates snippet for Laravel's URL class
     *
     * @param $model BaseItem
     * @param string $type
     * @param bool $bracketed
     * @return string
     */
    public static function url(BaseItem $model, $type = 'index', $bracketed = true)
    {

        if (in_array($type, ['show', 'edit', 'update', 'destroy'])) {

            $pattern = "URL::route('%s.%s', $%s->id)";
            if ($bracketed) {
                $pattern = "{{ URL::route('%s.%s', $%s->id) }}";
            }

            return sprintf($pattern, $model->route, $type, $model->variable);
        }

        $pattern = "URL::route('%s.%s')";
        if ($bracketed) {
            $pattern = "{{ URL::route('%s.%s') }}";
        }

        return sprintf($pattern, $model->route, $type);
    }

    /**
     * creates snippet for standalone object, ie. {{ $item }}
     *
     * @param $item BaseItem
     * @param bool $owner
     * @return string
     */

    public static function blade(BaseItem $item, $owner = false)
    {
        if ($owner && $item->owner) {
            return sprintf('{{ $%s->%s }}', $item->variable(), $item->owner->variable());
        }
        return sprintf('{{ $%s }}', $item->variable());
    }

}