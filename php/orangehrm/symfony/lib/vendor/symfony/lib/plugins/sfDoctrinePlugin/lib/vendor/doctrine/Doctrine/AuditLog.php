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
 * Doctrine_AuditLog
 *
 * @package     Doctrine
 * @subpackage  AuditLog
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision$
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 */
class Doctrine_AuditLog extends Doctrine_Record_Generator
{
    /**
     * Array of AuditLog Options
     *
     * @var string
     */
    protected $_options = array(
                            'className'     => '%CLASS%Version',
                            'versionColumn' => 'version',
                            'tableName'     => false,
                            'generateFiles' => false,
                            'table'         => false,
                            'pluginTable'   => false,
                            'children'      => array(),
                            'auditLog'      => true,
                            );

    /**
     * Accepts array of options to configure the AuditLog
     *
     * @param   array $options An array of options
     * @return  void
     */
    public function __construct(array $options = array())
    {
        $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);
    }

    /**
     * Set the table definition for the audit log table
     *
     * @return  void
     */
    public function setTableDefinition()
    {
        $name = $this->_options['table']->getComponentName();
        $columns = $this->_options['table']->getColumns();

        // remove all sequence, autoincrement and unique constraint definitions and add to the behavior model
        foreach ($columns as $column => $definition) {
            unset($definition['autoincrement']);
            unset($definition['sequence']);
            unset($definition['unique']);

            $fieldName = $this->_options['table']->getFieldName($column);
            if ($fieldName != $column) {
                $name = $column . ' as ' . $fieldName;
            } else {
                $name = $fieldName;
            }

            $this->hasColumn($name, $definition['type'], $definition['length'], $definition);
        }

        // the version column should be part of the primary key definition
        $this->hasColumn($this->_options['versionColumn'], 'integer', 8, array('primary' => true));
    }

    /**
     * Get array of information for the passed record and the specified version
     *
     * @param   Doctrine_Record $record
     * @param   integer         $version
     * @return  array           An array with version information
     */
    public function getVersion(Doctrine_Record $record, $version)
    {
        $className = $this->_options['className'];

        $q = new Doctrine_Query();

        $values = array();
        foreach ((array) $this->_options['table']->getIdentifier() as $id) {
            $conditions[] = $className . '.' . $id . ' = ?';
            $values[] = $record->get($id);
        }

        $where = implode(' AND ', $conditions) . ' AND ' . $className . '.' . $this->_options['versionColumn'] . ' = ?';

        $values[] = $version;

        $q->from($className)
          ->where($where);

        return $q->execute($values, Doctrine::HYDRATE_ARRAY);
    }

    /**
     * Get the max version number for a given Doctrine_Record
     *
     * @param Doctrine_Record $record
     * @return Integer $versionnumber
     */
    public function getMaxVersion(Doctrine_Record $record)
    {
        $className = $this->_options['className'];
        $select = 'MAX(' . $className . '.' . $this->_options['versionColumn'] . ') max_version';

        foreach ((array) $this->_options['table']->getIdentifier() as $id) {
            $conditions[] = $className . '.' . $id . ' = ?';
            $values[] = $record->get($id);
        }

        $q = Doctrine_Query::create()
                ->select($select)
                ->from($className)
                ->where(implode(' AND ',$conditions));

        $result = $q->execute($values, Doctrine::HYDRATE_ARRAY);

        return isset($result[0]['max_version']) ? $result[0]['max_version']:0;
    }
}