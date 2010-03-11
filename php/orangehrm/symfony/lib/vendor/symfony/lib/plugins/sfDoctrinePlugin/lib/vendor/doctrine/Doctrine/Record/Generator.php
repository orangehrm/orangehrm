<?php
/*
 *  $Id$
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
 * Doctrine_Record_Generator
 *
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @package     Doctrine
 * @subpackage  Plugin
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version     $Revision$
 * @link        www.phpdoctrine.org
 * @since       1.0
 */
abstract class Doctrine_Record_Generator extends Doctrine_Record_Abstract
{
    /**
     * _options
     *
     * @var array $_options     an array of plugin specific options
     */
    protected $_options = array('generateFiles'  => false,
                                'generatePath'   => false,
                                'builderOptions' => array(),
                                'identifier'     => false,
                                'table'          => false,
                                'pluginTable'    => false,
                                'children'       => array());

    /**
     * _initialized
     *
     * @var bool $_initialized
     */
    protected $_initialized = false;

    /**
     * __get
     * an alias for getOption
     *
     * @param string $option
     */
    public function __get($option)
    {
        if (isset($this->_options[$option])) {
            return $this->_options[$option];
        }
        return null;
    }

    /**
     * __isset
     *
     * @param string $option
     */
    public function __isset($option) 
    {
        return isset($this->_options[$option]);
    }

    /**
     * returns the value of an option
     *
     * @param $option       the name of the option to retrieve
     * @return mixed        the value of the option
     */
    public function getOption($name)
    {
        if ( ! isset($this->_options[$name])) {
            throw new Doctrine_Exception('Unknown option ' . $name);
        }
        
        return $this->_options[$name];
    }

    /**
     * sets given value to an option
     *
     * @param $option       the name of the option to be changed
     * @param $value        the value of the option
     * @return Doctrine_Plugin  this object
     */
    public function setOption($name, $value)
    {
        $this->_options[$name] = $value;
        
        return $this;
    }

    /**
     * addChild
     *
     * Add child record generator 
     *
     * @param  Doctrine_Record_Generator $generator 
     * @return void
     */
    public function addChild($generator)
    {
        $this->_options['children'][] = $generator;
    }

    /**
     * getOptions
     *
     * returns all options and their associated values
     *
     * @return array    all options as an associative array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * initialize
     *
     * Initialize the plugin. Call in Doctrine_Template setTableDefinition() in order to initiate a generator in a template
     * SEE: Doctrine_Template_I18n for an example
     *
     * @param  Doctrine_Table $table 
     * @return void
     */
    public function initialize(Doctrine_Table $table)
    {
      	if ($this->_initialized) {
      	    return false;
      	}
        
        $this->_initialized = true;

        $this->initOptions();

        $table->addGenerator($this, get_class($this));

        $this->_options['table'] = $table;

        $this->_options['className'] = str_replace('%CLASS%',
                                                   $this->_options['table']->getComponentName(),
                                                   $this->_options['className']);

        // check that class doesn't exist (otherwise we cannot create it)
        if ($this->_options['generateFiles'] === false && class_exists($this->_options['className'], false)) {
            return false;
        }

        $this->buildTable();

        $fk = $this->buildForeignKeys($this->_options['table']);

        $this->_table->setColumns($fk);

        $this->buildRelation();

        $this->setTableDefinition();
        $this->setUp();

        $definition = array();
        $definition['columns'] = $this->_table->getColumns();
        $definition['tableName'] = $this->_table->getTableName();
        $definition['actAs'] = $this->_table->getTemplates();

        $this->generateClass($definition);

        $this->buildChildDefinitions();

        $this->_table->initIdentifier();
    }

    public function buildTable()
    {
        // Bind model 
        $conn = $this->_options['table']->getConnection();
        $conn->getManager()->bindComponent($this->_options['className'], $conn->getName());

        // Create table
        $this->_table = new Doctrine_Table($this->_options['className'], $conn);

        // If custom table name set then lets use it
        if (isset($this->_options['tableName']) && $this->_options['tableName']) {
            $this->_table->setTableName($this->_options['tableName']);
        }

        // Maintain some options from the parent table
        $options = $this->_options['table']->getOptions();

        $newOptions = array();
        $maintain = array('type', 'collate', 'charset'); // This list may need updating
        foreach ($maintain as $key) {
            if (isset($options[$key])) {
                $newOptions[$key] = $options[$key];
            }
        }

        $this->_table->setOptions($newOptions);

        $conn->addTable($this->_table);
    }

    /** 
     * empty template method for providing the concrete plugins the ability
     * to initialize options before the actual definition is being built
     *
     * @return void
     */
    public function initOptions()
    {
        
    }

    /**
     * buildChildDefinitions
     *
     * @return void
     */
    public function buildChildDefinitions()
    {
        if ( ! isset($this->_options['children'])) {
            throw new Doctrine_Record_Exception("Unknown option 'children'.");
        }

        foreach ($this->_options['children'] as $child) {
            if ($child instanceof Doctrine_Template) {
                if ($child->getPlugin() !== null) {
                    $this->_table->addGenerator($child->getPlugin(), get_class($child->getPlugin()));
                }

                $this->_table->addTemplate(get_class($child), $child);

                $child->setInvoker($this);
                $child->setTable($this->_table);
                $child->setTableDefinition();
                $child->setUp();
            } else {
                $this->_table->addGenerator($child, get_class($child));
                $child->initialize($this->_table);
            }
        }
    }

    /**
     * buildForeignKeys
     *
     * generates foreign keys for the plugin table based on the owner table
     *
     * the foreign keys generated by this method can be used for 
     * setting the relations between the owner and the plugin classes
     *
     * @param Doctrine_Table $table     the table object that owns the plugin
     * @return array                    an array of foreign key definitions
     */
    public function buildForeignKeys(Doctrine_Table $table)
    {
        $fk = array();

        foreach ((array) $table->getIdentifier() as $column) {
            $def = $table->getDefinitionOf($column);

            unset($def['autoincrement']);
            unset($def['sequence']);
            unset($def['primary']);

            $col = $column;

            $def['primary'] = true;
            $fk[$col] = $def;
        }
        return $fk;
    }

    /**
     * buildLocalRelation
     *
     * @return void
     */
    public function buildLocalRelation()
    {
        $options = array('local'    => $this->_options['table']->getIdentifier(),
                         'foreign'  => $this->_options['table']->getIdentifier(),
                         'type'     => Doctrine_Relation::MANY);

        $options['type'] = Doctrine_Relation::ONE;
        $options['onDelete'] = 'CASCADE';
        $options['onUpdate'] = 'CASCADE';

        $this->_table->getRelationParser()->bind($this->_options['table']->getComponentName(), $options);
    }

    /**
     * buildForeignRelation
     *
     * @param string $alias Alias of the foreign relation
     * @return void
     */
    public function buildForeignRelation($alias = null)
    {
        $options = array('local'    => $this->_options['table']->getIdentifier(),
                         'foreign'  => $this->_options['table']->getIdentifier(),
                         'type'     => Doctrine_Relation::MANY);

        $aliasStr = '';

        if ($alias !== null) {
            $aliasStr = ' as ' . $alias;
        }

        $this->_options['table']->getRelationParser()->bind($this->_table->getComponentName() . $aliasStr,
                                                            $options);
    }

    /**
     * buildRelation
     *
     * this method can be used for generating the relation from the plugin 
     * table to the owner table. By default buildForeignRelation() and buildLocalRelation() are called
     * Those methods can be overridden or this entire method can be overridden
     *
     * @return void
     */
    public function buildRelation()
    {
    	$this->buildForeignRelation();
        $this->buildLocalRelation();
    }

    /**
     * generateClass
     *
     * generates the class definition for plugin class
     *
     * @param array $definition  Definition array defining columns, relations and options
     *                           for the model
     * @return void
     */
    public function generateClass(array $definition = array())
    {
        $definition['className'] = $this->_options['className'];

        $builder = new Doctrine_Import_Builder();

        if ($this->_options['generateFiles']) {
            if (isset($this->_options['generatePath']) && $this->_options['generatePath']) {
                $builder->setTargetPath($this->_options['generatePath']);
                $builderOptions = isset($this->_options['builderOptions']) ? (array) $this->_options['builderOptions']:array();
                $builder->setOptions($builderOptions);
                $builder->buildRecord($definition);
            } else {
                throw new Doctrine_Record_Exception('If you wish to generate files then you must specify the path to generate the files in.');
            }
        } else {
            $def = $builder->buildDefinition($definition);

            eval($def);
        }
    }
}