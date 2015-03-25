<?php namespace Foote\Ginny\Generator;

  /**
   * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
   *
   * (c) Matt Crowe <mattcrowe@zym.me>
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */

/**
 * @author Matt Crowe <mattcrowe@zym.me>
 */
interface GeneratorInterface
{

  public function map();

  public function generate();

  public function loadTwig();

  public function extendTwig();

  public function doSubset($name);

  public function doExtra($name);

  public function doModel($name);

  public function doBundle($name);

  public function genSchema();

  public function genFixtures();

  public function genFolders();

  public function genModels();

  public function genControllers();

  public function genViews();

  public function genTests();

  public function save($filename, $data);

}