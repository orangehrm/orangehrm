<?php

/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * You should have received a copy of the OrangeHRM Enterprise  proprietary license file along
 * with this program; if not, write to the OrangeHRM Inc. 538 Teal Plaza, Secaucus , NJ 0709
 * to get the file.
 *
 */

/**
 * Sql Data Generator class 
 *
 */
class SqlDataGenerator extends DataGenerator {

    const DISPLAY_GROUP_TYPE_ONE = "one";
    const DISPLAY_GROUP_TYPE_Many = "many";
    const EMPTY_FIELD_FILL = "---";
    const SEPERATOR = '|\n|';

    private $reportDefinition;
    private $logger;

    
    function __construct($reportDefinition) {
        $this->reportDefinition = $reportDefinition;
    }
    

    /**
     * Get Logger instance. Creates if not already created.
     *
     * @return Logger
     */
    protected function getLogger() {
        if (is_null($this->logger)) {
            $this->logger = Logger::getLogger('report.sqldatagenerator');
        }

        return($this->logger);
    }    

    /**
     * Constructs query using report definition and return results for a single
     * sub report.
     * @param integer $offset
     * @param integer $pageLimit
     * @param string[][] $values
     * @return string[]
     * @throws ReportException
     */
    public function getResultSet($offset = NULL, $pageLimit = NULL, $values = NULL) {
        $queryPart = $this->getQuery();
        $selectStatement = $this->generateSqlSelectStatement();

        if (!is_null($values)) {
            list($queryPart, $parameters) = $this->constructWhereClausePart($queryPart, $values);
        }

        if (!(is_null($offset) || is_null($pageLimit))) {
            $query = $selectStatement . $queryPart . " LIMIT " . $offset . ", " . ($pageLimit);
        } else if (is_null($offset) && is_null($pageLimit)) {
            $query = $selectStatement . " " . $queryPart;
        } else {
            throw new ReportException("Both offset and page limit should have a value or both should not have a value!!!!");
        }
        $this->getLogger()->debug($query);
        $results = $this->executeQuery($query, $parameters);
        return $results;
    }

    /**
     * Gets the query of a current sub report.
     * @return string
     */
    public function getQuery() {
        $subReport = $this->reportDefinition->getCurrentSubReport();
        $query = $this->reportDefinition->getSubReportQuery($subReport);
        return $query;
    }

    /**
     * Where clauses are dynamically constructed. This method constructs where
     * clause using given values.
     * @param string $queryPart
     * @param string[][] $values
     * @return array
     */
    protected function constructWhereClausePart($queryPart, $values) {
        
        $parameters = array();        
        
//        $where = 'WHERE $X{IN,hs_hr_employee.emp_firstname,firstName} AND $X{IN,hs_hr_employee.emp_lastname,lastName}';
        $pattern = '/\$X\{(.*?)\}/';

        $callback = function($matches) use ($values, &$parameters) {
                    $val = explode(",", $matches[1]);
                    
                    // $X{VAR,name}
                    
                    if ((count($val) == 2 && $val[0] == 'VAR')) {
                        
                        $operator = $val[0];
                        $name = $val[1];                        
                    } else {
                    
                        if (count($val) < 3) {                        
                                throw new ReportException('Invalid filter definition: ' . $matches[0]);
                        }
                        
                        $operator = $val[0];
                        $field = $val[1];
                        $name = $val[2];                        
                    }
                   
                    // If no value defined for this filter, ignore it (filter is set to true)
                    if (!isset($values[$name]) || is_null($values[$name])) {
                        return "true";
                    }
                    if ($operator == 'IN') {
                        
                        $valueArray = $values[$name];
                        
                        if (!is_array($valueArray) && is_string($valueArray)) {
                            $valueArray = explode(',', $valueArray);
                        }
                        if (!is_array($valueArray) || count($valueArray) == 0) {
                            return "true";
                        }                        

                        $placeHolders = rtrim(str_repeat('?,', count($valueArray)), ',');
                        $clause = $field . " IN (" . $placeHolders . ")";
                        
                        $parameters = array_merge($parameters, $valueArray);

                    } else if ($operator == '=') {
                        $clause = $field . ' = ?';
                        $parameters[] = $values[$name];
                                                
                    } else if ($operator == 'BETWEEN') {
                        $clause = $field . ' BETWEEN ? AND ?';
                        $parameters[] = $values[$name]['from'];
                        $parameters[] = $values[$name]['to'];
                    } else if ($operator == '>') {
                        $clause = $field . ' > ?';
                        $parameters[] = $values[$name];
                    } else if ($operator == '<') {
                        $clause = $field . ' < ?';
                        $parameters[] = $values[$name];
                    } else if ($operator == '>=') {
                        $clause = $field . ' >= ?';
                        $parameters[] = $values[$name];
                    } else if ($operator == '<=') {
                        $clause = $field . ' <= ?';
                        $parameters[] = $values[$name];
                    } else if ($operator == 'IS NOT NULL') {
                        if ($values[$name] == 'TRUE') {
                            $clause = $field . ' IS NOT NULL';
                        } else {
                            $clause = 'true';
                        }
                    } else if ($operator == 'IS NULL') {
                        if ($values[$name] == 'TRUE') {
                            $clause = $field . ' IS NULL';
                        } else {
                            $clause = 'true';
                        }
                    } else if ($operator == 'VAR') {
                        $clause = $values[$name];
                    } 

                    return $clause;
                };

        $str = preg_replace_callback($pattern, $callback, $queryPart);

        return array($str, $parameters);
    }

    /**
     * This method executes the given query.
     * @param string $query Query with placeholders for parameters
     * @param array $parameters Query parameters
     * @return string[]
     * @throws ReportException
     */
    public function executeQuery($query, $parameters = array()) {
        $pdo = Doctrine_Manager::connection()->getDbh();
        $results = array();

        try {
            $statement = $pdo->prepare($query);            
            $result = $statement->execute($parameters);
            
            // We are running with PDO::ATTR_ERRMODE set to PDO::ERRMODE_EXCEPTION, so any errors will
            // result in exceptions
            if ($result) {
                $results = $statement->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $exc) {
            throw new ReportException("Invalid Query " . $exc->getMessage() . " Query is : " . $query, 0, $exc);
        }

        return $results;
    }

    /**
     * Field elements contains information needed to build sql select statement.
     * This method uses that information to generate sql select statement.
     * @return string
     * @throws ReportException
     */
    public function generateSqlSelectStatement() {
        $subReport = $this->reportDefinition->getCurrentSubReport();

        $this->reportDefinition->resetDisplayGroup();

        $displayGroups = $this->reportDefinition->getDisplayGroups($subReport);
        if (is_null($displayGroups)) {
            throw new ReportException("Invalid XML Definition!!! ( DisplayGroups should be defined )");
        }

        $displayGroup = $this->reportDefinition->getCurrentDisplayGroup($displayGroups);
        if (is_null($displayGroup)) {
            throw new ReportException("Invalid XML Definition!!! ( At least one display group should be defined )");
        }

        $selectStatement = "SELECT ";

        while (!is_null($displayGroup)) {

            $displayGroupType = $this->reportDefinition->getDisplayGroupType($displayGroup);
            if (is_null($displayGroupType) || empty($displayGroupType)) {
                throw new ReportException("Invalid XML Definition!!! ( Display Group Type is not defined )");
            }

            if ($displayGroupType == self::DISPLAY_GROUP_TYPE_ONE) {

                $this->reportDefinition->resetField();
                $fields = $this->reportDefinition->getFields($displayGroup);

                if (is_null($fields)) {
                    throw new ReportException("Invalid XML Definition!!! ( There are no fields defined )");
                }

                $field = $this->reportDefinition->getCurrentField($fields);
                if (is_null($field)) {
                    throw new ReportException("Invalid XML Definition!!! ( There is no field defined )");
                }

                $firstRound = TRUE;
                while (!is_null($field)) {

                    if (!$firstRound) {
                        $selectStatement .= ",";
                    } else {
                        $firstRound = FALSE;
                    }

                    $fieldName = $this->reportDefinition->getFieldName($field);
                    if (is_null($fieldName) || empty($fieldName)) {
                        throw new ReportException("Invalid XML Definition!!! ( A field should have a name )");
                    }

                    $fieldAlias = $this->reportDefinition->getFieldAlias($field);

                    if ($fieldAlias != "") {
                        $fieldName = $fieldName . " AS " . $fieldAlias . " ";
                    }

                    $selectStatement = $selectStatement . " " . $fieldName;

                    $field = $this->reportDefinition->getNextField($fields);
                }
            } else if ($displayGroupType == self::DISPLAY_GROUP_TYPE_Many) {

                $this->reportDefinition->resetField();
                $fields = $this->reportDefinition->getFields($displayGroup);
                if (is_null($fields)) {
                    throw new ReportException("Invalid XML Definition!!! ( There are no fields defined )");
                }

                $field = $this->reportDefinition->getCurrentField($fields);
                if (is_null($field)) {
                    throw new ReportException("Invalid XML Definition!!! ( There is no field defined )");
                }

                $displayGroupName = $this->reportDefinition->getDisplayGroupName($displayGroup);
                if (is_null($displayGroupName) || empty($displayGroupName)) {
                    throw new ReportException("Invalid XML Definition!!! ( Display group name is not defined )");
                }

                $groupConcat = "GROUP_CONCAT(DISTINCT CONCAT_WS('|^^|'";

                while (!is_null($field)) {
                    $fieldName = $this->reportDefinition->getFieldName($field);
                    if (is_null($fieldName) || empty($fieldName)) {
                        throw new ReportException("Invalid XML Definition!!! ( A field should have a name )");
                    }

                    $groupConcat = $groupConcat . ", (CASE WHEN " . $fieldName . " IS NULL OR " . $fieldName . " = ' ' THEN '" . self::EMPTY_FIELD_FILL . "' ELSE " . $fieldName . " END )";

                    $field = $this->reportDefinition->getNextField($fields);
                }

                $groupConcat = $groupConcat . ") SEPARATOR '" . self::SEPERATOR . "')";
                $selectStatement = $selectStatement . " , " . $groupConcat . " AS " . $displayGroupName;
            } else {
                throw new ReportException("Invalid XML Definition!!! ( Invalid group type ) ");
            }

            $displayGroup = $this->reportDefinition->getNextDisplayGroup($displayGroups);
        }

        $this->reportDefinition->resetField();
        $this->reportDefinition->resetDisplayGroup();
        return $selectStatement;
    }

    /**
     * Gets the number of records that is returned when appliying values as 
     * filters.
     * @param string[][] $values
     * @return integer
     */
    public function getNumOfRecords($values = NULL) {
        $this->reportDefinition->resetSubReport();

        $queryPart = $this->getQuery();

        if (!is_null($values)) {
            list($queryPart, $parameters) = $this->constructWhereClausePart($queryPart, $values);
        }

        $query = "SELECT COUNT(*) As count " . $queryPart;
        $results = $this->executeQuery($query, $parameters);

        $count = count($results);
        if ($count == 1) {
            $count = $results[0]["count"];
        }

        return $count;
    }

}

