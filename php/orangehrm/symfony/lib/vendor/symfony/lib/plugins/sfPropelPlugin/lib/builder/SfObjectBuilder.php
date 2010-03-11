<?php

require_once 'propel/engine/builder/om/php5/PHP5ObjectBuilder.php';

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
 * @version    SVN: $Id: SfObjectBuilder.php 14378 2008-12-29 20:04:39Z Kris.Wallsmith $
 */
class SfObjectBuilder extends PHP5ObjectBuilder
{
  public function build()
  {
    $objectCode = parent::build();
    if (!DataModelBuilder::getBuildProperty('builderAddComments'))
    {
      $objectCode = sfToolkit::stripComments($objectCode);
    }

    if(!DataModelBuilder::getBuildProperty('builderAddIncludes'))
    {
       // remove all inline includes: object classes include the peers
      $objectCode = preg_replace("/include_once\s*.*Base.*Peer\.php.*\s*/", "", $objectCode);
    }

    return $objectCode;
  }

  protected function addIncludes(&$script)
  {
    if (!DataModelBuilder::getBuildProperty('builderAddIncludes'))
    {
      return;
    }

    parent::addIncludes($script);

    // include the i18n classes if needed
    if ($this->getTable()->getAttribute('isI18N'))
    {
      $relatedTable   = $this->getDatabase()->getTable($this->getTable()->getAttribute('i18nTable'));

      $script .= '
require_once \''.ClassTools::getFilePath($this->getStubObjectBuilder()->getPackage().'.', $relatedTable->getPhpName().'Peer').'\';
require_once \''.ClassTools::getFilePath($this->getStubObjectBuilder()->getPackage().'.', $relatedTable->getPhpName()).'\';
';
    }
  }

  protected function addClassBody(&$script)
  {
    parent::addClassBody($script);

    if ($this->getTable()->getAttribute('isI18N'))
    {
      if (count($this->getTable()->getPrimaryKey()) > 1)
      {
        throw new Exception('i18n support only works with a single primary key');
      }

      $this->addCultureAccessorMethod($script);
      $this->addCultureMutatorMethod($script);

      $this->addI18nMethods($script);
    }

    if (DataModelBuilder::getBuildProperty('builderAddBehaviors'))
    {
      $this->addCall($script);
    }
  }

  protected function addCall(&$script)
  {
    $script .= "

  public function __call(\$method, \$arguments)
  {
    if (!\$callable = sfMixer::getCallable('{$this->getClassname()}:'.\$method))
    {
      throw new sfException(sprintf('Call to undefined method {$this->getClassname()}::%s', \$method));
    }

    array_unshift(\$arguments, \$this);

    return call_user_func_array(\$callable, \$arguments);
  }

";
  }

  protected function addAttributes(&$script)
  {
    parent::addAttributes($script);

    if ($this->getTable()->getAttribute('isI18N'))
    {
      $script .= '
  /**
   * The value for the culture field.
   * @var string
   */
  protected $culture;
';
    }
  }

  protected function addCultureAccessorMethod(&$script)
  {
    $script .= '

  /**
   * Returns the culture.
   *
   * @return string The culture
   */
  public function getCulture()
  {
    return $this->culture;
  }
';
  }

  protected function addCultureMutatorMethod(&$script)
  {
    $script .= '
  /**
   * Sets the culture.
   *
   * @param string $culture The culture to set
   *
   * @return void
   */
  public function setCulture($culture)
  {
    $this->culture = $culture;
  }
';
  }

  protected function addI18nMethods(&$script)
  {
    $table = $this->getTable();
    $pks = $table->getPrimaryKey();
    $pk = $pks[0]->getPhpName();

    foreach ($table->getReferrers() as $fk)
    {
      $tblFK = $fk->getTable();
      if ($tblFK->getName() == $table->getAttribute('i18nTable'))
      {
        $className = $tblFK->getPhpName();
        $culture = '';
        $culture_peername = '';
        foreach ($tblFK->getColumns() as $col)
        {
          if (("true" === strtolower($col->getAttribute('isCulture'))))
          {
            $culture = $col->getPhpName();
            $culture_peername = PeerBuilder::getColumnName($col, $className);
          }
        }

        foreach ($tblFK->getColumns() as $col)
        {
          if ($col->isPrimaryKey()) continue;

          $script .= '
  public function get'.$col->getPhpName().'($culture = null)
  {
    return $this->getCurrent'.$className.'($culture)->get'.$col->getPhpName().'();
  }

  public function set'.$col->getPhpName().'($value, $culture = null)
  {
    $this->getCurrent'.$className.'($culture)->set'.$col->getPhpName().'($value);
  }
';
        }

$script .= '
  protected $current_i18n = array();

  public function getCurrent'.$className.'($culture = null)
  {
    if (is_null($culture))
    {
      $culture = is_null($this->culture) ? sfPropel::getDefaultCulture() : $this->culture;
    }

    if (!isset($this->current_i18n[$culture]))
    {
      $obj = '.$className.'Peer::retrieveByPK($this->get'.$pk.'(), $culture);
      if ($obj)
      {
        $this->set'.$className.'ForCulture($obj, $culture);
      }
      else
      {
        $this->set'.$className.'ForCulture(new '.$className.'(), $culture);
        $this->current_i18n[$culture]->set'.$culture.'($culture);
      }
    }

    return $this->current_i18n[$culture];
  }

  public function set'.$className.'ForCulture($object, $culture)
  {
    $this->current_i18n[$culture] = $object;
    $this->add'.$className.'($object);
  }
';
      }
    }
  }

  protected function addDoSave(&$script)
  {
    $tmp = '';
    parent::addDoSave($tmp);
    // add autosave to i18n object even if the base object is not changed
    $tmp = preg_replace_callback('#(\$this\->(.+?)\->isModified\(\))#', array($this, 'i18nDoSaveCallback'), $tmp);

    $script .= $tmp;
  }

  private function i18nDoSaveCallback($matches)
  {
    $value = $matches[1];

    // get the related class to see if it is a i18n one
    $table = $this->getTable();
    $column = null;
    foreach ($table->getForeignKeys() as $fk)
    {
      if ($matches[2] == $this->getFKVarName($fk))
      {
        $column = $fk;
        break;
      }
    }
    $foreign_table = $this->getDatabase()->getTable($fk->getForeignTableName());
    if ($foreign_table->getAttribute('isI18N'))
    {
      $foreign_tables_i18n_table = $this->getDatabase()->getTable($foreign_table->getAttribute('i18nTable'));
      $value .= ' || ($this->'.$matches[2].'->getCulture() && $this->'.$matches[2].'->getCurrent'.$foreign_tables_i18n_table->getPhpName().'()->isModified())';
    }

    return $value;
  }

  protected function addDelete(&$script)
  {
    $tmp = '';
    parent::addDelete($tmp);

    if (DataModelBuilder::getBuildProperty('builderAddBehaviors'))
    {
      // add sfMixer call
      $pre_mixer_script = "

    foreach (sfMixer::getCallables('{$this->getClassname()}:delete:pre') as \$callable)
    {
      \$ret = call_user_func(\$callable, \$this, \$con);
      if (\$ret)
      {
        return;
      }
    }

";
      $post_mixer_script = "

    foreach (sfMixer::getCallables('{$this->getClassname()}:delete:post') as \$callable)
    {
      call_user_func(\$callable, \$this, \$con);
    }

";
      $tmp = preg_replace('/{/', '{'.$pre_mixer_script, $tmp, 1);
      $tmp = preg_replace('/}\s*$/', $post_mixer_script.'  }', $tmp);
    }

    // update current script
    $script .= $tmp;
  }

  protected function addSave(&$script)
  {
    $tmp = '';
    parent::addSave($tmp);

    // add support for created_(at|on) and updated_(at|on) columns
    $date_script = '';
    $updated = false;
    $created = false;
    foreach ($this->getTable()->getColumns() as $col)
    {
      $clo = strtolower($col->getName());

      if (!$updated && in_array($clo, array('updated_at', 'updated_on')))
      {
        $updated = true;
        $date_script .= "
    if (\$this->isModified() && !\$this->isColumnModified(".$this->getColumnConstant($col)."))
    {
      \$this->set".$col->getPhpName()."(time());
    }
";
      }
      else if (!$created && in_array($clo, array('created_at', 'created_on')))
      {
        $created = true;
        $date_script .= "
    if (\$this->isNew() && !\$this->isColumnModified(".$this->getColumnConstant($col)."))
    {
      \$this->set".$col->getPhpName()."(time());
    }
";
      }
    }
    $tmp = preg_replace('/{/', '{'.$date_script, $tmp, 1);

    if (DataModelBuilder::getBuildProperty('builderAddBehaviors'))
    {
      // add sfMixer call
      $pre_mixer_script = "

    foreach (sfMixer::getCallables('{$this->getClassname()}:save:pre') as \$callable)
    {
      \$affectedRows = call_user_func(\$callable, \$this, \$con);
      if (is_int(\$affectedRows))
      {
        return \$affectedRows;
      }
    }

";
      $post_mixer_script = <<<EOF

    foreach (sfMixer::getCallables('{$this->getClassname()}:save:post') as \$callable)
    {
      call_user_func(\$callable, \$this, \$con, \$affectedRows);
    }

EOF;
      $tmp = preg_replace('/{/', '{'.$pre_mixer_script, $tmp, 1);
      $tmp = preg_replace('/(\$con\->commit\(\);)/', '$1'.$post_mixer_script, $tmp);
    }

    // update current script
    $script .= $tmp;
  }

  protected function addClassClose(&$script)
  {
    parent::addClassClose($script);

    $behaviors = $this->getTable()->getAttribute('behaviors');
    if ($behaviors)
    {
      $behavior_file_name = 'Base'.$this->getTable()->getPhpName().'Behaviors';
      $behavior_file_path = ClassTools::getFilePath($this->getStubObjectBuilder()->getPackage().'.om', $behavior_file_name);

      $behavior_include_script = <<<EOF


if (sfProjectConfiguration::getActive() instanceof sfApplicationConfiguration)
{
  include_once '%s';
}

EOF;
      $script .= sprintf($behavior_include_script, $behavior_file_path);
    }
  }

  protected function addConstants(&$script)
  {
    $script .= "\n  const PEER = '".$this->getPeerClassName()."';\n";
  }
}
