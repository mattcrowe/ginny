<?php namespace Foote\Ginny\Command;
/**
 * This file is part of the Ginny package: https://github.com/crowefoote/ginny
 *
 * (c) Matt Crowe <crowefoote@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * This class defines the arguments and options for GinnyCommand
 *
 * @author Matt Crowe <crowefoote@zym.me>
 */
class GinnyDefinition extends InputDefinition
{

    /**
     * Constructor
     */
    public function __construct()
    {

        $definition = [

            # Path Definitions
            new InputOption('root',             'r', 2, 'The application root.'),
            new InputOption('schema_path',      'z', 2, 'Path to schema files (relative to root).'),
            new InputOption('schema_filename',  'f', 2, 'Schema filename to be used as generation source.'),
            new InputOption('target_path',      't', 2, 'Path to save generated files (relative to root)'),
            new InputOption('template_path',    'v', 2, 'Templates for generated files.'),

            # Class Definitions
            new InputOption('generator_class',  'g', 2, 'Fully namespaced generator class.'),

            # Other Definitions
            new InputOption('bundle',           'b', 2, 'Name of loaded bundles(s) to generate, ex: "all" or "System" or "System:Content".'),
            new InputOption('model',            'm', 2, 'Name of loaded model(s) to generate, ex: "all" or "User" or "User:Role".'),
            new InputOption('subset',           's', 2, 'Function(s) to run, ex: "all" or "model:controller".'),
            new InputOption('extra',            'e', 2, 'Name of extra functions to run that are not specific to models, ex: "all".', "none"),
            new InputOption('prefix',           'p', 2, 'Name of routing prefix, ex: "Admin".'),

        ];

        parent::__construct($definition);
    }

}