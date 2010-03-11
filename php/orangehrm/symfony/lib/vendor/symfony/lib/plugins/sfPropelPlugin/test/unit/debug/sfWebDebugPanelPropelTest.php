<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(1, new lime_output_color());

class sfWebDebugPanelPropelTest extends sfWebDebugPanelPropel
{
  protected function getSqlLogs()
  {
    return array(
      'query: SELECT * FROM foo WHERE bar<1',
    );
  }
}

// ->getPanelContent()
$t->diag('->getPanelContent()');

$dispatcher = new sfEventDispatcher();
$debug = new sfWebDebug($dispatcher, new sfVarLogger($dispatcher));
$panel = new sfWebDebugPanelPropelTest($debug);
$t->like($panel->getPanelContent(), '/'.preg_quote('query: SELECT * FROM foo WHERE bar&lt;1', '/').'/', '->getPanelContent() returns escaped queries');
