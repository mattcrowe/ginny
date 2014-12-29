<?php namespace Foote\Ginny\Map;
/**
 * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
 *
 * (c) Matt Crowe <mattcrowe@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Foote\Ginny\Exception\GinnyMapException;
use Doctrine\Common\Collections\ArrayCollection as Collection;

/**
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class BaseAssociation extends BaseItem
{

    public $name;
    public $type;

    /**
     * @var BaseModel
     */
    public $owner;
    public $ownerKey;

    /**
     * @var BaseModel
     */
    public $target;
    public $targetKey;

    /**
     * @var BaseModel
     */
    public $pivot;

    /**
     * @var Collection|BaseField[]
     */
    public $keys;

    /**
     * @var Collection|BaseModel[]
     */
    public $models;

    /**
     * @param string $name
     */
//    function __construct(BaseModel $owner, $ownerKey, $type, BaseModel $target, $targetKey, BaseModel $pivot = NULL)
    function __construct($name, $params = [])
    {
        parent::__construct($name, $params);

        $this->keys = new Collection();
        $this->models = new Collection();

        $this->models->set('owner', $this->owner);
        $this->owner->addAssociation($this);

        switch($this->type) {

            case 'belongsTo':
                $this->models->set('child', $this->owner);
                $this->models->set('parent', $this->target);
                $this->addKey('local', $this->owner->fields->get($this->ownerKey));
                $this->addKey('parent', $this->target->fields->get($this->targetKey));

                $this->owner->fields->get($this->ownerKey)->parent = $this->target;
                break;

            case 'belongsToMany':

                $this->pivotOwnerBelongsTo = $this->pivot->associations->get($this->owner->name);
                $this->pivotTargetBelongsTo = $this->pivot->associations->get($this->target->name);

                $this->models->set('child', $this->owner);
                $this->models->set('parent', $this->target);
                $this->models->set('pivot', $this->pivot);
                $this->addKey('local', $this->owner->fields->get($this->ownerKey));
                $this->addKey('parent', $this->target->fields->get($this->targetKey));
                $this->addKey('pivot_local', $this->pivotOwnerBelongsTo->keys('local'));
                $this->addKey('pivot_parent', $this->pivotTargetBelongsTo->keys('local'));
                break;

            case 'hasMany':
                $this->models->set('child', $this->target);
                $this->models->set('parent', $this->owner);
                $this->addKey('local', $this->owner->fields->get($this->ownerKey));
                $this->addKey('foreign', $this->target->fields->get($this->targetKey));
                break;

            case 'hasOne':
                $this->models->set('child', $this->target);
                $this->models->set('parent', $this->owner);
                $this->addKey('local', $this->owner->fields->get($this->ownerKey));
                $this->addKey('foreign', $this->target->fields->get($this->targetKey));
                break;
        }


    }

    //public static function create(BaseModel $owner, $ownerKey, $type, BaseModel $target, $targetKey, BaseModel $pivot = NULL)
    public static function create($name, $params = [])
    {
        return new static($name, $params);
        //return new static($owner, $ownerKey, $type, $target, $targetKey, $pivot);
    }

    public function addKey($name, $value)
    {
        if (!$this->keys->containsKey($name)) {
            $this->keys->set($name, $value);
        }

        return $this;
    }

    public function owner()
    {
        return $this->models->get('owner');
    }

    public function pivot()
    {
        return $this->models->get('pivot');
    }

    public function parent()
    {
        return $this->models->get('parent');
    }

    public function child()
    {
        return $this->models->get('child');
    }

    public function local_key()
    {
        return $this->keys->get('local')->name;
    }

    public function parent_key()
    {
        return $this->keys->get('parent')->name;
    }

    public function foreign_key()
    {
        return $this->keys->get('foreign')->name;
    }

    public function pivot_local_key()
    {
        return $this->keys->get('pivot_local')->name;
    }

    public function pivot_parent_key()
    {
        return $this->keys->get('pivot_parent')->name;
    }

    public function keys($name)
    {
        return $this->keys->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {

        $s = sprintf('%s %s ',
            $this->local_key,
            $this->type
        );

        switch ($this->type) {
            case 'belongsTo':
                $s .= sprintf('%s:%s:%s',
                    $this->parent->bundle->name,
                    $this->parent->name,
                    $this->parent_key
                );
                break;
            case 'belongsToMany':
                $s .= sprintf('%s:%s:%s via %s:%s',
                    $this->parent->bundle->name,
                    $this->parent->name,
                    $this->parent_key,
                    $this->pivot->bundle->name,
                    $this->pivot->name
                );
                break;
            case 'hasOne':
                $s .= sprintf('%s:%s:%s',
                    $this->child->bundle->name,
                    $this->child->name,
                    $this->foreign_key
                );
                break;
            case 'hasMany':
                $s .= sprintf('%s:%s:%s',
                    $this->child->bundle->name,
                    $this->child->name,
                    $this->foreign_key
                );
                break;
        }

        return $s;
    }

    /**
     * {@inheritdoc}
     */
    public function update() {
        if ($this->type == 'hasMany') {
            if ($this->keys('foreign')->unique) {
                $this->type = 'hasOne';
            }
        }
    }

    /**
     * {@inheritdoc}
     */

    public function validate()
    {

    }


}