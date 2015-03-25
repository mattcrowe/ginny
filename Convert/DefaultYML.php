<?php namespace Foote\Ginny\Convert;

  /**
   * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
   *
   * (c) Matt Crowe <mattcrowe@zym.me>
   *
   * For the full copyright and license information, please view the LICENSE
   * file that was distributed with this source code.
   */

/**
 * SkipperXML parses a Skipper xml file into usable data map for a generator class
 *
 * @author Matt Crowe <mattcrowe@zym.me>
 */

use Foote\Ginny\Command\GinnyInput;

class DefaultYML
{

  public static function convert(GinnyInput $input)
  {

    $path = $input->getOption('schema_path');

    $parser = new \Symfony\Component\Yaml\Parser();

    if (is_dir($path)) {

      $file = file_get_contents($path . 'Bundle.yml');
      $data = $parser->parse($file);

      $data['models'] = [];
      $data['associations'] = [];
      $data['manyToManys'] = [];

      $filenames = explode(',', $input->getOption('schema_filename'));

      foreach ($filenames as $filename) {

        $file = $parser->parse(file_get_contents($path . $filename . '.yml'));

        if (!empty($file['model'])) {

          $defaults = [
            'name' => '',
            'description' => '',
          ];

          $model = array_merge($defaults, $file['model']);

          $defaults = [
            'name' => '',
            'type' => '',
            'size' => '',
            'default' => false,
            'required' => false,
            'unique' => false,
            'primary' => false,
            'autoIncrement' => false,
          ];

          foreach($model['fields'] as $n => $field) {
            $model['fields'][$n] = array_merge($defaults, $field);
          }

          $data['models'][] = $model;
        }

        if (!empty($file['associations'])) {
          $data['associations'] = array_merge($data['associations'],
            $file['associations']);
        }

        if (!empty($file['manyToManys'])) {
          $data['manyToManys'] = array_merge($data['manyToManys'],
            $file['manyToManys']);
        }

      }

      return ['bundles' => [$data]];
    }

    echo 'booger!';
    exit;

  }

}