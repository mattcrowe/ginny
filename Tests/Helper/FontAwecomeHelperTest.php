<?php namespace Foote\Ginny\Tests\Command;

/**
 * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
 *
 * (c) Matt Crowe <mattcrowe@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Foote\Ginny\Helper\FontAwesomeHelper;

/**
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class FontAwesomeHelperTest extends \PHPUnit_Framework_TestCase
{

  /**
   * @covers \Foote\Ginny\Helper\FontAwesomeHelper::icon
   */
  public function testblade()
  {

    $helper = new FontAwesomeHelper();

    $this->assertEquals(
      $helper->icon('test'),
      '<i class="fa fa-test"></i>'
    );

    $this->assertEquals(
      $helper->icon('test', 'test'),
      '<i class="fa fa-test">test</i>'
    );

    $this->assertEquals(
      $helper->icon('cancel'),
      '<i class="fa fa-times"></i>'
    );

    $this->assertEquals(
      $helper->icon('cancel', 'test'),
      '<i class="fa fa-times">test</i>'
    );
  }

}