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

/**
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class BaseMapTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers Foote\Ginny\Map\BaseMap::__construct
     * @covers Foote\Ginny\Map\BaseMap::addBundle
     */
    public function test()
    {

        $map = new BaseMap('Admin');

        $map->addBundle(new BaseBundle('BigThing'));

        $this->assertEquals($map->name, 'Admin');
        $this->assertEquals($map->prefix, 'Admin');
        $this->assertEquals($map->snake, 'admin');

        $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection', $map->bundles);

        $this->assertFalse($map->bundles->isEmpty());
    }

    /**
     * @covers Foote\Ginny\Map\BaseMap::dump
     */
    public function testdump()
    {

        $map = new BaseMap('Admin');
        $map->addBundle(new BaseBundle('Bundle'));

        $this->assertNotEmpty($map->dump());
    }

    /**
     * @covers Foote\Ginny\Map\BaseMap::update
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

        $map = new BaseMap('Admin');
        $map->addBundle($bundle);
        $map->update();

        //ensure $field->update() was invoked by $bundle->update()
        $this->assertEquals('boolean', $field->type);
    }

    /**
     * @covers Foote\Ginny\Map\BaseMap::validate
     */
    public function testValidatesOK()
    {

        $field = new BaseField('name');
        $field->type = 'string';

        $model = new BaseModel('Thing');
        $model->addField($field);

        $bundle = new BaseBundle('Bundle');
        $bundle->addModel($model);

        $map = new BaseMap('Admin');
        $map->addBundle($bundle);
        $map->validate();

        //all is good, no exceptions
        $this->assertTrue(true);
    }

    /**
     * @covers Foote\Ginny\Map\BaseMap::validate
     *
     * @expectedException \Foote\Ginny\Exception\GinnyMapException
     * @expectedExceptionCode 301
     */
    public function testInvalidModel()
    {

        $model = new BaseModel('Thing');

        $bundle = new BaseBundle('Bundle');
        $bundle->addModel($model);

        $map = new BaseMap('Admin');
        $map->addBundle($bundle);

        //additional model validations tested inside BaseBundleTest
        $map->validate();
    }


}