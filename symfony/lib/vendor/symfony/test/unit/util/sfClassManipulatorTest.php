<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(18);

$source = <<<EOF
<?php

class Foo
{
  function foo()
  {
    if (true)
    {
      return;
    }
  }

  function baz()
  {
    if (true)
    {
      return;
    }
  }
}
EOF;

$sourceWithCodeBefore = <<<EOF
<?php

class Foo
{
  function foo()
  {
    // code before
    if (true)
    {
      return;
    }
  }

  function baz()
  {
    if (true)
    {
      return;
    }
  }
}
EOF;

$sourceWithCodeAfter = <<<EOF
<?php

class Foo
{
  function foo()
  {
    if (true)
    {
      return;
    }
    // code after
  }

  function baz()
  {
    if (true)
    {
      return;
    }
  }
}
EOF;

$sourceWithCodeBeforeAndAfter = <<<EOF
<?php

class Foo
{
  function foo()
  {
    // code before
    if (true)
    {
      return;
    }
    // code after
  }

  function baz()
  {
    if (true)
    {
      return;
    }
  }
}
EOF;

// ->wrapMethod()
$t->diag('->wrapMethod()');
$m = new sfClassManipulator($source);
$t->is(fix_linebreaks($m->wrapMethod('bar', '// code before', '// code after')), fix_linebreaks($source), '->wrapMethod() does nothing if the method does not exist.');
$m = new sfClassManipulator($source);
$t->is(fix_linebreaks($m->wrapMethod('foo', '// code before')), fix_linebreaks($sourceWithCodeBefore), '->wrapMethod() adds code before the beginning of a method.');
$m = new sfClassManipulator($source);
$t->is(fix_linebreaks($m->wrapMethod('foo', '', '// code after')), fix_linebreaks($sourceWithCodeAfter), '->wrapMethod() adds code after the end of a method.');
$t->is(fix_linebreaks($m->wrapMethod('foo', '// code before')), fix_linebreaks($sourceWithCodeBeforeAndAfter), '->wrapMethod() adds code to the previously manipulated code.');

// ->getCode()
$t->diag('->getCode()');
$m = new sfClassManipulator($source);
$t->is(fix_linebreaks($m->getCode()), fix_linebreaks($source), '->getCode() returns the source code when no manipulations has been done');
$m->wrapMethod('foo', '', '// code after');
$t->is(fix_linebreaks($m->getCode()), fix_linebreaks($sourceWithCodeAfter), '->getCode() returns the modified code');

// ->setFile() ->getFile()
$t->diag('->setFile() ->getFile()');
$m = new sfClassManipulator($source);
$m->setFile('foo');
$t->is($m->getFile(), 'foo', '->setFile() sets the name of the file associated with the source code');

// ::fromFile()
$t->diag('::fromFile()');
$file = sys_get_temp_dir().'/sf_tmp.php';
file_put_contents($file, $source);
$m = sfClassManipulator::fromFile($file);
$t->is($m->getFile(), $file, '::fromFile() sets the file internally');

// ->save()
$t->diag('->save()');
$m = sfClassManipulator::fromFile($file);
$m->wrapMethod('foo', '', '// code after');
$m->save();
$t->is(fix_linebreaks(file_get_contents($file)), fix_linebreaks($sourceWithCodeAfter), '->save() saves the modified code if a file is associated with the instance');

unlink($file);

// ->filterMethod()
$t->diag('->filterMethod()');

class MethodFilterer
{
  public $lines = array();

  public function filter1($line)
  {
    $this->lines[] = $line;
    return $line;
  }

  public function filter2($line)
  {
    return str_replace(array(
      'if (true)',
      'function foo()',
    ), array(
      'if (false)',
      'function foo($arg)',
    ), $line);
  }
}
$f = new MethodFilterer();

$sourceFiltered = <<<EOF
<?php

class Foo
{
  function foo(\$arg)
  {
    if (false)
    {
      return;
    }
  }

  function baz()
  {
    if (true)
    {
      return;
    }
  }
}
EOF;

$sourceCRLF = str_replace('
', "\r\n", $source);
$sourceFilteredCRLF = str_replace('
', "\r\n", $sourceFiltered);
$sourceLF = str_replace('
', "\n", $source);
$sourceFilteredLF = str_replace('
', "\n", $sourceFiltered);

// CRLF
$t->diag('CRLF');

$m = new sfClassManipulator($sourceCRLF);
$f->lines = array();
$m->filterMethod('foo', array($f, 'filter1'));
$t->is($m->getCode(), $sourceCRLF, '->filterMethod() does not change the code if the filter does nothing');
$t->is_deeply($f->lines, array(
  "  function foo()\r\n",
  "  {\r\n",
  "    if (true)\r\n",
  "    {\r\n",
  "      return;\r\n",
  "    }\r\n",
  "  }",
), '->filterMethod() filters each line of the method');
$m->filterMethod('foo', array($f, 'filter2'));
$t->is($m->getCode(), $sourceFilteredCRLF, '->filterMethod() modifies the method');

// LF
$t->diag('LF');

$m = new sfClassManipulator($sourceLF);
$f->lines = array();
$m->filterMethod('foo', array($f, 'filter1'));
$t->is($m->getCode(), $sourceLF, '->filterMethod() does not change the code if the filter does nothing');
$t->is_deeply($f->lines, array(
  "  function foo()\n",
  "  {\n",
  "    if (true)\n",
  "    {\n",
  "      return;\n",
  "    }\n",
  "  }",
), '->filterMethod() filters each line of the method');
$m->filterMethod('foo', array($f, 'filter2'));
$t->is($m->getCode(), $sourceFilteredLF, '->filterMethod() modifies the method');

// no EOL
$t->diag('no EOL');

$sourceFlat = '<?php class Foo { function foo() { if (true) { return; } } function baz() { if (true) { return; } } }';
$m = new sfClassManipulator($sourceFlat);
$f->lines = array();
$m->filterMethod('foo', array($f, 'filter1'));
$t->is_deeply($f->lines, array('function foo() { if (true) { return; } }'), '->filterMethod() works when there are no line breaks');
$t->is($m->getCode(), $sourceFlat, '->filterMethod() works when there are no line breaks');

// mixed EOL
$t->diag('mixed EOL');

$sourceMixed = "<?php\r\n\nclass Foo\r\n{\n  function foo()\r\n  {\n    if (true)\r\n    {\n      return;\r\n    }\n  }\r\n\n  function baz()\r\n  {\n    if (true)\r\n    {\n      return;\r\n    }\n  }\r\n}";
$m = new sfClassManipulator($sourceMixed);
$f->lines = array();
$m->filterMethod('foo', array($f, 'filter1'));
$t->is_deeply($f->lines, array(
  "  function foo()\r\n",
  "  {\n",
  "    if (true)\r\n",
  "    {\n",
  "      return;\r\n",
  "    }\n",
  "  }",
), '->filterMethod() filters each line of a mixed EOL-style method');
