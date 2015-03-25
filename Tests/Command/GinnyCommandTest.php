<?php namespace Foote\Ginny\Tests\Command;

/**
 * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
 *
 * (c) Matt Crowe <mattcrowe@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Foote\Ginny\Command\GinnyCommand;
use Foote\Ginny\Command\GinnyInput;
use Foote\Ginny\Command\GinnyDefinition;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class GinnyCommandTest extends \PHPUnit_Framework_TestCase
{

  /**
   * @covers \Foote\Ginny\Command\GinnyCommand::configure
   */
  public function testconfigure()
  {
    $cmd = new GinnyCommand();

    $this->assertEquals($cmd->getName(), 'ginny:generate');
    $this->assertNotEmpty($cmd->getDescription());
    $this->assertEquals($cmd->getDefinition(), new GinnyDefinition());
  }

  /**
   * @covers \Foote\Ginny\Command\GinnyCommand::execute
   */
  public function testexecute()
  {

    $yaml = new \Symfony\Component\Yaml\Parser();
    $local_defaults = $yaml->parse(file_get_contents(__DIR__ . '/../configs/default.yml'));
    $local_defaults['root'] = __DIR__ . '/../../';

    $input = new GinnyInput($local_defaults);

    $cmd = new GinnyCommand();

    //nothing happens
    $cmd->run($input, new ConsoleOutput(ConsoleOutput::VERBOSITY_QUIET));
  }

}