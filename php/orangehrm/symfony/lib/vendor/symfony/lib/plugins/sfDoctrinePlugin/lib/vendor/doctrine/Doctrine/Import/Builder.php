<?php
/*
 *  $Id: Builder.php 5818 2009-06-03 19:45:58Z jwage $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.phpdoctrine.org>.
 */

/**
 * Doctrine_Import_Builder
 *
 * Import builder is responsible of building Doctrine_Record classes
 * based on a database schema.
 *
 * @package     Doctrine
 * @subpackage  Import
 * @link        www.phpdoctrine.org
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @since       1.0
 * @version     $Revision: 5818 $
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @author      Jukka Hassinen <Jukka.Hassinen@BrainAlliance.com>
 * @author      Nicolas Bérard-Nault <nicobn@php.net>
 * @author      Jonathan H. Wage <jwage@mac.com>
 */
class Doctrine_Import_Builder extends Doctrine_Builder
{
    /**
     * _path
     *
     * the path where imported files are being generated
     *
     * @var string $_path
     */
    protected $_path = '';

    /**
     * _packagesPrefix
     *
     * @var string
     */
    protected $_packagesPrefix = 'Package';

    /**
     * _packagesPath
     *
     * @var string
     */
    protected $_packagesPath = '';

    /**
     * _packagesFolderName
     *
     * @var string
     */
    protected $_packagesFolderName = 'packages';

    /**
     * _suffix
     *
     * File suffix to use when writing class definitions
     *
     * @var string $suffix
     */
    protected $_suffix = '.php';

    /**
     * _generateBaseClasses
     *
     * Bool true/false for whether or not to generate base classes
     *
     * @var boolean $generateBaseClasses
     */
    protected $_generateBaseClasses = true;

    /**
     * _generateTableClasses
     *
     * Bool true/false for whether or not to generate child table classes
     *
     * @var boolean $generateTableClasses
     */
    protected $_generateTableClasses = false;

    /**
     * _base
     *
     * Prefix to use for generated base classes
     *
     * @var string
     */
    protected $_baseClassPrefix = 'Base';

    /**
     * _baseClassesDirectory
     *
     * Directory to put the generate base classes in
     *
     * @var string $suffix
     */
    protected $_baseClassesDirectory = 'generated';

    /**
     * _baseClassName
     *
     * @var string
     */
    protected $_baseClassName = 'Doctrine_Record';

    /**
     * _generateAccessors
     *
     * @var boolean $generateAccessors
     */
    protected $_generateAccessors = false;

    /**
     * _tpl
     *
     * Class template used for writing classes
     *
     * @var $_tpl
     */
    protected static $_tpl;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->loadTemplate();
    }

    /**
     * setTargetPath
     *
     * @param string path   the path where imported files are being generated
     * @return
     */
    public function setTargetPath($path)
    {
        if ($path) {
            if ( ! $this->_packagesPath) {
                $this->setPackagesPath($path . DIRECTORY_SEPARATOR . $this->_packagesFolderName);
            }

            $this->_path = $path;
        }
    }

    /**
     * setPackagePath
     *
     * @param string $packagesPrefix
     * @return void
     */
    public function setPackagesPrefix($packagesPrefix)
    {
        $this->_packagesPrefix = $packagesPrefix;
    }

    /**
     * setPackagesPath
     *
     * @param string $packagesPath
     * @return void
     */
    public function setPackagesPath($packagesPath)
    {
        if ($packagesPath) {
            $this->_packagesPath = $packagesPath;
        }
    }

    /**
     * generateBaseClasses
     *
     * Specify whether or not to generate classes which extend from generated base classes
     *
     * @param  boolean $bool
     * @return boolean $bool
     */
    public function generateBaseClasses($bool = null)
    {
        if ($bool !== null) {
            $this->_generateBaseClasses = $bool;
        }

        return $this->_generateBaseClasses;
    }

    /**
     * generateTableClasses
     *
     * Specify whether or not to generate children table classes
     *
     * @param  boolean $bool
     * @return boolean $bool
     */
    public function generateTableClasses($bool = null)
    {
        if ($bool !== null) {
            $this->_generateTableClasses = $bool;
        }

        return $this->_generateTableClasses;
    }

    /**
     * Generate physical accessors for columns and relationships
     *
     * @param boolean $bool
     * @return boolean $generateAccessors
     */
    public function generateAccessors($bool = null)
    {
      if ($bool !== null) {
          $this->_generateAccessors = $bool;
      }

      return $this->_generateAccessors;
    }

    /**
     * setBaseClassPrefix
     *
     * @param string $prefix
     * @return void
     */
    public function setBaseClassPrefix($prefix)
    {
        $this->_baseClassPrefix = $prefix;
    }

    /**
     * getBaseClassPrefix
     *
     * @return void
     */
    public function getBaseClassPrefix()
    {
        return $this->_baseClassPrefix;
    }

    /**
     * setBaseClassesDirectory
     *
     * @return void
     */
    public function setBaseClassesDirectory($baseClassesDirectory)
    {
        $this->_baseClassesDirectory = $baseClassesDirectory;
    }

    /**
     * setBaseClassName
     *
     * @package default
     */
    public function setBaseClassName($className)
    {
        $this->_baseClassName = $className;
    }

    /**
     * setSuffix
     *
     * @param string $suffix
     * @return void
     */
    public function setSuffix($suffix)
    {
        $this->_suffix = $suffix;
    }

    /**
     * getTargetPath
     *
     * @return string       the path where imported files are being generated
     */
    public function getTargetPath()
    {
        return $this->_path;
    }

    /**
     * setOptions
     *
     * @param string $options
     * @return void
     */
    public function setOptions($options)
    {
        if ( ! empty($options)) {
            foreach ($options as $key => $value) {
                $this->setOption($key, $value);
            }
        }
    }

    /**
     * setOption
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function setOption($key, $value)
    {
        $name = 'set' . Doctrine_Inflector::classify($key);

        if (method_exists($this, $name)) {
            $this->$name($value);
        } else {
            $key = '_' . $key;
            $this->$key = $value;
        }
    }

    /**
     * loadTemplate
     *
     * Loads the class template used for generating classes
     *
     * @return void
     */
    public function loadTemplate()
    {
        if (isset(self::$_tpl)) {
            return;
        }

        self::$_tpl = '/**' . PHP_EOL
                    . ' * This class has been auto-generated by the Doctrine ORM Framework' . PHP_EOL
                    . ' */' . PHP_EOL
                    . '%sclass %s extends %s' . PHP_EOL
                    . '{'
                    . '%s' . PHP_EOL
                    . '%s' . PHP_EOL
                    . '}';
    }

    /*
     * Build the table definition of a Doctrine_Record object
     *
     * @param  string $table
     * @param  array  $tableColumns
     */
    public function buildTableDefinition(array $definition)
    {
        if (isset($definition['inheritance']['type']) && ($definition['inheritance']['type'] == 'simple' || $definition['inheritance']['type'] == 'column_aggregation')) {
            return;
        }

        $ret = array();

        $i = 0;

        if (isset($definition['inheritance']['type']) && $definition['inheritance']['type'] == 'concrete') {
            $ret[$i] = "        parent::setTableDefinition();";
            $i++;
        }

        if (isset($definition['tableName']) && !empty($definition['tableName'])) {
            $ret[$i] = "        ".'$this->setTableName(\''. $definition['tableName'].'\');';
            $i++;
        }

        if (isset($definition['columns']) && is_array($definition['columns']) && !empty($definition['columns'])) {
            $ret[$i] = $this->buildColumns($definition['columns']);
            $i++;
        }

        if (isset($definition['indexes']) && is_array($definition['indexes']) && !empty($definition['indexes'])) {
            $ret[$i] = $this->buildIndexes($definition['indexes']);
            $i++;
        }

        if (isset($definition['attributes']) && is_array($definition['attributes']) && !empty($definition['attributes'])) {
            $ret[$i] = $this->buildAttributes($definition['attributes']);
            $i++;
        }

        if (isset($definition['options']) && is_array($definition['options']) && !empty($definition['options'])) {
            $ret[$i] = $this->buildOptions($definition['options']);
            $i++;
        }

        if (isset($definition['checks']) && is_array($definition['checks']) && !empty($definition['checks'])) {
            $ret[$i] = $this->buildChecks($definition['checks']);
            $i++;
        }

        if (isset($definition['inheritance']['subclasses']) && ! empty($definition['inheritance']['subclasses'])) {
            $ret[$i] = "        ".'$this->setSubClasses('. $this->varExport($definition['inheritance']['subclasses']).');';
            $i++;
        }

        $code = implode(PHP_EOL, $ret);
        $code = trim($code);

        return PHP_EOL . "    public function setTableDefinition()" . PHP_EOL . '    {' . PHP_EOL . '        ' . $code . PHP_EOL . '    }';
    }

    /**
     * buildSetUp
     *
     * @param  array $options
     * @param  array $columns
     * @param  array $relations
     * @return string
     */
    public function buildSetUp(array $definition)
    {
        $ret = array();
        $i = 0;

        if (isset($definition['relations']) && is_array($definition['relations']) && ! empty($definition['relations'])) {
            foreach ($definition['relations'] as $name => $relation) {
                $class = isset($relation['class']) ? $relation['class']:$name;
                $alias = (isset($relation['alias']) && $relation['alias'] !== $relation['class']) ? ' as ' . $relation['alias'] : '';

                if ( ! isset($relation['type'])) {
                    $relation['type'] = Doctrine_Relation::ONE;
                }

                if ($relation['type'] === Doctrine_Relation::ONE) {
                    $ret[$i] = "        ".'$this->hasOne(\'' . $class . $alias . '\'';
                } else {
                    $ret[$i] = "        ".'$this->hasMany(\'' . $class . $alias . '\'';
                }

                $a = array();

                if (isset($relation['refClass'])) {
                    $a[] = '\'refClass\' => ' . $this->varExport($relation['refClass']);
                }
                
                if (isset($relation['refClassRelationAlias'])) {
                    $a[] = '\'refClassRelationAlias\' => ' . $this->varExport($relation['refClassRelationAlias']);
                }
                
                if (isset($relation['deferred']) && $relation['deferred']) {
                    $a[] = '\'default\' => ' . $this->varExport($relation['deferred']);
                }

                if (isset($relation['local']) && $relation['local']) {
                    $a[] = '\'local\' => ' . $this->varExport($relation['local']);
                }

                if (isset($relation['foreign']) && $relation['foreign']) {
                    $a[] = '\'foreign\' => ' . $this->varExport($relation['foreign']);
                }

                if (isset($relation['onDelete']) && $relation['onDelete']) {
                    $a[] = '\'onDelete\' => ' . $this->varExport($relation['onDelete']);
                }

                if (isset($relation['onUpdate']) && $relation['onUpdate']) {
                    $a[] = '\'onUpdate\' => ' . $this->varExport($relation['onUpdate']);
                }

                if (isset($relation['cascade']) && $relation['cascade']) {
                    $a[] = '\'cascade\' => ' . $this->varExport($relation['cascade']);
                }

                if (isset($relation['equal']) && $relation['equal']) {
                    $a[] = '\'equal\' => ' . $this->varExport($relation['equal']);
                }

                if (isset($relation['owningSide']) && $relation['owningSide']) {
                    $a[] = '\'owningSide\' => ' . $this->varExport($relation['owningSide']);
                }

                if ( ! empty($a)) {
                    $ret[$i] .= ', ' . 'array(' . PHP_EOL . str_repeat(' ', 13);
                    $length = strlen($ret[$i]);
                    $ret[$i] .= implode(',' . PHP_EOL . str_repeat(' ', 13), $a) . ')';
                }

                $ret[$i] .= ');'.PHP_EOL;
                $i++;
            }
        }

        if (isset($definition['actAs']) && is_array($definition['actAs']) && !empty($definition['actAs'])) {
            $ret[$i] = $this->buildActAs($definition['actAs']);
            $i++;
        }

        if (isset($definition['listeners']) && is_array($definition['listeners']) && !empty($definition['listeners'])) {
            $ret[$i] = $this->buildListeners($definition['listeners']);
            $i++;
        }

        $code = implode(PHP_EOL, $ret);
        $code = trim($code);

        // If the body of the function has contents then we need to 
        if ($code) {
            // If the body of the function has contents and we are using inheritance
            // then we need call the parent::setUp() before the body of the function
            // Class table inheritance is the only one we shouldn't call parent::setUp() for
            if ($code && isset($definition['inheritance']['type']) && $definition['inheritance']['type'] != 'class_table') {
                $code = "parent::setUp();" . PHP_EOL . '    ' . $code;
            }
        }

        // If we have some code for the function then lets define it and return it
        if ($code) {
            return '    public function setUp()' . PHP_EOL . '    {' . PHP_EOL . '        ' . $code . PHP_EOL . '    }';
        }
    }

    /**
     * Build php code for record checks
     *
     * @param array $checks
     * @return string $build
     */
    public function buildChecks($checks)
    {
        $build = '';
        foreach ($checks as $check) {
            $build .= "        \$this->check('" . $check . "');" . PHP_EOL;
        }
        return $build;
    }

    /**
     * buildColumns
     *
     * @param string $array
     * @return void
     */
    public function buildColumns(array $columns)
    {
        $build = null;
        foreach ($columns as $name => $column) {
            $columnName = isset($column['name']) ? $column['name']:$name;
            $build .= "        ".'$this->hasColumn(\'' . $columnName . '\', \'' . $column['type'] . '\'';

            if ($column['length']) {
                $build .= ', ' . $column['length'];
            } else {
                $build .= ', null';
            }

            $options = $column;

            // Remove name, alltypes, ntype. They are not needed in options array
            unset($options['name']);
            unset($options['alltypes']);
            unset($options['ntype']);

            // Remove notnull => true if the column is primary
            // Primary columns are implied to be notnull in Doctrine
            if (isset($options['primary']) && $options['primary'] == true && (isset($options['notnull']) && $options['notnull'] == true)) {
                unset($options['notnull']);
            }

            // Remove default if the value is 0 and the column is a primary key
            // Doctrine defaults to 0 if it is a primary key
            if (isset($options['primary']) && $options['primary'] == true && (isset($options['default']) && $options['default'] == 0)) {
                unset($options['default']);
            }

            // These can be removed if they are empty. They all default to a false/0/null value anyways
            $remove = array('fixed', 'primary', 'notnull', 'autoincrement', 'unsigned');
            foreach ($remove as $key) {
                if (isset($options[$key]) && empty($options[$key])) {
                    unset($options[$key]);
                }
            }

            // Remove null and empty array values
            foreach ($options as $key => $value) {
                if (is_null($value) || (is_array($value) && empty($value))) {
                    unset($options[$key]);
                }
            }

            if (is_array($options) && !empty($options)) {
                $build .= ', ' . $this->varExport($options);
            }

            $build .= ');' . PHP_EOL;
        }

        return $build;
    }

    /**
     * emit a behavior assign
     *
     * @param int $level
     * @param string $name
     * @param string $option
     * @return string assignation code
     */
    private function emitAssign($level, $name, $option)
    {
        // find class matching $name
        $classname = $name;
        if (class_exists("Doctrine_Template_$name", true)) {
            $classname = "Doctrine_Template_$name";
        }
        return "        \$" . strtolower($name) . "$level = new $classname($option);". PHP_EOL;
    }

    /**
     * emit an addChild
     *
     * @param int $level
     * @param string $name
     * @param string $option
     * @return string addChild code
     */
    private function emitAddChild($level, $parent, $name)
    {
        return "        \$" . strtolower($parent) . ($level - 1) . "->addChild(\$" . strtolower($name) . "$level);" . PHP_EOL;
    }

    /**
     * emit an indented actAs
     *
     * @param int $level
     * @param string $name
     * @param string $option
     * @return string actAs code
     */
    private function emitActAs($level, $name)
    {
        return "        \$this->actAs(\$" . strtolower($name) . "$level);" . PHP_EOL;
    }


    /**
     * buildActAs: builds a complete actAs code. It supports hierarchy of plugins
     * @param array $actAs array of plugin definitions and options
     */
    public function buildActAs($actAs)
    {
        $emittedActAs = array();
        $build = $this->innerBuildActAs($actAs, 0, null, $emittedActAs);
        foreach($emittedActAs as $str) {
            $build .= $str;
        }
        return $build;
    }

    /**
     * innerBuildActAs: build a complete actAs code that handles hierarchy of plugins
     *
     * @param array  $actAs array of plugin definitions and options
     * @param int    $level current indentation level
     * @param string $parent name of the parent template/plugin
     * @param array  $emittedActAs contains on output an array of actAs command to be appended to output
     * @return string actAs full definition
     */
    private function innerBuildActAs($actAs, $level = 0, $parent = null, array &$emittedActAs)
    {
        // rewrite special case of actAs: [Behavior] which gave [0] => Behavior
        if(is_array($actAs) && isset($actAs[0]) && !is_array($actAs[0])) {
            $actAs = array_flip($actAs);
        }

        $build = '';
        $currentParent = $parent;
        if(is_array($actAs)) {
            foreach($actAs as $template => $options) {
                if ($template == 'actAs') {
                    // found another actAs
                    $build .= $this->innerBuildActAs($options, $level + 1, $parent, $emittedActAs);
                } else if (is_array($options)) {
                    // remove actAs from options
                    $realOptions = array();
                    $leftActAs = array();
                    foreach($options as $name => $value) {
                        if ($name != 'actAs') {
                            $realOptions[$name] = $options[$name];
                        } else {
                            $leftActAs[$name] = $options[$name];
                        }
                    } 

                    $optionPHP = $this->varExport($realOptions);
                    $build .= $this->emitAssign($level, $template, $optionPHP); 
                    if ($level == 0) {
                        $emittedActAs[] = $this->emitActAs($level, $template);
                    } else {
                        $build .= $this->emitAddChild($level, $currentParent, $template);
                    }
                    // descend for the remainings actAs
                    $parent = $template;            
                    $build .= $this->innerBuildActAs($leftActAs, $level, $template, $emittedActAs);
                } else {
                    $build .= $this->emitAssign($level, $template, null);
                    if ($level == 0) {
                        $emittedActAs[] = $this->emitActAs($level, $template);
                    } else {
                        $build .= $this->emitAddChild($level, $currentParent, $template);
                    }
                    $parent = $template;            
                }
            }
        } else {
            $build .= $this->emitAssign($level, $actAs, null);
            if ($level == 0) {
                $emittedActAs[] = $this->emitActAs($level, $actAs);
            } else {
                $build .= $this->emitAddChild($level, $currentParent, $actAs);
            }
        }

        return $build;
    }

    /**
     * Build php code for adding record listeners
     *
     * @param string $listeners 
     * @return string $build
     */
    public function buildListeners($listeners)
    {
        $build = '';
        foreach($listeners as $listener) {
            $build .= "        \$this->addListener(new " . $listener . "());" . PHP_EOL;
        }

        return $build;
    }

    /**
     * buildAttributes
     *
     * @param string $array
     * @return void
     */
    public function buildAttributes(array $attributes)
    {
        $build = PHP_EOL;
        foreach ($attributes as $key => $value) {

            $values = array();
            if (is_bool($value))
            {
              $values[] = $value ? 'true':'false';
            } else {
                if ( ! is_array($value)) {
                    $value = array($value);
                }

                foreach ($value as $attr) {
                    $const = "Doctrine::" . strtoupper($key) . "_" . strtoupper($attr);
                    if (defined($const)) {
                        $values[] = $const;
                    } else {
                        $values[] = "'" . $attr . "'";
                    }
                }
            }

            $string = implode(' ^ ', $values);
            $build .= "        \$this->setAttribute(Doctrine::ATTR_" . strtoupper($key) . ", " . $string . ");" . PHP_EOL;
        }

        return $build;
    }

    /**
     * buildTableOptions
     *
     * @param string $array
     * @return void
     */
    public function buildOptions(array $options)
    {
        $build = '';
        foreach ($options as $name => $value) {
            $build .= "        \$this->option('$name', " . $this->varExport($value) . ");" . PHP_EOL;
        }

        return $build;
    }

    /**
     * buildIndexes
     *
     * @param string $array
     * @return void
     */
    public function buildIndexes(array $indexes)
    {
      $build = '';

      foreach ($indexes as $indexName => $definitions) {
          $build .= PHP_EOL . "        \$this->index('" . $indexName . "'";
          $build .= ', ' . $this->varExport($definitions);
          $build .= ');';
      }

      return $build;
    }

    /**
     * buildDefinition
     *
     * @param array $definition
     * @return string
     */
    public function buildDefinition(array $definition)
    {
        if ( ! isset($definition['className'])) {
            throw new Doctrine_Import_Builder_Exception('Missing class name.');
        }

        $abstract = isset($definition['abstract']) && $definition['abstract'] === true ? 'abstract ':null;
        $className = $definition['className'];
        $extends = isset($definition['inheritance']['extends']) ? $definition['inheritance']['extends']:$this->_baseClassName;

        if ( ! (isset($definition['no_definition']) && $definition['no_definition'] === true)) {
            $tableDefinitionCode = $this->buildTableDefinition($definition);
            $setUpCode = $this->buildSetUp($definition);
        } else {
            $tableDefinitionCode = null;
            $setUpCode = null;
        }

        if ($tableDefinitionCode && $setUpCode) {
            $setUpCode = PHP_EOL . $setUpCode;
        }

        $content = sprintf(self::$_tpl, $abstract,
                                       $className,
                                       $extends,
                                       $tableDefinitionCode,
                                       $setUpCode);

        return $content;
    }

    /**
     * buildRecord
     *
     * @param array $options
     * @param array $columns
     * @param array $relations
     * @param array $indexes
     * @param array $attributes
     * @param array $templates
     * @param array $actAs
     * @return void=
     */
    public function buildRecord(array $definition)
    {
        if ( ! isset($definition['className'])) {
            throw new Doctrine_Import_Builder_Exception('Missing class name.');
        }

        $definition['topLevelClassName'] = $definition['className'];

        if ($this->generateBaseClasses()) {
            $definition['is_package'] = (isset($definition['package']) && $definition['package']) ? true:false;

            if ($definition['is_package']) {
                $e = explode('.', trim($definition['package']));
                $definition['package_name'] = $e[0];

                $definition['package_path'] = ! empty($e) ? implode(DIRECTORY_SEPARATOR, $e):$definition['package_name'];
            }
            // Top level definition that extends from all the others
            $topLevel = $definition;
            unset($topLevel['tableName']);

            // If we have a package then we need to make this extend the package definition and not the base definition
            // The package definition will then extends the base definition
            $topLevel['inheritance']['extends'] = (isset($topLevel['package']) && $topLevel['package']) ? $this->_packagesPrefix . $topLevel['className']:$this->_baseClassPrefix . $topLevel['className'];
            $topLevel['no_definition'] = true;
            $topLevel['generate_once'] = true;
            $topLevel['is_main_class'] = true;
            unset($topLevel['connection']);

            // Package level definition that extends from the base definition
            if (isset($definition['package'])) {

                $packageLevel = $definition;
                $packageLevel['className'] = $topLevel['inheritance']['extends'];
                $packageLevel['inheritance']['extends'] = $this->_baseClassPrefix . $topLevel['className'];
                $packageLevel['no_definition'] = true;
                $packageLevel['abstract'] = true;
                $packageLevel['override_parent'] = true;
                $packageLevel['generate_once'] = true;
                $packageLevel['is_package_class'] = true;
                unset($packageLevel['connection']);

                $packageLevel['tableClassName'] = $packageLevel['className'] . 'Table';
                $packageLevel['inheritance']['tableExtends'] = isset($definition['inheritance']['extends']) ? $definition['inheritance']['extends'] . 'Table':'Doctrine_Table';

                $topLevel['tableClassName'] = $topLevel['topLevelClassName'] . 'Table';
                $topLevel['inheritance']['tableExtends'] = $packageLevel['className'] . 'Table';
            } else {
                $topLevel['tableClassName'] = $topLevel['className'] . 'Table';
                $topLevel['inheritance']['tableExtends'] = isset($definition['inheritance']['extends']) ? $definition['inheritance']['extends'] . 'Table':'Doctrine_Table';
            }

            $baseClass = $definition;
            $baseClass['className'] = $this->_baseClassPrefix . $baseClass['className'];
            $baseClass['abstract'] = true;
            $baseClass['override_parent'] = false;
            $baseClass['is_base_class'] = true;

            $this->writeDefinition($baseClass);

            if ( ! empty($packageLevel)) {
                $this->writeDefinition($packageLevel);
            }

            $this->writeDefinition($topLevel);
        } else {
            $this->writeDefinition($definition);
        }
    }

    /**
     * writeTableDefinition
     *
     * @return void
     */
    public function writeTableDefinition($className, $path, $options = array())
    {
        $content  = '<?php' . PHP_EOL;
        $content .= sprintf(self::$_tpl, false,
                                       $className,
                                       isset($options['extends']) ? $options['extends']:'Doctrine_Table',
                                       null,
                                       null,
                                       null
                                       );

        Doctrine_Lib::makeDirectories($path);

        $writePath = $path . DIRECTORY_SEPARATOR . $className . $this->_suffix;

        Doctrine::loadModel($className, $writePath);

        if ( ! file_exists($writePath)) {
            file_put_contents($writePath, $content);
        }
    }

    /**
     * writeDefinition
     *
     * @param array $options
     * @param array $columns
     * @param array $relations
     * @param array $indexes
     * @param array $attributes
     * @param array $templates
     * @param array $actAs
     * @return void
     */
    public function writeDefinition(array $definition)
    {
        $definitionCode = $this->buildDefinition($definition);

        $fileName = $definition['className'] . $this->_suffix;

        $packagesPath = $this->_packagesPath ? $this->_packagesPath:$this->_path;

        // If this is a main class that either extends from Base or Package class
        if (isset($definition['is_main_class']) && $definition['is_main_class']) {
            // If is package then we need to put it in a package subfolder
            if (isset($definition['is_package']) && $definition['is_package']) {
                $writePath = $this->_path . DIRECTORY_SEPARATOR . $definition['package_name'];
            // Otherwise lets just put it in the root of the path
            } else {
                $writePath = $this->_path;
            }

            if ($this->generateTableClasses()) {
                $this->writeTableDefinition($definition['tableClassName'], $writePath, array('extends' => $definition['inheritance']['tableExtends']));
            }
        }
        // If is the package class then we need to make the path to the complete package
        else if (isset($definition['is_package_class']) && $definition['is_package_class']) {
            if (isset($definition['package_custom_path'])) {
              $writePath = $definition['package_custom_path'];
            } else {
              $writePath = $packagesPath . DIRECTORY_SEPARATOR . $definition['package_path'];
            }

            if ($this->generateTableClasses()) {
                $this->writeTableDefinition($definition['tableClassName'], $writePath, array('extends' => $definition['inheritance']['tableExtends']));
            }
        }
        // If it is the base class of the doctrine record definition
        else if (isset($definition['is_base_class']) && $definition['is_base_class']) {
            // If it is a part of a package then we need to put it in a package subfolder
            if (isset($definition['is_package']) && $definition['is_package']) {
                $basePath = $this->_path . DIRECTORY_SEPARATOR . $definition['package_name'];
                $writePath = $basePath . DIRECTORY_SEPARATOR . $this->_baseClassesDirectory;
            // Otherwise lets just put it in the root generated folder
            } else {
                $writePath = $this->_path . DIRECTORY_SEPARATOR . $this->_baseClassesDirectory;
            }
        }

        // If we have a writePath from the if else conditionals above then use it
        if (isset($writePath)) {
            Doctrine_Lib::makeDirectories($writePath);

            $writePath .= DIRECTORY_SEPARATOR . $fileName;
        // Otherwise none of the conditions were met and we aren't generating base classes
        } else {
            Doctrine_Lib::makeDirectories($this->_path);

            $writePath = $this->_path . DIRECTORY_SEPARATOR . $fileName;
        }

        $code = "<?php" . PHP_EOL;

        if (isset($definition['connection']) && $definition['connection']) {
            $code .= "// Connection Component Binding" . PHP_EOL;
            $code .= "Doctrine_Manager::getInstance()->bindComponent('" . $definition['connectionClassName'] . "', '" . $definition['connection'] . "');" . PHP_EOL;
        }

        $code .= PHP_EOL . $definitionCode;

        if (isset($definition['generate_once']) && $definition['generate_once'] === true) {
            if ( ! file_exists($writePath)) {
                $bytes = file_put_contents($writePath, $code);
            }
        } else {
            $bytes = file_put_contents($writePath, $code);
        }

        if (isset($bytes) && $bytes === false) {
            throw new Doctrine_Import_Builder_Exception("Couldn't write file " . $writePath);
        }

        Doctrine::loadModel($definition['className'], $writePath);
    }
}