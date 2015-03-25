<?php namespace Foote\Ginny\Command;

/**
 * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
 *
 * (c) Matt Crowe <mattcrowe@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * This class is tasked with determining the options for GinnyCommand
 * according to the following hierarchy (the first taking precedence):
 *
 * 1. manual or CLI input
 * 2. user's config file, ie. ginny.yml
 * 3. GinnyCommand's defaults (if any, as provided in GinnyDefinition)
 *
 * @see GinnyDefinition
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class GinnyInput extends ArrayInput
{


  /**
   * Create a formatted array from $_SERVER['argv']
   * with full option name or shortcut
   *
   * ex: array(
   *  '--optionname' => 'option-value',
   *  '-s' => 'option-value',
   * )
   *
   * @return array
   */
  public static function getOptionsFromRequest()
  {

    $options = [];

    $argv = Request::createFromGlobals()->server->get('argv');

    // strip the application name
    array_shift($argv);

    foreach ($argv as $n => $arg) {

      //look for regular options, ie. "--root=my/root/path"
      if (substr($arg, 0, 2) == '--') {
        $bits = explode('=', $arg);
        $options[$bits[0]] = $bits[1];

        //else look for shortcut/value in a pair of array values, ie. "[ '-p', 'Admin']
      } elseif (substr($arg, 0, 1) == '-' && !empty($argv[$n + 1])) {

        //so if this one is the shortcut key,
        //then the next array value is the corresponding option.
        $options[$arg] = $argv[$n + 1];
      }
    }

    return $options;
  }


  /**
   * Merge options from $definition, $local_defaults and $passed
   *
   * Precedence: $passed > $local_defaults > $definition
   *
   * @param array() $local_defaults
   * @param array() $options
   *
   * @return \Symfony\Component\Console\Input\ArrayInput
   */
  public function __construct($local_defaults, $passed = null)
  {

    # if $passed is null, get and parse argv passed by CLI
    if (empty($passed)) {
      $passed = $this->getOptionsFromRequest();
    }

    # init array to construct ArrayInput
    $merged = [];

    $definition = new GinnyDefinition();

    foreach ($definition->getOptions() as $name => $option) {

      $merged['--' . $name] = $option->getDefault();

      // override if set in $local_defaults
      if (array_key_exists($name, $local_defaults)) {
        $merged['--' . $name] = $local_defaults[$name];
      }

      // override with shortcut in $passed
      if ($option->getShortcut()) {
        if (array_key_exists('-' . $option->getShortcut(), $passed)) {
          $merged['--' . $name] = $passed['-' . $option->getShortcut()];
        }
      }

      // likewise replace with fully-name option in $passed
      if (array_key_exists('--' . $name, $passed)) {
        $merged['--' . $name] = $passed['--' . $name];
      }

    }

    parent::__construct($merged);
  }

  /**
   * @return string
   */
  public function getFullSchemaPath()
  {
    return $this->getOption('root') . $this->getOption('schema_path') . $this->getOption('schema_filename');
  }

  /**
   * @return string
   */
  public function getFullTemplatePath()
  {
    return $this->getOption('root') . $this->getOption('template_path');
  }

  /**
   * @return string
   */
  public function getFullTargetPath()
  {
    return $this->getOption('root') . $this->getOption('target_path');
  }

  /**
   * Do some additional validation of options
   *
   * @throws \Exception
   * @return boolean
   */
  public function validate()
  {

    parent::validate();

//      s($this->getFullSchemaPath());
//      file_exists($this->getFullSchemaPath());
//      exit;

    # GinnyInputException::100
    if (strpos($this->getOption('schema_filename'), ',') === false && !file_exists($this->getFullSchemaPath())) {
      throw new \Foote\Ginny\Exception\GinnyInputException(
        'GinnyInput::getFullSchemaPath invalid: ' . $this->getFullSchemaPath(),
        100
      );
    }

    # GinnyInputException::101
    if (!file_exists($this->getFullTemplatePath())) {
      throw new \Foote\Ginny\Exception\GinnyInputException(
        'GinnyInput::getFullTemplatePath invalid: ' . $this->getFullTemplatePath(),
        101
      );
    }

    # GinnyInputException::102
    if (!file_exists($this->getFullTargetPath())) {
      throw new \Foote\Ginny\Exception\GinnyInputException(
        'GinnyInput::getFullTargetPath invalid: ' . $this->getFullTargetPath(),
        102
      );
    }

    # GinnyInputException::103
    if (!class_exists($this->getOption('generator_class'))) {
      throw new \Foote\Ginny\Exception\GinnyInputException(
        'GinnyInput::getOption(\'generator_class\') invalid: ' . $this->getOption('generator_class'),
        103
      );
    }

  }

}