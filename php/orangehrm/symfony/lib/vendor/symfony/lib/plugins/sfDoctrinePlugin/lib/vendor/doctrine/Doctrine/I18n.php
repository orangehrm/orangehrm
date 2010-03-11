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
 * Doctrine_I18n
 *
 * @package     Doctrine
 * @subpackage  I18n
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision$
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 */
class Doctrine_I18n extends Doctrine_Record_Generator
{
    protected $_options = array(
                            'className'     => '%CLASS%Translation',
                            'fields'        => array(),
                            'generateFiles' => false,
                            'table'         => false,
                            'pluginTable'   => false,
                            'children'      => array(),
                            'type'          => 'string',
                            'length'        => 2,
                            'options'       => array()
                            );

    /**
     * __construct
     *
     * @param string $options 
     * @return void
     */
    public function __construct($options)
    {
        $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);
    }

    public function buildRelation()
    {
        $this->buildForeignRelation('Translation');
        $this->buildLocalRelation();
    }

    /**
     * buildDefinition
     *
     * @param object $Doctrine_Table
     * @return void
     */
    public function setTableDefinition()
    {
      	if (empty($this->_options['fields'])) {
      	    throw new Doctrine_I18n_Exception('Fields not set.');
      	}

        $options = array('className' => $this->_options['className']);

        $cols = $this->_options['table']->getColumns();

        $columns = array();
        foreach ($cols as $column => $definition) {
            $fieldName = $this->_options['table']->getFieldName($column);
            if (in_array($fieldName, $this->_options['fields'])) {
                if ($column != $fieldName) {
                    $column .= ' as ' . $fieldName;
                }
                $columns[$column] = $definition;
                $this->_options['table']->removeColumn($fieldName);
            }
        }

        $this->hasColumns($columns);

        $options = $this->_options['options'];
        $options['fixed'] = true;
        $options['primary'] = true;

        $this->hasColumn('lang', $this->_options['type'], $this->_options['length'], $options);

        $this->bindQueryParts(array('indexBy' => 'lang'));
 
        // Rewrite any relations to our original table
        $originalName = $this->_options['table']->getClassnameToReturn();
        $relations = $this->_options['table']->getRelationParser()->getPendingRelations();
        foreach($relations as $table => $relation) {
            if ($table != $this->_table->getTableName() ) {
                // check that the localColumn is part of the moved col
                if (isset($relation['local']) && in_array($relation['local'], $this->_options['fields'])) {
                    // found one, let's rewrite it
                    $this->_options['table']->getRelationParser()->unsetPendingRelations($table);
        
                    // and bind the rewritten one
                    $this->_table->getRelationParser()->bind($table, $relation);
        
                    // now try to get the reverse relation, to rewrite it
                    $rp = Doctrine::getTable($table)->getRelationParser();
                    $others = $rp->getPendingRelation($originalName);
                    if (isset($others)) {
                        $others['class'] = $this->_table->getClassnameToReturn();
                        $others['alias'] = $this->_table->getClassnameToReturn();
                        $rp->unsetPendingRelations($originalName);
                        $rp->bind($this->_table->getClassnameToReturn() ,$others);
                    }
                }
            }
        }
    }
}