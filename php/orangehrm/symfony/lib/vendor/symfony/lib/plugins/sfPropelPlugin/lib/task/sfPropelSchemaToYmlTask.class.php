<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/sfPropelBaseTask.class.php');

/**
 * Creates schema.yml from schema.xml.
 *
 * @package    symfony
 * @subpackage propel
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPropelSchemaToYmlTask.class.php 23922 2009-11-14 14:58:38Z fabien $
 */
class sfPropelSchemaToYmlTask extends sfPropelBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->namespace = 'propel';
    $this->name = 'schema-to-yml';
    $this->briefDescription = 'Creates schema.yml from schema.xml';

    $this->detailedDescription = <<<EOF
The [propel:schema-to-yml|INFO] task converts XML schemas to YML:

  [./symfony propel:schema-to-yml|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->schemaToYML(self::CHECK_SCHEMA);
  }
}
