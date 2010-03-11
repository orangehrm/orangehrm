<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(22, new lime_output_color());

class myViewConfigHandler extends sfViewConfigHandler
{
  public function setConfiguration($config)
  {
    $this->yamlConfig = self::mergeConfig($config);
  }

  public function addHtmlAsset($viewName = '')
  {
    return parent::addHtmlAsset($viewName);
  }
}

$handler = new myViewConfigHandler();

// addHtmlAsset() basic asset addition
$t->diag('addHtmlAsset() basic asset addition');

$handler->setConfiguration(array(
  'myView' => array(
    'stylesheets' => array('foobar'),
  ),
));
$content = <<<EOF
  \$response->addStylesheet('foobar', '', array ());

EOF;
$t->is(fix_content($handler->addHtmlAsset('myView')), fix_content($content), 'addHtmlAsset() adds stylesheets to the response');

$handler->setConfiguration(array(
  'myView' => array(
    'stylesheets' => array(array('foobar' => array('position' => 'last'))),
  ),
));
$content = <<<EOF
  \$response->addStylesheet('foobar', 'last', array ());

EOF;
$t->is(fix_content($handler->addHtmlAsset('myView')), fix_content($content), 'addHtmlAsset() adds stylesheets to the response');

$handler->setConfiguration(array(
  'myView' => array(
    'javascripts' => array('foobar'),
  ),
));
$content = <<<EOF
  \$response->addJavascript('foobar', '', array ());

EOF;
$t->is(fix_content($handler->addHtmlAsset('myView')), fix_content($content), 'addHtmlAsset() adds JavaScript to the response');

$handler->setConfiguration(array(
  'myView' => array(
    'javascripts' => array(array('foobar' => array('position' => 'last'))),
  ),
));
$content = <<<EOF
  \$response->addJavascript('foobar', 'last', array ());

EOF;
$t->is(fix_content($handler->addHtmlAsset('myView')), fix_content($content), 'addHtmlAsset() adds JavaScript to the response');

// Insertion order for stylesheets
$t->diag('addHtmlAsset() insertion order for stylesheets');

$handler->setConfiguration(array(
  'myView' => array(
    'stylesheets' => array('foobar'),
  ),
  'all' => array(
    'stylesheets' => array('all_foobar'),
  ),
));
$content = <<<EOF
  \$response->addStylesheet('all_foobar', '', array ());
  \$response->addStylesheet('foobar', '', array ());

EOF;
$t->is(fix_content($handler->addHtmlAsset('myView')), fix_content($content), 'addHtmlAsset() adds view-specific stylesheets after application-wide assets');

$handler->setConfiguration(array(
  'all' => array(
    'stylesheets' => array('all_foobar'),
  ),
  'myView' => array(
    'stylesheets' => array('foobar'),
  ),
));
$content = <<<EOF
  \$response->addStylesheet('all_foobar', '', array ());
  \$response->addStylesheet('foobar', '', array ());

EOF;
$t->is(fix_content($handler->addHtmlAsset('myView')), fix_content($content), 'addHtmlAsset() adds view-specific stylesheets after application-wide assets');

$handler->setConfiguration(array(
  'myView' => array(
    'stylesheets' => array('foobar'),
  ),
  'default' => array(
    'stylesheets' => array('default_foobar'),
  ),
));
$content = <<<EOF
  \$response->addStylesheet('default_foobar', '', array ());
  \$response->addStylesheet('foobar', '', array ());

EOF;
$t->is(fix_content($handler->addHtmlAsset('myView')), fix_content($content), 'addHtmlAsset() adds view-specific stylesheets after default assets');

$handler->setConfiguration(array(
  'default' => array(
    'stylesheets' => array('default_foobar'),
  ),
  'myView' => array(
    'stylesheets' => array('foobar'),
  ),
));
$content = <<<EOF
  \$response->addStylesheet('default_foobar', '', array ());
  \$response->addStylesheet('foobar', '', array ());

EOF;
$t->is(fix_content($handler->addHtmlAsset('myView')), fix_content($content), 'addHtmlAsset() adds view-specific stylesheets after default assets');

$handler->setConfiguration(array(
  'default' => array(
    'stylesheets' => array('default_foobar'),
  ),
  'all' => array(
    'stylesheets' => array('all_foobar'),
  ),
));
$content = <<<EOF
  \$response->addStylesheet('default_foobar', '', array ());
  \$response->addStylesheet('all_foobar', '', array ());

EOF;
$t->is(fix_content($handler->addHtmlAsset('myView')), fix_content($content), 'addHtmlAsset() adds application-specific stylesheets after default assets');

$handler->setConfiguration(array(
  'all' => array(
    'stylesheets' => array('all_foobar'),
  ),
  'default' => array(
    'stylesheets' => array('default_foobar'),
  ),
));
$content = <<<EOF
  \$response->addStylesheet('default_foobar', '', array ());
  \$response->addStylesheet('all_foobar', '', array ());

EOF;
$t->is(fix_content($handler->addHtmlAsset('myView')), fix_content($content), 'addHtmlAsset() adds application-specific stylesheets after default assets');

// Insertion order for javascripts
$t->diag('addHtmlAsset() insertion order for javascripts');

$handler->setConfiguration(array(
  'myView' => array(
    'javascripts' => array('foobar'),
  ),
  'all' => array(
    'javascripts' => array('all_foobar'),
  ),
));
$content = <<<EOF
  \$response->addJavascript('all_foobar', '', array ());
  \$response->addJavascript('foobar', '', array ());

EOF;
$t->is(fix_content($handler->addHtmlAsset('myView')), fix_content($content), 'addHtmlAsset() adds view-specific javascripts after application-wide assets');

$handler->setConfiguration(array(
  'all' => array(
    'javascripts' => array('all_foobar'),
  ),
  'myView' => array(
    'javascripts' => array('foobar'),
  ),
));
$content = <<<EOF
  \$response->addJavascript('all_foobar', '', array ());
  \$response->addJavascript('foobar', '', array ());

EOF;
$t->is(fix_content($handler->addHtmlAsset('myView')), fix_content($content), 'addHtmlAsset() adds view-specific javascripts after application-wide assets');

$handler->setConfiguration(array(
  'myView' => array(
    'javascripts' => array('foobar'),
  ),
  'default' => array(
    'javascripts' => array('default_foobar'),
  ),
));
$content = <<<EOF
  \$response->addJavascript('default_foobar', '', array ());
  \$response->addJavascript('foobar', '', array ());

EOF;
$t->is(fix_content($handler->addHtmlAsset('myView')), fix_content($content), 'addHtmlAsset() adds view-specific javascripts after default assets');

$handler->setConfiguration(array(
  'default' => array(
    'javascripts' => array('default_foobar'),
  ),
  'myView' => array(
    'javascripts' => array('foobar'),
  ),
));
$content = <<<EOF
  \$response->addJavascript('default_foobar', '', array ());
  \$response->addJavascript('foobar', '', array ());

EOF;
$t->is(fix_content($handler->addHtmlAsset('myView')), fix_content($content), 'addHtmlAsset() adds view-specific javascripts after default assets');

$handler->setConfiguration(array(
  'default' => array(
    'javascripts' => array('default_foobar'),
  ),
  'all' => array(
    'javascripts' => array('all_foobar'),
  ),
));
$content = <<<EOF
  \$response->addJavascript('default_foobar', '', array ());
  \$response->addJavascript('all_foobar', '', array ());

EOF;
$t->is(fix_content($handler->addHtmlAsset('myView')), fix_content($content), 'addHtmlAsset() adds application-specific javascripts after default assets');

$handler->setConfiguration(array(
  'all' => array(
    'javascripts' => array('all_foobar'),
  ),
  'default' => array(
    'javascripts' => array('default_foobar'),
  ),
));
$content = <<<EOF
  \$response->addJavascript('default_foobar', '', array ());
  \$response->addJavascript('all_foobar', '', array ());

EOF;
$t->is(fix_content($handler->addHtmlAsset('myView')), fix_content($content), 'addHtmlAsset() adds application-specific javascripts after default assets');

// removal of assets
$t->diag('addHtmlAsset() removal of assets');

$handler->setConfiguration(array(
  'all' => array(
    'stylesheets' => array('all_foo', 'all_bar'),
  ),
  'myView' => array(
    'stylesheets' => array('foobar', '-all_bar'),
  ),
));
$content = <<<EOF
  \$response->addStylesheet('all_foo', '', array ());
  \$response->addStylesheet('foobar', '', array ());

EOF;
$t->is(fix_content($handler->addHtmlAsset('myView')), fix_content($content), 'addHtmlAsset() supports the - option to remove one stylesheet previously added');

$handler->setConfiguration(array(
  'all' => array(
    'javascripts' => array('all_foo', 'all_bar'),
  ),
  'myView' => array(
    'javascripts' => array('foobar', '-all_bar'),
  ),
));
$content = <<<EOF
  \$response->addJavascript('all_foo', '', array ());
  \$response->addJavascript('foobar', '', array ());

EOF;
$t->is(fix_content($handler->addHtmlAsset('myView')), fix_content($content), 'addHtmlAsset() supports the - option to remove one javascript previously added');

$handler->setConfiguration(array(
  'all' => array(
    'stylesheets' => array('foo', 'bar', '-*', 'baz'),
  ),
));
$content = <<<EOF
  \$response->addStylesheet('baz', '', array ());

EOF;
$t->is(fix_content($handler->addHtmlAsset('myView')), fix_content($content), 'addHtmlAsset() supports the -* option to remove all stylesheets previously added');

$handler->setConfiguration(array(
  'all' => array(
    'javascripts' => array('foo', 'bar', '-*', 'baz'),
  ),
));
$content = <<<EOF
  \$response->addJavascript('baz', '', array ());

EOF;
$t->is(fix_content($handler->addHtmlAsset('myView')), fix_content($content), 'addHtmlAsset() supports the -* option to remove all javascripts previously added');

$handler->setConfiguration(array(
  'all' => array(
    'stylesheets' => array('-*', 'foobar'),
  ),
  'default' => array(
    'stylesheets' => array('default_foo', 'default_bar'),
  ),
));
$content = <<<EOF
  \$response->addStylesheet('foobar', '', array ());

EOF;
$t->is(fix_content($handler->addHtmlAsset('myView')), fix_content($content), 'addHtmlAsset() supports the -* option to remove all assets previously added');

$handler->setConfiguration(array(
  'myView' => array(
    'stylesheets' => array('foobar', '-*', 'bar'),
    'javascripts' => array('foobar', '-*', 'bar'),
  ),
  'all' => array(
    'stylesheets' => array('all_foo', 'all_foofoo', 'all_barbar'),
    'javascripts' => array('all_foo', 'all_foofoo', 'all_barbar'),
  ),
  'default' => array(
    'stylesheets' => array('default_foo', 'default_foofoo', 'default_barbar'),
    'javascripts' => array('default_foo', 'default_foofoo', 'default_barbar'),
  ),
));
$content = <<<EOF
  \$response->addStylesheet('bar', '', array ());
  \$response->addJavascript('bar', '', array ());

EOF;
$t->is(fix_content($handler->addHtmlAsset('myView')), fix_content($content), 'addHtmlAsset() supports the -* option to remove all assets previously added');

function fix_content($content)
{
  return str_replace(array("\r\n", "\n", "\r"), "\n", $content);
}
