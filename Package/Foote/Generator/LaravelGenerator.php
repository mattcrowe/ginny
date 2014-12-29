<?php namespace Foote\Ginny\Package\Foote\Generator;
/**
 * This file is part of the Ginny package: https://github.com/mattcrowe/ginny
 *
 * (c) Matt Crowe <mattcrowe@zym.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Foote\Ginny\Generator\BaseGenerator;
use Foote\Ginny\Map\BaseMapper;
use Foote\Ginny\Package\Foote\Mapper\LaravelMapper;

/**
 * @author Matt Crowe <mattcrowe@zym.me>
 */
class LaravelGenerator extends BaseGenerator
{

    public function map()
    {

        $xml = file_get_contents($this->schema);

        $schema = \Foote\Ginny\Convert\SkipperXML::convert($xml);

        $mapper = new BaseMapper($this->input);
        $mapper->schema = json_decode(json_encode($schema));

        return LaravelMapper::get($mapper->map());
    }

    public function extendTwig()
    {
        $this->twig->addExtension(new \Foote\Ginny\Twig\UIExtension());
    }

    public function genFolders()
    {

        $path = sprintf('%s%s', $this->target, $this->bundle->name);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $folders = [
            'Model',
            'Controller/[Prefix]',
            'View/[Prefix]',
            'database',
            'database/migrations',
            'database/seeders',
            'Tests/Model',
            'Tests/Controller/[Prefix]',
        ];

        foreach ($folders as $folder) {

            $folder = str_replace('[Prefix]', $this->map->name, $folder);

            $path = sprintf('%s%s/%s', $this->target, $this->bundle->name, $folder);

            /**
             * is_dir() && file_exists() can return true on recently deleted folders
             */
            if (!@opendir($path)) {
                mkdir($path, 0777, true);
                $this->output->writeln('folder added: ' . $path);
            }
        }

        foreach ($this->bundle->models as $model) {

            if (!$this->doModel($model->name)) {
                continue;
            }

            $path = sprintf('%s%s/View/%s/%s', $this->target, $this->bundle->name, $this->map->name, $model->name);

            if (!@opendir($path)) {
                mkdir($path, 0777, true);
                $this->output->writeln('folder added: ' . $path);
            }
        }

    }

    public function genSchema()
    {
        foreach ($this->bundle->models as $model) {

            if (!$this->doModel($model->name)) {
                continue;
            }

            $content = $this->render('database/migrations/create', [
                'model' => $model,
            ]);

            $target_filename = sprintf('%s%s/database/migrations/%s_%s_create_%s_%s.php',
                $this->target,
                $model->bundle,
                date('Y_m_d'),
                '999999', //substr(microtime(), 2, 6),
                $model->bundle->snake,
                $model->snake
            );

            $this->save($target_filename, $content);

            $this->output->writeln('migration added: ' . $target_filename);
        }
    }

    public function genFixtures()
    {

        if ($this->doExtra('seeder')) {
            $content = $this->render('database/seeders/SeederList', [
                'bundle' => $this->bundle
            ]);

            $target_filename = sprintf('%s%s/database/seeders/%sDatabaseSeeder.php',
                $this->target,
                $this->bundle->name,
                $this->bundle->name
            );

            $this->save($target_filename, $content);

            $this->output->writeln('seeder added: ' . $target_filename);
        }

        foreach ($this->bundle->models as $model) {

            if (!$this->doModel($model->name)) {
                continue;
            }

            if ($model->manyToMany) {
                $content = $this->render('database/seeders/ManyToManySeeder', [
                    'model' => $model
                ]);
            } else {
                $content = $this->render('database/seeders/Seeder', [
                    'model' => $model
                ]);
            }

            $target_filename = sprintf('%s%s/database/seeders/%s%sSeeder.php',
                $this->target,
                $model->bundle,
                $model->bundle->name,
                $model->name
            );

            $this->save($target_filename, $content);

            $this->output->writeln('seeder added: ' . $target_filename);
        }
    }

    public function genModels()
    {

        foreach ($this->bundle->models as $model) {

            if (!$this->doModel($model->name)) {
                continue;
            }

            $content = $this->render('Model/Model', [
                'model' => $model
            ]);

            $target_filename = sprintf('%s%s/Model/%s.php',
                $this->target,
                $model->bundle,
                $model->name
            );

            $this->save($target_filename, $content);

            $this->output->writeln('model added: ' . $target_filename);
        }
    }

    public function genControllers()
    {
        foreach ($this->bundle->models as $model) {

            if (!$this->doModel($model->name)) {
                continue;
            }

            $content_path = "Controller/{$this->map->name}/Controller";

            $content = $this->render($content_path, [
                'bundle' => $this->bundle,
                'model' => $model,
            ]);

//            if ($model->owner()) {
//                $content_path = "Controller/{$this->map->name}/NestedController";
//                $content = $this->render($content_path, [
//                    'bundle' => $this->bundle,
//                    'model' => $model
//                ]);
//            }

            $target_filename = sprintf('%s%s/Controller/%s/%sController.php',
                $this->target,
                $model->bundle,
                $this->map->name,
                $model->plural
            );

            $this->save($target_filename, $content);

            $this->output->writeln('controller added: ' . $target_filename);
        }
    }

    public function genViews()
    {

        foreach ($this->bundle->models as $model) {

            if (!$this->doModel($model->name)) {
                continue;
            }

            $target = sprintf('%s%s/View/%s/%s',
                $this->target,
                $model->bundle,
                $this->map->name,
                $model->name
            );

            $content_path = "View/{$this->map->name}/";

            if ($this->doSubset('view-index') || $this->doSubset('views-all')) {
                $content = $this->render($content_path . 'index', [
                    'model' => $model
                ]);

                $target_filename = $target . '/index.blade.php';
                $this->save($target_filename, $content);
                $this->output->writeln('views added ' . $target_filename);
            }


            if ($this->doSubset('view-create') || $this->doSubset('views-all')) {
                $content = $this->render($content_path . 'create', [
                    'model' => $model
                ]);

                $target_filename = $target . '/create.blade.php';
                $this->save($target_filename, $content);
                $this->output->writeln('views added ' . $target_filename);
            }


            if ($this->doSubset('view-show') || $this->doSubset('views-all')) {
                $content = $this->render($content_path . 'show', [
                    'model' => $model
                ]);

                $target_filename = $target . '/show.blade.php';
                $this->save($target_filename, $content);
                $this->output->writeln('views added ' . $target_filename);
            }


            if ($this->doSubset('view-edit') || $this->doSubset('views-all')) {
                $content = $this->render($content_path . 'edit', [
                    'model' => $model
                ]);

                $target_filename = $target . '/edit.blade.php';
                $this->save($target_filename, $content);
                $this->output->writeln('views added ' . $target_filename);
            }

        }

    }

    public function genTests()
    {

        if ($this->doSubset('test-config') || $this->doSubset('tests-all')) {

            $content = $this->render("phpunit", [
                'map' => $this->map
            ]);

            $target_filename = sprintf('%s%s/phpunit.xml',
                $this->target,
                $this->bundle->name
            );

            $this->save($target_filename, $content);
            $this->output->writeln('test config added ' . $target_filename);
        }

        foreach ($this->bundle->models as $model) {

            if (!$this->doModel($model->name)) {
                continue;
            }

            $target = sprintf('%s%s/Tests',
                $this->target,
                $model->bundle
            );

            if ($this->doSubset('test-model') || $this->doSubset('tests-all')) {

                $content = $this->render("Tests/Model/Model", [
                    'model' => $model
                ]);

                $target_filename = sprintf('%s/Model/%sTest.php',
                    $target,
                    $model->name
                );

                $this->save($target_filename, $content);
                $this->output->writeln('test added ' . $target_filename);
            }

            if ($this->doSubset('test-controller') || $this->doSubset('tests-all')) {

                $content = $this->render("Tests/Controller/{$this->map->name}/Controller", [
                    'model' => $model
                ]);

                $target_filename = sprintf('%s/Controller/%s/%sControllerTest.php',
                    $target,
                    $this->map->name,
                    $model->plural
                );

                $this->save($target_filename, $content);
                $this->output->writeln('test added ' . $target_filename);
            }

        }

    }

}