<?php namespace Foote\Ginny\Map;

/**
 * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
 *
 * (c) Matt Crowe <mattcrowe@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\Common\Collections\ArrayCollection as Collection;
use Foote\Ginny\Exception\GinnyMapException;

/**
 * BaseModel represent a data model, such as User or CreditCard.
 *
 * It collects related fields BaseField[] and associations BaseAssociation[]
 *
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class BaseModel extends BaseItem
{

  /**
   * @var BaseBundle
   */

  public $bundle;

  /**
   * @var Collection|BaseField[]
   */
  public $fields;

  /**
   * @var Collection|BaseAssociation[]
   */
  public $associations;

  public $manyToMany = false;

  public $description;
  public $namespace;
  public $table;
  public $route;
  public $view;
  public $url;

  /**
   * @param string $name
   */
  function __construct($name, $params = [])
  {
    parent::__construct($name, $params);

    $this->fields = new Collection();
    $this->associations = new Collection();
  }

  public function addField(BaseField $field)
  {
    if (!$this->fields->containsKey($field->name)) {

      $field->model = $this;

      $this->fields->set($field->name, $field);
    }

    return $this;
  }

  public function addAssociation(BaseAssociation $association)
  {
    if (!$this->associations->containsKey($association->name)) {

      $association->models->set('owner', $this);

      $this->associations->set($association->name, $association);
    }

    return $this;
  }

  /**
   * @return Collection|static
   */
  function belongsTo()
  {
    return $this->associations->filter(function ($item) {
      return $item->type == 'belongsTo' ? $item : null;
    });
  }

  /**
   * @return Collection|static
   */
  function belongsToMany()
  {
    return $this->associations->filter(function ($item) {
      return $item->type == 'belongsToMany' ? $item : null;
    });
  }

  /**
   * @return \Doctrine\Common\Collections\Collection|static
   */
  function hasOne()
  {
    return $this->associations->filter(function ($item) {
      return $item->type == 'hasOne' ? $item : null;
    });
  }

  /**
   * @return \Doctrine\Common\Collections\Collection|static
   */
  function hasMany()
  {
    return $this->associations->filter(function ($item) {
      return $item->type == 'hasMany' ? $item : null;
    });
  }

  /**
   * @return null|BaseModel
   */
  function parent()
  {
    $association = $this->belongsTo()->first();
    if ($association && $association->owner) {
      return $association->owner;
    }

    return null;
  }

  /**
   * @return bool
   */
  function owns()
  {
    if (!$this->hasOne()->isEmpty() || !$this->hasMany()->isEmpty()) {
      return true;
    }

    return false;
  }

  /**
   * @return null|BaseModel[]
   */
  function children()
  {
    return $this->associations->filter(function ($item) {
      return in_array($item->type, ['hasMany', 'hasOne']) ? $item : null;
    });
  }

  /**
   * @return Collection|static
   */
  function primaryFields()
  {
    return $this->fields->filter(function ($item) {
      return $item->primary && $item->autoIncrement ? $item : null;
    });
  }

  /**
   * @return Collection|static
   */
  function ownedFields()
  {
    return $this->fields->filter(function ($item) {
      return $item->owner ? $item : null;
    });
  }

  /**
   * @return Collection|static
   */
  function dataFields()
  {
    return $this->fields->filter(function ($item) {
      return !$item->parent && !$item->primary ? $item : null;
    });
  }

  /**
   * @param $type
   * @return Collection
   */
  function fieldsByType($type)
  {
    $results = new Collection();
    foreach ($this->fields as $field) {
      if ($field->type == $type) {
        $results->set($field->name, $field);
      }
    }

    return $results;
  }

  /**
   * Guestimate the title field of the model.
   *
   * @return mixed
   */
  function titleField()
  {
    $keys = [
      'title',
      'name',
      'host',
      'url',
      'email',
    ];

    foreach ($keys as $key) {
      if ($this->fields->containsKey($key)) {
        return $key;
      }
    }

    return $this->fields->first()->name;
  }

  /**
   * {@inheritdoc}
   */
  public function dump()
  {

    $dump = [];

    $newLine = "\n\t\t   ";

    $fields = [];
    foreach ($this->fields as $field) {
      $fields[] = $field->dump();
    }

    $dump['fields'] = $newLine . implode($newLine, $fields);

    $associations = [];
    foreach ($this->associations as $association) {
      $associations[] = $association->dump();
    }

    $dump['associations'] = $newLine . implode($newLine, $associations);

    return $dump;
  }

  /**
   * {@inheritdoc}
   */
  public function update()
  {

    //self update

    foreach ($this->fields as $field) {
      $field->update();
    }
    foreach ($this->associations as $association) {
      $association->update();
    }

  }

  /**
   * {@inheritdoc}
   */
  public function validate()
  {

    //self validate

    # GinnyMapException::300
    if (empty($this->bundle)) {
      throw new GinnyMapException(
        'BaseModel::$bundle empty',
        300
      );
    }

    # GinnyMapException::301
    if ($this->fields->isEmpty()) {
      throw new GinnyMapException(
        'BaseModel::$fields empty',
        301
      );
    }

    foreach ($this->fields as $field) {
      $field->validate();
    }
    foreach ($this->associations as $association) {
      $association->validate();
    }
  }

}