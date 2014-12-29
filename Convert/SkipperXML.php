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
class SkipperXML
{

    public static function convert($xml)
    {

        $xml = simplexml_load_string($xml);

        $map = [
            'bundles' => []
        ];

        foreach ($xml->xpath('module') as $_bundle) {

            $bundle = [];

            $bundle['name'] = (string)$_bundle['name'];

            $bundle['models'] = [];
            $bundle['associations'] = [];
            $bundle['manyToManys'] = [];

            foreach ($xml->xpath('module/entity') as $_model) {

                $model = [];

                $model['name'] = (string)$_model['name'];

                $model['description'] = (string)$_model['description'];

                if (!empty($_model->{'orm-attributes'})) {
                    foreach ($_model->{'orm-attributes'}->attribute as $n => $orm_attribute) {
                        $model['ormAttributes'][(string)$orm_attribute['name']] = (string)$orm_attribute;
                    }
                }

                $model['fields'] = [];
                foreach ($_model->xpath('field') as $_field) {

                    $field = [];

                    $field['name'] = (string)$_field['name'];
                    $field['type'] = (string)$_field['type'];
                    $field['size'] = (string)$_field['size'];
                    $field['default'] = (string)$_field['default'];
                    $field['required'] = (string)$_field['required'];
                    $field['unique'] = (string)$_field['unique'];
                    $field['primary'] = (string)$_field['primary'];
                    $field['autoIncrement'] = (string)$_field['auto-increment'];

                    $model['fields'][] = $field;
                }

                $bundle['models'][] = $model;
            }

            foreach ($xml->xpath('module/association') as $_association) {

                $association = [];

                $association['caption'] = (string)$_association['caption'];
                $association['from'] = (string)$_association['from'];
                $association['to'] = (string)$_association['to'];
                $association['ownerAlias'] = (string)$_association['owner-alias'];
                $association['inverseAlias'] = (string)$_association['inverse-alias'];
                $association['field']['from'] = (string)$_association->xpath('association-field')[0]['from'];
                $association['field']['to'] = (string)$_association->xpath('association-field')[0]['to'];

                $bundle['associations'][] = $association;
            }

            foreach ($xml->xpath('module/many-to-many') as $_manyToMany) {

                $manyToMany = [];

                $_manyToManyModels = $_manyToMany->xpath('many-to-many-entity');

                $manyToMany['name'] = (string)$_manyToMany['mn-entity'];
                $manyToMany['caption'] = (string)$_manyToMany['caption'];

                $manyToMany['owner'] = [
                    'name' => (string)$_manyToManyModels[0]['name'],
                    'alias' => (string)$_manyToManyModels[0]['alias'],
                    'field' => [
                        'from' => (string)$_manyToManyModels[0]->xpath('many-to-many-field')[0]['from'],
                        'to' => (string)$_manyToManyModels[0]->xpath('many-to-many-field')[0]['to'],
                    ]];

                $manyToMany['inverse'] = [
                    'name' => (string)$_manyToManyModels[1]['name'],
                    'alias' => (string)$_manyToManyModels[1]['alias'],
                    'field' => [
                        'from' => (string)$_manyToManyModels[1]->xpath('many-to-many-field')[0]['from'],
                        'to' => (string)$_manyToManyModels[1]->xpath('many-to-many-field')[0]['to'],

                    ]];

                $bundle['manyToManys'][] = $manyToMany;

            }

            $map['bundles'][] = $bundle;
        }

        return $map;
//        $yaml = new \Symfony\Component\Yaml\Yaml();
//        return $yaml->dump($map);
    }

}