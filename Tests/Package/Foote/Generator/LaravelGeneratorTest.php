<?php namespace Foote\Ginny\Tests\Pacakge\Foote\Mapper;

/**
 * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
 *
 * (c) Matt Crowe <mattcrowe@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Foote\Ginny\Package\Foote\Generator\LaravelGenerator;
use Foote\Ginny\Command\GinnyInput;
use Foote\Ginny\Command\GinnyDefinition;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class LaravelGeneratorTest extends \PHPUnit_Framework_TestCase
{

  private function getInput()
  {
    $yaml = new \Symfony\Component\Yaml\Parser();
    $local_defaults = $yaml->parse(file_get_contents(__DIR__ . '/../../../configs/foote.laravel.yml'));
    $local_defaults['root'] = __DIR__ . '/../../../../';

    $input = new GinnyInput($local_defaults);

    $input->bind(new GinnyDefinition());

    return $input;
  }

  /**
   * @covers \Foote\Ginny\Package\Foote\Generator\LaravelGenerator::map
   * @covers \Foote\Ginny\Package\Foote\Generator\LaravelGenerator::extendTwig
   * @covers \Foote\Ginny\Package\Foote\Generator\LaravelGenerator::genSchema
   * @covers \Foote\Ginny\Package\Foote\Generator\LaravelGenerator::genFixtures
   * @covers \Foote\Ginny\Package\Foote\Generator\LaravelGenerator::genFolders
   * @covers \Foote\Ginny\Package\Foote\Generator\LaravelGenerator::genModels
   * @covers \Foote\Ginny\Package\Foote\Generator\LaravelGenerator::genControllers
   * @covers \Foote\Ginny\Package\Foote\Generator\LaravelGenerator::genViews
   * @covers \Foote\Ginny\Package\Foote\Generator\LaravelGenerator::genTests
   */
  public function testVarious()
  {

    $input = $this->getInput();

    //create target path
    $filesystem = new \Symfony\Component\Filesystem\Filesystem();

    $filesystem->remove($input->getFullTargetPath());
    $filesystem->mkdir($input->getFullTargetPath());

//        $this->assertCount(2, scandir($input->getFullTargetPath()));

    # init generator
    $generator = new LaravelGenerator($input,
      new ConsoleOutput(ConsoleOutput::VERBOSITY_QUIET));

    $this->assertTrue($generator->twig->hasExtension('ui_extension'));
    $this->assertNotEmpty($generator->map->bundles);

    $generator->bundle = $generator->map->bundles->first();

    # generate folder structure
    $generator->genFolders();
    $this->assertTrue($filesystem->exists($input->getFullTargetPath() . 'System/Controller/Admin'));
    $this->assertTrue($filesystem->exists($input->getFullTargetPath() . 'System/database/migrations'));
    $this->assertTrue($filesystem->exists($input->getFullTargetPath() . 'System/database/seeders'));
    $this->assertTrue($filesystem->exists($input->getFullTargetPath() . 'System/Model'));
    $this->assertTrue($filesystem->exists($input->getFullTargetPath() . 'System/View/Admin/User'));

    # generate schema
    $this->assertCount(2,
      scandir($input->getFullTargetPath() . 'System/database/migrations/'));
    $generator->genSchema();
    $this->assertGreaterThan(2,
      count(scandir($input->getFullTargetPath() . 'System/database/migrations/')));

    # generate fixtures
    $this->assertCount(2,
      scandir($input->getFullTargetPath() . 'System/database/seeders'));
    $generator->genFixtures();
    $this->assertTrue($filesystem->exists($input->getFullTargetPath() . 'System/database/seeders/SystemUserSeeder.php'));

    # generate models
    $this->assertCount(2,
      scandir($input->getFullTargetPath() . 'System/Model'));
    $generator->genModels();
    $this->assertTrue($filesystem->exists($input->getFullTargetPath() . 'System/Model/User.php'));
    $this->assertTrue($filesystem->exists($input->getFullTargetPath() . 'System/Model/UserRole.php'));

    # generate controllers
    $this->assertCount(2,
      scandir($input->getFullTargetPath() . 'System/Controller/Admin'));
    $generator->genControllers();
    $this->assertTrue($filesystem->exists($input->getFullTargetPath() . 'System/Controller/Admin/UsersController.php'));

    # generate views
    $this->assertCount(2,
      scandir($input->getFullTargetPath() . 'System/View/Admin/User'));
    $generator->genViews();
    $this->assertTrue($filesystem->exists($input->getFullTargetPath() . 'System/View/Admin/User/show.blade.php'));
    $this->assertTrue($filesystem->exists($input->getFullTargetPath() . 'System/View/Admin/User/create.blade.php'));
    $this->assertTrue($filesystem->exists($input->getFullTargetPath() . 'System/View/Admin/User/edit.blade.php'));
    $this->assertTrue($filesystem->exists($input->getFullTargetPath() . 'System/View/Admin/User/index.blade.php'));

    # generate tests
    $this->assertCount(2,
      scandir($input->getFullTargetPath() . 'System/Tests/Model'));
    $generator->genTests();
    $this->assertTrue($filesystem->exists($input->getFullTargetPath() . 'System/Tests/Controller/Admin/UsersControllerTest.php'));
    $this->assertTrue($filesystem->exists($input->getFullTargetPath() . 'System/Tests/Model/UserTest.php'));

    $filesystem->remove($input->getFullTargetPath());
    $filesystem->mkdir($input->getFullTargetPath());
  }

}