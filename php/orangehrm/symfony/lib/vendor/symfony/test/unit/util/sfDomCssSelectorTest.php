<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(71);

$html = <<<EOF
<html>
  <head>
  </head>
  <body>
    <h1>Test page</h1>

    <h2>Title 1</h2>
    <p class="header">header</p>
    <p class="foo bar foobar">multi-classes</p>
    <p class="myfoo">myfoo</p>
    <p class="myfoo" id="mybar">myfoo bis</p>

    <p onclick="javascript:alert('with a . and a # inside an attribute');">works great</p>

    <select>
      <option value="0">foo input</option>
    </select>

    <div id="simplelist">
      <ul id="list">
        <li>First</li>
        <li>Second with a <a href="http://www.google.com/" class="foo1 bar1 bar1-foo1 foobar1">link</a></li>
      </ul>

      <ul id="anotherlist">
        <li>First</li>
        <li>Second</li>
        <li>Third with <a class="bar1-foo1">another link</a></li>
      </ul>
    </div>

    <h2>Title 2</h2>
    <ul id="mylist">
      <li>element 1</li>
      <li>element 2</li>
      <li>
        <ul>
          <li>element 3</li>
          <li>element 4</li>
        </ul>
      </li>
    </ul>

    <div id="combinators">
      <ul>
        <li>test 1</li>
        <li>test 2</li>
        <ul>
          <li>test 3</li>
          <li>test 4</li>
        </ul>
      </ul>
    </div>

    <div id="adjacent_bug">
      <p>First paragraph</p>
      <p>Second paragraph</p>
      <p>Third <a href='#'>paragraph</a></p>
    </div>

    <div id="footer">footer</div>
  </body>
</html>
EOF;

$dom = new DomDocument('1.0', 'utf-8');
$dom->validateOnParse = true;
$dom->loadHTML($html);

$c = new sfDomCssSelector($dom);

// ->matchAll()
$t->diag('->matchAll()');

$t->diag('basic selectors');
$t->is($c->matchAll('h1')->getValues(), array('Test page'), '->matchAll() takes a CSS selector as its first argument');
$t->is($c->matchAll('h2')->getValues(), array('Title 1', 'Title 2'), '->matchAll() returns an array of matching texts');
$t->is($c->matchAll('#footer')->getValues(), array('footer'), '->matchAll() supports searching html elements by id');
$t->is($c->matchAll('div#footer')->getValues(), array('footer'), '->matchAll() supports searching html elements by id for a tag name');
$t->is($c->matchAll('*[class="myfoo"]')->getValues(), array('myfoo', 'myfoo bis'), '->matchAll() can take a * to match every elements');
$t->is($c->matchAll('*[class=myfoo]')->getValues(), array('myfoo', 'myfoo bis'), '->matchAll() can take a * to match every elements');
$t->is($c->matchAll('*[value="0"]')->getValues(), array('foo input'), '->matchAll() can take a * to match every elements');

$t->is($c->matchAll('.header')->getValues(), array('header'), '->matchAll() supports searching html elements by class name');
$t->is($c->matchAll('p.header')->getValues(), array('header'), '->matchAll() supports searching html elements by class name for a tag name');
$t->is($c->matchAll('div.header')->getValues(), array(), '->matchAll() supports searching html elements by class name for a tag name');
$t->is($c->matchAll('*.header')->getValues(), array('header'), '->matchAll() supports searching html elements by class name');

$t->is($c->matchAll('.foo')->getValues(), array('multi-classes'), '->matchAll() supports searching html elements by class name for multi-class elements');
$t->is($c->matchAll('.bar')->getValues(), array('multi-classes'), '->matchAll() supports searching html elements by class name for multi-class elements');
$t->is($c->matchAll('.foobar')->getValues(), array('multi-classes'), '->matchAll() supports searching html elements by class name for multi-class elements');

$t->is($c->matchAll('ul#mylist ul li')->getValues(), array('element 3', 'element 4'), '->matchAll() supports searching html elements by several selectors');

$t->is($c->matchAll('#nonexistant')->getValues(), array(), '->matchAll() returns an empty array if the id does not exist');

$t->diag('attribute selectors');
$t->is($c->matchAll('ul#list li a[href]')->getValues(), array('link'), '->matchAll() supports checking attribute existence');
$t->is($c->matchAll('ul#list li a[class~="foo1"]')->getValues(), array('link'), '->matchAll() supports checking attribute word matching');
$t->is($c->matchAll('ul#list li a[class~="bar1"]')->getValues(), array('link'), '->matchAll() supports checking attribute word matching');
$t->is($c->matchAll('ul#list li a[class~="foobar1"]')->getValues(), array('link'), '->matchAll() supports checking attribute word matching');
$t->is($c->matchAll('ul#list li a[class^="foo1"]')->getValues(), array('link'), '->matchAll() supports checking attribute starting with');
$t->is($c->matchAll('ul#list li a[class$="foobar1"]')->getValues(), array('link'), '->matchAll() supports checking attribute ending with');
$t->is($c->matchAll('ul#list li a[class*="oba"]')->getValues(), array('link'), '->matchAll() supports checking attribute with *');
$t->is($c->matchAll('ul#list li a[href="http://www.google.com/"]')->getValues(), array('link'), '->matchAll() supports checking attribute word matching');
$t->is($c->matchAll('ul#anotherlist li a[class|="bar1"]')->getValues(), array('another link'), '->matchAll() supports checking attribute starting with value followed by optional hyphen');

$t->is($c->matchAll('ul#list li a[class*="oba"][class*="ba"]')->getValues(), array('link'), '->matchAll() supports chaining attribute selectors');
$t->is($c->matchAll('p[class="myfoo"][id="mybar"]')->getValues(), array('myfoo bis'), '->matchAll() supports chaining attribute selectors');

$t->is($c->matchAll('p[onclick*="a . and a #"]')->getValues(), array('works great'), '->matchAll() support . # and spaces in attribute selectors');

$t->diag('combinators');
$t->is($c->matchAll('body  h1')->getValues(), array('Test page'), '->matchAll() takes a CSS selectors separated by one or more spaces');
$t->is($c->matchAll('div#combinators > ul  >   li')->getValues(), array('test 1', 'test 2'), '->matchAll() support > combinator');
$t->is($c->matchAll('div#combinators>ul>li')->getValues(), array('test 1', 'test 2'), '->matchAll() support > combinator with optional surrounding spaces');
$t->is($c->matchAll('div#combinators li  +   li')->getValues(), array('test 2', 'test 4'), '->matchAll() support + combinator');
$t->is($c->matchAll('div#combinators li+li')->getValues(), array('test 2', 'test 4'), '->matchAll() support + combinator with optional surrounding spaces');

$t->is($c->matchAll('h1, h2')->getValues(), array('Test page', 'Title 1', 'Title 2'), '->matchAll() takes a multiple CSS selectors separated by a ,');
$t->is($c->matchAll('h1,h2')->getValues(), array('Test page', 'Title 1', 'Title 2'), '->matchAll() takes a multiple CSS selectors separated by a ,');
$t->is($c->matchAll('h1  ,   h2')->getValues(), array('Test page', 'Title 1', 'Title 2'), '->matchAll() takes a multiple CSS selectors separated by a ,');
$t->is($c->matchAll('h1, h1,h1')->getValues(), array('Test page'), '->matchAll() returns nodes only once for multiple selectors');
$t->is($c->matchAll('h1,h2,h1')->getValues(), array('Test page', 'Title 1', 'Title 2'), '->matchAll() returns nodes only once for multiple selectors');

$t->is($c->matchAll('p[onclick*="a . and a #"], div#combinators > ul li + li')->getValues(), array('works great', 'test 2', 'test 4'), '->matchAll() mega example!');

$t->is($c->matchAll('.myfoo:contains("bis")')->getValues(), array('myfoo bis'), '->matchAll() :contains()');
$t->is($c->matchAll('.myfoo:eq(1)')->getValues(), array('myfoo bis'), '->matchAll() :eq()');
$t->is($c->matchAll('.myfoo:last')->getValues(), array('myfoo bis'), '->matchAll() :last');
$t->is($c->matchAll('.myfoo:first')->getValues(), array('myfoo'), '->matchAll() :first');
$t->is($c->matchAll('h2:first')->getValues(), array('Title 1'), '->matchAll() :first');
$t->is($c->matchAll('p.myfoo:first')->getValues(), array('myfoo'), '->matchAll() :first');
$t->is($c->matchAll('p:lt(2)')->getValues(), array('header', 'multi-classes'), '->matchAll() :lt');
$t->is($c->matchAll('p:gt(2)')->getValues(), array('myfoo bis', 'works great', 'First paragraph', 'Second paragraph', 'Third paragraph'), '->matchAll() :gt');
$t->is($c->matchAll('p:odd')->getValues(), array('multi-classes', 'myfoo bis', 'First paragraph', 'Third paragraph'), '->matchAll() :odd');
$t->is($c->matchAll('p:even')->getValues(), array('header', 'myfoo', 'works great', 'Second paragraph'), '->matchAll() :even');
$t->is($c->matchAll('#simplelist li:first-child')->getValues(), array('First', 'First'), '->matchAll() :first-child');
$t->is($c->matchAll('#simplelist li:nth-child(1)')->getValues(), array('First', 'First'), '->matchAll() :nth-child');
$t->is($c->matchAll('#simplelist li:nth-child(2)')->getValues(), array('Second with a link', 'Second'), '->matchAll() :nth-child');
$t->is($c->matchAll('#simplelist li:nth-child(3)')->getValues(), array('Third with another link'), '->matchAll() :nth-child');
$t->is($c->matchAll('#simplelist li:last-child')->getValues(), array('Second with a link', 'Third with another link'), '->matchAll() :last-child');

$t->diag('combinations of pseudo-selectors');
$t->is($c->matchAll('.myfoo:contains("myfoo"):contains("bis")')->getValues(), array('myfoo bis'), '->matchAll() :contains():contains()');
$t->is($c->matchAll('.myfoo:contains("myfoo"):last')->getValues(), array('myfoo bis'), '->matchAll() :contains():last');
$t->is($c->matchAll('.myfoo:last:contains("foobarbaz")')->getValues(), array(), '->matchAll() :last:contains()');
$t->is($c->matchAll('.myfoo:contains("myfoo"):contains(\'bis\'):contains(foo)')->getValues(), array('myfoo bis'), '->matchAll() :contains() supports different quote styles');

// ->matchAll()
$t->diag('->matchAll()');
$t->is($c->matchAll('ul')->matchAll('li')->getValues(), $c->matchAll('ul li')->getValues(), '->matchAll() returns a new sfDomCssSelector restricted to the result nodes');

// ->matchSingle()
$t->diag('->matchSingle()');
$t->is(array($c->matchAll('ul li')->getValue()), $c->matchSingle('ul li')->getValues(), '->matchSingle() returns a new sfDomCssSelector restricted to the first result node');

// ->getValues()
$t->diag('->getValues()');
$t->is($c->matchAll('p.myfoo')->getValues(), array('myfoo', 'myfoo bis'), '->getValues() returns all node values');

// ->getValue()
$t->diag('->getValue()');
$t->is($c->matchAll('h1')->getValue(), 'Test page', '->getValue() returns the first node value');

$t->is($c->matchAll('#adjacent_bug > p')->getValues(), array('First paragraph', 'Second paragraph', 'Third paragraph'), '->matchAll() suppports the + combinator');
$t->is($c->matchAll('#adjacent_bug > p > a')->getValues(), array('paragraph'), '->matchAll() suppports the + combinator');
$t->is($c->matchAll('#adjacent_bug p + p')->getValues(), array('Second paragraph', 'Third paragraph'), '->matchAll() suppports the + combinator');
$t->is($c->matchAll('#adjacent_bug > p + p')->getValues(), array('Second paragraph', 'Third paragraph'), '->matchAll() suppports the + combinator');
$t->is($c->matchAll('#adjacent_bug > p + p > a')->getValues(), array('paragraph'), '->matchAll() suppports the + combinator');

// Iterator interface
$t->diag('Iterator interface');
foreach ($c->matchAll('h2') as $key => $value)
{
  switch ($key)
  {
    case 0:
      $t->is($value->nodeValue, 'Title 1', 'The object is an iterator');
      break;
    case 1:
      $t->is($value->nodeValue, 'Title 2', 'The object is an iterator');
      break;
    default:
      $t->fail('The object is an iterator');
  }
}

// Countable interface
$t->diag('Countable interface');
$t->is(count($c->matchAll('h1')), 1, 'sfDomCssSelector implements Countable');
$t->is(count($c->matchAll('h2')), 2, 'sfDomCssSelector implements Countable');
