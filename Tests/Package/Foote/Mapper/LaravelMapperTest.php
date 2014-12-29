<?php namespace Foote\Ginny\Tests\Pacakge\Foote\Mapper;
/**
 * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
 *
 * (c) Matt Crowe <mattcrowe@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Foote\Ginny\Mapper\SkipperXML;
use Foote\Ginny\Package\Foote\Mapper\LaravelMapper;

/**
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class LaravelMapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Foote\Ginny\Package\Foote\Mapper\LaravelMapper::get
     */
    public function testget()
    {

        $mapper = new SkipperXML('Admin', __DIR__ . '/../../../../Package/Foote/schemas/default/System.skipper');

        $map = $mapper->map();

        $user = $map->bundles->get('System')->models->get('User');

        $this->assertEmpty($user->url);
        $this->assertEmpty($user->route);

        $map = LaravelMapper::get($map);

        $this->assertNotEmpty($user->url);
        $this->assertNotEmpty($user->route);
    }

}