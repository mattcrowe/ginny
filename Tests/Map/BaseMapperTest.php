<?php namespace Foote\Ginny\Tests\Map;
/**
 * This file is part of the Ginny package: https://github.com/crowefoote/ginny
 *
 * (c) Matt Crowe <crowefoote@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Foote\Ginny\Map\BaseMapper;
use Foote\Ginny\Command\GinnyInput;
use Foote\Ginny\Command\GinnyDefinition;

/**
 * @author Matt Crowe <crowefoote@zym.me>
 */
class BaseMapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers Foote\Ginny\Mapper\BaseMapper::__construct
     * @covers Foote\Ginny\Mapper\BaseMapper::map
     */
    public function test()
    {

        $yaml = new \Symfony\Component\Yaml\Parser();
        $local_defaults = $yaml->parse(file_get_contents(__DIR__ . '/../configs/default.yml'));
        $local_defaults['root'] = __DIR__ . '/../../';

        $input = new GinnyInput($local_defaults);

        $input->bind(new GinnyDefinition());

        $mapper = new BaseMapper($input);

        $xml = file_get_contents(__DIR__ . '/../schemas/system.skipper.xml');
        $schema = \Foote\Ginny\Convert\SkipperXML::convert($xml);
        $mapper->schema = json_decode(json_encode($schema));

        $map = $mapper->map();

        $this->assertEquals('Admin', $map->name);
        $this->assertEquals('Admin', $map->prefix);

        $this->assertTrue($map->bundles->containsKey('System'));
        //$this->assertTrue($map->bundles->containsKey('OtherBundle'));

        /**
         * test for some expected models, fields, and associations
         *
         * Client hasMany User
         *
         * User belongsTo Client
         * User hasOne Profile
         * User belongsToMany Role
         *
         * Role belongsToMany User
         *
         * Profile belongsTo User
         * Profile belongsTo OtherBundle\Model\Outside
         *
         * UserRole belongsTo User
         * UserRole belongsTo Role
         */

        //test the first bundle...

        $bundle = $map->bundles->first();

        $this->assertTrue($bundle->models->containsKey('Client'));
        $this->assertTrue($bundle->models->containsKey('User'));
        $this->assertTrue($bundle->models->containsKey('Role'));
        $this->assertTrue($bundle->models->containsKey('Profile'));
        $this->assertTrue($bundle->models->containsKey('UserRole'));

        foreach($bundle->models as $model) {

            $this->assertFalse($model->fields->isEmpty());

            if ($model->name == 'Client') {
                $this->assertFalse($model->associations->isEmpty());
                $this->assertTrue($model->hasOne()->isEmpty());
                $this->assertFalse($model->hasMany()->isEmpty());
                $this->assertTrue($model->belongsTo()->isEmpty());
                $this->assertTrue($model->belongsToMany()->isEmpty());
            }

            if ($model->name == 'User') {
                $this->assertFalse($model->associations->isEmpty());
                $this->assertFalse($model->hasOne()->isEmpty());
                $this->assertTrue($model->hasMany()->isEmpty());
                $this->assertFalse($model->belongsTo()->isEmpty());
                $this->assertFalse($model->belongsToMany()->isEmpty());
            }

            if ($model->name == 'Role') {
                $this->assertFalse($model->associations->isEmpty());
                $this->assertTrue($model->hasOne()->isEmpty());
                $this->assertTrue($model->hasMany()->isEmpty());
                $this->assertTrue($model->belongsTo()->isEmpty());
                $this->assertFalse($model->belongsToMany()->isEmpty());
            }

            if ($model->name == 'UserRole') {
                $this->assertFalse($model->associations->isEmpty());
                $this->assertTrue($model->hasOne()->isEmpty());
                $this->assertTrue($model->hasMany()->isEmpty());
                $this->assertFalse($model->belongsTo()->isEmpty());
                $this->assertTrue($model->belongsToMany()->isEmpty());
            }

            if ($model->name == 'Profile') {
                $this->assertFalse($model->associations->isEmpty());
                $this->assertTrue($model->hasOne()->isEmpty());
                $this->assertTrue($model->hasMany()->isEmpty());
                $this->assertFalse($model->belongsTo()->isEmpty());
                $this->assertTrue($model->belongsToMany()->isEmpty());
            }

        }
    }

}