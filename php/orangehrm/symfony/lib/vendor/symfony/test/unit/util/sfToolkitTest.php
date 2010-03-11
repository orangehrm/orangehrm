<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(113, new lime_output_color());

// ::stringToArray()
$t->diag('::stringToArray()');
$tests = array(
  'foo=bar' => array('foo' => 'bar'),
  'foo1=bar1 foo=bar   ' => array('foo1' => 'bar1', 'foo' => 'bar'),
  'foo1="bar1 foo1"' => array('foo1' => 'bar1 foo1'),
  'foo1="bar1 foo1" foo=bar' => array('foo1' => 'bar1 foo1', 'foo' => 'bar'),
  'foo1 = "bar1=foo1" foo=bar' => array('foo1' => 'bar1=foo1', 'foo' => 'bar'),
  'foo1= \'bar1 foo1\'    foo  =     bar' => array('foo1' => 'bar1 foo1', 'foo' => 'bar'),
  'foo1=\'bar1=foo1\' foo = bar' => array('foo1' => 'bar1=foo1', 'foo' => 'bar'),
  'foo1=  bar1 foo1 foo=bar' => array('foo1' => 'bar1 foo1', 'foo' => 'bar'),
  'foo1="l\'autre" foo=bar' => array('foo1' => 'l\'autre', 'foo' => 'bar'),
  'foo1="l"autre" foo=bar' => array('foo1' => 'l"autre', 'foo' => 'bar'),
  'foo_1=bar_1' => array('foo_1' => 'bar_1'),
);

foreach ($tests as $string => $attributes)
{
  $t->is(sfToolkit::stringToArray($string), $attributes, '->stringToArray()');
}

// ::isUTF8()
$t->diag('::isUTF8()');
$t->is('été', true, '::isUTF8() returns true if the parameter is an UTF-8 encoded string');
$t->is(sfToolkit::isUTF8('AZERTYazerty1234-_'), true, '::isUTF8() returns true if the parameter is an UTF-8 encoded string');
$t->is(sfToolkit::isUTF8('AZERTYazerty1234-_'.chr(254)), false, '::isUTF8() returns false if the parameter is not an UTF-8 encoded string');
// check a very long string
$string = str_repeat('Here is an UTF8 string avec du français.', 1000);
$t->is(sfToolkit::isUTF8($string), true, '::isUTF8() can operate on very large strings');

// ::literalize()
$t->diag('::literalize()');
foreach (array('true', 'on', '+', 'yes') as $param)
{
  $t->is(sfToolkit::literalize($param), true, sprintf('::literalize() returns true with "%s"', $param));
  if (strtoupper($param) != $param)
  {
    $t->is(sfToolkit::literalize(strtoupper($param)), true, sprintf('::literalize() returns true with "%s"', strtoupper($param)));
  }
  $t->is(sfToolkit::literalize(' '.$param.' '), true, sprintf('::literalize() returns true with "%s"', ' '.$param.' '));
}

foreach (array('false', 'off', '-', 'no') as $param)
{
  $t->is(sfToolkit::literalize($param), false, sprintf('::literalize() returns false with "%s"', $param));
  if (strtoupper($param) != $param)
  {
    $t->is(sfToolkit::literalize(strtoupper($param)), false, sprintf('::literalize() returns false with "%s"', strtoupper($param)));
  }
  $t->is(sfToolkit::literalize(' '.$param.' '), false, sprintf('::literalize() returns false with "%s"', ' '.$param.' '));
}

foreach (array('null', '~', '') as $param)
{
  $t->is(sfToolkit::literalize($param), null, sprintf('::literalize() returns null with "%s"', $param));
  if (strtoupper($param) != $param)
  {
    $t->is(sfToolkit::literalize(strtoupper($param)), null, sprintf('::literalize() returns null with "%s"', strtoupper($param)));
  }
  $t->is(sfToolkit::literalize(' '.$param.' '), null, sprintf('::literalize() returns null with "%s"', ' '.$param.' '));
}

// ::replaceConstants()
$t->diag('::replaceConstants()');
sfConfig::set('foo', 'bar');
$t->is(sfToolkit::replaceConstants('my value with a %foo% constant'), 'my value with a bar constant', '::replaceConstantsCallback() replaces constants enclosed in %');
$t->is(sfToolkit::replaceConstants('%Y/%m/%d %H:%M'), '%Y/%m/%d %H:%M', '::replaceConstantsCallback() does not replace unknown constants');
sfConfig::set('bar', null);
$t->is(sfToolkit::replaceConstants('my value with a %bar% constant'), 'my value with a  constant', '::replaceConstantsCallback() replaces constants enclosed in % even if value is null');
$t->is(sfToolkit::replaceConstants('my value with a %foobar% constant'), 'my value with a %foobar% constant', '::replaceConstantsCallback() returns the original string if the constant is not defined');
$t->is(sfToolkit::replaceConstants('my value with a %foo\'bar% constant'), 'my value with a %foo\'bar% constant', '::replaceConstantsCallback() returns the original string if the constant is not defined');
$t->is(sfToolkit::replaceConstants('my value with a %foo"bar% constant'), 'my value with a %foo"bar% constant', '::replaceConstantsCallback() returns the original string if the constant is not defined');

// ::isPathAbsolute()
$t->diag('::isPathAbsolute()');
$t->is(sfToolkit::isPathAbsolute('/test'), true, '::isPathAbsolute() returns true if path is absolute');
$t->is(sfToolkit::isPathAbsolute('\\test'), true, '::isPathAbsolute() returns true if path is absolute');
$t->is(sfToolkit::isPathAbsolute('C:\\test'), true, '::isPathAbsolute() returns true if path is absolute');
$t->is(sfToolkit::isPathAbsolute('d:/test'), true, '::isPathAbsolute() returns true if path is absolute');
$t->is(sfToolkit::isPathAbsolute('test'), false, '::isPathAbsolute() returns false if path is relative');
$t->is(sfToolkit::isPathAbsolute('../test'), false, '::isPathAbsolute() returns false if path is relative');
$t->is(sfToolkit::isPathAbsolute('..\\test'), false, '::isPathAbsolute() returns false if path is relative');

// ::stripComments()
$t->diag('::stripComments()');

$php = <<<EOF
<?php

# A perl like comment
// Another comment
/* A very long
comment
on several lines
*/

\$i = 1; // A comment on a PHP line
EOF;

$stripped_php = '<?php $i = 1; ';

$t->is(preg_replace('/\s*(\r?\n)+/', ' ', sfToolkit::stripComments($php)), $stripped_php, '::stripComments() strip all comments from a php string');
sfConfig::set('sf_strip_comments', false);
$t->is(sfToolkit::stripComments($php), $php, '::stripComments() do nothing if "sf_strip_comments" is false');

sfConfig::set('sf_strip_comments', true);

$php = <<<EOF
<?php
  \$pluginDirs = '/*/modules/lib/helper';
  \$pluginDirs = '/*/lib/helper';

EOF;

$t->is(sfToolkit::stripComments($php), $php, '::stripComments() correctly handles comments within strings');


// ::stripslashesDeep()
$t->diag('::stripslashesDeep()');
$t->is(sfToolkit::stripslashesDeep('foo'), 'foo', '::stripslashesDeep() strip slashes on string');
$t->is(sfToolkit::stripslashesDeep(addslashes("foo's bar")), "foo's bar", '::stripslashesDeep() strip slashes on array');
$t->is(sfToolkit::stripslashesDeep(array(addslashes("foo's bar"), addslashes("foo's bar"))), array("foo's bar", "foo's bar"), '::stripslashesDeep() strip slashes on deep arrays');
$t->is(sfToolkit::stripslashesDeep(array(array('foo' => addslashes("foo's bar")), addslashes("foo's bar"))), array(array('foo' => "foo's bar"), "foo's bar"), '::stripslashesDeep() strip slashes on deep arrays');

// ::clearDirectory()
$t->diag('::clearDirectory()');
$tmp_dir = sfToolkit::getTmpDir().DIRECTORY_SEPARATOR.'symfony_tests_'.rand(1, 999);
mkdir($tmp_dir);
file_put_contents($tmp_dir.DIRECTORY_SEPARATOR.'test', 'ok');
mkdir($tmp_dir.DIRECTORY_SEPARATOR.'foo');
file_put_contents($tmp_dir.DIRECTORY_SEPARATOR.'foo'.DIRECTORY_SEPARATOR.'bar', 'ok');
sfToolkit::clearDirectory($tmp_dir);
$t->ok(!is_dir($tmp_dir.DIRECTORY_SEPARATOR.'foo'), '::clearDirectory() removes all directories from the directory parameter');
$t->ok(!is_file($tmp_dir.DIRECTORY_SEPARATOR.'foo'.DIRECTORY_SEPARATOR.'bar'), '::clearDirectory() removes all directories from the directory parameter');
$t->ok(!is_file($tmp_dir.DIRECTORY_SEPARATOR.'test'), '::clearDirectory() removes all directories from the directory parameter');
rmdir($tmp_dir);

// ::clearGlob()
$t->diag('::clearGlob()');
$tmp_dir = sfToolkit::getTmpDir().DIRECTORY_SEPARATOR.'symfony_tests_'.rand(1, 999);
mkdir($tmp_dir);
mkdir($tmp_dir.DIRECTORY_SEPARATOR.'foo');
mkdir($tmp_dir.DIRECTORY_SEPARATOR.'bar');
file_put_contents($tmp_dir.DIRECTORY_SEPARATOR.'foo'.DIRECTORY_SEPARATOR.'bar', 'ok');
file_put_contents($tmp_dir.DIRECTORY_SEPARATOR.'foo'.DIRECTORY_SEPARATOR.'foo', 'ok');
file_put_contents($tmp_dir.DIRECTORY_SEPARATOR.'bar'.DIRECTORY_SEPARATOR.'bar', 'ok');
sfToolkit::clearGlob($tmp_dir.'/*/bar');
$t->ok(!is_file($tmp_dir.DIRECTORY_SEPARATOR.'foo'.DIRECTORY_SEPARATOR.'bar'), '::clearGlob() removes all files and directories matching the pattern parameter');
$t->ok(!is_file($tmp_dir.DIRECTORY_SEPARATOR.'foo'.DIRECTORY_SEPARATOR.'bar'), '::clearGlob() removes all files and directories matching the pattern parameter');
$t->ok(is_file($tmp_dir.DIRECTORY_SEPARATOR.'foo'.DIRECTORY_SEPARATOR.'foo'), '::clearGlob() removes all files and directories matching the pattern parameter');
sfToolkit::clearDirectory($tmp_dir);
rmdir($tmp_dir);

// ::arrayDeepMerge()
$t->diag('::arrayDeepMerge()');
$t->is(
  sfToolkit::arrayDeepMerge(array('d' => 'due', 't' => 'tre'), array('d' => 'bis', 'q' => 'quattro')),
  array('d' => 'bis', 't' => 'tre', 'q' => 'quattro'),
  '::arrayDeepMerge() merges linear arrays preserving literal keys'
);
$t->is(
  sfToolkit::arrayDeepMerge(array('d' => 'due', 't' => 'tre', 'c' => array('c' => 'cinco')), array('d' => array('due', 'bis'), 'q' => 'quattro', 'c' => array('c' => 'cinque', 'c2' => 'cinco'))),
  array('d' => array('due', 'bis'), 't' => 'tre', 'q' => 'quattro', 'c' => array('c' => 'cinque', 'c2' => 'cinco')),
  '::arrayDeepMerge() recursively merges arrays preserving literal keys'
);
$t->is(
  sfToolkit::arrayDeepMerge(array(2 => 'due', 3 => 'tre'), array(2 => 'bis', 4 => 'quattro')),
  array(2 => 'bis', 3 => 'tre', 4 => 'quattro'),
  '::arrayDeepMerge() merges linear arrays preserving numerical keys'
);
$t->is(
  sfToolkit::arrayDeepMerge(array(2 => array('due'), 3 => 'tre'), array(2 => array('bis', 'bes'), 4 => 'quattro')),
  array(2 => array('bis', 'bes'), 3 => 'tre', 4 => 'quattro'),
  '::arrayDeepMerge() recursively merges arrays preserving numerical keys'
);

$arr = array(
  'foobar' => 'foo',
  'foo' => array(
    'bar' => array(
      'baz' => 'foo bar',
    ),
  ),
  'bar' => array(
    'foo',
    'bar',
  ),
  'simple' => 'string',
);

// ::hasArrayValueForPath()
$t->diag('::hasArrayValueForPath()');

$t->is(sfToolkit::hasArrayValueForPath($arr, 'foobar'), true, '::hasArrayValueForPath() returns true if the path exists');
$t->is(sfToolkit::hasArrayValueForPath($arr, 'barfoo'), false, '::hasArrayValueForPath() returns false if the path does not exist');

$t->is(sfToolkit::hasArrayValueForPath($arr, 'foo[bar][baz]'), true, '::hasArrayValueForPath() works with deep paths');
$t->is(sfToolkit::hasArrayValueForPath($arr, 'foo[bar][bar]'), false, '::hasArrayValueForPath() works with deep paths');

$t->is(sfToolkit::hasArrayValueForPath($arr, 'foo[]'), true, '::hasArrayValueForPath() accepts a [] at the end to check for an array');
$t->is(sfToolkit::hasArrayValueForPath($arr, 'foobar[]'), false, '::hasArrayValueForPath() accepts a [] at the end to check for an array');
$t->is(sfToolkit::hasArrayValueForPath($arr, 'barfoo[]'), false, '::hasArrayValueForPath() accepts a [] at the end to check for an array');

$t->is(sfToolkit::hasArrayValueForPath($arr, 'bar[1]'), true, '::hasArrayValueForPath() can take an array indexed by integer');
$t->is(sfToolkit::hasArrayValueForPath($arr, 'bar[2]'), false, '::hasArrayValueForPath() can take an array indexed by integer');

$t->is(sfToolkit::hasArrayValueForPath($arr, 'foo[bar][baz][booze]'), false, '::hasArrayValueForPath() is not fooled by php mistaking strings and array');

// ::getArrayValueForPath()
$t->diag('::getArrayValueForPath()');

$t->is(sfToolkit::getArrayValueForPath($arr, 'foobar'), 'foo', '::getArrayValueForPath() returns the value of the path if it exists');
$t->is(sfToolkit::getArrayValueForPath($arr, 'barfoo'), null, '::getArrayValueForPath() returns null if the path does not exist');
$t->is(sfToolkit::getArrayValueForPath($arr, 'barfoo', 'bar'), 'bar', '::getArrayValueForPath() takes a default value as its third argument');

$t->is(sfToolkit::getArrayValueForPath($arr, 'foo[bar][baz]'), 'foo bar', '::getArrayValueForPath() works with deep paths');
$t->is(sfToolkit::getArrayValueForPath($arr, 'foo[bar][bar]'), false, '::getArrayValueForPath() works with deep paths');
$t->is(sfToolkit::getArrayValueForPath($arr, 'foo[bar][bar]', 'bar'), 'bar', '::getArrayValueForPath() works with deep paths');

$t->is(sfToolkit::getArrayValueForPath($arr, 'foo[]'), array('bar' => array('baz' => 'foo bar')), '::getArrayValueForPath() accepts a [] at the end to check for an array');
$t->is(sfToolkit::getArrayValueForPath($arr, 'foobar[]'), null, '::getArrayValueForPath() accepts a [] at the end to check for an array');
$t->is(sfToolkit::getArrayValueForPath($arr, 'barfoo[]'), null, '::getArrayValueForPath() accepts a [] at the end to check for an array');
$t->is(sfToolkit::getArrayValueForPath($arr, 'foobar[]', 'foo'), 'foo', '::getArrayValueForPath() accepts a [] at the end to check for an array');

$t->is(sfToolkit::getArrayValueForPath($arr, 'bar[1]'), 'bar', '::getArrayValueForPath() can take an array indexed by integer');
$t->is(sfToolkit::getArrayValueForPath($arr, 'bar[2]'), null, '::getArrayValueForPath() can take an array indexed by integer');
$t->is(sfToolkit::getArrayValueForPath($arr, 'bar[2]', 'foo'), 'foo', '::getArrayValueForPath() can take an array indexed by integer');

$t->is(sfToolkit::getArrayValueForPath($arr, 'foo[bar][baz][booze]'), null, '::getArrayValueForPath() is not fooled by php mistaking strings and array');
$t->is(sfToolkit::getArrayValueForPathByRef($arr, 'foo[bar][baz][booze]'), null, '::getArrayValueForPathByRef() is not fooled by php mistaking strings and array');

// ::removeArrayValueForPath()
$t->diag('::removeArrayValueForPath()');
$t->is(sfToolkit::removeArrayValueForPath($arr, 'foobar'), 'foo', '::removeArrayValueForPath() returns the removed value');
$t->is($arr, array(
  'foo' => array(
    'bar' => array(
      'baz' => 'foo bar',
    ),
  ),
  'bar' => array(
    'foo',
    'bar',
  ),
  'simple' => 'string',
), '::removeArrayValueForPath() removes a key');
$t->is(sfToolkit::removeArrayValueForPath($arr, 'barfoo'), null, '::removeArrayValueForPath() returns null if the key does not exist');
$t->is(sfToolkit::removeArrayValueForPath($arr, 'barfoo', 'bar'), 'bar', '::removeArrayValueForPath() takes the default value as a third argument');
$t->is(sfToolkit::removeArrayValueForPath($arr, 'foo[bar][baz][booze]'), null, '::removeArrayValueForPath() is not fooled by php mistaking strings and array');
$t->is(sfToolkit::removeArrayValueForPath($arr, 'foo[simple][bad]'), null, '::removeArrayValueForPath() is not fooled by php mistaking strings and array');

$t->is(sfToolkit::removeArrayValueForPath($arr, 'foo[bar][baz]'), 'foo bar', '::removeArrayValueForPath() works with deep paths');
$t->is($arr, array(
  'foo' => array(
    'bar' => array(
    ),
  ),
  'bar' => array(
    'foo',
    'bar',
  ),
  'simple' => 'string',
), '::removeArrayValueForPath() works with deep paths');

// ::addIncludePath()
$t->diag('::addIncludePath()');
$path = get_include_path();
$t->is(sfToolkit::addIncludePath(dirname(__FILE__)), $path, '::addIncludePath() returns the previous include_path');
$t->is(get_include_path(), dirname(__FILE__).PATH_SEPARATOR.$path, '::addIncludePath() adds a path to the front of include_path');

sfToolkit::addIncludePath(dirname(__FILE__), 'back');
$t->is(get_include_path(), $path.PATH_SEPARATOR.dirname(__FILE__), '::addIncludePath() moves a path to the end of include_path');

sfToolkit::addIncludePath(array(
  dirname(__FILE__),
  dirname(__FILE__).'/..',
));
$t->is(get_include_path(), dirname(__FILE__).PATH_SEPARATOR.dirname(__FILE__).'/..'.PATH_SEPARATOR.$path, '::addIncludePath() adds multiple paths the the front of include_path');

sfToolkit::addIncludePath(array(
  dirname(__FILE__),
  dirname(__FILE__).'/..',
), 'back');
$t->is(get_include_path(), $path.PATH_SEPARATOR.dirname(__FILE__).PATH_SEPARATOR.dirname(__FILE__).'/..', '::addIncludePath() adds multiple paths the the back of include_path');

try
{
  sfToolkit::addIncludePath(dirname(__FILE__), 'foobar');
  $t->fail('::addIncludePath() throws an exception if position is not valid');
}
catch (Exception $e)
{
  $t->pass('::addIncludePath() throws an exception if position is not valid');
}

