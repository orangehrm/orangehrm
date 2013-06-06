<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__).'/../../bootstrap/unit.php';

$t = new lime_test(10);

class sfWebDebugTest extends sfWebDebug
{
  public function __construct()
  {
    $this->options['image_root_path'] = '';
    $this->options['request_parameters'] = array();
  }
}

$debug = new sfWebDebugTest();

// ->injectToolbar()
$t->diag('->injectToolbar()');

$before = '<html><head></head><body></body></html>';
$after = $debug->injectToolbar($before);

$t->like($after, '/<style type="text\/css">/', '->injectToolbar() adds styles');
$t->like($after, '/<script type="text\/javascript">/', '->injectToolbar() adds javascript');
$t->like($after, '/<div id="sfWebDebug">/', '->injectToolbar() adds the toolbar');

$before = '';
$after = $debug->injectToolbar($before);

$t->unlike($after, '/<style type="text\/css">/', '->injectToolbar() does not add styles if there is no head');
$t->unlike($after, '/<script type="text\/javascript">/', '->injectToolbar() does not add javascripts if there is no body');
$t->like($after, '/<div id="sfWebDebug">/', '->injectToolbar() adds the toolbar if there is no body');

$before = <<<HTML
<html>
<head></head>
<body>
<textarea><html><head></head><body></body></html></textarea>
</body>
</html>
HTML;

$after = $debug->injectToolbar($before);

$t->is(substr_count($after, '<style type="text/css">'), 1, '->injectToolbar() adds styles once');
$t->is(substr_count($after, '<script type="text/javascript">'), 1, '->injectToolbar() adds javascripts once');
$t->is(substr_count($after, '<div id="sfWebDebug">'), 1, '->injectToolbar() adds styles once');

$t->isa_ok(strpos($after, '<textarea><html><head></head><body></body></html></textarea>'), 'integer', '->injectToolbar() leaves inner pages untouched');
