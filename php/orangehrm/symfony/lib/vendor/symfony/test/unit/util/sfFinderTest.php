<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

class my_lime_test extends lime_test
{
  public function arrays_are_equal($a, $b, $message)
  {
    sort($a);
    sort($b);

    return $this->is($a, $b, $message);
  }
}
$t = new my_lime_test(37, new lime_output_color());

require_once($_test_dir.'/../lib/util/sfFinder.class.php');

$fixtureDir = dirname(__FILE__).'/fixtures/finder';
$phpFiles = array(
  'dir1/dir2/file21.php',
  'dir1/file12.php',
);
$txtFiles = array(
  'file2.txt',
);
$regexpFiles = array(
  'dir1/dir2/file21.php',
  'dir1/dir2/file22',
  'dir1/dir2/file23',
  'dir1/dir2/file24',
  'file2.txt'
);
$allFiles = array(
  'dir1/dir2/dir3/file31',
  'dir1/dir2/dir4/file41',
  'dir1/dir2/file21.php',
  'dir1/dir2/file22',
  'dir1/dir2/file23',
  'dir1/dir2/file24',
  'dir1/file11',
  'dir1/file12.php',
  'dir1/file13',
  'file1',
  'file2.txt',
);
$minDepth1Files = array(
  'dir1/dir2/dir3/file31',
  'dir1/dir2/dir4/file41',
  'dir1/dir2/file21.php',
  'dir1/dir2/file22',
  'dir1/dir2/file23',
  'dir1/dir2/file24',
  'dir1/file11',
  'dir1/file12.php',
  'dir1/file13',
);
$maxDepth2Files = array(
  'dir1/dir2/file21.php',
  'dir1/dir2/file22',
  'dir1/dir2/file23',
  'dir1/dir2/file24',
  'dir1/file11',
  'dir1/file12.php',
  'dir1/file13',
  'file1',
  'file2.txt',
);
$anyWithoutDir2 = array(
  'dir1',
  'dir1/dir2',
  'dir1/file11',
  'dir1/file12.php',
  'dir1/file13',
  'file1',
  'file2.txt',
);

// ::type()
$t->diag('::type()');
$finder = sfFinder::type('file');
$t->ok($finder instanceof sfFinder, '::type() returns a sfFinder instance');
$t->is($finder->get_type(), 'file', '::type() takes a file, dir, or any as its first argument');
$finder = sfFinder::type('dir');
$t->is($finder->get_type(), 'directory', '::type() takes a file, dir, or any as its first argument');
$finder = sfFinder::type('any');
$t->is($finder->get_type(), 'any', '::type() takes a file, dir, or any as its first argument');
$finder = sfFinder::type('somethingelse');
$t->is($finder->get_type(), 'file', '::type() takes a file, dir, or any as its first argument');

// ->setType() ->get_type()
$t->diag('->setType() ->get_type()');
$finder = sfFinder::type('file');
$finder->setType('dir');
$t->is($finder->get_type(), 'directory', '->getType() returns the type of searched files');
$t->is($finder->setType('file'), $finder, '->setType() implements a fluent interface');

// ->name()
$t->diag('->name()');
$finder = sfFinder::type('file');
$t->is($finder->name('*.php'), $finder, '->name() implements the fluent interface');

$t->diag('->name() file name support');
$finder = sfFinder::type('file')->name('file21.php')->relative();
$t->arrays_are_equal($finder->in($fixtureDir), array('dir1/dir2/file21.php'), '->name() can take a file name as an argument');

$t->diag('->name() globs support');
$finder = sfFinder::type('file')->name('*.php')->relative();
$t->arrays_are_equal($finder->in($fixtureDir), $phpFiles, '->name() can take a glob pattern as an argument');

$t->diag('->name() regexp support');
$finder = sfFinder::type('file')->name('/^file2.*$/')->relative();
$t->arrays_are_equal($finder->in($fixtureDir), $regexpFiles, '->name() can take a regexp as an argument');

$t->diag('->name() array / args / chaining');
$finder = sfFinder::type('file')->name(array('*.php', '*.txt'))->relative();
$t->arrays_are_equal($finder->in($fixtureDir), array_merge($phpFiles, $txtFiles), '->name() can take an array of patterns');
$finder = sfFinder::type('file')->name('*.php', '*.txt')->relative();
$t->arrays_are_equal($finder->in($fixtureDir), array_merge($phpFiles, $txtFiles), '->name() can take patterns as arguments');
$finder = sfFinder::type('file')->name('*.php')->name('*.txt')->relative();
$t->arrays_are_equal($finder->in($fixtureDir), array_merge($phpFiles, $txtFiles), '->name() can be called several times');

// ->not_name()
$t->diag('->not_name()');
$finder = sfFinder::type('file');
$t->is($finder->not_name('*.php'), $finder, '->not_name() implements the fluent interface');

$t->diag('->not_name() file name support');
$finder = sfFinder::type('file')->not_name('file21.php')->relative();
$t->arrays_are_equal($finder->in($fixtureDir), array_values(array_diff($allFiles, array('dir1/dir2/file21.php'))), '->not_name() can take a file name as an argument');

$t->diag('->not_name() globs support');
$finder = sfFinder::type('file')->not_name('*.php')->relative();
$t->arrays_are_equal($finder->in($fixtureDir), array_values(array_diff($allFiles, $phpFiles)), '->not_name() can take a glob pattern as an argument');

$t->diag('->not_name() regexp support');
$finder = sfFinder::type('file')->not_name('/^file2.*$/')->relative();
$t->arrays_are_equal($finder->in($fixtureDir), array_values(array_diff($allFiles, $regexpFiles)), '->not_name() can take a regexp as an argument');

$t->diag('->not_name() array / args / chaining');
$finder = sfFinder::type('file')->not_name(array('*.php', '*.txt'))->relative();
$t->arrays_are_equal($finder->in($fixtureDir), array_values(array_diff($allFiles, array_merge($phpFiles, $txtFiles))), '->not_name() can take an array of patterns');
$finder = sfFinder::type('file')->not_name('*.php', '*.txt')->relative();
$t->arrays_are_equal($finder->in($fixtureDir), array_values(array_diff($allFiles, array_merge($phpFiles, $txtFiles))), '->not_name() can take patterns as arguments');
$finder = sfFinder::type('file')->not_name('*.php')->not_name('*.txt')->relative();
$t->arrays_are_equal($finder->in($fixtureDir), array_values(array_diff($allFiles, array_merge($phpFiles, $txtFiles))), '->not_name() can be called several times');

$t->diag('->name() ->not_name() in the same query');
$finder = sfFinder::type('file')->not_name('/^file2.*$/')->name('*.php')->relative();
$t->arrays_are_equal($finder->in($fixtureDir), array('dir1/file12.php'), '->not_name() and ->name() can be called in the same query');

// ->size()
$t->diag('->size()');
$finder = sfFinder::type('file');
$t->is($finder->size('> 2K'), $finder, '->size() implements the fluent interface');

$finder = sfFinder::type('file')->size('> 100K')->relative();
$t->is($finder->in($fixtureDir), array(), '->size() takes a size comparison string as its argument');
$finder = sfFinder::type('file')->size('> 1K')->relative();
$t->is($finder->in($fixtureDir), array('file1'), '->size() takes a size comparison string as its argument');
$finder = sfFinder::type('file')->size('> 1K')->size('< 2K')->relative();
$t->is($finder->in($fixtureDir), array(), '->size() takes a size comparison string as its argument');

// ->mindepth() ->maxdepth()
$t->diag('->mindepth() ->maxdepth()');
$finder = sfFinder::type('file');
$t->is($finder->mindepth(1), $finder, '->mindepth() implements the fluent interface');
$t->is($finder->maxdepth(1), $finder, '->maxdepth() implements the fluent interface');

$finder = sfFinder::type('file')->relative()->mindepth(1);
$t->arrays_are_equal($finder->in($fixtureDir), $minDepth1Files, '->mindepth() takes a minimum depth as its argument');
$finder = sfFinder::type('file')->relative()->maxdepth(2);
$t->arrays_are_equal($finder->in($fixtureDir), $maxDepth2Files, '->maxdepth() takes a maximum depth as its argument');
$finder = sfFinder::type('file')->relative()->mindepth(1)->maxdepth(2);
$t->arrays_are_equal($finder->in($fixtureDir), array_values(array_intersect($minDepth1Files, $maxDepth2Files)), '->maxdepth() and ->mindepth() can be called in the same query');

// ->discard()
$t->diag('->discard()');
$t->is($finder->discard('file2.txt'), $finder, '->discard() implements the fluent interface');

$t->diag('->discard() file name support');
$finder = sfFinder::type('file')->relative()->discard('file2.txt');
$t->arrays_are_equal($finder->in($fixtureDir), array_values(array_diff($allFiles, array('file2.txt'))), '->discard() can discard a file name');

$t->diag('->discard() glob support');
$finder = sfFinder::type('file')->relative()->discard('*.php');
$t->arrays_are_equal($finder->in($fixtureDir), array_values(array_diff($allFiles, $phpFiles)), '->discard() can discard a glob pattern');

$t->diag('->discard() regexp support');
$finder = sfFinder::type('file')->relative()->discard('/^file2.*$/');
$t->arrays_are_equal($finder->in($fixtureDir), array_values(array_diff($allFiles, $regexpFiles)), '->discard() can discard a regexp pattern');

// ->prune()
$t->diag('->prune()');
$t->is($finder->prune('dir2'), $finder, '->prune() implements the fluent interface');

$finder = sfFinder::type('any')->relative()->prune('dir2');
$t->arrays_are_equal($finder->in($fixtureDir), $anyWithoutDir2, '->prune() ignore all files/directories under the given directory');
