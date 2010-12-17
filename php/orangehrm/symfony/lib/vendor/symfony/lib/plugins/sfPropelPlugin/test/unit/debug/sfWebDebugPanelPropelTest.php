<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(5);

class sfWebDebugPanelPropelTest extends sfWebDebugPanelPropel
{
  protected function getPropelConfiguration()
  {
    $config = new PropelConfiguration(array());
    $config->setParameter('debugpdo.logging.details.slow.enabled', true);
    $config->setParameter('debugpdo.logging.details.slow.threshold', 1);
    return $config;
  }
}

class sfWebDebugPanelPropelTestDifferentGlue extends sfWebDebugPanelPropel
{
  protected function getPropelConfiguration()
  {
    $config = new PropelConfiguration(array());
    $config->setParameter('debugpdo.logging.outerglue', 'xx');
    $config->setParameter('debugpdo.logging.innerglue', '/ ');
    $config->setParameter('debugpdo.logging.details.slow.enabled', true);
    $config->setParameter('debugpdo.logging.details.slow.threshold', 5);
    return $config;
  }
}

// ->getPanelContent()
$t->diag('->getPanelContent()');

$dispatcher = new sfEventDispatcher();
$logger = new sfVarLogger($dispatcher);
$logger->log('{sfPropelLogger} SELECT * FROM foo WHERE bar<1');
$logger->log('{sfPropelLogger} time: 3.42 sec | mem: 2.8 MB | SELECT * FROM foo WHERE aText like \' | foo\'');
$panel = new sfWebDebugPanelPropelTest(new sfWebDebug($dispatcher, $logger));
$content = $panel->getPanelContent();
$t->like($content, '/bar&lt;1/', '->getPanelContent() returns escaped queries');
$t->like($content, '/aText like &#039; | foo&#039;/', '->getPanelContent() works with glue string in SQL');
$t->like($content, '/sfWebDebugWarning/', '->getPanelContent() contains a slow query warning');

$logger = new sfVarLogger($dispatcher);
$logger->log('{sfPropelLogger} time/ 3.42 secxxmem/ 2.8 MBxxSELECT * FROM foo WHERE bar == 42');
$panel = new sfWebDebugPanelPropelTestDifferentGlue(new sfWebDebug($dispatcher, $logger));
$content = $panel->getPanelContent();
$t->like($content, '/time\/ 3.42 sec, mem\/ 2.8 MB/', '->getPanelContent() works with strange glue strings');
$t->unlike($content, '/sfWebDebugWarning/', '->getPanelContent() should not contain a slow warning');
