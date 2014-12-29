<?php namespace Foote\Ginny\Tests\Command;
/**
 * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
 *
 * (c) Matt Crowe <mattcrowe@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Foote\Ginny\Twig\InflectorExtension;

/**
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class InflectorExtensionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * covers \Foote\Ginny\Twig\InflectorExtension::getName
     * covers \Foote\Ginny\Twig\InflectorExtension::getFilters
     */
    public function testblade()
    {

        $extension = new InflectorExtension();

        $this->assertEquals($extension->getName(), 'inflector_extension');

        /**
         * @var $filter \Twig_SimpleFilter
         */
        foreach($extension->getFilters() as $filter) {

            $callable = $filter->getCallable();

            if ($filter->getName() == 'low') {
                $this->assertEquals($callable('ABC'), 'abc');
            };

            if ($filter->getName() == 'up') {
                $this->assertEquals($callable('abc'), 'ABC');
            };

            if ($filter->getName() == 'singularize') {
                $this->assertEquals($callable('things'), 'thing');
            };

            if ($filter->getName() == 'pluralize') {
                $this->assertEquals($callable('thing'), 'things');
                $this->assertEquals($callable('knife'), 'knives');
                $this->assertEquals($callable('child'), 'children');
            };

            if ($filter->getName() == 'camelize') {
                $this->assertEquals($callable('one_big_thing'), 'OneBigThing');
            };

            if ($filter->getName() == 'variableize') {
                $this->assertEquals($callable('one_big_thing'), 'oneBigThing');
            };

            if ($filter->getName() == 'underscore') {
                $this->assertEquals($callable('OneBigThing'), 'one_big_thing');
            };

            if ($filter->getName() == 'humanize') {
                $this->assertEquals($callable('one_big_thing'), 'One big thing');
            };
        }
    }

}