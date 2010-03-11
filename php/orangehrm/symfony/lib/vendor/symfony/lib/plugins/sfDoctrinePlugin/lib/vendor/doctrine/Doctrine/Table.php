<?php
/*
 *  $Id: Table.php 5801 2009-06-02 17:30:27Z piccoloprincipe $
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
 * Doctrine_Table   represents a database table
 *                  each Doctrine_Table holds the information of foreignKeys and associations
 *
 *
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @package     Doctrine
 * @subpackage  Table
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version     $Revision: 5801 $
 * @link        www.phpdoctrine.org
 * @since       1.0
 */
class Doctrine_Table extends Doctrine_Configurable implements Countable
{
    /**
     * @var array $data                                 temporary data which is then loaded into Doctrine_Record::$_data
     */
    protected $_data             = array();

    /**
     * @var mixed $identifier   The field names of all fields that are part of the identifier/primary key
     */
    protected $_identifier = array();

    /**
     * @see Doctrine_Identifier constants
     * @var integer $identifierType                     the type of identifier this table uses
     */
    protected $_identifierType;

    /**
     * @var Doctrine_Connection $conn                   Doctrine_Connection object that created this table
     */
    protected $_conn;

    /**
     * @var array $identityMap                          first level cache
     */
    protected $_identityMap        = array();

    /**
     * @var Doctrine_Table_Repository $repository       record repository
     */
    protected $_repository;

    /**
     * @var array $columns                  an array of column definitions,
     *                                      keys are column names and values are column definitions
     *
     *                                      the definition array has atleast the following values:
     *
     *                                      -- type         the column type, eg. 'integer'
     *                                      -- length       the column length, eg. 11
     *
     *                                      additional keys:
     *                                      -- notnull      whether or not the column is marked as notnull
     *                                      -- values       enum values
     *                                      -- notblank     notblank validator + notnull constraint
     *                                      ... many more
     */
    protected $_columns          = array();

    /**
     * @var array $_fieldNames            an array of field names. used to look up field names
     *                                    from column names.
     *                                    keys are column names and values are field names
     */
    protected $_fieldNames    = array();

    /**
     *
     * @var array $_columnNames             an array of column names
     *                                      keys are field names and values column names.
     *                                      used to look up column names from field names.
     *                                      this is the reverse lookup map of $_fieldNames.
     */
    protected $_columnNames = array();

    /**
     * @var integer $columnCount            cached column count, Doctrine_Record uses this column count in when
     *                                      determining its state
     */
    protected $columnCount;

    /**
     * @var boolean $hasDefaultValues       whether or not this table has default values
     */
    protected $hasDefaultValues;

    /**
     * @var array $options                  an array containing all options
     *
     *      -- name                         name of the component, for example component name of the GroupTable is 'Group'
     *
     *      -- parents                      the parent classes of this component
     *
     *      -- declaringClass               name of the table definition declaring class (when using inheritance the class
     *                                      that defines the table structure can be any class in the inheritance hierarchy,
     *                                      hence we need reflection to check out which class actually calls setTableDefinition)
     *
     *      -- tableName                    database table name, in most cases this is the same as component name but in some cases
     *                                      where one-table-multi-class inheritance is used this will be the name of the inherited table
     *
     *      -- sequenceName                 Some databases need sequences instead of auto incrementation primary keys,
     *                                      you can set specific sequence for your table by calling setOption('sequenceName', $seqName)
     *                                      where $seqName is the name of the desired sequence
     *
     *      -- enumMap                      enum value arrays
     *
     *      -- inheritanceMap               inheritanceMap is used for inheritance mapping, keys representing columns and values
     *                                      the column values that should correspond to child classes
     *
     *      -- type                         table type (mysql example: INNODB)
     *
     *      -- charset                      character set
     *
     *      -- foreignKeys                  the foreign keys of this table
     *
     *      -- checks                       the check constraints of this table, eg. 'price > dicounted_price'
     *
     *      -- collate                      collate attribute
     *
     *      -- indexes                      the index definitions of this table
     *
     *      -- treeImpl                     the tree implementation of this table (if any)
     *
     *      -- treeOptions                  the tree options
     *
     *      -- queryParts                   the bound query parts
     *
     *      -- versioning
     */
    protected $_options      = array('name'           => null,
                                     'tableName'      => null,
                                     'sequenceName'   => null,
                                     'inheritanceMap' => array(),
                                     'enumMap'        => array(),
                                     'type'           => null,
                                     'charset'        => null,
                                     'collate'        => null,
                                     'treeImpl'       => null,
                                     'treeOptions'    => array(),
                                     'indexes'        => array(),
                                     'parents'        => array(),
                                     'joinedParents'  => array(),
                                     'queryParts'     => array(),
                                     'versioning'     => null,
                                     'subclasses'     => array(),
                                     );

    /**
     * @var Doctrine_Tree $tree                 tree object associated with this table
     */
    protected $_tree;

    /**
     * @var Doctrine_Relation_Parser $_parser   relation parser object
     */
    protected $_parser;

    /**
     * @see Doctrine_Template
     * @var array $_templates                   an array containing all templates attached to this table
     */
    protected $_templates   = array();

    /**
     * @see Doctrine_Record_Filter
     * @var array $_filters                     an array containing all record filters attached to this table
     */
    protected $_filters     = array();

    /**
     * @see Doctrine_Record_Generator
     * @var array $_generators                  an array containing all generators attached to this table
     */
    protected $_generators     = array();

    /**
     * @var array $_invokedMethods              method invoker cache
     */
    protected $_invokedMethods = array();

    /**
     * @var Doctrine_Record $record             empty instance of the given model
     */
    protected $record;

    /**
     * the constructor
     *
     * @throws Doctrine_Connection_Exception    if there are no opened connections
     * @param string $name                      the name of the component
     * @param Doctrine_Connection $conn         the connection associated with this table
     */
    public function __construct($name, Doctrine_Connection $conn, $initDefinition = false)
    {
        $this->_conn = $conn;

        $this->setParent($this->_conn);

        $this->_options['name'] = $name;
        $this->_parser = new Doctrine_Relation_Parser($this);

        if ($initDefinition) {
            $this->record = $this->initDefinition();

            $this->initIdentifier();

            $this->record->setUp();

            // if tree, set up tree
            if ($this->isTree()) {
                $this->getTree()->setUp();
            }
        } else {
            if ( ! isset($this->_options['tableName'])) {
                $this->setTableName(Doctrine_Inflector::tableize($this->_options['name']));
            }
        }
        $this->_filters[]  = new Doctrine_Record_Filter_Standard();
        $this->_repository = new Doctrine_Table_Repository($this);
        
        $this->construct();
    }
    
    /**
     * construct
     * Empty template method to provide concrete Table classes with the possibility
     * to hook into the constructor procedure
     *
     * @return void
     */
    public function construct()
    { }

    /**
     * Initializes the in-memory table definition.
     *
     * @param string $name
     */
    public function initDefinition()
    {
        $name = $this->_options['name'];
        if ( ! class_exists($name) || empty($name)) {
            throw new Doctrine_Exception("Couldn't find class " . $name);
        }
        $record = new $name($this);

        $names = array();

        $class = $name;

        // get parent classes

        do {
            if ($class === 'Doctrine_Record') {
                break;
            }

            $name = $class;
            $names[] = $name;
        } while ($class = get_parent_class($class));

        if ($class === false) {
            throw new Doctrine_Table_Exception('Class "' . $name . '" must be a child class of Doctrine_Record');
        }

        // reverse names
        $names = array_reverse($names);
        // save parents
        array_pop($names);
        $this->_options['parents'] = $names;

        // create database table
        if (method_exists($record, 'setTableDefinition')) {
            $record->setTableDefinition();
            // get the declaring class of setTableDefinition method
            $method = new ReflectionMethod($this->_options['name'], 'setTableDefinition');
            $class = $method->getDeclaringClass();

        } else {
            $class = new ReflectionClass($class);
        }

        $this->_options['joinedParents'] = array();

        foreach (array_reverse($this->_options['parents']) as $parent) {

            if ($parent === $class->getName()) {
                continue;
            }
            $ref = new ReflectionClass($parent);

            if ($ref->isAbstract() || ! $class->isSubClassOf($parent)) {
                continue;
            }
            $parentTable = $this->_conn->getTable($parent);

            $found = false;
            $parentColumns = $parentTable->getColumns();

            foreach ($parentColumns as $columnName => $definition) {
                if ( ! isset($definition['primary']) || $definition['primary'] === false) {
                    if (isset($this->_columns[$columnName])) {
                        $found = true;
                        break;
                    } else {
                        if ( ! isset($parentColumns[$columnName]['owner'])) {
                            $parentColumns[$columnName]['owner'] = $parentTable->getComponentName();
                        }

                        $this->_options['joinedParents'][] = $parentColumns[$columnName]['owner'];
                    }
                } else {
                    unset($parentColumns[$columnName]);
                }
            }

            if ($found) {
                continue;
            }

            foreach ($parentColumns as $columnName => $definition) {
                $fullName = $columnName . ' as ' . $parentTable->getFieldName($columnName);
                $this->setColumn($fullName, $definition['type'], $definition['length'], $definition, true);
            }

            break;
        }

        $this->_options['joinedParents'] = array_values(array_unique($this->_options['joinedParents']));

        $this->_options['declaringClass'] = $class;

        // set the table definition for the given tree implementation
        if ($this->isTree()) {
            $this->getTree()->setTableDefinition();
        }

        $this->columnCount = count($this->_columns);

        if ( ! isset($this->_options['tableName'])) {
            $this->setTableName(Doctrine_Inflector::tableize($class->getName()));
        }

        return $record;
    }

    /**
     * Initializes the table identifier(s)/primary key(s)
     *
     * @return void
     */
    public function initIdentifier()
    {
        switch (count($this->_identifier)) {
            case 0:
                if ( ! empty($this->_options['joinedParents'])) {
                    $root = current($this->_options['joinedParents']);

                    $table = $this->_conn->getTable($root);

                    $this->_identifier = $table->getIdentifier();

                    $this->_identifierType = ($table->getIdentifierType() !== Doctrine::IDENTIFIER_AUTOINC)
                                            ? $table->getIdentifierType() : Doctrine::IDENTIFIER_NATURAL;

                    // add all inherited primary keys
                    foreach ((array) $this->_identifier as $id) {
                        $definition = $table->getDefinitionOf($id);

                        // inherited primary keys shouldn't contain autoinc
                        // and sequence definitions
                        unset($definition['autoincrement']);
                        unset($definition['sequence']);

                        // add the inherited primary key column
                        $fullName = $id . ' as ' . $table->getFieldName($id);
                        $this->setColumn($fullName, $definition['type'], $definition['length'],
                                $definition, true);
                    }
                } else {
                    $definition = array('type' => 'integer',
                                        'length' => 20,
                                        'autoincrement' => true,
                                        'primary' => true);
                    $this->setColumn('id', $definition['type'], $definition['length'], $definition, true);
                    $this->_identifier = 'id';
                    $this->_identifierType = Doctrine::IDENTIFIER_AUTOINC;
                }
                $this->columnCount++;
                break;
            case 1:
                foreach ($this->_identifier as $pk) {
                    $e = $this->getDefinitionOf($pk);

                    $found = false;

                    foreach ($e as $option => $value) {
                        if ($found) {
                            break;
                        }

                        $e2 = explode(':', $option);

                        switch (strtolower($e2[0])) {
                            case 'autoincrement':
                            case 'autoinc':
                                if ($value !== false) {
                                    $this->_identifierType = Doctrine::IDENTIFIER_AUTOINC;
                                    $found = true;
                                }
                                break;
                            case 'seq':
                            case 'sequence':
                                $this->_identifierType = Doctrine::IDENTIFIER_SEQUENCE;
                                $found = true;

                                if (is_string($value)) {
                                    $this->_options['sequenceName'] = $value;
                                } else {
                                    if (($sequence = $this->getAttribute(Doctrine::ATTR_DEFAULT_SEQUENCE)) !== null) {
                                        $this->_options['sequenceName'] = $sequence;
                                    } else {
                                        $this->_options['sequenceName'] = $this->_conn->formatter->getSequenceName($this->_options['tableName']);
                                    }
                                }
                                break;
                        }
                    }
                    if ( ! isset($this->_identifierType)) {
                        $this->_identifierType = Doctrine::IDENTIFIER_NATURAL;
                    }
                }

                $this->_identifier = $pk;

                break;
            default:
                $this->_identifierType = Doctrine::IDENTIFIER_COMPOSITE;
        }
    }

    /**
     * Gets the owner of a column.
     * The owner of a column is the name of the component in a hierarchy that
     * defines the column.
     *
     * @param string $columnName   The column name
     * @return string  The name of the owning/defining component
     */
    public function getColumnOwner($columnName)
    {
        if (isset($this->_columns[$columnName]['owner'])) {
            return $this->_columns[$columnName]['owner'];
        } else {
            return $this->getComponentName();
        }
    }

    /**
     * Gets the record instance for this table. The Doctrine_Table instance always holds at least one
     * instance of a model so that it can be reused for several things, but primarily it is first
     * used to instantiate all the internal in memory meta data
     *
     * @return object  Empty instance of the record
     */
    public function getRecordInstance()
    {
        if ( ! $this->record) {
            $this->record = new $this->_options['name'];
        }
        return $this->record;
    }

    /**
     * Checks whether a column is inherited from a component further up in the hierarchy.
     *
     * @param $columnName  The column name
     * @return boolean  TRUE if column is inherited, FALSE otherwise.
     */
    public function isInheritedColumn($columnName)
    {
        return (isset($this->_columns[$columnName]['owner']));
    }

    /**
     * Checks whether a field is part of the table identifier/primary key field(s).
     *
     * @param string $fieldName  The field name
     * @return boolean  TRUE if the field is part of the table identifier/primary key field(s),
     *                  FALSE otherwise.
     */
    public function isIdentifier($fieldName)
    {
        return ($fieldName === $this->getIdentifier() ||
                in_array($fieldName, (array) $this->getIdentifier()));
    }

    /**
     * Checks whether a field identifier is of type autoincrement
     *
     * @return boolean TRUE  if the identifier is autoincrement
     *                 FALSE otherwise
     */
    public function isIdentifierAutoincrement()
    {
        return $this->getIdentifierType() === Doctrine::IDENTIFIER_AUTOINC;
    }

    /**
     * Checks whether a field identifier is a composite key.
     *
     * @return boolean TRUE  if the identifier is a composite key,
     *                 FALSE otherwise
     */
    public function isIdentifierComposite()
    {
        return $this->getIdentifierType() === Doctrine::IDENTIFIER_COMPOSITE;
    }

    /**
     * getMethodOwner
     *
     * @param string $method
     * @return void
     */
    public function getMethodOwner($method)
    {
        return (isset($this->_invokedMethods[$method])) ?
                      $this->_invokedMethods[$method] : false;
    }

    /**
     * setMethodOwner
     *
     * @param string $method
     * @param string $class
     */
    public function setMethodOwner($method, $class)
    {
        $this->_invokedMethods[$method] = $class;
    }

    /**
     * export
     * exports this table to database based on column and option definitions
     *
     * @throws Doctrine_Connection_Exception    if some error other than Doctrine::ERR_ALREADY_EXISTS
     *                                          occurred during the create table operation
     * @return boolean                          whether or not the export operation was successful
     *                                          false if table already existed in the database
     */
    public function export()
    {
        $this->_conn->export->exportTable($this);
    }

    /**
     * getExportableFormat
     * returns exportable presentation of this object
     *
     * @return array
     */
    public function getExportableFormat($parseForeignKeys = true)
    {
        $columns = array();
        $primary = array();

        foreach ($this->getColumns() as $name => $definition) {

            if (isset($definition['owner'])) {
                continue;
            }

            switch ($definition['type']) {
                case 'boolean':
                    if (isset($definition['default'])) {
                        $definition['default'] = $this->getConnection()->convertBooleans($definition['default']);
                    }
                    break;
            }
            $columns[$name] = $definition;

            if (isset($definition['primary']) && $definition['primary']) {
                $primary[] = $name;
            }
        }

        $options['foreignKeys'] = isset($this->_options['foreignKeys']) ?
                $this->_options['foreignKeys'] : array();

        if ($parseForeignKeys && $this->getAttribute(Doctrine::ATTR_EXPORT)
                & Doctrine::EXPORT_CONSTRAINTS) {

            $constraints = array();

            $emptyIntegrity = array('onUpdate' => null,
                                    'onDelete' => null);

            foreach ($this->getRelations() as $name => $relation) {
                $fk = $relation->toArray();
                $fk['foreignTable'] = $relation->getTable()->getTableName();

                if ($relation->getTable() === $this && in_array($relation->getLocal(), $primary)) {
                    if ($relation->hasConstraint()) {
                        throw new Doctrine_Table_Exception("Badly constructed integrity constraints. Cannot define constraint of different fields in the same table.");
                    }
                    continue;
                }

                $integrity = array('onUpdate' => $fk['onUpdate'],
                                   'onDelete' => $fk['onDelete']);

                if ($relation instanceof Doctrine_Relation_LocalKey) {
                    $def = array('local'        => $relation->getLocalColumnName(),
                                 'foreign'      => $relation->getForeignColumnName(),
                                 'foreignTable' => $relation->getTable()->getTableName());

                    if (($key = array_search($def, $options['foreignKeys'])) === false) {
                        $options['foreignKeys'][] = $def;
                        if ($integrity !== $emptyIntegrity) {
                            $constraints[] = $integrity;
                        }
                    } else {
                        if ($integrity !== $emptyIntegrity) {
                            $constraints[$key] = $integrity;
                        }
                    }
                }
            }

            foreach ($constraints as $k => $def) {
                $options['foreignKeys'][$k] = array_merge($options['foreignKeys'][$k], $def);
            }
        }

        $options['primary'] = $primary;

        return array('tableName' => $this->getOption('tableName'),
                     'columns'   => $columns,
                     'options'   => array_merge($this->getOptions(), $options));
    }

    /**
     * getRelationParser
     * return the relation parser associated with this table
     *
     * @return Doctrine_Relation_Parser     relation parser object
     */
    public function getRelationParser()
    {
        return $this->_parser;
    }

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
     * getOptions
     * returns all options of this table and the associated values
     *
     * @return array    all options and their values
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * setOptions
     *
     * @param string $options
     * @return void
     */
    public function setOptions($options)
    {
        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }
    }

    /**
     * addForeignKey
     *
     * adds a foreignKey to this table
     *
     * @return void
     */
    public function addForeignKey(array $definition)
    {
        $this->_options['foreignKeys'][] = $definition;
    }

    /**
     * addCheckConstraint
     *
     * adds a check constraint to this table
     *
     * @return void
     */
    public function addCheckConstraint($definition, $name)
    {
        if (is_string($name)) {
            $this->_options['checks'][$name] = $definition;
        } else {
            $this->_options['checks'][] = $definition;
        }

        return $this;
    }

    /**
     * addIndex
     *
     * adds an index to this table
     *
     * @return void
     */
    public function addIndex($index, array $definition)
    {
        if (isset($definition['fields'])) {
	        foreach ((array) $definition['fields'] as $key => $field) {
		        if (is_numeric($key)) {
                    $definition['fields'][$key] = $this->getColumnName($field);
                } else {
                    $columnName = $this->getColumnName($key);

                    unset($definition['fields'][$key]);

                    $definition['fields'][$columnName] = $field;
                }
            }
        }

        $this->_options['indexes'][$index] = $definition;
    }

    /**
     * getIndex
     *
     * @return array|boolean        array on success, FALSE on failure
     */
    public function getIndex($index)
    {
        if (isset($this->_options['indexes'][$index])) {
            return $this->_options['indexes'][$index];
        }

        return false;
    }

    /**
     * DESCRIBE WHAT THIS METHOD DOES, PLEASE!
     *
     * @todo Name proposal: addRelation
     */
    public function bind($args, $type)
    {
        $options = ( ! isset($args[1])) ? array() : $args[1];
        $options['type'] = $type;

        $this->_parser->bind($args[0], $options);
    }

    /**
     * hasRelation
     *
     * @param string $alias      the relation to check if exists
     * @return boolean           true if the relation exists otherwise false
     */
    public function hasRelation($alias)
    {
        return $this->_parser->hasRelation($alias);
    }

    /**
     * getRelation
     *
     * @param string $alias      relation alias
     */
    public function getRelation($alias, $recursive = true)
    {
        return $this->_parser->getRelation($alias, $recursive);
    }

    /**
     * getRelations
     * returns an array containing all relation objects
     *
     * @return array        an array of Doctrine_Relation objects
     */
    public function getRelations()
    {
        return $this->_parser->getRelations();
    }

    /**
     * createQuery
     * creates a new Doctrine_Query object and adds the component name
     * of this table as the query 'from' part
     *
     * @param string Optional alias name for component aliasing.
     *
     * @return Doctrine_Query
     */
    public function createQuery($alias = '')
    {
        if ( ! empty($alias)) {
            $alias = ' ' . trim($alias);
        }
        return Doctrine_Query::create($this->_conn)->from($this->getComponentName() . $alias);
    }

    /**
     * getRepository
     *
     * @return Doctrine_Table_Repository
     */
    public function getRepository()
    {
        return $this->_repository;
    }

    /**
     * setOption
     * sets an option and returns this object in order to
     * allow flexible method chaining
     *
     * @see Doctrine_Table::$_options   for available options
     * @param string $name              the name of the option to set
     * @param mixed $value              the value of the option
     * @return Doctrine_Table           this object
     */
    public function setOption($name, $value)
    {
        switch ($name) {
            case 'name':
            case 'tableName':
                break;
            case 'enumMap':
            case 'inheritanceMap':
            case 'index':
            case 'treeOptions':
                if ( ! is_array($value)) {
                throw new Doctrine_Table_Exception($name . ' should be an array.');
                }
                break;
        }
        $this->_options[$name] = $value;
    }

    /**
     * getOption
     * returns the value of given option
     *
     * @param string $name  the name of the option
     * @return mixed        the value of given option
     */
    public function getOption($name)
    {
        if (isset($this->_options[$name])) {
            return $this->_options[$name];
        }
        return null;
    }

    /**
     * getColumnName
     *
     * returns a column name for column alias
     * if the actual name for the alias cannot be found
     * this method returns the given alias
     *
     * @param string $alias         column alias
     * @return string               column name
     */
    public function getColumnName($fieldName)
    {
        // FIX ME: This is being used in places where an array is passed, but it should not be an array
        // For example in places where Doctrine should support composite foreign/primary keys
        $fieldName = is_array($fieldName) ? $fieldName[0]:$fieldName;

        if (isset($this->_columnNames[$fieldName])) {
            return $this->_columnNames[$fieldName];
        }

        return strtolower($fieldName);
    }

    /**
     * getColumnDefinition
     *
     * @param string $columnName
     * @return void
     */
    public function getColumnDefinition($columnName)
    {
        if ( ! isset($this->_columns[$columnName])) {
            return false;
        }
        return $this->_columns[$columnName];
    }

    /**
     * getColumnAlias
     *
     * returns a column alias for a column name
     * if no alias can be found the column name is returned.
     *
     * @param string $columnName    column name
     * @return string               column alias
     */
    public function getFieldName($columnName)
    {
        if (isset($this->_fieldNames[$columnName])) {
            return $this->_fieldNames[$columnName];
        }
        return $columnName;
    }
    public function setColumns(array $definitions)
    {
        foreach ($definitions as $name => $options) {
            $this->setColumn($name, $options['type'], $options['length'], $options);
        }
    }
    /**
     * setColumn
     *
     * @param string $name
     * @param string $type
     * @param integer $length
     * @param mixed $options
     * @param boolean $prepend   Whether to prepend or append the new column to the column list.
     *                           By default the column gets appended.
     * @throws Doctrine_Table_Exception     if trying use wrongly typed parameter
     * @return void
     */
    public function setColumn($name, $type, $length = null, $options = array(), $prepend = false)
    {
        if (is_string($options)) {
            $options = explode('|', $options);
        }

        foreach ($options as $k => $option) {
            if (is_numeric($k)) {
                if ( ! empty($option)) {
                    $options[$option] = true;
                }
                unset($options[$k]);
            }
        }

        // extract column name & field name
        if (stripos($name, ' as '))
        {
            if (strpos($name, ' as')) {
                $parts = explode(' as ', $name);
            } else {
                $parts = explode(' AS ', $name);
            }

            if (count($parts) > 1) {
                $fieldName = $parts[1];
            } else {
                $fieldName = $parts[0];
            }

            $name = strtolower($parts[0]);
        } else {
            $fieldName = $name;
            $name = strtolower($name);
        }

        $name = trim($name);
        $fieldName = trim($fieldName);

        if ($prepend) {
            $this->_columnNames = array_merge(array($fieldName => $name), $this->_columnNames);
            $this->_fieldNames = array_merge(array($name => $fieldName), $this->_fieldNames);
        } else {
            $this->_columnNames[$fieldName] = $name;
            $this->_fieldNames[$name] = $fieldName;
        }

        if ($length == null) {
            switch ($type) {
                case 'decimal':
                    $length = 18;
                break;
                case 'string':
                case 'clob':
                case 'float':
                case 'integer':
                case 'array':
                case 'object':
                case 'blob':
                case 'gzip':
                    // use php int max
                    $length = 2147483647;
                break;
                case 'boolean':
                    $length = 1;
                case 'date':
                    // YYYY-MM-DD ISO 8601
                    $length = 10;
                case 'time':
                    // HH:NN:SS+00:00 ISO 8601
                    $length = 14;
                case 'timestamp':
                    // YYYY-MM-DDTHH:MM:SS+00:00 ISO 8601
                    $length = 25;
                break;
            }
        }

        $options['type'] = $type;
        $options['length'] = $length;

        if ($prepend) {
            $this->_columns = array_merge(array($name => $options), $this->_columns);
        } else {
            $this->_columns[$name] = $options;
        }

        if (isset($options['primary']) && $options['primary']) {
            if (isset($this->_identifier)) {
                $this->_identifier = (array) $this->_identifier;
            }
            if ( ! in_array($fieldName, $this->_identifier)) {
                $this->_identifier[] = $fieldName;
            }
        }
        if (isset($options['default'])) {
            $this->hasDefaultValues = true;
        }
    }

    /**
     * hasDefaultValues
     * returns true if this table has default values, otherwise false
     *
     * @return boolean
     */
    public function hasDefaultValues()
    {
        return $this->hasDefaultValues;
    }

    /**
     * getDefaultValueOf
     * returns the default value(if any) for given column
     *
     * @param string $fieldName
     * @return mixed
     */
    public function getDefaultValueOf($fieldName)
    {
        $columnName = $this->getColumnName($fieldName);
        if ( ! isset($this->_columns[$columnName])) {
            throw new Doctrine_Table_Exception("Couldn't get default value. Column ".$columnName." doesn't exist.");
        }
        if (isset($this->_columns[$columnName]['default'])) {
            return $this->_columns[$columnName]['default'];
        } else {
            return null;
        }
    }

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }

    /**
     * @return integer
     */
    public function getIdentifierType()
    {
        return $this->_identifierType;
    }

    /**
     * hasColumn
     * @return boolean
     */
    public function hasColumn($columnName)
    {
        return isset($this->_columns[strtolower($columnName)]);
    }

    /**
     * hasField
     * @return boolean
     */
    public function hasField($fieldName)
    {
        return isset($this->_columnNames[$fieldName]);
    }

    /**
     * sets the connection for this class
     *
     * @params Doctrine_Connection      a connection object
     * @return Doctrine_Table           this object
     */
    public function setConnection(Doctrine_Connection $conn)
    {
        $this->_conn = $conn;

        $this->setParent($this->_conn);

        return $this;
    }

    /**
     * returns the connection associated with this table (if any)
     *
     * @return Doctrine_Connection|null     the connection object
     */
    public function getConnection()
    {
        return $this->_conn;
    }

    /**
     * creates a new record
     *
     * @param $array             an array where keys are field names and
     *                           values representing field values
     * @return Doctrine_Record   the created record object
     */
    public function create(array $array = array())
    {
        $record = new $this->_options['name']($this, true);
        $record->fromArray($array);

        return $record;
    }
    
    /**
     * adds a named query in the query registry
     *
     * @param $queryKey  Query key name
     * @param $query      DQL string or Doctrine_Query object
     * @return void
	 */
	public function addNamedQuery($queryKey, $query)
    {
        $registry = Doctrine_Manager::getInstance()->getQueryRegistry();
        $registry->add($this->getComponentName() . '/' . $queryKey, $query);
    }
    
    /**
     * creates a named query in the query registry
     *
     * @param $queryKey  Query key name
     * @return Doctrine_Query
	 */
	public function createNamedQuery($queryKey)
    {
        $queryRegistry = Doctrine_Manager::getInstance()->getQueryRegistry();

        if (strpos($queryKey, '/') !== false) {
            $e = explode('/', $queryKey);
            
            return $queryRegistry->get($e[1], $e[0]);
        }

        return $queryRegistry->get($queryKey, $this->getComponentName());
    }


    /**
     * finds a record by its identifier
     *
     * @param mixed $name         Database Row ID or Query Name defined previously as a NamedQuery
     * @param mixed $params       This argument is the hydration mode (Doctrine::HYDRATE_ARRAY or 
     *                            Doctrine::HYDRATE_RECORD) if first param is a Database Row ID. 
     *                            Otherwise this argument expect an array of query params.
     * @param int $hydrationMode  Optional Doctrine::HYDRATE_ARRAY or Doctrine::HYDRATE_RECORD if 
     *                            first argument is a NamedQuery
     * @return mixed              Doctrine_Collection, array, Doctrine_Record or false if no result
     */
    public function find()
    {
        $num_args = func_num_args();

        // Named Query or IDs
        $name = func_get_arg(0);
        
        if (is_null($name)) { 
            return false;
        }

        $ns = $this->getComponentName();
        $m = $name;
        
        // Check for possible cross-access
        if ( ! is_array($name) && strpos($name, '/') !== false) {
            list($ns, $m) = explode('/', $name);
        }

        // Define query to be used
        if (
            ! is_array($name) && 
            Doctrine_Manager::getInstance()->getQueryRegistry()->has($m, $ns)
        ) {
            // We're dealing with a named query
            $q = $this->createNamedQuery($name);

            // Parameters construction
            $params = ($num_args >= 2) ? func_get_arg(1) : array();

            // Hydration mode
            $hydrationMode = ($num_args == 3) ? func_get_arg(2) : null;

            // Executing query
            $res = $q->execute($params, $hydrationMode);
        } else {
            // We're passing a single ID or an array of IDs
            $q = $this->createQuery('dctrn_find')
                ->where('dctrn_find.' . implode(' = ? AND dctrn_find.', (array) $this->getIdentifier()) . ' = ?')
                ->limit(1);
                
            // Parameters construction
            $params = is_array($name) ? array_values($name) : array($name);

            // Hydration mode
            $hydrationMode = ($num_args == 2) ? func_get_arg(1) : null;
            
            // Executing query
            $res = $q->fetchOne($params, $hydrationMode);
        }

        $q->free();
        
        return $res;
    }

    /**
     * findAll
     * returns a collection of records
     *
     * @param int $hydrationMode        Doctrine::HYDRATE_ARRAY or Doctrine::HYDRATE_RECORD
     * @return Doctrine_Collection
     */
    public function findAll($hydrationMode = null)
    {
        return $this->createQuery('dctrn_find')
            ->execute(array(), $hydrationMode);
    }

    /**
     * findBySql
     * finds records with given SQL where clause
     * returns a collection of records
     *
     * @param string $dql               DQL after WHERE clause
     * @param array $params             query parameters
     * @param int $hydrationMode        Doctrine::HYDRATE_ARRAY or Doctrine::HYDRATE_RECORD
     * @return Doctrine_Collection
     *
     * @todo This actually takes DQL, not SQL, but it requires column names
     *       instead of field names. This should be fixed to use raw SQL instead.
     */
    public function findBySql($dql, $params = array(), $hydrationMode = null)
    {
        return $this->createQuery('dctrn_find')
            ->where($dql)->execute($params, $hydrationMode);
    }

    /**
     * findByDql
     * finds records with given DQL where clause
     * returns a collection of records
     *
     * @param string $dql               DQL after WHERE clause
     * @param array $params             query parameters
     * @param int $hydrationMode        Doctrine::HYDRATE_ARRAY or Doctrine::HYDRATE_RECORD
     * @return Doctrine_Collection
     */
    public function findByDql($dql, $params = array(), $hydrationMode = null)
    {
        $parser = new Doctrine_Query($this->_conn);
        $component = $this->getComponentName();
        $query = 'FROM ' . $component . ' dctrn_find WHERE ' . $dql;

        return $parser->query($query, $params, $hydrationMode);
    }
    
    /**
     * findBy
     *
     * @param string $column
     * @param string $value
     * @param string $hydrationMode
     * @return void
     */
    protected function findBy($fieldName, $value, $hydrationMode = null)
    {
        return $this->createQuery('dctrn_find')
            ->where('dctrn_find.' . $fieldName . ' = ?', array($value))
            ->execute(array(), $hydrationMode);
    }

    /**
     * findOneBy
     *
     * @param string $column
     * @param string $value
     * @param string $hydrationMode
     * @return void
     */
    protected function findOneBy($fieldName, $value, $hydrationMode = null)
    {
        return $this->createQuery('dctrn_find')
                    ->where('dctrn_find.' . $fieldName . ' = ?', array($value))
                    ->limit(1)
                    ->fetchOne(array(), $hydrationMode);
    }

    /**
     * execute
     * fetches data using the provided queryKey and
     * the associated query in the query registry
     *
     * if no query for given queryKey is being found a
     * Doctrine_Query_Registry exception is being thrown
     *
     * @param string $queryKey      the query key
     * @param array $params         prepared statement params (if any)
     * @return mixed                the fetched data
     */
    public function execute($queryKey, $params = array(), $hydrationMode = Doctrine::HYDRATE_RECORD)
    {
        return $this->createNamedQuery($queryKey)->execute($params, $hydrationMode);
    }

    /**
     * executeOne
     * fetches data using the provided queryKey and
     * the associated query in the query registry
     *
     * if no query for given queryKey is being found a
     * Doctrine_Query_Registry exception is being thrown
     *
     * @param string $queryKey      the query key
     * @param array $params         prepared statement params (if any)
     * @return mixed                the fetched data
     */
    public function executeOne($queryKey, $params = array(), $hydrationMode = Doctrine::HYDRATE_RECORD)
    {
        return $this->createNamedQuery($queryKey)->fetchOne($params, $hydrationMode);
    }

    /**
     * clear
     * clears the first level cache (identityMap)
     *
     * @return void
     * @todo what about a more descriptive name? clearIdentityMap?
     */
    public function clear()
    {
        $this->_identityMap = array();
    }

    /**
     * addRecord
     * adds a record to identity map
     *
     * @param Doctrine_Record $record       record to be added
     * @return boolean
     * @todo Better name? registerRecord?
     */
    public function addRecord(Doctrine_Record $record)
    {
        $id = implode(' ', $record->identifier());

        if (isset($this->_identityMap[$id])) {
            return false;
        }

        $this->_identityMap[$id] = $record;

        return true;
    }

    /**
     * removeRecord
     * removes a record from the identity map, returning true if the record
     * was found and removed and false if the record wasn't found.
     *
     * @param Doctrine_Record $record       record to be removed
     * @return boolean
     */
    public function removeRecord(Doctrine_Record $record)
    {
        $id = implode(' ', $record->identifier());

        if (isset($this->_identityMap[$id])) {
            unset($this->_identityMap[$id]);
            return true;
        }

        return false;
    }

    /**
     * getRecord
     * first checks if record exists in identityMap, if not
     * returns a new record
     *
     * @return Doctrine_Record
     */
    public function getRecord()
    {
        if ( ! empty($this->_data)) {
            $identifierFieldNames = $this->getIdentifier();

            if ( ! is_array($identifierFieldNames)) {
                $identifierFieldNames = array($identifierFieldNames);
            }

            $found = false;
            foreach ($identifierFieldNames as $fieldName) {
                if ( ! isset($this->_data[$fieldName])) {
                    // primary key column not found return new record
                    $found = true;
                    break;
                }
                $id[] = $this->_data[$fieldName];
            }

            if ($found) {
                $recordName = $this->getComponentName();
                $record = new $recordName($this, true);
                $this->_data = array();
                return $record;
            }
            
            $id = implode(' ', $id);

            if (isset($this->_identityMap[$id])) {
                $record = $this->_identityMap[$id];
                if ($record->getTable()->getAttribute(Doctrine::ATTR_HYDRATE_OVERWRITE)) {
                    $record->hydrate($this->_data);
                    if ($record->state() == Doctrine_Record::STATE_PROXY) {
                        if (count($this->_data) >= $this->getColumnCount()) {
                            $record->state(Doctrine_Record::STATE_CLEAN);
                        }
                    }
                } else {
                    $record->hydrate($this->_data, false);
                }
            } else {
                $recordName = $this->getComponentName();
                $record = new $recordName($this);
                $this->_identityMap[$id] = $record;
            }
            $this->_data = array();
        } else {
            $recordName = $this->getComponentName();
            $record = new $recordName($this, true);
        }

        return $record;
    }

    /**
     * Get the classname to return. Most often this is just the options['name']
     *
     * Check the subclasses option and the inheritanceMap for each subclass to see
     * if all the maps in a subclass is met. If this is the case return that
     * subclass name. If no subclasses match or if there are no subclasses defined
     * return the name of the class for this tables record.
     *
     * @todo this function could use reflection to check the first time it runs
     * if the subclassing option is not set.
     *
     * @return string The name of the class to create
     * @deprecated
     */
    public function getClassnameToReturn()
    {
        if ( ! isset($this->_options['subclasses'])) {
            return $this->_options['name'];
        }
        foreach ($this->_options['subclasses'] as $subclass) {
            $table = $this->_conn->getTable($subclass);
            $inheritanceMap = $table->getOption('inheritanceMap');
            $nomatch = false;
            foreach ($inheritanceMap as $key => $value) {
                if ( ! isset($this->_data[$key]) || $this->_data[$key] != $value) {
                    $nomatch = true;
                    break;
                }
            }
            if ( ! $nomatch) {
                return $table->getComponentName();
            }
        }
        return $this->_options['name'];
    }

    /**
     * @param $id                       database row id
     * @throws Doctrine_Find_Exception
     */
    final public function getProxy($id = null)
    {
        if ($id !== null) {
            $identifierColumnNames = $this->getIdentifierColumnNames();
            $query = 'SELECT ' . implode(', ', (array) $identifierColumnNames)
                . ' FROM ' . $this->getTableName()
                . ' WHERE ' . implode(' = ? && ', (array) $identifierColumnNames) . ' = ?';
            $query = $this->applyInheritance($query);

            $params = array_merge(array($id), array_values($this->_options['inheritanceMap']));

            $this->_data = $this->_conn->execute($query, $params)->fetch(PDO::FETCH_ASSOC);

            if ($this->_data === false)
                return false;
        }
        return $this->getRecord();
    }

    /**
     * applyInheritance
     * @param $where                    query where part to be modified
     * @return string                   query where part with column aggregation inheritance added
     */
    final public function applyInheritance($where)
    {
        if ( ! empty($this->_options['inheritanceMap'])) {
            $a = array();
            foreach ($this->_options['inheritanceMap'] as $field => $value) {
                $a[] = $this->getColumnName($field) . ' = ?';
            }
            $i = implode(' AND ', $a);
            $where .= ' AND ' . $i;
        }
        return $where;
    }

    /**
     * count
     *
     * @return integer
     */
    public function count()
    {
        return $this->createQuery()->count();
    }

    /**
     * @return Doctrine_Query  a Doctrine_Query object
     */
    public function getQueryObject()
    {
        $graph = new Doctrine_Query($this->getConnection());
        $graph->load($this->getComponentName());
        return $graph;
    }

    /**
     * @param string $fieldName
     * @return array
     */
    public function getEnumValues($fieldName)
    {
        $columnName = $this->getColumnName($fieldName);
        if (isset($this->_columns[$columnName]['values'])) {
            return $this->_columns[$columnName]['values'];
        } else {
            return array();
        }
    }

    /**
     * enumValue
     *
     * @param string $field
     * @param integer $index
     * @return mixed
     */
    public function enumValue($fieldName, $index)
    {
        if ($index instanceof Doctrine_Null) {
            return $index;
        }

        $columnName = $this->getColumnName($fieldName);
        if ( ! $this->_conn->getAttribute(Doctrine::ATTR_USE_NATIVE_ENUM)
            && isset($this->_columns[$columnName]['values'][$index])
        ) {
            return $this->_columns[$columnName]['values'][$index];
        }

        return $index;
    }

    /**
     * enumIndex
     *
     * @param string $field
     * @param mixed $value
     * @return mixed
     */
    public function enumIndex($fieldName, $value)
    {
        $values = $this->getEnumValues($fieldName);

        $index = array_search($value, $values);
        if ($index === false || !$this->_conn->getAttribute(Doctrine::ATTR_USE_NATIVE_ENUM)) {
            return $index;
        }
        return $value;
    }

    /**
     * validateField
     *
     * @param string $name
     * @param string $value
     * @param Doctrine_Record $record
     * @return Doctrine_Validator_ErrorStack $errorStack
     */
    public function validateField($fieldName, $value, Doctrine_Record $record = null)
    {
        if ($record instanceof Doctrine_Record) {
            $errorStack = $record->getErrorStack();
        } else {
            $record  = $this->create();
            $errorStack = new Doctrine_Validator_ErrorStack($this->getOption('name'));
        }

        if ($value === self::$_null) {
            $value = null;
        } else if ($value instanceof Doctrine_Record && $value->exists()) {
            $value = $value->getIncremented();
        } else if ($value instanceof Doctrine_Record && ! $value->exists()) {
            foreach($this->getRelations() as $relation) {
                if ($fieldName == $relation->getLocalFieldName() && (get_class($value) == $relation->getClass() || is_subclass_of($value, $relation->getClass()))) {
                    return $errorStack;
                }
            }
        }

        $dataType = $this->getTypeOf($fieldName);

        // Validate field type, if type validation is enabled
        if ($this->getAttribute(Doctrine::ATTR_VALIDATE) & Doctrine::VALIDATE_TYPES) {
            if ( ! Doctrine_Validator::isValidType($value, $dataType)) {
                $errorStack->add($fieldName, 'type');
            }
            if ($dataType == 'enum') {
                $enumIndex = $this->enumIndex($fieldName, $value);
                if ($enumIndex === false && $value !== null) {
                    $errorStack->add($fieldName, 'enum');
                }
            }
        }

        // Validate field length, if length validation is enabled
        if ($this->getAttribute(Doctrine::ATTR_VALIDATE) & Doctrine::VALIDATE_LENGTHS) {
            if ( ! Doctrine_Validator::validateLength($value, $dataType, $this->getFieldLength($fieldName))) {
                $errorStack->add($fieldName, 'length');
            }
        }

        // Run all custom validators
        foreach ($this->getFieldValidators($fieldName) as $validatorName => $args) {
            if ( ! is_string($validatorName)) {
                $validatorName = $args;
                $args = array();
            }

            $validator = Doctrine_Validator::getValidator($validatorName);
            $validator->invoker = $record;
            $validator->field = $fieldName;
            $validator->args = $args;
            if ( ! $validator->validate($value)) {
                $errorStack->add($fieldName, $validator);
            }
        }

        return $errorStack;
    }

    /**
     * getColumnCount
     *
     * @return integer      the number of columns in this table
     */
    public function getColumnCount()
    {
        return $this->columnCount;
    }

    /**
     * returns all columns and their definitions
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->_columns;
    }

    /**
     * Removes the passed field name from the table schema information
     *
     * @return boolean
     */
    public function removeColumn($fieldName)
    {
        if ( ! $this->hasField($fieldName)) {
          return false;
        }

        $columnName = $this->getColumnName($fieldName);
        unset($this->_columnNames[$fieldName], $this->_fieldNames[$columnName], $this->_columns[$columnName]);
        $this->columnCount = count($this->_columns);
        return true;
    }

    /**
     * returns an array containing all the column names.
     *
     * @return array
     */
    public function getColumnNames(array $fieldNames = null)
    {
        if ($fieldNames === null) {
            return array_keys($this->_columns);
        } else {
           $columnNames = array();
           foreach ($fieldNames as $fieldName) {
               $columnNames[] = $this->getColumnName($fieldName);
           }
           return $columnNames;
        }
    }

    /**
     * returns an array with all the identifier column names.
     *
     * @return array
     */
    public function getIdentifierColumnNames()
    {
        return $this->getColumnNames((array) $this->getIdentifier());
    }

    /**
     * returns an array containing all the field names.
     *
     * @return array
     */
    public function getFieldNames()
    {
        return array_values($this->_fieldNames);
    }

    /**
     * getDefinitionOf
     *
     * @return mixed        array on success, false on failure
     */
    public function getDefinitionOf($fieldName)
    {
        $columnName = $this->getColumnName($fieldName);
        return $this->getColumnDefinition($columnName);
    }

    /**
     * getTypeOf
     *
     * @return mixed        string on success, false on failure
     */
    public function getTypeOf($fieldName)
    {
        return $this->getTypeOfColumn($this->getColumnName($fieldName));
    }

    /**
     * getTypeOfColumn
     *
     * @return mixed  The column type or FALSE if the type cant be determined.
     */
    public function getTypeOfColumn($columnName)
    {
        return isset($this->_columns[$columnName]) ? $this->_columns[$columnName]['type'] : false;
    }

    /**
     * setData
     * doctrine uses this function internally
     * users are strongly discouraged to use this function
     *
     * @param array $data               internal data
     * @return void
     */
    public function setData(array $data)
    {
        $this->_data = $data;
    }

    /**
     * returns internal data, used by Doctrine_Record instances
     * when retrieving data from database
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * prepareValue
     * this method performs special data preparation depending on
     * the type of the given column
     *
     * 1. It unserializes array and object typed columns
     * 2. Uncompresses gzip typed columns
     * 3. Gets the appropriate enum values for enum typed columns
     * 4. Initializes special null object pointer for null values (for fast column existence checking purposes)
     *
     * example:
     * <code type='php'>
     * $field = 'name';
     * $value = null;
     * $table->prepareValue($field, $value); // Doctrine_Null
     * </code>
     *
     * @throws Doctrine_Table_Exception     if unserialization of array/object typed column fails or
     * @throws Doctrine_Table_Exception     if uncompression of gzip typed column fails         *
     * @param string $field     the name of the field
     * @param string $value     field value
     * @param string $typeHint  Type hint used to pass in the type of the value to prepare
     *                          if it is already known. This enables the method to skip
     *                          the type determination. Used i.e. during hydration.
     * @return mixed            prepared value
     */
    public function prepareValue($fieldName, $value, $typeHint = null)
    {
        if ($value === self::$_null) {
            return self::$_null;
        } else if ($value === null) {
            return null;
        } else {
            $type = is_null($typeHint) ? $this->getTypeOf($fieldName) : $typeHint;

            switch ($type) {
                case 'integer':
                case 'string';
                    // don't do any casting here PHP INT_MAX is smaller than what the databases support
                break;
                case 'enum':
                    return $this->enumValue($fieldName, $value);
                break;
                case 'boolean':
                    return (boolean) $value;
                break;
                case 'array':
                case 'object':
                    if (is_string($value)) {
                        $value = empty($value) ? null:unserialize($value);

                        if ($value === false) {
                            throw new Doctrine_Table_Exception('Unserialization of ' . $fieldName . ' failed.');
                        }
                        return $value;
                    }
                break;
                case 'gzip':
                    $value = gzuncompress($value);

                    if ($value === false) {
                        throw new Doctrine_Table_Exception('Uncompressing of ' . $fieldName . ' failed.');
                    }
                    return $value;
                break;
            }
        }
        return $value;
    }

    /**
     * getTree
     *
     * getter for associated tree
     *
     * @return mixed  if tree return instance of Doctrine_Tree, otherwise returns false
     */
    public function getTree()
    {
        if (isset($this->_options['treeImpl'])) {
            if ( ! $this->_tree) {
                $options = isset($this->_options['treeOptions']) ? $this->_options['treeOptions'] : array();
                $this->_tree = Doctrine_Tree::factory($this,
                    $this->_options['treeImpl'],
                    $options
                );
            }
            return $this->_tree;
        }
        return false;
    }

    /**
     * getComponentName
     *
     * @return void
     */
    public function getComponentName()
    {
        return $this->_options['name'];
    }

    /**
     * getTableName
     *
     * @return void
     */
    public function getTableName()
    {
        return $this->_options['tableName'];
    }

    /**
     * setTableName
     *
     * @param string $tableName
     * @return void
     */
    public function setTableName($tableName)
    {
        $this->setOption('tableName', $this->_conn->formatter->getTableName($tableName));
    }

    /**
     * isTree
     *
     * determine if table acts as tree
     *
     * @return mixed  if tree return true, otherwise returns false
     */
    public function isTree()
    {
        return ( ! is_null($this->_options['treeImpl'])) ? true : false;
    }
    
    /**
     * getTemplates
     * returns all templates attached to this table
     *
     * @return array     an array containing all templates
     */
    public function getTemplates()
    {
        return $this->_templates;
    }

    /**
     * getTemplate
     *
     * @param string $template
     * @return void
     */
    public function getTemplate($template)
    {
        if ( ! isset($this->_templates[$template])) {
            throw new Doctrine_Table_Exception('Template ' . $template . ' not loaded');
        }

        return $this->_templates[$template];
    }

    /**
     * Check if the table has a template name
     *
     * @param string $template
     * @return boolean $bool
     */
    public function hasTemplate($template)
    {
        return isset($this->_templates[$template]);
    }

    /**
     * Add template to the table
     *
     * @param string $template
     * @param Doctrine_Template $impl
     * @return Doctrine_Table
     */
    public function addTemplate($template, Doctrine_Template $impl)
    {
        $this->_templates[$template] = $impl;

        return $this;
    }

    /**
     * Get all the generators for the table
     *
     * @return array $generators
     */

    public function getGenerators()
    {
        return $this->_generators;
    }

    /**
     * Get generator instance for a passed name
     *
     * @param string $generator
     * @return Doctrine_Record_Generator $generator
     */
    public function getGenerator($generator)
    {
        if ( ! isset($this->_generators[$generator])) {
            throw new Doctrine_Table_Exception('Generator ' . $generator . ' not loaded');
        }

        return $this->_generators[$generator];
    }

    /**
     * Check if a generator name exists
     *
     * @param string $generator
     * @return void
     */
    public function hasGenerator($generator)
    {
        return isset($this->_generators[$generator]);
    }

    /**
     * Add a generate to the table instance
     *
     * @param Doctrine_Record_Generator $generator
     * @param string $name
     * @return Doctrine_Table
     */
    public function addGenerator(Doctrine_Record_Generator $generator, $name = null)
    {
        if ($name === null) {
            $this->_generators[] = $generator;
        } else {
            $this->_generators[$name] = $generator;
        }
        return $this;
    }

    /**
     * bindQueryParts
     * binds query parts to given component
     *
     * @param array $queryParts         an array of pre-bound query parts
     * @return Doctrine_Record          this object
     */
    public function bindQueryParts(array $queryParts)
    {
        $this->_options['queryParts'] = $queryParts;

        return $this;
    }

    /**
     * bindQueryPart
     * binds given value to given query part
     *
     * @param string $queryPart
     * @param mixed $value
     * @return Doctrine_Record          this object
     */
    public function bindQueryPart($queryPart, $value)
    {
        $this->_options['queryParts'][$queryPart] = $value;

        return $this;
    }

    /**
     * Gets the names of all validators that are applied on a field.
     *
     * @param string  The field name.
     * @return array  The names of all validators that are applied on the specified field.
     */
    public function getFieldValidators($fieldName)
    {
        $validators = array();
        $columnName = $this->getColumnName($fieldName);
        // this loop is a dirty workaround to get the validators filtered out of
        // the options, since everything is squeezed together currently
        foreach ($this->_columns[$columnName] as $name => $args) {
             if (empty($name)
                    || $name == 'primary'
                    || $name == 'protected'
                    || $name == 'autoincrement'
                    || $name == 'default'
                    || $name == 'values'
                    || $name == 'sequence'
                    || $name == 'zerofill'
                    || $name == 'owner'
                    || $name == 'scale'
                    || $name == 'type'
                    || $name == 'length'
                    || $name == 'fixed'
                    || $name == 'comment') {
                continue;
            }
            if ($name == 'notnull' && isset($this->_columns[$columnName]['autoincrement'])
                    && $this->_columns[$columnName]['autoincrement'] === true) {
                continue;
            }
            // skip it if it's explicitly set to FALSE (i.e. notnull => false)
            if ($args === false) {
                continue;
            }
            $validators[$name] = $args;
        }

        return $validators;
    }

    /**
     * Gets the (maximum) length of a field.
     */
    public function getFieldLength($fieldName)
    {
        return $this->_columns[$this->getColumnName($fieldName)]['length'];
    }

    /**
     * getBoundQueryPart
     *
     * @param string $queryPart
     * @return string $queryPart
     */
    public function getBoundQueryPart($queryPart)
    {
        if ( ! isset($this->_options['queryParts'][$queryPart])) {
            return null;
        }

        return $this->_options['queryParts'][$queryPart];
    }

    /**
     * unshiftFilter
     *
     * @param  object Doctrine_Record_Filter $filter
     * @return object $this
     */
    public function unshiftFilter(Doctrine_Record_Filter $filter)
    {
        $filter->setTable($this);

        $filter->init();

        array_unshift($this->_filters, $filter);

        return $this;
    }

    /**
     * getFilters
     *
     * @return array $filters
     */
    public function getFilters()
    {
        return $this->_filters;
    }

    /**
     * returns a string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return Doctrine_Lib::getTableAsString($this);
    }

    /**
     * Resolve the passed find by field name to the appropriate field name
     * regardless of whether the user passes a column name, field name, or a Doctrine_Inflector::classified()
     * version of their column name. It will be inflected with Doctrine_Inflector::tableize() 
     * to get the column or field name
     *
     * @param string $name 
     * @return string $fieldName
     */
    protected function _resolveFindByFieldName($name)
    {
        $fieldName = Doctrine_Inflector::tableize($name);
        if ($this->hasColumn($name) || $this->hasField($name)) {
            return $this->getFieldName($this->getColumnName($name));
        } else if ($this->hasColumn($fieldName) || $this->hasField($fieldName)) {
            return $this->getFieldName($this->getColumnName($fieldName));
        } else {
            return false;
        }
    }

    /**
     * __call
     *
     * Adds support for magic finders.
     * findByColumnName, findByRelationAlias
     * findById, findByContactId, etc.
     *
     * @return void
     */
    public function __call($method, $arguments)
    {
        $lcMethod = strtolower($method);

        if (substr($lcMethod, 0, 6) == 'findby') {
            $by = substr($method, 6, strlen($method));
            $method = 'findBy';
        } else if (substr($lcMethod, 0, 9) == 'findoneby') {
            $by = substr($method, 9, strlen($method));
            $method = 'findOneBy';
        }

        if (isset($by)) {
            if ( ! isset($arguments[0])) {
                throw new Doctrine_Table_Exception('You must specify the value to findBy');
            }

            $fieldName = $this->_resolveFindByFieldName($by);
            $hydrationMode = isset($arguments[1]) ? $arguments[1]:null;
            if ($this->hasField($fieldName)) {
                return $this->$method($fieldName, $arguments[0], $hydrationMode);
            } else if ($this->hasRelation($by)) {
                $relation = $this->getRelation($by);

                if ($relation['type'] === Doctrine_Relation::MANY) {
                    throw new Doctrine_Table_Exception('Cannot findBy many relationship.');
                }

                return $this->$method($relation['local'], $arguments[0], $hydrationMode);
            } else {
                throw new Doctrine_Table_Exception('Cannot find by: ' . $by . '. Invalid column or relationship alias.');
            }
        }

        // Forward the method on to the record instance and see if it has anything or one of its behaviors
        try {
            return call_user_func_array(array($this->getRecordInstance(), $method . 'TableProxy'), $arguments);
        } catch (Doctrine_Record_UnknownPropertyException $e) {}

        throw new Doctrine_Table_Exception(sprintf('Unknown method %s::%s', get_class($this), $method));
    }
}
