<?php namespace Foote\Ginny\Command;
/**
 * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
 *
 * (c) Matt Crowe <mattcrowe@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;

use Symfony\Component\Console\Command\Command;

/**
 * Ginny Framework Code Generation Tool
 *
 * CLI examples:
 *
 * sudo php ginny System.skipper -e all -x all -s all -p Admin
 * sudo php ginny System.skipper --models=all --extra=all --subsets=all --prefix Admin
 *
 * CLI definition:
 *
 * @see \Foote\Ginny\Command\GinnyDefinition
 *
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class GinnyCommand extends Command
{

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('ginny:generate');
        $this->setDescription('generate MVC bundle for your favorite framework');
        $this->setDefinition(new GinnyDefinition());
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $genClass = $input->getOption('generator_class');

        /* @var $generator \Foote\Ginny\Generator\GeneratorInterface */
        $generator = new $genClass($input, $output);

        $generator->generate();

        $output->writeln('Generated! Well, hopefully...');
    }

}