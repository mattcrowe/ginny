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
 * BaseBundle is meant primarily to be a collection of BaseModel.
 *
 * For convenience it also collects BaseAssociation.
 *
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class BaseBundle extends BaseItem
{

    /**
     * @var BaseMap
     */
    public $map;

    /**
     * @var Collection|BaseModel[]
     */
    public $models;

    function __construct($name, $params=[])
    {
        parent::__construct($name, $params);

        $this->models = new Collection();
    }

    public function addModel(BaseModel $model)
    {
        if (!$this->models->containsKey($model->name)) {

            $model->bundle = $this;

            $this->models->set($model->name, $model);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function dump() {

        $dump = [];

        foreach($this->models as $model) {
            $dump['Model:' . $model->name] = $model->dump();
        }

        return $dump;
    }

    /**
     * {@inheritdoc}
     */
    public function update() {

        //self update

        foreach($this->models as $model) {
            $model->update();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function validate() {

        //self validate

        foreach($this->models as $model) {
            $model->validate();
        }
    }

}