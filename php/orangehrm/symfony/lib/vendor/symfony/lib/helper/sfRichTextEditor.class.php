<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfRichTextEditor is an abstract class for rich text editor classes.
 *
 * @package    symfony
 * @subpackage helper
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfRichTextEditor.class.php 9101 2008-05-20 08:38:20Z FabianLange $
 */
abstract class sfRichTextEditor
{
  protected
    $name = '',
    $content = '',
    $options = array();

  /**
   * Initializes this rich text editor.
   *
   * @param string $name     The tag name
   * @param string $content  The rich text editor content
   * @param array  $options  An array of options
   */
  public function initialize($name, $content, $options = array())
  {
    $this->name = $name;
    $this->content = $content;
    $this->options = $options;
  }

  /**
   * Returns the rich text editor as HTML.
   *
   * @return string Rich text editor HTML representation
   */
  abstract public function toHTML();
}
