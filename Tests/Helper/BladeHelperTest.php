<?php namespace Foote\Ginny\Tests\Command;

/**
 * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
 *
 * (c) Matt Crowe <mattcrowe@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Foote\Ginny\Map\BaseItem;
use Foote\Ginny\Helper\BladeHelper;

/**
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class BladeHelperTest extends \PHPUnit_Framework_TestCase
{

  /**
   * @covers \Foote\Ginny\Helper\BladeHelper::url
   */
  public function testurl()
  {

    $model = new BaseItem('test');
    $model->route = 'admin.system.tests';

    $helper = new BladeHelper();

    $this->assertEquals(
      $helper->url($model),
      "{{ URL::route('admin.system.tests.index') }}");

    $this->assertEquals(
      $helper->url($model, 'create'),
      "{{ URL::route('admin.system.tests.create') }}");

    $this->assertEquals(
      $helper->url($model, 'create', false),
      "URL::route('admin.system.tests.create')");

    $this->assertEquals(
      $helper->url($model, 'edit'),
      "{{ URL::route('admin.system.tests.edit', \$test->id) }}");

    $this->assertEquals(
      $helper->url($model, 'show'),
      "{{ URL::route('admin.system.tests.show', \$test->id) }}");

    $this->assertEquals(
      $helper->url($model, 'update'),
      "{{ URL::route('admin.system.tests.update', \$test->id) }}");

    $this->assertEquals(
      $helper->url($model, 'destroy'),
      "{{ URL::route('admin.system.tests.destroy', \$test->id) }}");

    $this->assertEquals(
      $helper->url($model, 'edit', false),
      "URL::route('admin.system.tests.edit', \$test->id)");

    $this->assertEquals(
      $helper->url($model, 'show', false),
      "URL::route('admin.system.tests.show', \$test->id)");

    $this->assertEquals(
      $helper->url($model, 'update', false),
      "URL::route('admin.system.tests.update', \$test->id)");

    $this->assertEquals(
      $helper->url($model, 'destroy', false),
      "URL::route('admin.system.tests.destroy', \$test->id)");

  }

  /**
   * @covers \Foote\Ginny\Helper\BladeHelper::blade
   */
  public function testblade()
  {

    $isolated = new BaseItem('isolated');
    $child = new BaseItem('child');
    $child->owner = new BaseItem('parent');

    $helper = new BladeHelper();

    $this->assertEquals($helper->blade($isolated), '{{ $isolated }}');
    $this->assertEquals($helper->blade($isolated, true), '{{ $isolated }}');
    $this->assertEquals($helper->blade($child), '{{ $child }}');
    $this->assertEquals($helper->blade($child, true), '{{ $child->parent }}');
  }

}