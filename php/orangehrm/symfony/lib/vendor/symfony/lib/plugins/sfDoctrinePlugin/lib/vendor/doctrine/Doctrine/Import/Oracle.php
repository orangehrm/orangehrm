<?php
/*
 *  $Id: Oracle.php 5850 2009-06-09 08:52:35Z jwage $
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
 * @package     Doctrine
 * @subpackage  Import
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @version     $Revision: 5850 $
 * @link        www.phpdoctrine.org
 * @since       1.0
 */
class Doctrine_Import_Oracle extends Doctrine_Import
{
    /**
     * lists all databases
     *
     * @return array
     */
    public function listDatabases()
    {
        if ( ! $this->conn->getAttribute(Doctrine::ATTR_EMULATE_DATABASE)) {
            throw new Doctrine_Import_Exception('database listing is only supported if the "emulate_database" option is enabled');
        }
        /**
        if ($this->conn->options['database_name_prefix']) {
            $query = 'SELECT SUBSTR(username, ';
            $query.= (strlen($this->conn->getAttribute(['database_name_prefix'])+1);
            $query.= ") FROM sys.dba_users WHERE username LIKE '";
            $query.= $this->conn->options['database_name_prefix']."%'";
        } else {
        */
        $query   = 'SELECT username FROM sys.dba_users';

        $result2 = $this->conn->standaloneQuery($query);
        $result  = $result2->fetchColumn();

        return $result;
    }

    /**
     * lists all availible database functions
     *
     * @return array
     */
    public function listFunctions()
    {
        $query = "SELECT name FROM sys.user_source WHERE line = 1 AND type = 'FUNCTION'";

        return $this->conn->fetchColumn($query);
    }

    /**
     * lists all database triggers
     *
     * @param string|null $database
     * @return array
     */
    public function listTriggers($database = null)
    {

    }

    /**
     * lists all database sequences
     *
     * @param string|null $database
     * @return array
     */
    public function listSequences($database = null)
    {
        $query = "SELECT sequence_name FROM sys.user_sequences";

        $tableNames = $this->conn->fetchColumn($query);

        return array_map(array($this->conn->formatter, 'fixSequenceName'), $tableNames);
    }

    /**
     * lists table constraints
     *
     * @param string $table     database table name
     * @return array
     */
    public function listTableConstraints($table)
    {
        $table = $this->conn->quote($table, 'text');

        $query = 'SELECT index_name name FROM user_constraints'
               . ' WHERE table_name = ' . $table . ' OR table_name = ' . strtoupper($table);

        $constraints = $this->conn->fetchColumn($query);

        return array_map(array($this->conn->formatter, 'fixIndexName'), $constraints);
    }

    /**
     * lists table constraints
     *
     * @param string $table     database table name
     * @return array
     */
    public function listTableColumns($table)
    {
		$sql = <<<QEND
SELECT tc.column_name, data_type,
CASE WHEN data_type = 'NUMBER' THEN data_precision ELSE data_length END AS data_length,
nullable, data_default, data_scale, data_precision, pk.primary
FROM all_tab_columns tc
LEFT JOIN (
 select 'primary' primary, cc.table_name, cc.column_name from all_constraints cons
 join all_cons_columns cc on cons.constraint_name = cc.constraint_name
 where cons.constraint_type = 'P'
) pk ON pk.column_name = tc.column_name and pk.table_name = tc.table_name
WHERE tc.table_name = :tableName ORDER BY column_id
QEND;
        $result = $this->conn->fetchAssoc($sql, array(':tableName' => $table));

        $descr = array();

        foreach($result as $val) {
            $val = array_change_key_case($val, CASE_LOWER);
            $decl = $this->conn->dataDict->getPortableDeclaration($val);

            $descr[$val['column_name']] = array(
               'name'       => $val['column_name'],
               'notnull'    => (bool) ($val['nullable'] === 'N'),
               'ntype'      => $val['data_type'],
               'type'       => $decl['type'][0],
               'alltypes'   => $decl['type'],
               'fixed'      => $decl['fixed'],
               'unsigned'   => $decl['unsigned'],
               'default'    => $val['data_default'],
               'length'     => $val['data_length'],
               'primary'    => $val['primary'] ? true:false,
               'scale'      => isset($val['scale']) ? $val['scale']:null,
            );
        }

        return $descr;
    }

    /**
     * lists table constraints
     *
     * @param string $table     database table name
     * @return array
     */
    public function listTableIndexes($table)
    {
        $table = $this->conn->quote($table, 'text');
        $query = 'SELECT index_name name FROM user_indexes'
               . ' WHERE table_name = ' . $table . ' OR table_name = ' . strtoupper($table)
               . ' AND generated = ' . $this->conn->quote('N', 'text');

        $indexes = $this->conn->fetchColumn($query);

        return array_map(array($this->conn->formatter, 'fixIndexName'), $indexes);
    }
    
    /**
     * list table relations
     */
    public function listTableRelations($table)
    {
        $relations = array();
        $sql  = 'SELECT ac.table_name AS referenced_table_name, lcc.column_name AS local_column_name, rcc.column_name AS referenced_column_name '
              . 'FROM all_constraints ac '
              . 'JOIN all_cons_columns lcc ON ac.r_constraint_name = lcc.constraint_name '
              . 'JOIN all_cons_columns rcc ON ac.constraint_name = rcc.constraint_name '
              . "WHERE ac.constraint_type = 'R'" 
              . "AND ac.r_constraint_name IN (SELECT constraint_name FROM all_constraints WHERE constraint_type IN ('P', 'U') AND table_name = :tableName)";
        
        $results = $this->conn->fetchAssoc($sql, array(':tableName' => $table));
        foreach ($results as $result) 
        {
            $result = array_change_key_case($result, CASE_LOWER);
            $relations[] = array('table'   => $result['referenced_table_name'],
                                 'local'   => $result['local_column_name'],
                                 'foreign' => $result['referenced_column_name']);
        }
        return $relations;
    }
    /**
     * lists tables
     *
     * @param string|null $database
     * @return array
     */
    public function listTables($database = null)
    {
        $query = 'SELECT table_name FROM sys.user_tables';
        return $this->conn->fetchColumn($query);
    }

    /**
     * lists table triggers
     *
     * @param string $table     database table name
     * @return array
     */
    public function listTableTriggers($table)
    {

    }

    /**
     * lists table views
     *
     * @param string $table     database table name
     * @return array
     */
    public function listTableViews($table)
    {

    }

    /**
     * lists database users
     *
     * @return array
     */
    public function listUsers()
    {
        /**
        if ($this->conn->options['emulate_database'] && $this->conn->options['database_name_prefix']) {
            $query = 'SELECT SUBSTR(username, ';
            $query.= (strlen($this->conn->options['database_name_prefix'])+1);
            $query.= ") FROM sys.dba_users WHERE username NOT LIKE '";
            $query.= $this->conn->options['database_name_prefix']."%'";
        } else {
        */

        $query = 'SELECT username FROM sys.dba_users';
        //}

        return $this->conn->fetchColumn($query);
    }

    /**
     * lists database views
     *
     * @param string|null $database
     * @return array
     */
    public function listViews($database = null)
    {
        $query = 'SELECT view_name FROM sys.user_views';
        return $this->conn->fetchColumn($query);
    }
}
