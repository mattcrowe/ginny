<?php namespace Foote\Ginny\Tests\Map;

/**
 * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
 *
 * (c) Matt Crowe <mattcrowe@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Foote\Ginny\Map\BaseBundle;
use Foote\Ginny\Map\BaseModel;
use Foote\Ginny\Map\BaseField;
use Foote\Ginny\Map\BaseAssociation;

/**
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class BaseModelTest extends \PHPUnit_Framework_TestCase
{

  /**
   * @covers Foote\Ginny\Map\BaseModel::__construct
   * @covers Foote\Ginny\Map\BaseModel::addField
   * @covers Foote\Ginny\Map\BaseModel::addAssociation
   * @covers Foote\Ginny\Map\BaseModel::belongsTo
   * @covers Foote\Ginny\Map\BaseModel::belongsToMany
   * @covers Foote\Ginny\Map\BaseModel::hasOne
   * @covers Foote\Ginny\Map\BaseModel::hasMany
   * @covers Foote\Ginny\Map\BaseModel::owns
   * @covers Foote\Ginny\Map\BaseModel::children
   * @covers Foote\Ginny\Map\BaseModel::parent
   * @covers Foote\Ginny\Map\BaseModel::primaryFields
   * @covers Foote\Ginny\Map\BaseModel::ownedFields
   * @covers Foote\Ginny\Map\BaseModel::dataFields
   * @covers Foote\Ginny\Map\BaseModel::fieldsByType
   * @covers Foote\Ginny\Map\BaseModel::titleField
   */
  public function test()
  {

    // constuctor
    $user = new BaseModel('User');
    $client = new BaseModel('Client');
    $profile = new BaseModel('Profile');
    $comment = new BaseModel('Comment');

    $this->assertEquals('User', $user->name);
    $this->assertEquals('User', $user->single);
    $this->assertEquals('Users', $user->plural);

    $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection',
      $user->fields);
    $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection',
      $user->associations);

    # test BaseModel::owner = NULL (before we add belongsTo association)
    $this->assertNull($user->parent());

    # test BaseModel::owns = false (before we add field with owner)
    $this->assertFalse($user->owns());

    /**
     * Add $fields BaseField[] to $user BaseModel
     *
     * User.id
     * User.name
     * User.client_id
     *
     * @see BaseField
     */

    $fields['User']['id'] = new BaseField('id');
    $fields['User']['id']->primary = true;
    $fields['User']['id']->autoIncrement = true;
    $user->addField($fields['User']['id']);

    $fields['User']['name'] = new BaseField('name');
    $fields['User']['name']->type = 'string';
    $user->addField($fields['User']['name']);

    $fields['User']['client_id'] = new BaseField('client_id');
    $fields['User']['client_id']->type = 'integer';
    $fields['User']['client_id']->owner = $client;
    $user->addField($fields['User']['client_id']);

    /**
     * Test functions that fetched filtered collections from $user->fields
     *
     * @see BaseModel::titleField
     * @see BaseModel::primaryFields
     * @see BaseModel::dataFields
     * @see BaseModel::ownedFields
     * @see BaseModel::fieldsByType
     */

    $this->assertEquals('name', $user->titleField());

    $collection['primaryFields'] = $user->primaryFields();
    $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection',
      $collection['primaryFields']);
    $this->assertTrue($collection['primaryFields']->containsKey('id'));
    $this->assertFalse($collection['primaryFields']->containsKey('name'));
    $this->assertFalse($collection['primaryFields']->containsKey('client_id'));

    $collection['dataFields'] = $user->dataFields();
    $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection',
      $collection['dataFields']);
    $this->assertFalse($collection['dataFields']->containsKey('id'));
    $this->assertTrue($collection['dataFields']->containsKey('name'));
    $this->assertFalse($collection['dataFields']->containsKey('client_id'));

    $collection['ownedFields'] = $user->ownedFields();
    $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection',
      $collection['ownedFields']);
    $this->assertFalse($collection['ownedFields']->containsKey('id'));
    $this->assertFalse($collection['ownedFields']->containsKey('name'));
    $this->assertTrue($collection['ownedFields']->containsKey('client_id'));

    $collection['fieldsByType'] = $user->fieldsByType('string');
    $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection',
      $collection['fieldsByType']);
    $this->assertFalse($collection['fieldsByType']->containsKey('id'));
    $this->assertTrue($collection['fieldsByType']->containsKey('name'));
    $this->assertFalse($collection['fieldsByType']->containsKey('client_id'));

    /**
     * Add $association BaseAssociation[] to $user BaseModel
     *
     * User belongsTo Client
     * User hasOne Profile
     * User hasMany Comment
     *
     * @see BaseAssociation
     */

    BaseAssociation::create('Client', [
        'owner' => $user,
        'ownerKey' => 'client_id',
        'type' => 'belongsTo',
        'target' => $client,
        'targetKey' => 'id'
      ]
    );

    BaseAssociation::create('Profile', [
        'owner' => $user,
        'ownerKey' => 'id',
        'type' => 'hasOne',
        'target' => $profile,
        'targetKey' => 'user_id'
      ]
    );

    BaseAssociation::create('Comment', [
        'owner' => $user,
        'ownerKey' => 'id',
        'type' => 'hasMany',
        'target' => $comment,
        'targetKey' => 'user_id'
      ]
    );

    # test BaseModel::owns = true (now we had added a child association)
    $this->assertTrue($user->owns());

    /**
     * Test functions that fetched filtered collections from $user->fields
     *
     * @see BaseModel::parent
     * @see BaseModel::belongsTo
     * @see BaseModel::hasOne
     * @see BaseModel::hasMany
     */

    $this->assertEquals($client, $user->parent());

    $collection['belongsTo'] = $user->belongsTo();
    $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection',
      $collection['belongsTo']);
    $this->assertTrue($collection['belongsTo']->containsKey('Client'));
    $this->assertFalse($collection['belongsTo']->containsKey('Profile'));
    $this->assertFalse($collection['belongsTo']->containsKey('Comment'));

    $collection['hasOne'] = $user->hasOne();
    $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection',
      $collection['hasOne']);
    $this->assertFalse($collection['hasOne']->containsKey('Client'));
    $this->assertTrue($collection['hasOne']->containsKey('Profile'));
    $this->assertFalse($collection['hasOne']->containsKey('Comment'));

    $collection['hasMany'] = $user->hasMany();
    $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection',
      $collection['hasMany']);
    $this->assertFalse($collection['hasMany']->containsKey('Client'));
    $this->assertFalse($collection['hasMany']->containsKey('Profile'));
    $this->assertTrue($collection['hasMany']->containsKey('Comment'));

    $collection['children'] = $user->children();
    $this->assertTrue($collection['children']->containsKey('Profile'));
    $this->assertTrue($collection['children']->containsKey('Comment'));

  }

  /**
   * @covers Foote\Ginny\Map\BaseModel::primaryFields
   * @covers Foote\Ginny\Map\BaseModel::belongsToMany
   * @covers Foote\Ginny\Map\BaseModel::titleField
   */
  public function testManyToMany()
  {

    // constuctor
    $user = new BaseModel('User');
    $role = new BaseModel('Role');
    $userRole = new BaseModel('UserRole');
    $userRole->manyToMany = true;

    $this->assertEquals('UserRole', $userRole->name);
    $this->assertEquals('UserRoles', $userRole->plural);

    /**
     * Add $fields BaseField[] to $userRole BaseModel
     *
     * UserRole.id
     * UserRole.user_id
     * UserRole.role_id
     *
     * @see BaseField
     */

    $fields['UserRole']['id'] = new BaseField('id');
    $fields['UserRole']['id']->primary = true;
    $fields['UserRole']['id']->autoIncrement = true;
    $userRole->addField($fields['UserRole']['id']);

    $fields['UserRole']['user_id'] = new BaseField('user_id');
    $fields['UserRole']['user_id']->type = 'integer';
    $fields['UserRole']['user_id']->owner = $user;
    $userRole->addField($fields['UserRole']['user_id']);

    $fields['UserRole']['role_id'] = new BaseField('role_id');
    $fields['UserRole']['role_id']->type = 'integer';
    $fields['UserRole']['role_id']->owner = $role;
    $userRole->addField($fields['UserRole']['role_id']);

    /**
     * Test functions that fetched filtered collections from $user->fields
     *
     * @see BaseModel::titleField
     * @see BaseModel::primaryFields
     */

    $this->assertEquals('id', $userRole->titleField());

    $collection['primaryFields'] = $userRole->primaryFields();
    $this->assertTrue($collection['primaryFields']->containsKey('id'));
    $this->assertFalse($collection['primaryFields']->containsKey('user_id'));
    $this->assertFalse($collection['primaryFields']->containsKey('role_id'));

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

    BaseAssociation::create('Role', [
        'owner' => $user,
        'ownerKey' => 'id',
        'type' => 'belongsToMany',
        'target' => $role,
        'targetKey' => 'id',
        'pivot' => $userRole
      ]
    );

    BaseAssociation::create('User', [
        'owner' => $role,
        'ownerKey' => 'id',
        'type' => 'belongsToMany',
        'target' => $user,
        'targetKey' => 'id',
        'pivot' => $userRole
      ]
    );

    /**
     * Test functions that fetched filtered collections from $user->fields
     *
     * @see BaseModel::belongsToMany
     */

    $collection['belongsToMany'] = $user->belongsToMany();
    $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection',
      $collection['belongsToMany']);
    $this->assertTrue($collection['belongsToMany']->containsKey('Role'));


    $collection['belongsToMany'] = $role->belongsToMany();
    $this->assertInstanceOf('\Doctrine\Common\Collections\ArrayCollection',
      $collection['belongsToMany']);
    $this->assertTrue($collection['belongsToMany']->containsKey('User'));
  }

  /**
   * @covers Foote\Ginny\Map\BaseModel::update
   * @covers Foote\Ginny\Map\BaseModel::validate
   * @covers Foote\Ginny\Map\BaseModel::dump
   */
  public function testupdate()
  {

    $bundle = new BaseBundle('Bundle');

    $site = BaseModel::create('Site');
    $site->bundle = $bundle;
    $site->addField(BaseField::create('id', ['type' => 'integer']));

    $user = BaseModel::create('User');
    $user->bundle = $bundle;
    $user->addField(BaseField::create('id', ['type' => 'integer']));
    $user->addField(BaseField::create('site_id', ['type' => 'integer']));

    BaseAssociation::create('Site', [
        'owner' => $user,
        'ownerKey' => 'site_id',
        'type' => 'belongsTo',
        'target' => $site,
        'targetKey' => 'id'
      ]
    );

    $user->update();
    $user->validate();

    //all is good, no exceptions
    $this->assertTrue(true);

    $this->assertNotEmpty($user->dump());
  }

  /**
   * @covers Foote\Ginny\Map\BaseModel::validate
   *
   * @expectedException \Foote\Ginny\Exception\GinnyMapException
   * @expectedExceptionCode 300
   */
  public function testMissingBundle()
  {
    $field = new BaseField('name');
    $field->type = 'string';

    $model = new BaseModel('Thing');
    $model->addField($field);

    $model->validate();

    //all is good, no exceptions
    $this->assertTrue(true);
  }

  /**
   * @covers Foote\Ginny\Map\BaseModel::validate
   *
   * @expectedException \Foote\Ginny\Exception\GinnyMapException
   * @expectedExceptionCode 301
   */
  public function testMissingFields()
  {

    $model = new BaseModel('Thing');

    $bundle = new BaseBundle('Bundle');
    $bundle->addModel($model);

    $model->validate();

    //all is good, no exceptions
    $this->assertTrue(true);
  }

}