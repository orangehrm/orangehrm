<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(65, new lime_output_color());

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

// ->getTexts()
$t->diag('->getTexts()');

$t->diag('basic selectors');
$t->is($c->getTexts('h1'), array('Test page'), '->getTexts() takes a CSS selector as its first argument');
$t->is($c->getTexts('h2'), array('Title 1', 'Title 2'), '->getTexts() returns an array of matching texts');
$t->is($c->getTexts('#footer'), array('footer'), '->getTexts() supports searching html elements by id');
$t->is($c->getTexts('div#footer'), array('footer'), '->getTexts() supports searching html elements by id for a tag name');
$t->is($c->getTexts('*[class="myfoo"]'), array('myfoo', 'myfoo bis'), '->getTexts() can take a * to match every elements');

$t->is($c->getTexts('.header'), array('header'), '->getTexts() supports searching html elements by class name');
$t->is($c->getTexts('p.header'), array('header'), '->getTexts() supports searching html elements by class name for a tag name');
$t->is($c->getTexts('div.header'), array(), '->getTexts() supports searching html elements by class name for a tag name');
$t->is($c->getTexts('*.header'), array('header'), '->getTexts() supports searching html elements by class name');

$t->is($c->getTexts('.foo'), array('multi-classes'), '->getTexts() supports searching html elements by class name for multi-class elements');
$t->is($c->getTexts('.bar'), array('multi-classes'), '->getTexts() supports searching html elements by class name for multi-class elements');
$t->is($c->getTexts('.foobar'), array('multi-classes'), '->getTexts() supports searching html elements by class name for multi-class elements');

$t->is($c->getTexts('ul#mylist ul li'), array('element 3', 'element 4'), '->getTexts() supports searching html elements by several selectors');

$t->is($c->getTexts('#nonexistant'), array(), '->getTexts() returns an empty array if the id does not exist');

$t->diag('attribute selectors');
$t->is($c->getTexts('ul#list li a[href]'), array('link'), '->getTexts() supports checking attribute existence');
$t->is($c->getTexts('ul#list li a[class~="foo1"]'), array('link'), '->getTexts() supports checking attribute word matching');
$t->is($c->getTexts('ul#list li a[class~="bar1"]'), array('link'), '->getTexts() supports checking attribute word matching');
$t->is($c->getTexts('ul#list li a[class~="foobar1"]'), array('link'), '->getTexts() supports checking attribute word matching');
$t->is($c->getTexts('ul#list li a[class^="foo1"]'), array('link'), '->getTexts() supports checking attribute starting with');
$t->is($c->getTexts('ul#list li a[class$="foobar1"]'), array('link'), '->getTexts() supports checking attribute ending with');
$t->is($c->getTexts('ul#list li a[class*="oba"]'), array('link'), '->getTexts() supports checking attribute with *');
$t->is($c->getTexts('ul#list li a[href="http://www.google.com/"]'), array('link'), '->getTexts() supports checking attribute word matching');
$t->is($c->getTexts('ul#anotherlist li a[class|="bar1"]'), array('another link'), '->getTexts() supports checking attribute starting with value followed by optional hyphen');

$t->is($c->getTexts('ul#list li a[class*="oba"][class*="ba"]'), array('link'), '->getTexts() supports chaining attribute selectors');
$t->is($c->getTexts('p[class="myfoo"][id="mybar"]'), array('myfoo bis'), '->getTexts() supports chaining attribute selectors');

$t->is($c->getTexts('p[onclick*="a . and a #"]'), array('works great'), '->getTexts() support . # and spaces in attribute selectors');

$t->diag('combinators');
$t->is($c->getTexts('body  h1'), array('Test page'), '->getTexts() takes a CSS selectors separated by one or more spaces');
$t->is($c->getTexts('div#combinators > ul  >   li'), array('test 1', 'test 2'), '->getTexts() support > combinator');
$t->is($c->getTexts('div#combinators>ul>li'), array('test 1', 'test 2'), '->getTexts() support > combinator with optional surrounding spaces');
$t->is($c->getTexts('div#combinators li  +   li'), array('test 2', 'test 4'), '->getTexts() support + combinator');
$t->is($c->getTexts('div#combinators li+li'), array('test 2', 'test 4'), '->getTexts() support + combinator with optional surrounding spaces');

$t->is($c->getTexts('h1, h2'), array('Test page', 'Title 1', 'Title 2'), '->getTexts() takes a multiple CSS selectors separated by a ,');
$t->is($c->getTexts('h1,h2'), array('Test page', 'Title 1', 'Title 2'), '->getTexts() takes a multiple CSS selectors separated by a ,');
$t->is($c->getTexts('h1  ,   h2'), array('Test page', 'Title 1', 'Title 2'), '->getTexts() takes a multiple CSS selectors separated by a ,');
$t->is($c->getTexts('h1, h1,h1'), array('Test page'), '->getTexts() returns nodes only once for multiple selectors');
$t->is($c->getTexts('h1,h2,h1'), array('Test page', 'Title 1', 'Title 2'), '->getTexts() returns nodes only once for multiple selectors');

$t->is($c->getTexts('p[onclick*="a . and a #"], div#combinators > ul li + li'), array('works great', 'test 2', 'test 4'), '->getTexts() mega example!');

$t->is($c->getTexts('.myfoo:contains("bis")'), array('myfoo bis'), '->getTexts() :contains()');
$t->is($c->getTexts('.myfoo:eq(1)'), array('myfoo bis'), '->getTexts() :eq()');
$t->is($c->getTexts('.myfoo:last'), array('myfoo bis'), '->getTexts() :last');
$t->is($c->getTexts('.myfoo:first'), array('myfoo'), '->getTexts() :first');
$t->is($c->getTexts('h2:first'), array('Title 1'), '->getTexts() :first');
$t->is($c->getTexts('p.myfoo:first'), array('myfoo'), '->getTexts() :first');
$t->is($c->getTexts('p:lt(2)'), array('header', 'multi-classes'), '->getTexts() :lt');
$t->is($c->getTexts('p:gt(2)'), array('myfoo bis', 'works great', 'First paragraph', 'Second paragraph', 'Third paragraph'), '->getTexts() :gt');
$t->is($c->getTexts('p:odd'), array('multi-classes', 'myfoo bis', 'First paragraph', 'Third paragraph'), '->getTexts() :odd');
$t->is($c->getTexts('p:even'), array('header', 'myfoo', 'works great', 'Second paragraph'), '->getTexts() :even');
$t->is($c->getTexts('#simplelist li:first-child'), array('First', 'First'), '->getTexts() :first-child');
$t->is($c->getTexts('#simplelist li:nth-child(1)'), array('First', 'First'), '->getTexts() :nth-child');
$t->is($c->getTexts('#simplelist li:nth-child(2)'), array('Second with a link', 'Second'), '->getTexts() :nth-child');
$t->is($c->getTexts('#simplelist li:nth-child(3)'), array('Third with another link'), '->getTexts() :nth-child');
$t->is($c->getTexts('#simplelist li:last-child'), array('Second with a link', 'Third with another link'), '->getTexts() :last-child');

$t->diag('combinations of pseudo-selectors');
$t->is($c->getTexts('.myfoo:contains("myfoo"):contains("bis")'), array('myfoo bis'), '->getTexts() :contains():contains()');
$t->is($c->getTexts('.myfoo:contains("myfoo"):last'), array('myfoo bis'), '->getTexts() :contains():last');
$t->is($c->getTexts('.myfoo:last:contains("foobarbaz")'), array(), '->getTexts() :last:contains()');
$t->is($c->getTexts('.myfoo:contains("myfoo"):contains(\'bis\'):contains(foo)'), array('myfoo bis'), '->getTexts() :contains() supports different quote styles');

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

$t->is($c->getTexts('#adjacent_bug > p'), array('First paragraph', 'Second paragraph', 'Third paragraph'), '->getTexts() suppports the + combinator');
$t->is($c->getTexts('#adjacent_bug > p > a'), array('paragraph'), '->getTexts() suppports the + combinator');
$t->is($c->getTexts('#adjacent_bug p + p'), array('Second paragraph', 'Third paragraph'), '->getTexts() suppports the + combinator');
$t->is($c->getTexts('#adjacent_bug > p + p'), array('Second paragraph', 'Third paragraph'), '->getTexts() suppports the + combinator');
$t->is($c->getTexts('#adjacent_bug > p + p > a'), array('paragraph'), '->getTexts() suppports the + combinator');
