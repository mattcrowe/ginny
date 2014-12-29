<?php namespace Foote\Ginny\Generator;
/**
 * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
 *
 * (c) Matt Crowe <mattcrowe@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Foote\Ginny\Command\GinnyInput;
use Foote\Ginny\Map\BaseBundle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Foote\Ginny\Map\BaseMapper;

/**
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class BaseGenerator implements GeneratorInterface
{

    /**
     * @var \Foote\Ginny\Map\BaseMap
     */
    public $map;

    /**
     * @var \Foote\Ginny\Map\BaseBundle
     */
    public $bundle;

    /**
     * @var InputInterface
     */
    public $input;

    /**
     * @var OutputInterface
     */
    public $output;

    /**
     * @var \Twig_Environment
     */
    public $twig;

    public $prefix;
    public $target;
    public $template;
    public $schema;

    public function __construct(GinnyInput $input, OutputInterface $output)
    {

        $this->input = $input;
        $this->output = $output;

        $this->target = $input->getFullTargetPath();
        $this->template = $input->getFullTemplatePath();
        $this->schema = $input->getFullSchemaPath();

        $this->map = $this->map();

//        s($this->map->dump());
//        exit;

        $this->loadTwig();
    }

    public function loadTwig()
    {
        $this->twig = new \Twig_Environment(new \Twig_Loader_String(), [
            'debug' => true,
            'autoescape' => false,
        ]);

        $this->twig->addExtension(new \Twig_Extension_Debug());
        $this->twig->addExtension(new \Foote\Ginny\Twig\InflectorExtension());
        $this->twig->addExtension(new \Foote\Ginny\Twig\GinnyExtension());

        $this->extendTwig();
    }

    public function extendTwig()
    {

    }

    public function map()
    {

        $xml = file_get_contents($this->schema);

        $schema = \Foote\Ginny\Convert\SkipperXML::convert($xml);

        $mapper = new BaseMapper($this->input);
        $mapper->schema = json_decode(json_encode($schema));

        return $mapper->map();
    }

    private function doOption($option, $name)
    {
        $options = explode(':', $this->input->getOption($option));

        if (in_array('all', $options)) {
            return true;
        }

        if (in_array($name, $options)) {
            return true;
        }

        return false;
    }

    public function doSubset($name)
    {
        return $this->doOption('subset', $name);
    }

    public function doExtra($name)
    {
        return $this->doOption('extra', $name);
    }

    public function doModel($name)
    {
        return $this->doOption('model', $name);
    }

    public function doBundle($name)
    {
        return $this->doOption('bundle', $name);
    }

    public function render($view, $parameters)
    {

        $path = sprintf('%s%s.php.twig', $this->template, $view);

        $content = file_get_contents($path);

        return $this->twig->render($content, $parameters);
    }

    public function save($filename, $data) {

        if (file_exists($filename)) {
            $contents = file_get_contents($filename);
            if (strpos($contents, '#KEEP!') !== false) {
                return false;
            }
        }

        return file_put_contents($filename, $data);
    }

    public function generate()
    {

        foreach ($this->map->bundles as $bundle) {

            if (!$this->doBundle($bundle->name)) {
                continue;
            }

            $this->bundle = $bundle;

            if ($this->doSubset('folders')) {
                $this->genFolders();
            }

            if ($this->doSubset('schema')) {
                $this->genSchema();
            }

            if ($this->doSubset('models')) {
                $this->genModels();
            }

            if ($this->doSubset('fixtures')) {
                $this->genFixtures();
            }

            if ($this->doSubset('controllers')) {
                $this->genControllers();
            }

            if ($this->doSubset('views') || $this->doSubset('views-all')) {
                $this->genViews();
            }

            if ($this->doSubset('tests') || $this->doSubset('tests-all')) {
                $this->genTests();
            }

        }

    }

    public function genSchema()
    {

    }

    public function genFixtures()
    {

    }

    public function genFolders()
    {

    }

    public function genModels()
    {

    }

    public function genControllers()
    {

    }

    public function genViews()
    {

    }

    public function genTests()
    {

    }

}