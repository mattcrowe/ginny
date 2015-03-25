<?php namespace Foote\Ginny\Tests\Command;

/**
 * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
 *
 * (c) Matt Crowe <mattcrowe@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Foote\Ginny\Twig\UIExtension;

/**
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class UIExtensionTest extends \PHPUnit_Framework_TestCase
{

  /**
   * covers \Foote\Ginny\Twig\UIExtension::getName
   * covers \Foote\Ginny\Twig\UIExtension::getFilters
   * covers \Foote\Ginny\Twig\UIExtension::getFunctions
   */
  public function testui()
  {

    $extension = new UIExtension();

    $this->assertEquals($extension->getName(), 'ui_extension');

    /**
     * @var $filter \Twig_SimpleFilter
     */
    foreach ($extension->getFilters() as $filter) {

      $callable = $filter->getCallable();

      if ($filter->getName() == 'blade') {

        $user = \Foote\Ginny\Map\BaseModel::create('User');

        $this->assertEquals($callable($user), '{{ $user }}');
      };
    }

    /**
     * @var $filter \Twig_SimpleFunction
     */
    foreach ($extension->getFunctions() as $filter) {

      $callable = $filter->getCallable();

      if ($filter->getName() == 'blade_url') {
        $user = \Foote\Ginny\Map\BaseModel::create('User');
        $user->route = 'admin.system.users';
        $this->assertEquals($callable($user),
          "{{ URL::route('admin.system.users.index') }}");
      };

      if ($filter->getName() == 'i') {
        $this->assertEquals($callable('create'), '<i class="fa fa-plus"></i>');
      };
    }
  }

}