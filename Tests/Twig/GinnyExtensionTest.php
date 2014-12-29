<?php namespace Foote\Ginny\Tests\Command;
/**
 * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
 *
 * (c) Matt Crowe <mattcrowe@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Foote\Ginny\Twig\GinnyExtension;

/**
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class GinnyExtensionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * covers \Foote\Ginny\Twig\GinnyExtension::getName
     * covers \Foote\Ginny\Twig\GinnyExtension::getFilters
     */
    public function testblade()
    {

        $extension = new GinnyExtension();

        $this->assertEquals($extension->getName(), 'ginny_extension');

        $data = 'A

        B';

        /**
         * @var $filter \Twig_SimpleFilter
         */
        foreach($extension->getFilters() as $filter) {

            $callable = $filter->getCallable();

            if ($filter->getName() == 'inline') {
                $this->assertEquals($callable($data), 'A B');
            };
            if ($filter->getName() == 'spaceless') {
                $this->assertEquals($callable($data), 'AB');
            };
        }
    }

}