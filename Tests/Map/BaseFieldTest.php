<?php namespace Foote\Ginny\Tests\Map;
/**
 * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
 *
 * (c) Matt Crowe <mattcrowe@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Foote\Ginny\Map\BaseModel;
use Foote\Ginny\Map\BaseField;

/**
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class BaseFieldTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers Foote\Ginny\Map\BaseModel::__construct
     * @covers Foote\Ginny\Map\BaseField::sensitive
     * @covers Foote\Ginny\Map\BaseField::update
     * @covers Foote\Ginny\Map\BaseField::validate
     */
    public function test()
    {

        //constuctor
        $field = new BaseField('field_name');
        $this->assertEquals($field->name, 'field_name');
        $this->assertNull($field->type);
        $this->assertFalse($field->sensitive());

        //convert to boolean
        $field->size = 1;
        $field->type = 'integer';
        $field->update();
        $this->assertEquals('boolean', $field->type);

        //fieldname like "password" is marked as sensitive
        $field = new BaseField('password');
        $this->assertTrue($field->sensitive());
    }

    /**
     * @covers Foote\Ginny\Map\BaseField::create
     */
    public function testcreate()
    {
        $field = BaseField::create('id', ['type'=>'integer']);
        $this->assertEquals('id', $field->name);
        $this->assertEquals('integer', $field->type);
    }

    /**
     * @covers Foote\Ginny\Map\BaseField::dump
     */
    public function testdump()
    {
        $field = BaseField::create('field_name', ['type'=>'string']);
        $field->size = 100;
        $this->assertEquals('field_name: string(100)', $field->dump());

        $field = BaseField::create('field_name', ['type'=>'integer']);
        $field->size = 5;
        $this->assertEquals('field_name: integer(5)', $field->dump());

        $field = BaseField::create('field_name', ['type'=>'text']);
        $this->assertEquals('field_name: text', $field->dump());

        $field = BaseField::create('field_name', ['type'=>'text']);
        $field->required = false;
        $this->assertEquals('field_name: text, NULLABLE', $field->dump());

        $field = BaseField::create('field_name', ['type'=>'text']);
        $field->default = 'some text';
        $this->assertEquals('field_name: text, DEFAULT=\'some text\'', $field->dump());

        $field = BaseField::create('field_name', ['type'=>'integer']);
        $field->primary = true;
        $field->size = 5;
        $this->assertEquals('field_name: integer(5), PRIMARY', $field->dump());

        $field->unique = true;
        $this->assertEquals('field_name: integer(5), UNIQUE, PRIMARY', $field->dump());

        $field->autoIncrement = true;
        $this->assertEquals('field_name: integer(5), UNIQUE, PRIMARY, AUTOINC', $field->dump());
    }

    /**
     * @covers Foote\Ginny\Map\BaseField::update
     */
    public function testupdate()
    {
        //convert to boolean
        $field = new BaseField('name');
        $field->size = 1;
        $field->type = 'integer';
        $field->update();
        $this->assertEquals('boolean', $field->type);
    }

    /**
     * @covers Foote\Ginny\Map\BaseField::validate
     */
    public function testValidatesOK()
    {
        $field = new BaseField('name');
        $field->type = 'text';
        $field->model = new BaseModel('Thing');
        $field->validate();

        //all is good, no exceptions
        $this->assertTrue(true);
    }

    /**
     * @covers Foote\Ginny\Map\BaseField::validate
     *
     * @expectedException \Foote\Ginny\Exception\GinnyMapException
     * @expectedExceptionCode 400
     */
    public function testEmptyTypeException()
    {
        $field = new BaseField('name');
        $field->model = new BaseModel('Thing');
        $field->validate();
    }

    /**
     * @covers Foote\Ginny\Map\BaseField::validate
     *
     * @expectedException \Foote\Ginny\Exception\GinnyMapException
     * @expectedExceptionCode 401
     */
    public function testEmptyModelException()
    {
        $field = new BaseField('name');
        $field->type = 'text';
        $field->validate();
    }

}