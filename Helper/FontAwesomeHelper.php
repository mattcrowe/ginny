<?php namespace Foote\Ginny\Helper;

  /**
   * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
   *
   * (c) Matt Crowe <mattcrowe@zym.me>
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */

/**
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class FontAwesomeHelper
{
  public static $presets = [
    '+' => 'plus',
    '>>' => 'angle-double-right',
    'cancel' => 'times',
    'create' => 'plus',
    'destroy' => 'trash-o',
    'index' => 'list-ul',
    'list' => 'list-ul',
    'record' => 'asterisk',
    'show' => 'square-o',
  ];

  public static function icon($class, $content = '')
  {
    if (array_key_exists($class, self::$presets)) {
      $class = self::$presets[$class];
    }

    return sprintf('<i class="fa fa-%s">%s</i>', $class, $content);
  }

}