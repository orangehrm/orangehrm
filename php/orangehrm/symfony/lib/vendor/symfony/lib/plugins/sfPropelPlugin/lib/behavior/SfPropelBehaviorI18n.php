<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Internationalizes Propel models.
 *
 * This behavior is intended to be applied at the database level.
 *
 * @package     sfPropelPlugin
 * @subpackage  behavior
 * @author      Kris Wallsmith <kris.wallsmith@symfony-project.com>
 * @version     SVN: $Id: SfPropelBehaviorI18n.php 24597 2009-11-30 19:53:50Z Kris.Wallsmith $
 */
class SfPropelBehaviorI18n extends SfPropelBehaviorBase
{
  protected $parameters = array(
    'i18n_table' => null,
  );

  /**
   * Looks for tables marked as I18N and adds behaviors.
   */
  public function modifyDatabase()
  {
    $translationBehavior = Propel::importClass($this->getBuildProperty('propel.behavior.symfony_i18n_translation.class'));

    foreach ($this->getDatabase()->getTables() as $table)
    {
      $behaviors = $table->getBehaviors();

      if (!isset($behaviors['symfony_i18n']) && 'true' == $table->getAttribute('isI18N'))
      {
        $i18nTable = $this->getDatabase()->getTable($table->getAttribute('i18nTable'));

        // add the current behavior to the translatable model
        $behavior = clone $this;
        $behavior->setParameters(array('i18n_table' => $i18nTable->getName()));
        $table->addBehavior($behavior);

        // add the translation behavior to the translation model
        $behavior = new $translationBehavior();
        $behavior->setName('symfony_i18n_translation');
        $behavior->setParameters(array('culture_column' => $this->getCultureColumn($i18nTable)->getName()));
        $i18nTable->addBehavior($behavior);
      }
    }
  }

  public function modifyTable()
  {
    if ($this->isDisabled())
    {
      return;
    }

    if (count($this->getTable()->getPrimaryKey()) > 1)
    {
      throw new Exception('i18n support only works with a single primary key');
    }
  }

  public function objectAttributes()
  {
    if ($this->isDisabled())
    {
      return;
    }

    return <<<EOF

/**
 * @var string The value for the culture field
 */
protected \$culture = null;

/**
 * @var array Current I18N objects
 */
protected \$current_i18n = array();

EOF;
  }

  public function objectMethods()
  {
    if ($this->isDisabled())
    {
      return;
    }

    $script = <<<EOF

/**
 * Returns the culture.
 *
 * @return string The culture
 */
public function getCulture()
{
  return \$this->culture;
}

/**
 * Sets the culture.
 *
 * @param string $culture The culture to set
 *
 * @return {$this->getTable()->getPhpName()}
 */
public function setCulture(\$culture)
{
  \$this->culture = \$culture;
  return \$this;
}

EOF;

    // add accessors and mutators for each of the i18nTable's columns
    $foreignKey = $this->getI18nTable()->getBehavior('symfony_i18n_translation')->getForeignKey();
    $refPhpName = $foreignKey->getRefPhpName() ? $foreignKey->getRefPhpName() : $this->getI18nTable()->getPhpName();

    foreach ($this->getI18nTable()->getColumns() as $column)
    {
      if ($column->isPrimaryKey())
      {
        continue;
      }

      $script .= <<<EOF

/**
 * Returns the "{$column->getName()}" value from the current {@link {$this->getI18nTable()->getPhpName()}}.
 */
public function get{$column->getPhpName()}(\$culture = null)
{
  return \$this->getCurrent{$refPhpName}(\$culture)->get{$column->getPhpName()}();
}

/**
 * Sets the "{$column->getName()}" value of the current {@link {$this->getI18nTable()->getPhpName()}}.
 *
 * @return {$this->getTable()->getPhpName()}
 */
public function set{$column->getPhpName()}(\$value, \$culture = null)
{
  \$this->getCurrent{$refPhpName}(\$culture)->set{$column->getPhpName()}(\$value);
  return \$this;
}

EOF;
    }

    $script .= <<<EOF

/**
 * Returns the current translation.
 *
 * @return {$this->getI18nTable()->getPhpName()}
 */
public function getCurrent{$refPhpName}(\$culture = null)
{
  if (null === \$culture)
  {
    \$culture = null === \$this->culture ? sfPropel::getDefaultCulture() : \$this->culture;
  }

  if (!isset(\$this->current_i18n[\$culture]))
  {
    \$object = \$this->isNew() ? null : {$this->getI18nTable()->getPhpName()}Peer::retrieveByPK(\$this->getPrimaryKey(), \$culture);
    if (\$object)
    {
      \$this->set{$refPhpName}ForCulture(\$object, \$culture);
    }
    else
    {
      \$this->set{$refPhpName}ForCulture(new {$this->getI18nTable()->getPhpName()}(), \$culture);
      \$this->current_i18n[\$culture]->set{$this->getI18nTable()->getBehavior('symfony_i18n_translation')->getCultureColumn()->getPhpName()}(\$culture);
    }
  }

  return \$this->current_i18n[\$culture];
}

/**
 * Sets the translation object for a culture.
 */
public function set{$refPhpName}ForCulture({$this->getI18nTable()->getPhpName()} \$object, \$culture)
{
  \$this->current_i18n[\$culture] = \$object;
  \$this->add{$refPhpName}(\$object);
}

EOF;

    if (!$this->hasPrimaryString($this->getTable()) && $this->hasPrimaryString($this->getI18nTable()))
    {
      $script .= <<<EOF

/**
 * @see {$this->getI18nTable()->getPhpName()}
 */
public function __toString()
{
  return (string) \$this->getCurrent{$refPhpName}();
}

EOF;
    }

    return $script;
  }

  public function staticMethods()
  {
    $foreignKey = $this->getI18nTable()->getBehavior('symfony_i18n_translation')->getForeignKey();
    $refPhpName = $foreignKey->getRefPhpName() ? $foreignKey->getRefPhpName() : $this->getI18nTable()->getPhpName();
    $join = in_array($this->getBuildProperty('propel.useLeftJoinsInDoJoinMethods'), array(true, null), true) ? 'LEFT' : 'INNER';

    $behaviors = $this->getTable()->getBehaviors();
    $mixerHook = !isset($behaviors['symfony_behaviors']) ? '' : <<<EOF

  foreach (sfMixer::getCallables('Base{$this->getTable()->getPhpName()}:doSelectJoin:doSelectJoin') as \$sf_hook)
  {
    call_user_func(\$sf_hook, '{$this->getTable()->getPhpName()}', \$criteria, \$con);
  }

EOF;

    return <<<EOF

/**
 * Returns the i18n model class name.
 *
 * @return string The i18n model class name
 */
static public function getI18nModel()
{
  return '{$this->getI18nTable()->getPhpName()}';
}

/**
 * Selects a collection of {@link {$this->getTable()->getPhpName()}} objects with a {@link {$this->getI18nTable()->getPhpName()}} translation populated.
 *
 * @param Criteria  \$criteria
 * @param string    \$culture
 * @param PropelPDO \$con
 * @param string    \$join_behavior
 *
 * @return array
 */
static public function doSelectWithI18n(Criteria \$criteria, \$culture = null, \$con = null, \$join_behavior = Criteria::{$join}_JOIN)
{
  \$criteria = clone \$criteria;

  if (null === \$culture)
  {
    \$culture = sfPropel::getDefaultCulture();
  }

  // Set the correct dbName if it has not been overridden
  if (\$criteria->getDbName() == Propel::getDefaultDB()) {
  	\$criteria->setDbName(self::DATABASE_NAME);
  }

  {$this->getTable()->getPhpName()}Peer::addSelectColumns(\$criteria);
  \$startcol = ({$this->getTable()->getPhpName()}Peer::NUM_COLUMNS - {$this->getTable()->getPhpName()}Peer::NUM_LAZY_LOAD_COLUMNS);
  {$this->getI18nTable()->getPhpName()}Peer::addSelectColumns(\$criteria);
  \$criteria->addJoin({$this->getLocalColumn()->getConstantName()}, {$this->getForeignColumn()->getConstantName()}, \$join_behavior);
  \$criteria->add({$this->getCultureColumn($this->getI18nTable())->getConstantName()}, \$culture);
{$mixerHook}
  \$stmt = BasePeer::doSelect(\$criteria, \$con);
	\$results = array();

	while (\$row = \$stmt->fetch(PDO::FETCH_NUM)) {
		\$key1 = {$this->getTable()->getPhpName()}Peer::getPrimaryKeyHashFromRow(\$row, 0);
		if (null !== (\$obj1 = {$this->getTable()->getPhpName()}Peer::getInstanceFromPool(\$key1))) {
			// We no longer rehydrate the object, since this can cause data loss.
  		// See http://propel.phpdb.org/trac/ticket/509
  		// \$obj1->hydrate(\$row, 0, true); // rehydrate
  	} else {
			\$cls = {$this->getTable()->getPhpName()}Peer::getOMClass(false);
			\$obj1 = new \$cls();
			\$obj1->hydrate(\$row);
      {$this->getTable()->getPhpName()}Peer::addInstanceToPool(\$obj1, \$key1);
		} // if \$obj1 already loaded

		\$key2 = {$this->getI18nTable()->getPhpName()}Peer::getPrimaryKeyHashFromRow(\$row, \$startcol);
		if (\$key2 !== null) {
			\$obj2 = {$this->getI18nTable()->getPhpName()}Peer::getInstanceFromPool(\$key2);
			if (!\$obj2) {
				\$cls = {$this->getI18nTable()->getPhpName()}Peer::getOMClass(false);
				\$obj2 = new \$cls();
				\$obj2->hydrate(\$row, \$startcol);
				{$this->getI18nTable()->getPhpName()}Peer::addInstanceToPool(\$obj2, \$key2);
			} // if obj2 already loaded

      \$obj1->set{$refPhpName}ForCulture(\$obj2, \$culture);
		} // if joined row was not null

		\$results[] = \$obj1;
	}

	\$stmt->closeCursor();

	return \$results;
}

EOF;
  }

  /**
   * Returns the current table's i18n translation table.
   *
   * @return Table
   */
  public function getI18nTable()
  {
    return $this->getDatabase()->getTable($this->getParameter('i18n_table'));
  }

  /**
   * Finds the supplied translation table's culture column.
   *
   * @return Column
   *
   * @throws InvalidArgumentException If there is not a column marked as "isCulture"
   */
  protected function getCultureColumn(Table $table)
  {
    foreach ($table->getColumns() as $column)
    {
      if ('true' == $column->getAttribute('isCulture'))
      {
        return $column;
      }
    }

    throw new InvalidArgumentException(sprintf('The table "%s" does not have a column marked with the "isCulture" attribute.', $table->getName()));
  }

  /**
   * Returns the column on the current model referenced by the translation model.
   *
   * @return Column
   */
  protected function getLocalColumn()
  {
    $columns = $this->getI18nTable()->getBehavior('symfony_i18n_translation')->getForeignKey()->getForeignColumns();
    return $this->getTable()->getColumn($columns[0]);
  }

  /**
   * Returns the column on the translation table the references the current model.
   *
   * @return Column
   */
  protected function getForeignColumn()
  {
    $columns = $this->getI18nTable()->getBehavior('symfony_i18n_translation')->getForeignKey()->getLocalColumns();
    return $this->getI18nTable()->getColumn($columns[0]);
  }

  /**
   * Checks whether the supplied table has a primary string defined.
   *
   * @param  Table $table
   *
   * @return boolean
   */
  protected function hasPrimaryString(Table $table)
  {
    foreach ($table->getColumns() as $column)
    {
      if ($column->isPrimaryString())
      {
        return true;
      }
    }

    return false;
  }
}
