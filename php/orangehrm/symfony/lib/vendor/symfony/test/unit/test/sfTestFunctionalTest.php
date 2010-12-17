<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(2);

class mockBrowser extends sfBrowser
{
  public function setResponseContent($content)
  {
    $this->dom = new DomDocument('1.0');
    $this->dom->validateOnParse = true;
    @$this->dom->loadHTML($content);
    $this->domCssSelector = new sfDomCssSelector($this->dom);
  }
}

class mockLime extends lime_test
{
  public function __destruct()
  {
  }
}

class mockTestFunctional extends sfTestFunctional
{
  public $called = array();

  public function call($uri, $method = 'get', $parameters = array(), $changeStack = true)
  {
    $this->called[] = func_get_args();
  }
}

$html = <<<HTML
<html>
<head>
</head>
<body>
<a href="/somewhere" class="clickme"></a>
</body>
</html>
HTML;

$browser = new mockBrowser();
$browser->setResponseContent($html);

$lime = new mockLime();
$tester = new mockTestFunctional($browser, $lime);

try
{
  $tester->click('a.clickme');
  $t->pass('->click() accepts a CSS selector');
}
catch (Exception $e)
{
  $t->diag($e->getMessage());
  $t->fail('->click() accepts a CSS selector');
}

$t->is_deeply($tester->called, array(array('/somewhere', 'get', array())), '->click() parses a CSS selector');
