<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

/**
 * Customized build model task that sets @subpackage to model\package\base
 *
 */
class orangehrmBuildModelTask extends sfDoctrineBuildModelTask {

    /**
     * @see sfTask
     */
    protected function configure() {
        $this->namespace = 'orangehrm';
        $this->name = 'build-model';        
    }

    /**
     * @see sfTask
     */
    protected function execute($arguments = array(), $options = array()) {
        $this->logSection('doctrine', 'generating model classes');

        $config = $this->getCliConfig();
        $builderOptions = $this->configuration->getPluginConfiguration('sfDoctrinePlugin')->getModelBuilderOptions();

        $stubFinder = sfFinder::type('file')->prune('base')->name('*' . $builderOptions['suffix']);
        $before = $stubFinder->in($config['models_path']);

        $schema = $this->prepareSchemaFile($config['yaml_schema_path']);

        $import = new Doctrine_Import_Schema();
        $import->setOptions($builderOptions);
        $import->importSchema($schema, 'yml', $config['models_path']);

        // markup base classes with magic methods
        foreach (sfYaml::load($schema) as $model => $definition) {
            

            $pluginName = isset($definition['package']) ? substr($definition['package'], 0, strpos($definition['package'], '.')) : '';
            $modelName = str_replace('orangehrm', '', $pluginName);
            $modelName = str_replace('Plugin', '', $modelName);
            if (!empty($modelName)) {
                $subPackageName = 'model\\' . strtolower($modelName) . '\\base';
            } else {
                $subPackageName = 'model\\base';
            }
            
            $file = sprintf('%s%s/%s/Base%s%s', $config['models_path'], isset($definition['package']) ? '/' . substr($definition['package'], 0, strpos($definition['package'], '.')) : '', $builderOptions['baseClassesDirectory'], $model, $builderOptions['suffix']);
            $code = file_get_contents($file);

            // introspect the model without loading the class
            if (preg_match_all('/@property (\w+) \$(\w+)/', $code, $matches, PREG_SET_ORDER)) {
                $properties = array();
                foreach ($matches as $match) {
                    $properties[$match[2]] = $match[1];
                }

                $typePad = max(array_map('strlen', array_merge(array_values($properties), array($model))));
                $namePad = max(array_map('strlen', array_keys(array_map(array('sfInflector', 'camelize'), $properties))));
                $setters = array();
                $getters = array();

                foreach ($properties as $name => $type) {
                    $camelized = sfInflector::camelize($name);
                    $collection = 'Doctrine_Collection' == $type;

                    $getters[] = sprintf('@method %-' . $typePad . 's %s%-' . ($namePad + 2) . 's Returns the current record\'s "%s" %s', $type, 'get', $camelized . '()', $name, $collection ? 'collection' : 'value');
                    $setters[] = sprintf('@method %-' . $typePad . 's %s%-' . ($namePad + 2) . 's Sets the current record\'s "%s" %s', $model, 'set', $camelized . '()', $name, $collection ? 'collection' : 'value');
                }

                // use the last match as a search string
                $code = str_replace($match[0], $match[0] . PHP_EOL . ' * ' . PHP_EOL . ' * ' . implode(PHP_EOL . ' * ', array_merge($getters, $setters)), $code);
                
                $tokens = array(
                    '##SUBPACKAGE##' => $subPackageName
                );
                $code = str_replace(array_keys($tokens), array_values($tokens), $code);
        
                file_put_contents($file, $code);
            }
        }

        $properties = parse_ini_file(sfConfig::get('sf_config_dir') . '/properties.ini', true);
        $tokens = array(
            '##PACKAGE##' => isset($properties['symfony']['name']) ? $properties['symfony']['name'] : 'symfony',
            '##SUBPACKAGE##' => 'model',
            '##NAME##' => isset($properties['symfony']['author']) ? $properties['symfony']['author'] : 'Your name here',
            ' <##EMAIL##>' => '',
            "{\n\n}" => "{\n}\n",
        );

        // cleanup new stub classes
        $after = $stubFinder->in($config['models_path']);
        $this->getFilesystem()->replaceTokens(array_diff($after, $before), '', '', $tokens);

        // cleanup base classes
        $baseFinder = sfFinder::type('file')->name('Base*' . $builderOptions['suffix']);
        $baseDirFinder = sfFinder::type('dir')->name('base');
        $this->getFilesystem()->replaceTokens($baseFinder->in($baseDirFinder->in($config['models_path'])), '', '', $tokens);

        $this->reloadAutoload();
    }

}

