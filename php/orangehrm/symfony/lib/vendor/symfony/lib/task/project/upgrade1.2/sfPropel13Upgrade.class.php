<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2008 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Upgrades propel.ini, databases.yml, and attempts to update object model for
 * changes between Propel 1.2 and Propel 1.3.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPropelUpgrade.class.php 8369 2008-04-08 23:39:27Z dwhittle $
 */
class sfPropel13Upgrade extends sfUpgrade
{
  public function upgrade()
  {
    $file = sfConfig::get('sf_config_dir').'/propel.ini';
    if(is_readable($file))
    {
      // builders paths
      $content = file_get_contents($file);
      $content = str_replace('addon.propel.builder.', 'plugins.sfPropelPlugin.lib.propel.builder.', $content, $count);

      if ($count)
      {
        $this->logSection('propel', sprintf('Migrating %s', $file));
        file_put_contents($file, $content);
      }

      // nested sets paths
      if (false === strpos($content, 'nestedsetpeer'))
      {
        $content .= <<<EOF
propel.builder.nestedset.class         = plugins.sfPropelPlugin.lib.builder.SfNestedSetBuilder
propel.builder.nestedsetpeer.class     = plugins.sfPropelPlugin.lib.builder.SfNestedSetPeerBuilder
EOF;
        $this->logSection('propel', sprintf('Migrating %s', $file));
        file_put_contents($file, $content);
      }

      // add default date and time format
      $content = file_get_contents($file);
      if (false === strpos($content, 'propel.defaultDateFormat'))
      {
        $content .= <<<EOF

propel.defaultTimeStampFormat = Y-m-d H:i:s
propel.defaultTimeFormat = H:i:s
propel.defaultDateFormat = Y-m-d

EOF;
        $this->logSection('propel', sprintf('Migrating %s', $file));
        file_put_contents($file, $content);
      }
    }

    $models = sfFinder::type('file')->name('*.php')->maxdepth(0)->in(sfConfig::get('sf_lib_dir').'/model');
    foreach ($models as $model)
    {
      if(is_readable($model))
      {
        $content = file_get_contents($model);
        $content = str_replace(array('public function save($con = null)', 'public function delete($con = null)', 'SQLException', '->begin();', '->rollback();', '->prepareStatement(', '->executeQuery();'),
                               array('public function save(PropelPDO $con = null)', 'public function delete(PropelPDO $con = null)', 'PDOException', '->beginTransaction();', '->rollBack();', '->prepare(', '->execute();'),
                               $content, $count);

        if ($count)
        {
          $this->logSection('propel', sprintf('Migrating %s', $model));
          file_put_contents($model, $content);
        }
      }

    }
  }
}
