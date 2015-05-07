<?php namespace Foote\Ginny\Map;

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
use Foote\Ginny\Command\GinnyInput;

/**
 * SkipperXML parses a Skipper xml file into usable data map for a generator class
 *
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class BaseMapper
{

  /**
   * @var BaseMap
   */
  public $map;

  /**
   * @var GinnyInput
   */
  public $input;

  /**
   * @var \SimpleXMLElement
   */
  public $xml;

  public $filename;

  public $schema;

  /**
   * @param $prefix
   * @param $filename
   */

  public function __construct(GinnyInput $input)
  {
    $this->input = $input;
    $this->prefix = $input->getOption('prefix');
    $this->map = new BaseMap($input->getOption('prefix'));
    $this->map->namespace = $input->getOption('namespace');
  }

  public function map()
  {

    foreach ($this->schema->bundles as $_bundle) {

      $bundle = new BaseBundle($_bundle->name);

      $this->map->addBundle($bundle);

      # add models to bundle
      foreach ($_bundle->models as $_model) {

        if (strpos($_model->name, ':') != false) {
          continue;
        }

        $model = new BaseModel($_model->name);

        $bundle->addModel($model);

        $model->description = $_model->description;
        $model->table = $model->bundle->snake . '_' . $model->snakes;

        if (!empty($_model->ormAttributes)) {
          foreach ($_model->ormAttributes as $key => $val) {
            if ('table' == $key) {
              $model->table = $val;
            }
          }
        }

        # add fields to model
        foreach ($_model->fields as $_field) {

          $field = new BaseField($_field->name);

          $model->addField($field);

          $field->type = $_field->type;
          $field->size = $_field->size;
          $field->default = $_field->default;
          $field->required = $_field->required;
          $field->unique = $_field->unique;
          $field->primary = $_field->primary;
          $field->autoIncrement = $_field->autoIncrement;
        }

      }

      # add relationships to models
      foreach ($_bundle->models as $_model) {

        if (strpos($_model->name, ':') != false) {
          continue;
        }

        $model = $bundle->models->get($_model->name);

        foreach ($_bundle->associations as $_association) {

          # belongsTo (ex. User belongsTo Site)

          if ($model->name == $_association->from) {

            $parent = $bundle->models->get($_association->to);

            new BaseAssociation($parent->name, [
              'owner' => $model, //User
              'ownerKey' => $_association->field->from, //User.site_id
              'type' => 'belongsTo',
              'target' => $parent, //Site
              'targetKey' => $_association->field->to //Site.id
            ]);

          }

          # hasOne and hasMany (ex. User hasOne Profile, User hasMany Comment)

          if ($model->name == $_association->to) {

            $child = $bundle->models->get($_association->from);

            new BaseAssociation($child->name, [
              'owner' => $model, //User
              'ownerKey' => $_association->field->to, //User.id
              'type' => 'hasMany',
              'target' => $child, //Comment
              'targetKey' => $_association->field->from //Comment.user_id
            ]);

            /**
             * for User hasOne Profile, if Profile.user is set to unique, the association
             * will change to "hasOne" on $association->update()
             */

          }

        }

        # manyToMany
        foreach ($_bundle->manyToManys as $_manyToMany) {

          if ($model->name == $_manyToMany->name) {

            /**
             * example: User <--> UserRole <--> Role
             */

            // identify UserRole as a manyToMany table
            $model->manyToMany = true;

            // ex: Role
            $manyModel1 = $bundle->models->get($_manyToMany->owner->name); //Role

            // ex: User
            $manyModel2 = $bundle->models->get($_manyToMany->inverse->name); //User

            new BaseAssociation($manyModel2->name, [
              'owner' => $model,
              //UserRole
              'ownerKey' => $_manyToMany->inverse->field->from,
              //UserRole.user_id
              'type' => 'belongsTo',
              'target' => $manyModel2,
              //User
              'targetKey' => $_manyToMany->owner->field->to
              //User.id
            ]);

            new BaseAssociation($manyModel1->name, [
              'owner' => $model, //UserRole
              'ownerKey' => $_manyToMany->owner->field->from, //UserRole.role_id
              'type' => 'belongsTo',
              'target' => $manyModel1, //Role
              'targetKey' => $_manyToMany->inverse->field->to //Role.id
            ]);

            new BaseAssociation($manyModel1->name, [
              'owner' => $manyModel2, //User
              'ownerKey' => $_manyToMany->owner->field->to, //User.id
              'type' => 'belongsToMany',
              'target' => $manyModel1, //Role
              'targetKey' => $_manyToMany->inverse->field->to, //Role.id
              'pivot' => $model //UserRole
            ]);

            new BaseAssociation($manyModel2->name, [
              'owner' => $manyModel1, //Role
              'ownerKey' => $_manyToMany->inverse->field->to, //Role.id
              'type' => 'belongsToMany',
              'target' => $manyModel2, //User
              'targetKey' => $_manyToMany->owner->field->to, //User.id
              'pivot' => $model //UserRole
            ]);

          }
        }

      }
    }

    $this->map->update();
    $this->map->validate();
    //s($this->map->dump());

    return $this->map;
  }

}