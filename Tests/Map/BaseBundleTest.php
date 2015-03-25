<?php namespace Foote\Ginny\Tests\Map;

/**
 * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
 *
 * (c) Matt Crowe <mattcrowe@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Foote\Ginny\Map\BaseMap;
use Foote\Ginny\Map\BaseBundle;
use Foote\Ginny\Map\BaseModel;
use Foote\Ginny\Map\BaseField;
use Foote\Ginny\Map\BaseAssociation;
use Symfony\Component\HttpKernel\Tests\Bundle\BundleTest;

/**
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class BaseBundleTest extends \PHPUnit_Framework_TestCase
{

  /**
   * @covers Foote\Ginny\Map\BaseBundle::__construct
   * @covers Foote\Ginny\Map\BaseBundle::addModel
   */
  public function test()
  {

    $bundle = new BaseBundle('BigThing');
    $bundle->addModel(new BaseModel('LittleThing'));
    $bundle->addModel(new BaseModel('OtherLittleThing'));

    $map = new BaseMap('Admin');
    $map->addBundle($bundle);

    $this->assertEquals($bundle->name, 'BigThing');
    $this->assertEquals($bundle->single, 'BigThing');
    $this->assertEquals($bundle->plural, 'BigThings');

    $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection',
      $bundle->models);

    $this->assertFalse($bundle->models->isEmpty());

  }

  /**
   * @covers Foote\Ginny\Map\BaseBundle::dump
   */
  public function testdump()
  {

    $bundle = new BaseBundle('Bundle');
    $bundle->map = new BaseMap('Admin');
    $bundle->models->set('model', new BaseModel('Model'));

    $this->assertNotEmpty($bundle->dump());
  }

  /**
   * @covers Foote\Ginny\Map\BaseBundle::update
   */
  public function testupdate()
  {

    $field = new BaseField('name');
    $field->type = 'integer';
    $field->size = 1;

    $model = new BaseModel('Thing');
    $model->addField($field);

    $bundle = new BaseBundle('Bundle');
    $bundle->addModel($model);

    $bundle->update();

    //ensure $field->update() was invoked by $bundle->update()
    $this->assertEquals('boolean', $field->type);
  }

  /**
   * @covers Foote\Ginny\Map\BaseBundle::validate
   */
  public function testValidatesOK()
  {

    $field = new BaseField('name');
    $field->type = 'string';

    $model = new BaseModel('Thing');
    $model->addField($field);

    $bundle = new BaseBundle('Bundle');
    $bundle->addModel($model);

    $bundle->validate();

    //all is good, no exceptions
    $this->assertTrue(true);
  }

  /**
   * @covers Foote\Ginny\Map\BaseBundle::validate
   *
   * @expectedException \Foote\Ginny\Exception\GinnyMapException
   * @expectedExceptionCode 301
   */
  public function testInvalidModel()
  {

    $model = new BaseModel('Thing');

    $bundle = new BaseBundle('Bundle');
    $bundle->addModel($model);

    //additional model validations tested inside BaseModelTest
    $bundle->validate();
  }

}