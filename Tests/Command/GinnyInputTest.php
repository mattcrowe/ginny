<?php namespace Foote\Ginny\Tests\Command;
/**
 * This file is part of the Ginny package: https://github.com/crowefoote/ginny
 *
 * (c) Matt Crowe <crowefoote@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Foote\Ginny\Command\GinnyInput;
use Foote\Ginny\Command\GinnyDefinition;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\InputOption;

/**
 * @author Matt Crowe <crowefoote@zym.me>
 */
class GinnyInputTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers \Foote\Ginny\Command\GinnyInput::getOptionsFromRequest
     * @covers \Foote\Ginny\Command\GinnyInput::__construct
     */
    public function testgetOptionsFromRequest()
    {

        $request = Request::createFromGlobals();

        $argv = $request->server->get('argv');

        $request->server->set('argv', [
            'ginny:generate', //GinnyInput shifts out application name
            '--root=my/root/path/', //regular option
            '--extra=none', //another regulation option
            '-p', //option reference by shortcut
            'Manage',
            'test1',
            'test2=result2',
        ]);

        // ensures that $request->server in GinnyInput::get() loads the new argv
        $request->overrideGlobals();

        $passed = GinnyInput::getOptionsFromRequest();

        $this->assertTrue(array_key_exists('--root', $passed));
        $this->assertTrue(array_key_exists('--extra', $passed));
        $this->assertTrue(array_key_exists('-p', $passed));

        $this->assertEquals('my/root/path/', $passed['--root']);
        $this->assertEquals('none', $passed['--extra']);
        $this->assertEquals('Manage', $passed['-p']);

        //test that the other stuff isn't pass on
        $this->assertFalse(array_key_exists('test1', $passed));
        $this->assertFalse(array_key_exists('--test1', $passed));
        $this->assertFalse(array_key_exists('test2', $passed));
        $this->assertFalse(array_key_exists('--test2', $passed));

        $yaml = new \Symfony\Component\Yaml\Parser();
        $local_defaults = $yaml->parse(file_get_contents(__DIR__ . '/../../ginny.dist.yml'));
        $local_defaults['root'] = __DIR__ . '/../../';

        /**
         * Ensure that GinnyInput::getOptionsFromRequest() is invoked when get()
         * is called without $passed. -p=Manage above should override prefix=Admin
         * found in $local_defaults
         */

        $input = new GinnyInput($local_defaults);
        $this->assertEquals('Manage', $input->getParameterOption('--prefix'));

        // restore $_SERVER['argv']
        $request->server->set('argv', $argv);
        $request->overrideGlobals();
    }

    /**
     * @covers \Foote\Ginny\Command\GinnyInput::__construct
     */
    public function testget()
    {

        $yaml = new \Symfony\Component\Yaml\Parser();
        $local_defaults = $yaml->parse(file_get_contents(__DIR__ . '/../../ginny.dist.yml'));
        $local_defaults['root'] = __DIR__ . '/../../';

        $input = new GinnyInput($local_defaults, [
            '--schema_filename' => 'test',
            '-p' => 'Manage'
        ]);

        // local default "root" intact
        $this->assertEquals($local_defaults['root'], $input->getParameterOption('--root'));

        // local default "schema_filename" overridden
        $this->assertEquals('test', $input->getParameterOption('--schema_filename'));

        // local default "prefix" overridden via shortcut
        $this->assertEquals('Manage', $input->getParameterOption('--prefix'));
    }

    /**
     * @covers \Foote\Ginny\Command\GinnyInput::getFullSchemaPath
     */
    public function testgetFullSchemaPath()
    {

        $yaml = new \Symfony\Component\Yaml\Parser();
        $local_defaults = $yaml->parse(file_get_contents(__DIR__ . '/../configs/default.yml'));
        $local_defaults['root'] = __DIR__ . '/../../';

        $input = new GinnyInput($local_defaults, [
            '-f' => 'my-test-file.xml'
        ]);

        $input->bind(new GinnyDefinition());

        $this->assertEquals(
            $local_defaults['root'] . 'Tests/schemas/my-test-file.xml',
            $input->getFullSchemaPath()
        );
    }

    /**
     * @covers \Foote\Ginny\Command\GinnyInput::getFullTemplatePath
     */
    public function testgetFullTemplatePath()
    {

        $yaml = new \Symfony\Component\Yaml\Parser();
        $local_defaults = $yaml->parse(file_get_contents(__DIR__ . '/../configs/default.yml'));
        $local_defaults['root'] = __DIR__ . '/../../';

        $input = new GinnyInput($local_defaults, [
            '-v' => 'test/template/path'
        ]);

        $input->bind(new GinnyDefinition());

        $this->assertEquals(
            $local_defaults['root'] . 'test/template/path',
            $input->getFullTemplatePath()
        );
    }

    /**
     * @covers \Foote\Ginny\Command\GinnyInput::getFullTargetPath
     */
    public function testgetFullTargetPath()
    {

        $yaml = new \Symfony\Component\Yaml\Parser();
        $local_defaults = $yaml->parse(file_get_contents(__DIR__ . '/../configs/default.yml'));
        $local_defaults['root'] = __DIR__ . '/../../';

        $input = new GinnyInput($local_defaults, [
            '-t' => 'test/target/path/'
        ]);

        $input->bind(new GinnyDefinition());

        $this->assertEquals(
            $local_defaults['root'] . 'test/target/path/',
            $input->getFullTargetPath()
        );
    }

    /**
     * @covers \Foote\Ginny\Command\GinnyInput::validate
     */
    public function testvalidate()
    {

        $yaml = new \Symfony\Component\Yaml\Parser();
        $local_defaults = $yaml->parse(file_get_contents(__DIR__ . '/../configs/default.yml'));
        $local_defaults['root'] = __DIR__ . '/../../';

        $input = new GinnyInput($local_defaults);

        $input->bind(new GinnyDefinition());

        $input->validate();

        // no exceptions thrown? our test config file is valid and we continue...

        $this->assertTrue(true);
    }

    /**
     * @covers \Foote\Ginny\Command\GinnyInput::validate
     *
     * @expectedException \Foote\Ginny\Exception\GinnyInputException
     * @expectedExceptionCode 100
     */
    public function testFullSchemaPathException()
    {

        $yaml = new \Symfony\Component\Yaml\Parser();
        $local_defaults = $yaml->parse(file_get_contents(__DIR__ . '/../configs/default.yml'));
        $local_defaults['root'] = __DIR__ . '/../../';

        $input = new GinnyInput($local_defaults, [
            '-f' => 'my-test-file.xml'
        ]);

        $input->bind(new GinnyDefinition());

        $input->validate();
    }

    /**
     * @covers \Foote\Ginny\Command\GinnyInput::validate
     *
     * @expectedException \Foote\Ginny\Exception\GinnyInputException
     * @expectedExceptionCode 101
     */
    public function testGetFullTemplatePathException()
    {

        $yaml = new \Symfony\Component\Yaml\Parser();
        $local_defaults = $yaml->parse(file_get_contents(__DIR__ . '/../configs/default.yml'));
        $local_defaults['root'] = __DIR__ . '/../../';

        $input = new GinnyInput($local_defaults, [
            '-v' => 'bad-path'
        ]);

        $input->bind(new GinnyDefinition());

        $input->validate();
    }

    /**
     * @covers \Foote\Ginny\Command\GinnyInput::validate
     *
     * @expectedException \Foote\Ginny\Exception\GinnyInputException
     * @expectedExceptionCode 102
     */
    public function testGetFullTargetPathException()
    {

        $yaml = new \Symfony\Component\Yaml\Parser();
        $local_defaults = $yaml->parse(file_get_contents(__DIR__ . '/../configs/default.yml'));
        $local_defaults['root'] = __DIR__ . '/../../';

        $input = new GinnyInput($local_defaults, [
            '-t' => 'bad-path'
        ]);

        $input->bind(new GinnyDefinition());

        $input->validate();
    }

    /**
     * @covers \Foote\Ginny\Command\GinnyInput::validate
     *
     * @expectedException \Foote\Ginny\Exception\GinnyInputException
     * @expectedExceptionCode 103
     */
    public function testInvalidGeneratorNamespaceException()
    {

        $yaml = new \Symfony\Component\Yaml\Parser();
        $local_defaults = $yaml->parse(file_get_contents(__DIR__ . '/../configs/default.yml'));
        $local_defaults['root'] = __DIR__ . '/../../';

        $input = new GinnyInput($local_defaults, [
            '-g' => 'InvalidGeneratorName'
        ]);

        $input->bind(new GinnyDefinition());

        $input->validate();
    }

}