<?php

require_once 'propel/engine/builder/om/php5/PHP5PeerBuilder.php';

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @package    symfony
 * @subpackage propel
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: SfPeerBuilder.php 17357 2009-04-16 11:46:01Z FabianLange $
 */
class SfPeerBuilder extends PHP5PeerBuilder
{
  public function build()
  {
    $peerCode = parent::build();
    if (!DataModelBuilder::getBuildProperty('builderAddComments'))
    {
      $peerCode =  sfToolkit::stripComments($peerCode);
    }

    if (!DataModelBuilder::getBuildProperty('builderAddIncludes'))
    {
      // remove all inline includes: peer class include inline the mapbuilder classes
      $peerCode = preg_replace("/(include|require)_once\s*.*Base.*Peer\.php.*\s*/", "", $peerCode);
      $peerCode = preg_replace("/(include|require)_once\s*.*MapBuilder\.php.*\s*/", "", $peerCode);
    }

    // change Propel::import() calls to sfPropel::import()
    $peerCode = str_replace('Propel::import(', 'sfPropel::import(', $peerCode);

    return $peerCode;
  }

  protected function addIncludes(&$script)
  {
    if (!DataModelBuilder::getBuildProperty('builderAddIncludes'))
    {
      return;
    }

    parent::addIncludes($script);
  }

  protected function addSelectMethods(&$script)
  {
    parent::addSelectMethods($script);

    if ($this->getTable()->getAttribute('isI18N'))
    {
      $this->addDoSelectWithI18n($script);
      $this->addI18nMethods($script);
    }

    $this->addUniqueColumnNamesMethod($script);
  }

  protected function addI18nMethods(&$script)
  {
    $table = $this->getTable();
    foreach ($table->getReferrers() as $fk)
    {
      $tblFK = $fk->getTable();
      if ($tblFK->getName() == $table->getAttribute('i18nTable'))
      {
        $i18nClassName = $tblFK->getPhpName();
        break;
      }
    }

    $script .= "

  /**
   * Returns the i18n model class name.
   *
   * @return string The i18n model class name
   */
  public static function getI18nModel()
  {
    return '$i18nClassName';
  }
";
  }

  protected function addDoSelectWithI18n(&$script)
  {
    $table = $this->getTable();
    $thisTableObjectBuilder = OMBuilder::getNewObjectBuilder($table);
    $className = $table->getPhpName();
    $pks = $table->getPrimaryKey();
    $pk = PeerBuilder::getColumnName($pks[0], $className);

    // get i18n table name and culture column name
    foreach ($table->getReferrers() as $fk)
    {
      $tblFK = $fk->getTable();
      if ($tblFK->getName() == $table->getAttribute('i18nTable'))
      {
        $i18nClassName = $tblFK->getPhpName();

        // FIXME
        $i18nPeerClassName = $i18nClassName.'Peer';

        $i18nTable = $table->getDatabase()->getTable($tblFK->getName());
        $i18nTableObjectBuilder = OMBuilder::getNewObjectBuilder($i18nTable);
        $i18nTablePeerBuilder = OMBuilder::getNewPeerBuilder($i18nTable);
        $i18nPks = $i18nTable->getPrimaryKey();
        $i18nPk = PeerBuilder::getColumnName($i18nPks[0], $i18nClassName);

        $culturePhpName = '';
        $cultureColumnName = '';
        foreach ($tblFK->getColumns() as $col)
        {
          if (('true' == trim(strtolower($col->getAttribute('isCulture')))))
          {
            $culturePhpName = $col->getPhpName();
            $cultureColumnName = PeerBuilder::getColumnName($col, $i18nClassName);
          }
        }
      }
    }

    $script .= "

  /**
   * Selects a collection of $className objects pre-filled with their i18n objects.
   *
   * @return array Array of $className objects.
   * @throws PropelException Any exceptions caught during processing will be
   *     rethrown wrapped into a PropelException.
   */
  public static function doSelectWithI18n(Criteria \$c, \$culture = null, PropelPDO \$con = null)
  {
    // we're going to modify criteria, so copy it first
    \$c = clone \$c;
    if (\$culture === null)
    {
      \$culture = sfPropel::getDefaultCulture();
    }
";

    if (DataModelBuilder::getBuildProperty('builderAddBehaviors'))
    {
      $script .= "

    foreach (sfMixer::getCallables('{$this->getClassname()}:doSelectJoin:doSelectJoin') as \$callable)
    {
      call_user_func(\$callable, '{$this->getClassname()}', \$c, \$con);
    }

";
    }

    $script .= "
    // Set the correct dbName if it has not been overridden
    if (\$c->getDbName() == Propel::getDefaultDB())
    {
      \$c->setDbName(self::DATABASE_NAME);
    }

    ".$this->getPeerClassname()."::addSelectColumns(\$c);
    \$startcol = (".$this->getPeerClassname()."::NUM_COLUMNS - ".$this->getPeerClassname()."::NUM_LAZY_LOAD_COLUMNS);

    ".$i18nPeerClassName."::addSelectColumns(\$c);

    \$c->addJoin(".$pk.", ".$i18nPk.");
    \$c->add(".$cultureColumnName.", \$culture);

    \$stmt = ".$this->basePeerClassname."::doSelect(\$c, \$con);
    \$results = array();

    while(\$row = \$stmt->fetch(PDO::FETCH_NUM)) {
";
            if ($table->getChildrenColumn()) {
              $script .= "
      \$omClass = ".$this->getPeerClassname()."::getOMClass(\$row, \$startcol);
";
            } else {
              $script .= "
      \$omClass = ".$this->getPeerClassname()."::getOMClass();
";
            }
            $script .= "
      \$cls = Propel::importClass(\$omClass);
      \$obj1 = new \$cls();
      \$obj1->hydrate(\$row);
      \$obj1->setCulture(\$culture);
";
            if ($i18nTable->getChildrenColumn()) {
              $script .= "
      \$omClass = ".$i18nTablePeerBuilder->getPeerClassname()."::getOMClass(\$row, \$startcol);
";
            } else {
              $script .= "
      \$omClass = ".$i18nTablePeerBuilder->getPeerClassname()."::getOMClass();
";
            }

            $script .= "
      \$cls = Propel::importClass(\$omClass);
      \$obj2 = new \$cls();
      \$obj2->hydrate(\$row, \$startcol);

      \$obj1->set".$i18nClassName."ForCulture(\$obj2, \$culture);
      \$obj2->set".$className."(\$obj1);

      \$results[] = \$obj1;
    }
    return \$results;
  }
";
  }

  protected function addDoValidate(&$script)
  {
      $tmp = '';
      parent::addDoValidate($tmp);

      /**
       * @todo setup 1.1 global validation errors for propel model validation
       */
      $script .= str_replace("return {$this->basePeerClassname}::doValidate(".$this->getPeerClassname()."::DATABASE_NAME, ".$this->getPeerClassname()."::TABLE_NAME, \$columns);\n",
        "\$res =  {$this->basePeerClassname}::doValidate(".$this->getPeerClassname()."::DATABASE_NAME, ".$this->getPeerClassname()."::TABLE_NAME, \$columns);\n".
        "    if (\$res !== true) {\n".
        "        \$request = sfContext::getInstance()->getRequest();\n".
        "        foreach (\$res as \$failed) {\n".
        "            \$col = ".$this->getPeerClassname()."::translateFieldname(\$failed->getColumn(), BasePeer::TYPE_COLNAME, BasePeer::TYPE_PHPNAME);\n".
        "        }\n".
        "    }\n\n".
        "    return \$res;\n", $tmp);
  }

  protected function addDoSelectStmt(&$script)
  {
    $tmp = '';
    parent::addDoSelectStmt($tmp);

    if (DataModelBuilder::getBuildProperty('builderAddBehaviors'))
    {
      $mixer_script = "

    foreach (sfMixer::getCallables('{$this->getClassname()}:doSelectStmt:doSelectStmt') as \$callable)
    {
      call_user_func(\$callable, '{$this->getClassname()}', \$criteria, \$con);
    }

";

     $tmp = preg_replace('/{/', '{'.$mixer_script, $tmp, 1);
    }

    $script .= $tmp;
  }

  protected function addDoSelectJoin(&$script)
  {
    $tmp = '';
    parent::addDoSelectJoin($tmp);

    if (DataModelBuilder::getBuildProperty('builderAddBehaviors'))
    {
      $mixer_script = "

    foreach (sfMixer::getCallables('{$this->getClassname()}:doSelectJoin:doSelectJoin') as \$callable)
    {
      call_user_func(\$callable, '{$this->getClassname()}', \$c, \$con);
    }

";
      $tmp = preg_replace('/{/', '{'.$mixer_script, $tmp, 1);
    }

    $script .= $tmp;
  }

  protected function addDoSelectJoinAll(&$script)
  {
    $tmp = '';
    parent::addDoSelectJoinAll($tmp);

    if (DataModelBuilder::getBuildProperty('builderAddBehaviors'))
    {
      $mixer_script = "

    foreach (sfMixer::getCallables('{$this->getClassname()}:doSelectJoinAll:doSelectJoinAll') as \$callable)
    {
      call_user_func(\$callable, '{$this->getClassname()}', \$c, \$con);
    }

";
      $tmp = preg_replace('/{/', '{'.$mixer_script, $tmp, 1);
    }

    $script .= $tmp;
  }

  protected function addDoSelectJoinAllExcept(&$script)
  {
    $tmp = '';
    parent::addDoSelectJoinAllExcept($tmp);

    if (DataModelBuilder::getBuildProperty('builderAddBehaviors'))
    {
      $mixer_script = "

    foreach (sfMixer::getCallables('{$this->getClassname()}:doSelectJoinAllExcept:doSelectJoinAllExcept') as \$callable)
    {
      call_user_func(\$callable, '{$this->getClassname()}', \$c, \$con);
    }

";
      $tmp = preg_replace('/{/', '{'.$mixer_script, $tmp, 1);
    }

    $script .= $tmp;
  }

  protected function addDoUpdate(&$script)
  {
    $tmp = '';
    parent::addDoUpdate($tmp);

    if (DataModelBuilder::getBuildProperty('builderAddBehaviors'))
    {
      // add sfMixer call
      $pre_mixer_script = "

    foreach (sfMixer::getCallables('{$this->getClassname()}:doUpdate:pre') as \$callable)
    {
      \$ret = call_user_func(\$callable, '{$this->getClassname()}', \$values, \$con);
      if (false !== \$ret)
      {
        return \$ret;
      }
    }

";

      $post_mixer_script = "

    foreach (sfMixer::getCallables('{$this->getClassname()}:doUpdate:post') as \$callable)
    {
      call_user_func(\$callable, '{$this->getClassname()}', \$values, \$con, \$ret);
    }

    return \$ret;
";

      $tmp = preg_replace('/{/', '{'.$pre_mixer_script, $tmp, 1);
      $tmp = preg_replace("/\t\treturn ([^}]+)/", "\t\t\$ret = $1".$post_mixer_script.'  ', $tmp, 1);
    }

    $script .= $tmp;
  }

  protected function addDoInsert(&$script)
  {
    $tmp = '';
    parent::addDoInsert($tmp);

    if (DataModelBuilder::getBuildProperty('builderAddBehaviors'))
    {
      // add sfMixer call
      $pre_mixer_script = "

    foreach (sfMixer::getCallables('{$this->getClassname()}:doInsert:pre') as \$callable)
    {
      \$ret = call_user_func(\$callable, '{$this->getClassname()}', \$values, \$con);
      if (false !== \$ret)
      {
        return \$ret;
      }
    }

";

      $post_mixer_script = "
    foreach (sfMixer::getCallables('{$this->getClassname()}:doInsert:post') as \$callable)
    {
      call_user_func(\$callable, '{$this->getClassname()}', \$values, \$con, \$pk);
    }

    return";

      $tmp = preg_replace('/{/', '{'.$pre_mixer_script, $tmp, 1);
      $tmp = preg_replace("/\t\treturn/", "\t\t".$post_mixer_script, $tmp, 1);
    }

    $script .= $tmp;
  }

  protected function addClassClose(&$script)
  {
    parent::addClassClose($script);

    $behavior_file_name = 'Base'.$this->getTable()->getPhpName().'Behaviors';
    $behavior_file_path = ClassTools::getFilePath($this->getStubObjectBuilder()->getPackage().'.om', $behavior_file_name);

    $absolute_behavior_file_path = sfConfig::get('sf_root_dir').'/'.$behavior_file_path;

    if (file_exists($absolute_behavior_file_path))
    {
      unlink($absolute_behavior_file_path);
    }

    $behaviors = $this->getTable()->getAttribute('behaviors');
    if ($behaviors)
    {
      file_put_contents($absolute_behavior_file_path, sprintf("<?php\nsfPropelBehavior::add('%s', %s);\n", $this->getTable()->getPhpName(), var_export(unserialize($behaviors), true)));

      $behavior_include_script = <<<EOF


if (sfProjectConfiguration::getActive() instanceof sfApplicationConfiguration)
{
  include_once '%s';
}

EOF;
      $script .= sprintf($behavior_include_script, $behavior_file_path);
    }
  }

  protected function addUniqueColumnNamesMethod(&$script)
  {
    $unices = array();
    foreach ($this->getTable()->getUnices() as $unique)
    {
      $unices[] = sprintf("array('%s')", implode("', '", $unique->getColumns()));
    }

    $unices = array_unique($unices);

    $unices = implode(', ', $unices);
    $script .= <<<EOF


  static public function getUniqueColumnNames()
  {
    return array($unices);
  }
EOF;
  }

	/**
	 * Adds the doCountJoin*() methods.
	 * @param      string &$script The script will be modified in this method.
	 */
	protected function addDoCountJoin(&$script)
	{
		$table = $this->getTable();
		$className = $this->getObjectClassname();
		$countFK = count($table->getForeignKeys());
		$join_behavior = $this->getJoinBehavior();

		if ($countFK >= 1) {

			foreach ($table->getForeignKeys() as $fk) {

				$joinTable = $table->getDatabase()->getTable($fk->getForeignTableName());

				if (!$joinTable->isForReferenceOnly()) {

					if ( $fk->getForeignTableName() != $table->getName() ) {

						$thisTableObjectBuilder = $this->getNewObjectBuilder($table);
						$joinedTableObjectBuilder = $this->getNewObjectBuilder($joinTable);
						$joinedTablePeerBuilder = $this->getNewPeerBuilder($joinTable);

						$joinClassName = $joinedTableObjectBuilder->getObjectClassname();

						$script .= "

	/**
	 * Returns the number of rows matching criteria, joining the related ".$thisTableObjectBuilder->getFKPhpNameAffix($fk, $plural = false)." table
	 *
	 * @param      Criteria \$c
	 * @param      boolean \$distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO \$con
	 * @param      String    \$join_behavior the type of joins to use, defaults to $join_behavior
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoin".$thisTableObjectBuilder->getFKPhpNameAffix($fk, $plural = false)."(Criteria \$criteria, \$distinct = false, PropelPDO \$con = null, \$join_behavior = $join_behavior)
	{
		// we're going to modify criteria, so copy it first
		\$criteria = clone \$criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		\$criteria->setPrimaryTableName(".$this->getPeerClassname()."::TABLE_NAME);

		if (\$distinct && !in_array(Criteria::DISTINCT, \$criteria->getSelectModifiers())) {
			\$criteria->setDistinct();
		}

		if (!\$criteria->hasSelectClause()) {
			".$this->getPeerClassname()."::addSelectColumns(\$criteria);
		}

		\$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

		// Set the correct dbName
		\$criteria->setDbName(self::DATABASE_NAME);

		if (\$con === null) {
			\$con = Propel::getConnection(".$this->getPeerClassname()."::DATABASE_NAME, Propel::CONNECTION_READ);
		}
";

						$lfMap = $fk->getLocalForeignMapping();
						$left = array();
						$right = array();
						foreach ($fk->getLocalColumns() as $columnName ) {
							array_push($left, $this->getColumnConstant($table->getColumn($columnName) ) );
							array_push($right, $joinedTablePeerBuilder->getColumnConstant($joinTable->getColumn( $lfMap[$columnName] ) ) );
						}
						$script .= "
		\$criteria->addJoin(array(";
						foreach ($left as $lCol) {
							$script .= $lCol;
							if ($lCol != $left[count($left)]) {
								$script .= ",";
							}
						}
						$script .= "), array(";
						foreach ($right as $rCol) {
							$script .= $rCol;
							if ($rCol != $right[count($right)]) {
								$script .= ",";
							}
						}
						$script .= "), \$join_behavior);
";

    if (DataModelBuilder::getBuildProperty('builderAddBehaviors'))
    {
      $script .= "

    foreach (sfMixer::getCallables('{$this->getClassname()}:doCount:doCount') as \$callable)
    {
      call_user_func(\$callable, '{$this->getClassname()}', \$criteria, \$con);
    }

";
    }

      $script .= "
		\$stmt = ".$this->basePeerClassname."::doCount(\$criteria, \$con);

		if (\$row = \$stmt->fetch(PDO::FETCH_NUM)) {
			\$count = (int) \$row[0];
		} else {
			\$count = 0; // no rows returned; we infer that means 0 matches.
		}
		\$stmt->closeCursor();
		return \$count;
	}
";
					}
				}
			}
		}

	}

	/**
	 * Adds the doCountJoinAll() method.
	 * @param      string &$script The script will be modified in this method.
	 */
	protected function addDoCountJoinAll(&$script)
	{
		$table = $this->getTable();
		$className = $this->getObjectClassname();
		$join_behavior = $this->getJoinBehavior();

		$script .= "

	/**
	 * Returns the number of rows matching criteria, joining all related tables
	 *
	 * @param      Criteria \$c
	 * @param      boolean \$distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO \$con
	 * @param      String    \$join_behavior the type of joins to use, defaults to $join_behavior
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAll(Criteria \$criteria, \$distinct = false, PropelPDO \$con = null, \$join_behavior = $join_behavior)
	{
		// we're going to modify criteria, so copy it first
		\$criteria = clone \$criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		\$criteria->setPrimaryTableName(".$this->getPeerClassname()."::TABLE_NAME);

		if (\$distinct && !in_array(Criteria::DISTINCT, \$criteria->getSelectModifiers())) {
			\$criteria->setDistinct();
		}

		if (!\$criteria->hasSelectClause()) {
			".$this->getPeerClassname()."::addSelectColumns(\$criteria);
		}

		\$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

		// Set the correct dbName
		\$criteria->setDbName(self::DATABASE_NAME);

		if (\$con === null) {
			\$con = Propel::getConnection(".$this->getPeerClassname()."::DATABASE_NAME, Propel::CONNECTION_READ);
		}
";

		foreach ($table->getForeignKeys() as $fk) {
			// want to cover this case, but the code is not there yet.
			if ( $fk->getForeignTableName() != $table->getName() ) {
				$joinTable = $table->getDatabase()->getTable($fk->getForeignTableName());
				$joinedTablePeerBuilder = $this->getNewPeerBuilder($joinTable);

				$joinClassName = $joinedTablePeerBuilder->getObjectClassname();

				$lfMap = $fk->getLocalForeignMapping();
				$left = array();
				$right = array();
				foreach ($fk->getLocalColumns() as $columnName ) {
					array_push($left, $this->getColumnConstant($table->getColumn($columnName) ) );
					array_push($right, $joinedTablePeerBuilder->getColumnConstant($joinTable->getColumn( $lfMap[$columnName] ) ) );
				}
				$script .= "
		\$criteria->addJoin(array(";
				foreach ($left as $lCol) {
					$script .= $lCol;
					if ($lCol != $left[count($left)]) {
						$script .= ",";
					}
				}
				$script .= "), array(";
				foreach ($right as $rCol) {
					$script .= $rCol;
					if ($rCol != $right[count($right)]) {
						$script .= ",";
					}
				}
				$script .= "), \$join_behavior);";
			} // if fk->getForeignTableName != table->getName
		} // foreach [sub] foreign keys

    if (DataModelBuilder::getBuildProperty('builderAddBehaviors'))
    {
      $script .= "

    foreach (sfMixer::getCallables('{$this->getClassname()}:doCount:doCount') as \$callable)
    {
      call_user_func(\$callable, '{$this->getClassname()}', \$criteria, \$con);
    }

";
    }


		$script .= "
		\$stmt = ".$this->basePeerClassname."::doCount(\$criteria, \$con);

		if (\$row = \$stmt->fetch(PDO::FETCH_NUM)) {
			\$count = (int) \$row[0];
		} else {
			\$count = 0; // no rows returned; we infer that means 0 matches.
		}
		\$stmt->closeCursor();
		return \$count;
	}";
	} // end addDoCountJoinAll()

	/**
	 * Adds the doCountJoinAllExcept*() methods.
	 * @param      string &$script The script will be modified in this method.
	 */
	protected function addDoCountJoinAllExcept(&$script)
	{
		$table = $this->getTable();
		$join_behavior = $this->getJoinBehavior();

		$fkeys = $table->getForeignKeys();  // this sep assignment is necessary otherwise sub-loops over
		// getForeignKeys() will cause this to only execute one time.
		foreach ($fkeys as $fk ) {

			$tblFK = $table->getDatabase()->getTable($fk->getForeignTableName());

			$excludedTable = $table->getDatabase()->getTable($fk->getForeignTableName());

			$thisTableObjectBuilder = $this->getNewObjectBuilder($table);
			$excludedTableObjectBuilder = $this->getNewObjectBuilder($excludedTable);
			$excludedTablePeerBuilder = $this->getNewPeerBuilder($excludedTable);

			$excludedClassName = $excludedTableObjectBuilder->getObjectClassname();

			$script .= "

	/**
	 * Returns the number of rows matching criteria, joining the related ".$thisTableObjectBuilder->getFKPhpNameAffix($fk, $plural = false)." table
	 *
	 * @param      Criteria \$c
	 * @param      boolean \$distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO \$con
	 * @param      String    \$join_behavior the type of joins to use, defaults to $join_behavior
	 * @return     int Number of matching rows.
	 */
	public static function doCountJoinAllExcept".$thisTableObjectBuilder->getFKPhpNameAffix($fk, $plural = false)."(Criteria \$criteria, \$distinct = false, PropelPDO \$con = null, \$join_behavior = $join_behavior)
	{
		// we're going to modify criteria, so copy it first
		\$criteria = clone \$criteria;

		if (\$distinct && !in_array(Criteria::DISTINCT, \$criteria->getSelectModifiers())) {
			\$criteria->setDistinct();
		}

		if (!\$criteria->hasSelectClause()) {
			".$this->getPeerClassname()."::addSelectColumns(\$criteria);
		}

		\$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count

		// Set the correct dbName
		\$criteria->setDbName(self::DATABASE_NAME);

		if (\$con === null) {
			\$con = Propel::getConnection(".$this->getPeerClassname()."::DATABASE_NAME, Propel::CONNECTION_READ);
		}
	";

			foreach ($table->getForeignKeys() as $subfk) {
				// want to cover this case, but the code is not there yet.
				if ( $subfk->getForeignTableName() != $table->getName() ) {
					$joinTable = $table->getDatabase()->getTable($subfk->getForeignTableName());
					$joinTablePeerBuilder = $this->getNewPeerBuilder($joinTable);
					$joinClassName = $joinTablePeerBuilder->getObjectClassname();

					if ($joinClassName != $excludedClassName)
					{
						$lfMap = $subfk->getLocalForeignMapping();
						$left = array();
						$right = array();
						foreach ($subfk->getLocalColumns() as $columnName ) {
							array_push($left, $this->getColumnConstant($table->getColumn($columnName) ) );
							array_push($right, $joinTablePeerBuilder->getColumnConstant($joinTable->getColumn( $lfMap[$columnName] ) ) );
						}
						$script .= "
				\$criteria->addJoin(array(";
						foreach ($left as $lCol) {
							$script .= $lCol;
							if ($lCol != $left[count($left)]) {
								$script .= ",";
							}
						}
						$script .= "), array(";
						foreach ($right as $rCol) {
							$script .= $rCol;
							if ($rCol != $right[count($right)]) {
								$script .= ",";
							}
						}
						$script .= "), \$join_behavior);";
					}
				}
			} // foreach fkeys


    if (DataModelBuilder::getBuildProperty('builderAddBehaviors'))
    {
      $script .= "

    foreach (sfMixer::getCallables('{$this->getClassname()}:doCount:doCount') as \$callable)
    {
      call_user_func(\$callable, '{$this->getClassname()}', \$criteria, \$con);
    }

";
    }


			$script .= "
		\$stmt = ".$this->basePeerClassname."::doCount(\$criteria, \$con);

		if (\$row = \$stmt->fetch(PDO::FETCH_NUM)) {
			\$count = (int) \$row[0];
		} else {
			\$count = 0; // no rows returned; we infer that means 0 matches.
		}
		\$stmt->closeCursor();
		return \$count;
	}
";
		} // foreach fk

	} // addDoCountJoinAllExcept

	/**
	 * Adds the doCount() method.
	 * @param      string &$script The script will be modified in this method.
	 */
	protected function addDoCount(&$script)
	{
		$script .= "
	/**
	 * Returns the number of rows matching criteria.
	 *
	 * @param      Criteria \$criteria
	 * @param      boolean \$distinct Whether to select only distinct columns; deprecated: use Criteria->setDistinct() instead.
	 * @param      PropelPDO \$con
	 * @return     int Number of matching rows.
	 */
	public static function doCount(Criteria \$criteria, \$distinct = false, PropelPDO \$con = null)
	{
		// we may modify criteria, so copy it first
		\$criteria = clone \$criteria;

		// We need to set the primary table name, since in the case that there are no WHERE columns
		// it will be impossible for the BasePeer::createSelectSql() method to determine which
		// tables go into the FROM clause.
		\$criteria->setPrimaryTableName(".$this->getPeerClassname()."::TABLE_NAME);

		if (\$distinct && !in_array(Criteria::DISTINCT, \$criteria->getSelectModifiers())) {
			\$criteria->setDistinct();
		}

		if (!\$criteria->hasSelectClause()) {
			".$this->getPeerClassname()."::addSelectColumns(\$criteria);
		}

		\$criteria->clearOrderByColumns(); // ORDER BY won't ever affect the count
		\$criteria->setDbName(self::DATABASE_NAME); // Set the correct dbName

		if (\$con === null) {
			\$con = Propel::getConnection(".$this->getPeerClassname()."::DATABASE_NAME, Propel::CONNECTION_READ);
		}
";
    if (DataModelBuilder::getBuildProperty('builderAddBehaviors'))
    {
      $script .= "

    foreach (sfMixer::getCallables('{$this->getClassname()}:doCount:doCount') as \$callable)
    {
      call_user_func(\$callable, '{$this->getClassname()}', \$criteria, \$con);
    }

";
    }
      $script .= "
		// BasePeer returns a PDOStatement
		\$stmt = ".$this->basePeerClassname."::doCount(\$criteria, \$con);

		if (\$row = \$stmt->fetch(PDO::FETCH_NUM)) {
			\$count = (int) \$row[0];
		} else {
			\$count = 0; // no rows returned; we infer that means 0 matches.
		}
		\$stmt->closeCursor();
		return \$count;
	}";
	}


}
