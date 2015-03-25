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
class BaseMap extends BaseItem
{

  public $namespace;
  public $prefix;

  /**
   * @var Collection|BaseBundle[]
   */
  public $bundles;

  function __construct($name, $params = [])
  {
    parent::__construct($name, $params);

    $this->prefix = $name;

    $this->bundles = new Collection();

    return $this;
  }

  public function addBundle(BaseBundle $bundle)
  {
    if (!$this->bundles->containsKey($bundle->name)) {

      $bundle->map = $this;

      $this->bundles->set($bundle->name, $bundle);
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function dump()
  {

    $dump['Map:' . $this->name] = [];

    foreach ($this->bundles as $bundle) {
      $dump['Map:' . $this->name]['Bundle:' . $bundle->name] = $bundle->dump();
    }

    return $dump;
  }

  /**
   * {@inheritdoc}
   */
  public function update()
  {

    //self update

    foreach ($this->bundles as $bundle) {
      $bundle->update();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validate()
  {

    //self validate

    foreach ($this->bundles as $bundle) {
      $bundle->validate();
    }
  }

}