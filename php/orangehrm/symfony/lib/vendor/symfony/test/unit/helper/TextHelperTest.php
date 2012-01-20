<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../../test/bootstrap/unit.php');

require_once(dirname(__FILE__).'/../../../lib/helper/TagHelper.php');
require_once(dirname(__FILE__).'/../../../lib/helper/TextHelper.php');

$t = new lime_test(56);

// truncate_text()
$t->diag('truncate_text()');
$t->is(truncate_text(''), '', 'text_truncate() does nothing on an empty string');

$t->is(truncate_text('Test'), 'Test', 'text_truncate() truncates to 30 characters by default');

$text = str_repeat('A', 35);
$truncated = str_repeat('A', 27).'...';
$t->is(truncate_text($text), $truncated, 'text_truncate() adds ... to truncated text');

$text = str_repeat('A', 35);
$truncated = str_repeat('A', 22).'...';
$t->is(truncate_text($text, 25), $truncated, 'text_truncate() takes the max length as its second argument');

$text = str_repeat('A', 35);
$truncated = str_repeat('A', 21).'BBBB';
$t->is(truncate_text($text, 25, 'BBBB'), $truncated, 'text_truncate() takes the ... text as its third argument');

$text = str_repeat('A', 10).str_repeat(' ', 10).str_repeat('A', 10);
$truncated_true = str_repeat('A', 10).'...';
$truncated_false = str_repeat('A', 10).str_repeat(' ', 2).'...';
$t->is(truncate_text($text, 15, '...', false), $truncated_false, 'text_truncate() accepts a truncate lastspace boolean as its fourth argument');
$t->is(truncate_text($text, 15, '...', true), $truncated_true, 'text_truncate() accepts a truncate lastspace boolean as its fourth argument');

if(extension_loaded('mbstring'))
{
  $oldEncoding = mb_internal_encoding();
  $t->is(truncate_text('のビヘイビアにパラメーターを渡すことで特定のモデルでのフォーム生成を無効にできます', 11), 'のビヘイビアにパ...', 'text_truncate() handles unicode characters using mbstring if available');
  $t->is(mb_internal_encoding(), $oldEncoding, 'text_truncate() sets back the internal encoding in case it changes it');
}
else
{
  $t->skip('mbstring extension is not enabled', 2);
}

// highlight_text()
$t->diag('highlight_text()');
$t->is(highlight_text("This is a beautiful morning", "beautiful"),
  "This is a <strong class=\"highlight\">beautiful</strong> morning",
  'text_highlighter() highlights a word given as its second argument'
);

$t->is(highlight_text("This is a beautiful morning, but also a beautiful day", "beautiful"),
  "This is a <strong class=\"highlight\">beautiful</strong> morning, but also a <strong class=\"highlight\">beautiful</strong> day",
  'text_highlighter() highlights all occurrences of a word given as its second argument'
);

$t->is(highlight_text("This is a beautiful morning, but also a beautiful day", "beautiful", '<b>\\1</b>'),
  "This is a <b>beautiful</b> morning, but also a <b>beautiful</b> day",
  'text_highlighter() takes a pattern as its third argument'
);

$t->is(highlight_text('', 'beautiful'), '', 'text_highlighter() returns an empty string if input is empty');
$t->is(highlight_text('', ''), '', 'text_highlighter() returns an empty string if input is empty');
$t->is(highlight_text('foobar', 'beautiful'), 'foobar', 'text_highlighter() does nothing is string to highlight is not present');
$t->is(highlight_text('foobar', ''), 'foobar', 'text_highlighter() returns input if string to highlight is not present');

$t->is(highlight_text("This is a beautiful! morning", "beautiful!"), "This is a <strong class=\"highlight\">beautiful!</strong> morning", 'text_highlighter() escapes search string to be safe in a regex');
$t->is(highlight_text("This is a beautiful! morning", "beautiful! morning"), "This is a <strong class=\"highlight\">beautiful! morning</strong>", 'text_highlighter() escapes search string to be safe in a regex');
$t->is(highlight_text("This is a beautiful? morning", "beautiful? morning"), "This is a <strong class=\"highlight\">beautiful? morning</strong>", 'text_highlighter() escapes search string to be safe in a regex');

$t->is(highlight_text("The http://www.google.com/ website is great", "http://www.google.com/"), "The <strong class=\"highlight\">http://www.google.com/</strong> website is great", 'text_highlighter() escapes search string to be safe in a regex');

// excerpt_text()
$t->diag('excerpt_text()');
$t->is(excerpt_text('', 'foo', 5), '', 'text_excerpt() return an empty string if argument is empty');
$t->is(excerpt_text('foo', '', 5), '', 'text_excerpt() return an empty string if phrase is empty');
$t->is(excerpt_text("This is a beautiful morning", "beautiful", 5), "...is a beautiful morn...", 'text_excerpt() creates an excerpt of a text');
$t->is(excerpt_text("This is a beautiful morning", "this", 5), "This is a...", 'text_excerpt() creates an excerpt of a text');
$t->is(excerpt_text("This is a beautiful morning", "morning", 5), "...iful morning", 'text_excerpt() creates an excerpt of a text');
$t->is(excerpt_text("This is a beautiful morning", "morning", 5, '...', true), "... morning", 'text_excerpt() takes a fifth argument allowing excerpt on whitespace');
$t->is(excerpt_text("This is a beautiful morning", "beautiful", 5, '...', true), "... a beautiful ...", 'text_excerpt() takes a fifth argument allowing excerpt on whitespace');
$t->is(excerpt_text("This is a beautiful morning", "This", 5, '...', true), "This is ...", 'text_excerpt() takes a fifth argument allowing excerpt on whitespace');
$t->is(excerpt_text("This is a beautiful morning", "day"), '', 'text_excerpt() does nothing if the search string is not in input');

// wrap_text()
$t->diag('wrap_text()');
$line = 'This is a very long line to be wrapped...';
$t->is(wrap_text($line), "This is a very long line to be wrapped...\n", 'wrap_text() wraps long lines with a default of 80');
$t->is(wrap_text($line, 10), "This is a\nvery long\nline to be\nwrapped...\n", 'wrap_text() takes a line length as its second argument');
$t->is(wrap_text($line, 5), "This\nis a\nvery\nlong\nline\nto be\nwrapped...\n", 'wrap_text() takes a line length as its second argument');

// simple_format_text()
$t->diag('simple_format_text()');
$t->is(simple_format_text("crazy\r\n cross\r platform linebreaks"), "<p>crazy\n<br /> cross\n<br /> platform linebreaks</p>", 'text_simple_format() replaces \n by <br />');
$t->is(simple_format_text("A paragraph\n\nand another one!"), "<p>A paragraph</p><p>and another one!</p>", 'text_simple_format() replaces \n\n by <p>');
$t->is(simple_format_text("A paragraph\n\n\n\nand another one!"), "<p>A paragraph</p><p>and another one!</p>", 'text_simple_format() replaces \n\n\n\n by <p>');
$t->is(simple_format_text("A paragraph\n With a newline"), "<p>A paragraph\n<br /> With a newline</p>", 'text_simple_format() wrap all string with <p>');
$t->is(simple_format_text("1\n2\n3"), "<p>1\n<br />2\n<br />3</p>", 'text_simple_format() Ticket #6824');

// text_strip_links()
$t->diag('text_strip_links()');
$t->is(strip_links_text("<a href='almost'>on my mind</a>"), "on my mind", 'text_strip_links() strips all links in input');
$t->is(strip_links_text('<a href="first.html">first</a> and <a href="second.html">second</a>'), "first and second", 'text_strip_links() strips all links in input');

// auto_link_text()
$t->diag('auto_link_text()');
$email_raw = 'fabien.potencier@symfony-project.com';
$email_result = '<a href="mailto:'.$email_raw.'">'.$email_raw.'</a>';
$link_raw = 'http://www.google.com';
$link_result = '<a href="'.$link_raw.'">'.$link_raw.'</a>';
$link2_raw = 'www.google.com';
$link2_result = '<a href="http://'.$link2_raw.'">'.$link2_raw.'</a>';

$t->is(auto_link_text('hello '.$email_raw, 'email_addresses'), 'hello '.$email_result, 'auto_link_text() converts emails to links');
$t->is(auto_link_text('Go to '.$link_raw, 'urls'), 'Go to '.$link_result, 'auto_link_text() converts absolute URLs to links');
$t->is(auto_link_text('Go to '.$link_raw, 'email_addresses'), 'Go to '.$link_raw, 'auto_link_text() takes a second parameter');
$t->is(auto_link_text('Go to '.$link_raw.' and say hello to '.$email_raw), 'Go to '.$link_result.' and say hello to '.$email_result, 'auto_link_text() converts emails and URLs if no second argument is given');
$t->is(auto_link_text('<p>Link '.$link_raw.'</p>'), '<p>Link '.$link_result.'</p>', 'auto_link_text() converts URLs to links');
$t->is(auto_link_text('<p>'.$link_raw.' Link</p>'), '<p>'.$link_result.' Link</p>', 'auto_link_text() converts URLs to links');
$t->is(auto_link_text('Go to '.$link2_raw, 'urls'), 'Go to '.$link2_result, 'auto_link_text() converts URLs to links even if link does not start with http://');
$t->is(auto_link_text('Go to '.$link2_raw, 'email_addresses'), 'Go to '.$link2_raw, 'auto_link_text() converts URLs to links');
$t->is(auto_link_text('<p>Link '.$link2_raw.'</p>'), '<p>Link '.$link2_result.'</p>', 'auto_link_text() converts URLs to links');
$t->is(auto_link_text('<p>'.$link2_raw.' Link</p>'), '<p>'.$link2_result.' Link</p>', 'auto_link_text() converts URLs to links');
$t->is(auto_link_text('<p>http://www.google.com/?q=symfony Link</p>'), '<p><a href="http://www.google.com/?q=symfony">http://www.google.com/?q=symfony</a> Link</p>', 'auto_link_text() converts URLs to links');
$t->is(auto_link_text('<p>http://www.google.com/?q=symfony+link</p>', 'all', array(), true), '<p><a href="http://www.google.com/?q=symfony+link">http://www.google.com/?q=symfony+li...</a></p>', 'auto_link_text() truncates URLs in links');
$t->is(auto_link_text('<p>http://www.google.com/?q=symfony+link</p>', 'all', array(), true, 32, '***'), '<p><a href="http://www.google.com/?q=symfony+link">http://www.google.com/?q=symfony***</a></p>', 'auto_link_text() takes truncation parameters');
$t->is(auto_link_text('<p>http://twitter.com/#!/fabpot</p>'),'<p><a href="http://twitter.com/#!/fabpot">http://twitter.com/#!/fabpot</a></p>',"auto_link_text() converts URLs with complex fragments to links");
$t->is(auto_link_text('<p>http://twitter.com/#!/fabpot is Fabien Potencier on Twitter</p>'),'<p><a href="http://twitter.com/#!/fabpot">http://twitter.com/#!/fabpot</a> is Fabien Potencier on Twitter</p>',"auto_link_text() converts URLs with complex fragments and trailing text to links");
$t->is(auto_link_text('hello '.$email_result, 'email_addresses'), 'hello '.$email_result, "auto_link_text() does not double-link emails");
$t->is(auto_link_text('<p>Link '.$link_result.'</p>'), '<p>Link '.$link_result.'</p>', "auto_link_text() does not double-link emails");
