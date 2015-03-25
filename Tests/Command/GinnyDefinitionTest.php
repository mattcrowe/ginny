<?php namespace Foote\Ginny\Tests\Command;

/**
 * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
 *
 * (c) Matt Crowe <mattcrowe@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Foote\Ginny\Command\GinnyDefinition;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\InputOption;

/**
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class GinnyDefinitionTest extends \PHPUnit_Framework_TestCase
{

  /**
   * @covers \Foote\Ginny\Command\GinnyDefinition::__construct
   */
  public function test__construct()
  {

    $ginnyDefinition = new GinnyDefinition();

    /**
     * There's not much to check here, but let's at least double check
     * there are no arguments and some options.
     */

    $this->assertEquals(0, $ginnyDefinition->getArgumentCount());
    $this->assertTrue($ginnyDefinition->hasOption('root'));
    $this->assertTrue($ginnyDefinition->hasOption('model'));
    $this->assertTrue($ginnyDefinition->hasOption('extra'));
  }

}