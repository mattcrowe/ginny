<?php namespace Foote\Ginny\Tests\Map;
/**
 * This file is part of the Ginny package: https://github.com/crowefoote/ginny
 *
 * (c) Matt Crowe <crowefoote@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Foote\Ginny\Map\BaseBundle;
use Foote\Ginny\Map\BaseModel;
use Foote\Ginny\Map\BaseAssociation;
use Foote\Ginny\Map\BaseField;

/**
 * @author Matt Crowe <crowefoote@zym.me>
 */
class BaseAssociationTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @covers Foote\Ginny\Map\BaseAssociation::__construct
     * @covers Foote\Ginny\Map\BaseAssociation::create
     * @covers Foote\Ginny\Map\BaseAssociation::addKey
     * @covers Foote\Ginny\Map\BaseAssociation::owner
     * @covers Foote\Ginny\Map\BaseAssociation::parent
     * @covers Foote\Ginny\Map\BaseAssociation::child
     * @covers Foote\Ginny\Map\BaseAssociation::keys
     * @covers Foote\Ginny\Map\BaseAssociation::local_key
     * @covers Foote\Ginny\Map\BaseAssociation::parent_key
     * @covers Foote\Ginny\Map\BaseAssociation::foreign_key
     * @covers Foote\Ginny\Map\BaseAssociation::validate
     * @covers Foote\Ginny\Map\BaseAssociation::dump
     */
    public function testcreate()
    {
        $bundle = new BaseBundle('Bundle');

        $site = BaseModel::create('Site');
        $site->bundle = $bundle;
        $site->addField(BaseField::create('id', ['type'=>'integer']));

        $user = BaseModel::create('User');
        $user->bundle = $bundle;
        $user->addField(BaseField::create('id', ['type'=>'integer']));
        $user->addField(BaseField::create('site_id', ['type'=>'integer']));

        // User belongsTo Site
        $association = BaseAssociation::create('UserSite', [
                'owner' => $user,
                'ownerKey' => 'site_id',
                'type' => 'belongsTo',
                'target' => $site,
                'targetKey' => 'id'
            ]
        );

        $this->assertEquals($user, $association->models->get('owner'));
        $this->assertEquals($user, $association->owner());
        $this->assertEquals($user, $association->models->get('child'));
        $this->assertEquals($user, $association->child());
        $this->assertEquals($site, $association->models->get('parent'));
        $this->assertEquals($site, $association->parent());
        $this->assertEquals('site_id', $association->local_key());
        $this->assertEquals('site_id', $association->keys('local'));
        $this->assertEquals('id', $association->parent_key());
        $this->assertEquals('id', $association->keys('parent'));
        $this->assertNotEmpty($association->dump());

        // Site hasMany User
        $association = BaseAssociation::create('SiteUser', [
                'owner' => $site,
                'ownerKey' => 'id',
                'type' => 'hasMany',
                'target' => $user,
                'targetKey' => 'site_id'
            ]
        );

        $this->assertEquals($site, $association->models->get('owner'));
        $this->assertEquals($user, $association->models->get('child'));
        $this->assertEquals($site, $association->models->get('parent'));
        $this->assertEquals('id', $association->local_key());
        $this->assertEquals('id', $association->keys('local'));
        $this->assertEquals('site_id', $association->foreign_key());
        $this->assertEquals('site_id', $association->keys('foreign'));
        $this->assertNotEmpty($association->dump());

        // Site hasOne Profile
        $profile = BaseModel::create('Profile');
        $profile->bundle = $bundle;
        $profile->addField(BaseField::create('id', ['type'=>'integer']));
        $profile->addField(BaseField::create('site_id', ['type'=>'integer']));

        $association = BaseAssociation::create('SiteProfile', [
                'owner' => $site,
                'ownerKey' => 'id',
                'type' => 'hasOne',
                'target' => $profile,
                'targetKey' => 'site_id'
            ]
        );

        $this->assertEquals($site, $association->models->get('owner'));
        $this->assertEquals($profile, $association->models->get('child'));
        $this->assertEquals($site, $association->models->get('parent'));
        $this->assertNotEmpty($association->dump());

        //nothing happens
        $association->validate();
    }

    /**
     * @covers Foote\Ginny\Map\BaseAssociation::__construct
     * @covers Foote\Ginny\Map\BaseAssociation::create
     * @covers Foote\Ginny\Map\BaseAssociation::pivot
     * @covers Foote\Ginny\Map\BaseAssociation::pivot_local_key
     * @covers Foote\Ginny\Map\BaseAssociation::pivot_parent_key
     * @covers Foote\Ginny\Map\BaseAssociation::dump
     */
    public function testManyToMany()
    {


        $user = BaseModel::create('User');
        $user->addField(BaseField::create('id', ['type'=>'integer']));

        $role = BaseModel::create('Role');
        $role->addField(BaseField::create('id', ['type'=>'integer']));

        $userRole = BaseModel::create('UserRole');
        $userRole->manyToMany = true;
        $userRole->addField(BaseField::create('user_id', ['type'=>'integer']));
        $userRole->addField(BaseField::create('role_id', ['type'=>'integer']));

        $bundle = BaseBundle::create('Bundle');
        $bundle->addModel($user);
        $bundle->addModel($role);
        $bundle->addModel($userRole);

        /**
         *
         * UserRole belongsTo User
         * UserRole belongsTo Role
         * User belongsToMany Role
         * Role belongsToMany User
         *
         * we need $userRole to have it's respective "belongsTo" before we
         * add belongsToMany to $user and $role
         *
         * @see BaseAssociation
         */

        BaseAssociation::create('User', [
                'owner' => $userRole,
                'ownerKey' => 'user_id',
                'type' => 'belongsTo',
                'target' => $user,
                'targetKey' => 'id'
            ]
        );

        BaseAssociation::create('Role', [
                'owner' => $userRole,
                'ownerKey' => 'role_id',
                'type' => 'belongsTo',
                'target' => $role,
                'targetKey' => 'id'
            ]
        );

        # now we are ready for $user belongsToMany $role, etc

        $association = BaseAssociation::create('UserRole', [
                'owner' => $user,
                'ownerKey' => 'id',
                'type' => 'belongsToMany',
                'target' => $role,
                'targetKey' => 'id',
                'pivot' => $userRole
            ]
        );


        $this->assertEquals($userRole, $association->pivot());
        $this->assertEquals('user_id', $association->keys('pivot_local')->name);
        $this->assertEquals('user_id', $association->pivot_local_key());
        $this->assertEquals('role_id', $association->keys('pivot_parent')->name);
        $this->assertEquals('role_id', $association->pivot_parent_key());
        $this->assertNotEmpty($association->dump());
    }

    /**
     * @covers Foote\Ginny\Map\BaseAssociation::update
     */
    public function testupdate()
    {

        $bundle = new BaseBundle('Bundle');

        $site = BaseModel::create('Site');
        $site->bundle = $bundle;
        $site->addField(BaseField::create('id', ['type'=>'integer']));

        // Site hasOne Profile
        $profile = BaseModel::create('Profile');
        $profile->bundle = $bundle;
        $profile->addField(BaseField::create('id', ['type'=>'integer']));

        //add unique field to Profile
        $field = BaseField::create('site_id', ['type'=>'integer']);
        $field->unique = true;

        $profile->addField($field);

        $association = BaseAssociation::create('SiteProfile', [
                'owner' => $site,
                'ownerKey' => 'id',
                'type' => 'hasMany',
                'target' => $profile,
                'targetKey' => 'site_id'
            ]
        );

        //hasMany should be converted to hasOne since Profile.site_id is unique
        $association->update();

        $this->assertEquals('hasOne', $association->type);
    }

}