<?php
/*
 * This file is a modified version of sql.php from the PHPMyAdmin project. (http://www.phpmyadmin.net/)
 * (Original file: http://phpmyadmin.svn.sourceforge.net/viewvc/phpmyadmin/trunk/phpMyAdmin/libraries/export/sql.php)
 *
 * See license/3rdParty/PHPMyAdmin.license for the license.
 *
 * Modifications done by OrangeHRM.
 * 1) Function PMA_getTableDef() was renamed to getTableConstraints()
 *    All other functioins were removed from the file and function was changed for OrangeHRM requirements.
 */
	function getTableConstraints($table) {
		$crlf = "\r\n";

		$sql_constraints = "";
		$result = mysql_query('SHOW CREATE TABLE `'.$table.'`');

    	if ($result != FALSE && ($row = mysql_fetch_row($result))) {
        	$create_query = $row[1];
        	unset($row);

        	// Convert end of line chars to one that we want (note that MySQL doesn't return query it will accept in all cases)
        	if (strpos($create_query, "(\r\n ")) {
            	$create_query = str_replace("\r\n", $crlf, $create_query);
       		} elseif (strpos($create_query, "(\n ")) {
            	$create_query = str_replace("\n", $crlf, $create_query);
        	} elseif (strpos($create_query, "(\r ")) {
            	$create_query = str_replace("\r", $crlf, $create_query);
        	}

        	// Should we use IF NOT EXISTS?
        	if (isset($GLOBALS['if_not_exists'])) {
            	$create_query     = preg_replace('/^CREATE TABLE/', 'CREATE TABLE IF NOT EXISTS', $create_query);
        	}

        	// are there any constraints to cut out?
        	if (preg_match('@CONSTRAINT|FOREIGN[\s]+KEY@', $create_query)) {

            	// Split the query into lines, so we can easily handle it. We know lines are separated by $crlf (done few lines above).
            	$sql_lines = explode($crlf, $create_query);
            	$sql_count = count($sql_lines);

            	// lets find first line with constraints
            	for ($i = 0; $i < $sql_count; $i++) {
                	if (preg_match('@CONSTRAINT|FOREIGN[\s]+KEY@', $sql_lines[$i])) break;
            	}

            	// remove , from the end of create statement
            	$sql_lines[$i - 1] = preg_replace('@,$@', '', $sql_lines[$i - 1]);

            	$sql_constraints .= 'ALTER TABLE `'.$table.'`';

            	$first = TRUE;
            	for($j = $i; $j < $sql_count; $j++) {
                	if (preg_match('@CONSTRAINT|FOREIGN[\s]+KEY@', $sql_lines[$j])) {
                    	if (!$first) {
                        	$sql_constraints .= $crlf;
                    	}
                    	if (strpos($sql_lines[$j], 'CONSTRAINT') === FALSE) {
                        	$sql_constraints .= preg_replace('/(FOREIGN[\s]+KEY)/', 'ADD \1', $sql_lines[$j]);
                    	} else {
                        	$sql_constraints .= preg_replace('/(CONSTRAINT)/', 'ADD \1', $sql_lines[$j]);
                    	}
                    	$first = FALSE;
                	} else {
                    	break;
                	}
            	}
            	$sql_constraints .= ';' . $crlf;
            	$create_query = implode($crlf, array_slice($sql_lines, 0, $i)) . $crlf . implode($crlf, array_slice($sql_lines, $j, $sql_count - 1));
            	unset($sql_lines);
        	}

   		}

    	return $sql_constraints;

	}

?>
