<?php namespace Foote\Ginny\Package\Foote\Mapper;

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
class LaravelMapper
{

  public static function get($map)
  {

    foreach ($map->bundles as $bundle) {

      foreach ($bundle->models as $model) {
        $model->url = '';
        $model->route = '';

        if ($map->prefix) {
          $model->url .= '/backend/' . $map->snake;
          $model->route .= $map->snake . '.';
        }

        $model->url .= sprintf('/%s/%s', $model->bundle->snake, $model->snakes);

        $model->route .= sprintf('%s.%s', $model->bundle->snake,
          $model->snakes);

        $model->view .= sprintf('%s.%s.%s',
          $model->bundle->snake(),
          $model->snakes(),
          strtolower($map->prefix)
        );

      }
    }

    return $map;
  }

}