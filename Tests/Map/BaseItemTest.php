<?php namespace Foote\Ginny\Tests\Map;
/**
 * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
 *
 * (c) Matt Crowe <mattcrowe@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Foote\Ginny\Map\BaseItem;

/**
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class BaseItemTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers Foote\Ginny\Map\BaseItem::__construct
     * @covers Foote\Ginny\Map\BaseItem::__toString
     * @covers Foote\Ginny\Map\BaseItem::__get
     * @covers Foote\Ginny\Map\BaseItem::single
     * @covers Foote\Ginny\Map\BaseItem::plural
     * @covers Foote\Ginny\Map\BaseItem::variable
     * @covers Foote\Ginny\Map\BaseItem::variables
     * @covers Foote\Ginny\Map\BaseItem::camel
     * @covers Foote\Ginny\Map\BaseItem::camels
     * @covers Foote\Ginny\Map\BaseItem::human
     * @covers Foote\Ginny\Map\BaseItem::title
     * @covers Foote\Ginny\Map\BaseItem::snake
     * @covers Foote\Ginny\Map\BaseItem::snakes
     * @covers Foote\Ginny\Map\BaseItem::low
     * @covers Foote\Ginny\Map\BaseItem::up
     * @covers Foote\Ginny\Map\BaseItem::getter
     * @covers Foote\Ginny\Map\BaseItem::getters
     * @covers Foote\Ginny\Map\BaseItem::setter
     * @covers Foote\Ginny\Map\BaseItem::setters
     * @covers Foote\Ginny\Map\BaseItem::adder
     * @covers Foote\Ginny\Map\BaseItem::remover
     * @covers Foote\Ginny\Map\BaseItem::update
     * @covers Foote\Ginny\Map\BaseItem::validate
     * @covers Foote\Ginny\Map\BaseItem::create
     * @covers Foote\Ginny\Map\BaseItem::dump
     */
    public function test()
    {

        $item = new BaseItem('BigThing');

        $this->assertEquals($item->name, 'BigThing');
        $this->assertEquals($item->__toString(), 'BigThing');
        $this->assertEquals($item->undefined_property, NULL);
        $this->assertEquals($item->single, 'BigThing');
        $this->assertEquals($item->plural, 'BigThings');
        $this->assertEquals($item->variable, 'bigThing');
        $this->assertEquals($item->variables, 'bigThings');
        $this->assertEquals($item->camel, 'BigThing');
        $this->assertEquals($item->camels, 'BigThings');
        $this->assertEquals($item->human, 'Big thing');
        $this->assertEquals($item->title, 'Big Thing');
        $this->assertEquals($item->snake, 'big_thing');
        $this->assertEquals($item->snakes, 'big_things');
        $this->assertEquals($item->low, 'bigthing');
        $this->assertEquals($item->up, 'BIGTHING');
        $this->assertEquals($item->getter, 'getBigThing');
        $this->assertEquals($item->getters, 'getBigThings');
        $this->assertEquals($item->setter, 'setBigThing');
        $this->assertEquals($item->setters, 'setBigThings');
        $this->assertEquals($item->adder, 'addBigThing');
        $this->assertEquals($item->remover, 'removeBigThing');

        $item2 = BaseItem::create('BigThing');

        $this->assertEquals($item, $item2);

        $item3 = BaseItem::create('BigThing', ['name'=>'OtherThing']);

        $this->assertEquals('OtherThing', $item3->name);

        //nothing happens here
        $item->update();
        $item->validate();
        $item->dump();
    }

}