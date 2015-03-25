<?php namespace Foote\Ginny\Tests\Command;

/**
 * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
 *
 * (c) Matt Crowe <mattcrowe@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Output\ConsoleOutput;

use Foote\Ginny\Command\GinnyInput;
use Foote\Ginny\Command\GinnyDefinition;
use Foote\Ginny\Map\BaseMap;
use Foote\Ginny\Map\BaseBundle;
use Foote\Ginny\Generator\BaseGenerator;

/**
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class BaseGeneratorTest extends \PHPUnit_Framework_TestCase
{

  private function getInput()
  {
    $yaml = new \Symfony\Component\Yaml\Parser();
    $local_defaults = $yaml->parse(file_get_contents(__DIR__ . '/../configs/default.yml'));
    $local_defaults['root'] = __DIR__ . '/../../';

    $input = new GinnyInput($local_defaults);

    $input->bind(new GinnyDefinition());

    return $input;
  }

  /**
   * @covers \Foote\Ginny\Generator\BaseGenerator::__construct
   * @covers \Foote\Ginny\Generator\BaseGenerator::map
   * @covers \Foote\Ginny\Generator\BaseGenerator::loadTwig
   * @covers \Foote\Ginny\Generator\BaseGenerator::extendTwig
   * @covers \Foote\Ginny\Generator\BaseGenerator::genSchema
   * @covers \Foote\Ginny\Generator\BaseGenerator::genFixtures
   * @covers \Foote\Ginny\Generator\BaseGenerator::genFolders
   * @covers \Foote\Ginny\Generator\BaseGenerator::genModels
   * @covers \Foote\Ginny\Generator\BaseGenerator::genControllers
   * @covers \Foote\Ginny\Generator\BaseGenerator::genViews
   */
  public function testVarious()
  {

    $input = $this->getInput();

    $generator = new BaseGenerator($input,
      new ConsoleOutput(ConsoleOutput::VERBOSITY_QUIET));

    $this->assertNotNull($generator->twig);
    $this->assertTrue($generator->twig->hasExtension('ginny_extension'));


    //does nothing
    $generator->extendTwig();
    $generator->genSchema();
    $generator->genFixtures();
    $generator->genFolders();
    $generator->genModels();
    $generator->genControllers();
    $generator->genViews();
  }

  /**
   * @covers \Foote\Ginny\Generator\BaseGenerator::doOption
   * @covers \Foote\Ginny\Generator\BaseGenerator::doSubset
   */
  public function testdoSubset()
  {

    // where subset=folder
    $input = $this->getInput();
    $input->setOption('subset', 'folder');
    $generator = new BaseGenerator($input,
      new ConsoleOutput(ConsoleOutput::VERBOSITY_QUIET));
    $this->assertFalse($generator->doSubset('schema'));

    // where subset=all
    $input->setOption('subset', 'all');
    $this->assertTrue($generator->doSubset('schema'));

    // where subset=folder:schema
    $input->setOption('subset', 'folder:schema');
    $this->assertTrue($generator->doSubset('schema'));

    // where subset=schema
    $input->setOption('subset', 'schema');
    $this->assertTrue($generator->doSubset('schema'));
  }

  /**
   * @covers \Foote\Ginny\Generator\BaseGenerator::doExtra
   */
  public function testdoExtra()
  {

    // where extra=seeder
    $input = $this->getInput();
    $input->setOption('extra', 'seeder');
    $generator = new BaseGenerator($input,
      new ConsoleOutput(ConsoleOutput::VERBOSITY_QUIET));
    $this->assertFalse($generator->doExtra('bin'));

    // where extra=all
    $input->setOption('extra', 'all');
    $this->assertTrue($generator->doExtra('bin'));

    // where extra=seeder:bin
    $input->setOption('extra', 'seeder:bin');
    $this->assertTrue($generator->doExtra('bin'));

    // where extra=bin
    $input->setOption('extra', 'bin');
    $this->assertTrue($generator->doExtra('bin'));
  }

  /**
   * @covers \Foote\Ginny\Generator\BaseGenerator::doModel
   */
  public function testdoModel()
  {

    // where model=user
    $input = $this->getInput();
    $input->setOption('model', 'user');
    $generator = new BaseGenerator($input,
      new ConsoleOutput(ConsoleOutput::VERBOSITY_QUIET));
    $this->assertFalse($generator->doModel('site'));

    // where model=all
    $input->setOption('model', 'all');
    $this->assertTrue($generator->doModel('site'));

    // where model=user:site
    $input->setOption('model', 'user:site');
    $this->assertTrue($generator->doModel('site'));

    // where model=site
    $input->setOption('model', 'site');
    $this->assertTrue($generator->doModel('site'));
  }

  /**
   * @covers \Foote\Ginny\Generator\BaseGenerator::doBundle
   */
  public function testdoBundle()
  {

    // where bundle=system
    $input = $this->getInput();
    $input->setOption('bundle', 'system');
    $generator = new BaseGenerator($input,
      new ConsoleOutput(ConsoleOutput::VERBOSITY_QUIET));
    $this->assertFalse($generator->doBundle('content'));

    // where bundle=all
    $input->setOption('bundle', 'all');
    $this->assertTrue($generator->doBundle('content'));

    // where bundle=system:content
    $input->setOption('bundle', 'system:content');
    $this->assertTrue($generator->doBundle('content'));

    // where bundle=content
    $input->setOption('bundle', 'content');
    $this->assertTrue($generator->doBundle('content'));
  }

  /**
   * @covers \Foote\Ginny\Generator\BaseGenerator::render
   */
  public function testrender()
  {

    // where bundle=system
    $input = $this->getInput();
    $input->setOption('bundle', 'system');
    $generator = new BaseGenerator($input,
      new ConsoleOutput(ConsoleOutput::VERBOSITY_QUIET));

    $result = $generator->render('Model/Model', ['test' => 'hello']);

    $this->assertEquals('hello test', $result);
  }

  /**
   * @covers \Foote\Ginny\Generator\BaseGenerator::generate
   */
  public function testgenerate()
  {

    // where bundle=system
    $input = $this->getInput();
    $input->setOption('bundle', 'System');
    $generator = new BaseGenerator($input,
      new ConsoleOutput(ConsoleOutput::VERBOSITY_QUIET));

    $map = new BaseMap('Admin');
    $map->addBundle(new BaseBundle('System'));
    $map->addBundle(new BaseBundle('Content'));

    $generator->map = $map;

    //does nothing
    $generator->generate();
  }

}