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
 * Doctrine_Hydrate_RecordDriver
 * Hydration strategy used for creating collections of entity objects.
 *
 * @package     Doctrine
 * @subpackage  Hydrate
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision$
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @author      Roman Borschel <roman@code-factory.org>
 */
class Doctrine_Hydrator_RecordDriver extends Doctrine_Locator_Injectable
{
    protected $_collections = array();
    protected $_tables = array();
    private $_initializedRelations = array();

    public function getElementCollection($component)
    {
        $coll = new Doctrine_Collection($component);
        $this->_collections[] = $coll;

        return $coll;
    }

    public function getLastKey($coll) 
    {
        $coll->end();
        
        return $coll->key();
    }
    
    public function initRelated(Doctrine_Record $record, $name)
    {
        if ( ! isset($this->_initializedRelations[$record->getOid()][$name])) {
            $relation = $record->getTable()->getRelation($name);
            $coll = new Doctrine_Collection($relation->getTable()->getComponentName());
            $coll->setReference($record, $relation);
            $record[$name] = $coll;
            $this->_initializedRelations[$record->getOid()][$name] = true;
        }
        return true;
    }
    
    public function registerCollection(Doctrine_Collection $coll)
    {
        $this->_collections[] = $coll;
    }
    
    public function getNullPointer() 
    {
        return self::$_null;
    }
    
    public function getElement(array $data, $component)
    {
        $component = $this->_getClassNameToReturn($data, $component);
        if ( ! isset($this->_tables[$component])) {
            $this->_tables[$component] = Doctrine::getTable($component);
            $this->_tables[$component]->setAttribute(Doctrine::ATTR_LOAD_REFERENCES, false);
        }

        $this->_tables[$component]->setData($data);
        $record = $this->_tables[$component]->getRecord();

        return $record;
    }
    
    public function flush()
    {
        // take snapshots from all initialized collections
        foreach ($this->_collections as $key => $coll) {
            $coll->takeSnapshot();
        }
        foreach ($this->_tables as $table) {
            $table->setAttribute(Doctrine::ATTR_LOAD_REFERENCES, true);
        }
        $this->_initializedRelations = null;
        $this->_collections = null;
        $this->_tables = null;
    }
    
    /**
     * sets the last element of given data array / collection
     * as previous element
     *
     * @param boolean|integer $index
     * @return void
     * @todo Detailed documentation
     */
    public function setLastElement(&$prev, &$coll, $index, $dqlAlias, $oneToOne)
    {
        if ($coll === self::$_null) {
            unset($prev[$dqlAlias]); // Ticket #1228
            return;
        }

        if ($index !== false) {
            // Link element at $index to previous element for the component
            // identified by the DQL alias $alias
            $prev[$dqlAlias] = $coll[$index];
            return;
        }
        
        if (count($coll) > 0) {
            $prev[$dqlAlias] = $coll->getLast();
        }
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
     *
     */
    protected function _getClassnameToReturn(array &$data, $component)
    {
        if ( ! isset($this->_tables[$component])) {
            $this->_tables[$component] = Doctrine::getTable($component);
            $this->_tables[$component]->setAttribute(Doctrine::ATTR_LOAD_REFERENCES, false);
        }
        
        if ( ! ($subclasses = $this->_tables[$component]->getOption('subclasses'))) {
            return $component;
        }
        
        foreach ($subclasses as $subclass) {
            $table = Doctrine::getTable($subclass);
            $inheritanceMap = $table->getOption('inheritanceMap');
            list($key, $value) = each($inheritanceMap);
            $key = $this->_tables[$component]->getFieldName($key);
            if ( ! isset($data[$key]) || $data[$key] != $value) {
                continue;
            } else {
                return $table->getComponentName();
            }
        }
        return $component;
    }
}
