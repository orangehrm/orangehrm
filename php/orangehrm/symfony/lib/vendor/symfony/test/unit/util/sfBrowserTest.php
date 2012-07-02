<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(74);

// ->click()
$t->diag('->click()');

class myClickBrowser extends sfBrowser
{
  public function setHtml($html)
  {
    $this->dom = new DomDocument('1.0', 'UTF-8');
    $this->dom->validateOnParse = true;
    $this->dom->loadHTML($html);
    $this->domCssSelector = new sfDomCssSelector($this->dom);
  }

  public function getFiles()
  {
    $f = $this->files;
    $this->files = array();

    return $f;
  }

  public function call($uri, $method = 'get', $parameters = array(), $changeStack = true)
  {
    $uri = $this->fixUri($uri);

    $this->fields = array();

    return array($method, $uri, $parameters);
  }

  public function getDefaultServerArray($name)
  {
    return isset($this->defaultServerArray[$name]) ? $this->defaultServerArray[$name] : false;
  }
}

$html = <<<EOF
<html>
  <body>
    <a href="/mylink" id="clickable-link" class="one-of-many-clickable-links">test link</a>
    <a href="/myimagelink" class="one-of-many-clickable-links"><img src="myimage.gif" alt="image link" /></a>
    <form action="/myform" method="post">
      <input type="text" name="text_default_value" value="default" />
      <input type="text" name="text" value="" />
      <input type="text" name="i_am_disabled" value="i_am_disabled" disabled="disabled" />
      <textarea name="textarea">content</textarea>
      <select name="select">
        <option value="first">first</option>
        <option value="selected" selected="selected">selected</option>
        <option value="last">last</option>
      </select>
      <select name="select_multiple" multiple="multiple">
        <option value="first">first</option>
        <option value="selected" selected="selected">selected</option>
        <option value="last" selected="selected">last</option>
      </select>
      <input name="article[title]" value="title"/>
      <select name="article[category]" multiple="multiple">
        <option value="1">1</option>
        <option value="2" selected="selected">2</option>
        <option value="3" selected="selected">3</option>
      </select>
      <input name="article[or][much][longer]" value="very long!" />
      <input name="myarray[]" value="value1" />
      <input name="myarray[]" value="value2" />
      <input name="myarray[]" value="value3" />
      <input type="file" name="myfile" />
      <input type="checkbox" name="checkbox1" value="checkboxvalue" checked="checked" />
      <input type="checkbox" name="checkbox2" checked="checked" />
      <input type="checkbox" name="checkbox3" />
      <input type="radio" name="radio1" value="a" id="a-radio" />
      <input type="radio" name="radio1" value="b" id="b-radio" />
      <input type="button" name="mybutton" value="mybuttonvalue" />
      <input type="submit" name="submit" value="submit" id="clickable-input-submit" />
    </form>

    <form action="/myform1" method="get">
      <input type="text" name="text_default_value" value="default" />
      <input type="submit" name="submit" value="submit1" />
    </form>

    <form action="/myform2">
      <input type="text" name="text_default_value" value="default" />
      <input type="submit" name="submit" value="submit2" />
    </form>

    <form action="/myform3?key=value">
      <input type="text" name="text_default_value" value="default" />
      <input type="submit" name="submit" value="submit3" />
    </form>

    <form action="/myform4">
      <div><span>
        <input type="submit" name="submit" value="submit4" />
        <input type="image" src="myimage.png" alt="image submit" name="submit_image" value="image" />
      </span></div>
    </form>

    <form action="/myform5">
      <div><span>
        <button id="submit5">Click</button>
        <input type="image" src="myimage.png" alt="image submit" name="submit_image" value="image" />
      </span></div>
    </form>

    <form action="/myform6" method="post">
      <div><span>
        <input type="text" name="foo[bar]" value="foo" />
        <input type="text" name="foo[bar]" value="bar" />
        <input type="text" name="bar" value="foo" />
        <input type="text" name="bar" value="bar" />
        <input type="submit" name="submit" value="submit6" />
      </span></div>
    </form>

    <a href="/myotherlink">test link</a>
    <a href="/submitlink">submit</a>
    <a href="/submitimagelink"><img src="myimage.gif" alt="submit" /></a>

    <input type="submit" id="orphaned-input-submit" />

    <ul class="css-selector-test">
      <li>my first <a href="myfirstlink">paragraph</a></li>
      <li>my second <a href="mysecondlink">paragraph</a></li>
    </ul>

  </body>
</html>
EOF;

$b = new myClickBrowser();
$b->setHtml($html);

try
{
  $b->click('nonexistantname');
  $t->fail('->click() throws an error if the name does not exist');
}
catch (Exception $e)
{
  $t->pass('->click() throws an error if the name does not exist');
}

try
{
  list($method, $uri, $parameters) = $b->click('submit5');
  $t->pass('->click() clicks on button links');
}
catch(Exception $e)
{
  $t->fail('->click() clicks on button links');
}

list($method, $uri, $parameters) = $b->click('test link');
$t->is($uri, '/mylink', '->click() clicks on links');

list($method, $uri, $parameters) = $b->click('test link', array(), array('position' => 2));
$t->is($uri, '/myotherlink', '->click() can take a third argument to tell the position of the link to click on');

list($method, $uri, $parameters) = $b->click('image link');
$t->is($uri, '/myimagelink', '->click() clicks on image links');

list($method, $uri, $parameters) = $b->click('submit', null, array('position' => 2));
$t->is($uri, '/submitlink', '->click() clicks on submit link at position 2');

list($method, $uri, $parameters) = $b->click('submit', null, array('position' => 3));
$t->is($uri, '/submitimagelink', '->click() clicks on submit image link at position 3');

list($method, $uri, $parameters) = $b->click('submit');
$t->is($method, 'post', '->click() gets the form method');
$t->is($uri, '/myform', '->click() clicks on form submit buttons');
$t->is($parameters['text_default_value'], 'default', '->click() uses default form field values (input)');
$t->is($parameters['text'], '', '->click() uses default form field values (input)');
$t->is($parameters['textarea'], 'content', '->click() uses default form field values (textarea)');
$t->is($parameters['select'], 'selected', '->click() uses default form field values (select)');
$t->is($parameters['select_multiple'], array('selected', 'last'), '->click() uses default form field values (select - multiple)');
$t->is($parameters['article']['title'], 'title', '->click() recognizes array names');
$t->is($parameters['article']['category'], array('2', '3'), '->click() recognizes array names');
$t->is($parameters['article']['or']['much']['longer'], 'very long!', '->click() recognizes array names');
$t->is($parameters['submit'], 'submit', '->click() populates button clicked');
$t->ok(!isset($parameters['mybutton']), '->click() do not populate buttons not clicked');
$t->is($parameters['myarray'], array('value1', 'value2', 'value3'), '->click() recognizes array names');
$t->is($parameters['checkbox1'], 'checkboxvalue', '->click() returns the value of the checkbox value attribute');
$t->is($parameters['checkbox2'], '1', '->click() returns 1 if the checkbox has no value');

list($method, $uri, $parameters) = $b->click('mybuttonvalue');
$t->is($uri, '/myform', '->click() clicks on form buttons');
$t->is($parameters['text_default_value'], 'default', '->click() uses default form field values');
$t->is($parameters['mybutton'], 'mybuttonvalue', '->click() populates button clicked');
$t->ok(!isset($parameters['submit']), '->click() do not populate buttons not clicked');

list($method, $uri, $parameters) = $b->click('submit1');
$t->is($uri, '/myform1?text_default_value=default&submit=submit1', '->click() clicks on form buttons');
$t->is($method, 'get', '->click() gets the form method');

list($method, $uri, $parameters) = $b->click('submit2');
$t->is($method, 'get', '->click() defaults to get method');

list($method, $uri, $parameters) = $b->click('submit3');
$t->is($uri, '/myform3?key=value&text_default_value=default&submit=submit3', '->click() concatenates fields values with existing action parameters');

list($method, $uri, $parameters) = $b->click('submit4');
$t->is($uri, '/myform4?submit=submit4', '->click() can click on submit button anywhere in a form');

list($method, $uri, $parameters) = $b->click('image submit');
$t->is($uri, '/myform4?submit_image=image', '->click() can click on image button in forms');

list($method, $uri, $parameters) = $b->click('submit', array(
  'text_default_value' => 'myvalue',
  'text' => 'myothervalue',
  'textarea' => 'mycontent',
  'select' => 'last',
  'select_multiple' => array('first', 'selected', 'last'),
  'article' => array(
    'title' => 'mytitle',
    'category' => array(1, 2, 3),
    'or' => array('much' => array('longer' => 'long')),
  ),
));
$t->is($parameters['text_default_value'], 'myvalue', '->click() takes an array of parameters as its second argument');
$t->is($parameters['text'], 'myothervalue', '->click() can override input fields');
$t->is($parameters['textarea'], 'mycontent', '->click() can override textarea fields');
$t->is($parameters['select'], 'last', '->click() can override select fields');
$t->is($parameters['select_multiple'], array('first', 'selected', 'last'), '->click() can override select (multiple) fields');
$t->is($parameters['article']['title'], 'mytitle', '->click() can override array fields');
$t->is($parameters['article']['category'], array(1, 2, 3), '->click() can override array fields');
$t->is($parameters['article']['or']['much']['longer'], 'long', '->click() recognizes array names');
$t->is(isset($parameters['i_am_disabled']), false, '->click() ignores disabled fields');

list($method, $uri, $parameters) = $b->click('#clickable-link');
$t->is($method, 'get', '->click() accepts a CSS selector');
$t->is($uri, '/mylink', '->click() accepts a CSS selector');
$t->is_deeply($parameters, array(), '->click() accepts a CSS selector');

list($method, $uri, $parameters) = $b->click('.one-of-many-clickable-links', array(), array('position' => 2));
$t->is($method, 'get', '->click() accepts a CSS selector and position option');
$t->is($uri, '/myimagelink', '->click() accepts a CSS selector and position option');
$t->is_deeply($parameters, array(), '->click() accepts a CSS selector and position option');

list($method, $uri, $parameters) = $b->click('#clickable-input-submit');
$t->is($method, 'post', '->click() accepts a CSS selector for a submit input');
$t->is($uri, '/myform', '->click() accepts a CSS selector for a submit input');

try
{
  $b->click('#orphaned-input-submit');
  $t->fail('->click() throws an error if a submit is clicked outside a form');
}
catch (Exception $e)
{
  $t->pass('->click() throws an error if a submit is clicked outside a form');
}

// ->setField()
$t->diag('->setField()');
list($method, $uri, $parameters) = $b->
  setField('text_default_value', 'myvalue')->
  setField('text', 'myothervalue')->
  setField('article[title]', 'mytitle')->
  setField('myarray[0]', 'value0')->
  setField('myarray[1]', 'value1')->
  setField('myarray[2]', 'value2')->
  click('submit')
;
$t->is($parameters['text_default_value'], 'myvalue', '->setField() overrides default form field values');
$t->is($parameters['text'], 'myothervalue', '->setField() overrides default form field values');
$t->is($parameters['article']['title'], 'mytitle', '->setField() overrides default form field values');
$t->is($parameters['myarray'], array('value0', 'value1', 'value2'), '->setField() overrides default form field values');

list($method, $uri, $parameters) = $b->
  setField('text_default_value', 'myvalue')->
  setField('text', 'myothervalue')->
  click('submit', array('text_default_value' => 'yourvalue', 'text' => 'yourothervalue'))
;
$t->is($parameters['text_default_value'], 'yourvalue', '->setField() is overriden by parameters from click call');
$t->is($parameters['text'], 'yourothervalue', '->setField() is overriden by parameters from click call');

// ->deselect()/select()
$t->diag('->deselect()/select()');
list($method, $uri, $parameters) = $b->
  deselect('checkbox1')->
  select('checkbox3')->
  select('b-radio')->
  click('submit')
;
$t->is(isset($parameters['checkbox1']), false, '->deselect() unckecks a checkbox');
$t->is(isset($parameters['checkbox3']), true, '->select() ckecks a checkbox');
$t->is($parameters['radio1'], 'b', '->select() selects a radiobutton');
list($method, $uri, $parameters) = $b->
  select('a-radio')->
  click('submit')
;
$t->is($parameters['radio1'], 'a', '->select() toggles radiobuttons');

try
{
  $b->deselect('b-radio');
  $t->fail('->deselect() cannot deselect radiobuttons');
}
catch(Exception $e)
{
  $t->pass('->deselect() cannot deselect radiobuttons');
}

list($method, $uri, $parameters) = $b->click('li:contains("first") a');
$t->is($uri, 'myfirstlink', 'click accept css selectors without "[" or "]"');

// ->call()
$t->diag('->call()');
$b->call('https://app-test/index.phpmain/index');
$t->is($b->getDefaultServerArray('HTTPS'), 'on', '->call() detects secure requests');
$t->is($b->getDefaultServerArray('HTTPS'), 'on', '->call() preserves SSL information between requests');
$b->call('http://app-test/index.phpmain/index');
$t->is($b->getDefaultServerArray('HTTPS'), null, '->call() preserve non-secure requests');

// file uploads
$t->diag('file uploads');

$unexistentFilename = sfConfig::get('sf_test_cache_dir') . DIRECTORY_SEPARATOR . 'unexistent-file-'.md5(getmypid().'-'.microtime());
$existentFilename =  sfConfig::get('sf_test_cache_dir') . DIRECTORY_SEPARATOR . 'existent-file-'.md5(getmypid().'-'.microtime());
file_put_contents($existentFilename, 'test');

list($method, $uri, $parameters) = $b->click('submit', array('myfile'=>$unexistentFilename));
$files = $b->getFiles();
$t->is($method, 'post', 'file upload is using right method');
$t->is(!isset($parameters['myfile']), 'file upload key is removed from the main request');
$t->is(isset($files['myfile'])&&is_array($files['myfile']), true, 'file upload set up a _FILE entry for our test file');
$t->is(array_keys($files['myfile']), array('name','type','tmp_name','error','size'), 'file upload returns correctly formatted array');
$t->is($files['myfile']['error'], UPLOAD_ERR_NO_FILE, 'unexistent file does not exists (UPLOAD_ERR_NO_FILE)');

list($method, $uri, $parameters) = $b->click('submit', array('myfile' => $existentFilename));
$files = $b->getFiles();

$t->is($files['myfile']['error'], UPLOAD_ERR_OK, 'existent file exists (UPLOAD_ERR_OK)');
$t->is($files['myfile']['name'], basename($existentFilename), 'name key ok');

// bug #7816
$t->diag('bug #7816');
list($method, $uri, $parameters) = $b->click('submit6');
$t->is($parameters['bar'], 'bar', '->click() overrides input elements defined several times');
$t->is($parameters['foo']['bar'], 'bar', '->click() overrides input elements defined several times');
