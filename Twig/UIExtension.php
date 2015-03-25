<?php namespace Foote\Ginny\Twig;

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
class UIExtension extends \Twig_Extension
{

  public function getName()
  {
    return 'ui_extension';
  }


  public function getFilters()
  {
    return [
      /**
       * @see \Foote\Ginny\Helper\BladeHelper::blade
       */
      new \Twig_SimpleFilter('blade',
        ['\Foote\Ginny\Helper\BladeHelper', 'blade'],
        ['is_safe' => ['html']]
      ),
    ];
  }

  public function getFunctions()
  {
    return [
      /**
       * @see \Foote\Ginny\Helper\FontAwesomeHelper::icon
       */
      new \Twig_SimpleFunction('i',
        ['\Foote\Ginny\Helper\FontAwesomeHelper', 'icon'],
        ['is_safe' => ['html']]
      ),
      /**
       * @see \Foote\Ginny\Helper\BladeHelper::url
       */
      new \Twig_SimpleFunction('blade_url',
        ['\Foote\Ginny\Helper\BladeHelper', 'url'],
        ['is_safe' => ['html']]
      ),
    ];
  }

}