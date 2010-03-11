<?php

/**
 * sfIMessageSource interface file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the BSD License.
 *
 * Copyright(c) 2004 by Qiang Xue. All rights reserved.
 *
 * To contact the author write to {@link mailto:qiang.xue@gmail.com Qiang Xue}
 * The latest version of PRADO can be obtained from:
 * {@link http://prado.sourceforge.net/}
 *
 * @author     Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @version    $Id: sfIMessageSource.class.php 9128 2008-05-21 00:58:19Z Carl.Vondrick $
 * @package    symfony
 * @subpackage i18n
 */
 
/**
 * sfIMessageSource interface.
 * 
 * All messages source used by MessageFormat must be of sfIMessageSource.
 * It defines a set of operations to add and retrieve messages from the
 * message source. In addition, message source can load a particular 
 * catalogue.
 *
 * @author Xiang Wei Zhuo <weizhuo[at]gmail[dot]com>
 * @version v1.0, last update on Fri Dec 24 17:40:19 EST 2004
 * @package    symfony
 * @subpackage i18n
 */
interface sfIMessageSource
{
  /**
   * Loads the translation table for this particular catalogue.
   * The translation should be loaded in the following order.
   *  # [1] call getCatalogueList($catalogue) to get a list of variants for for the specified $catalogue.
   *  # [2] for each of the variants, call getSource($variant) to get the resource, could be a file or catalogue ID.
   *  # [3] verify that this resource is valid by calling isValidSource($source)
   *  # [4] try to get the messages from the cache
   *  # [5] if a cache miss, call load($source) to load the message array
   *  # [6] store the messages to cache.
   *  # [7] continue with the foreach loop, e.g. goto [2].
   *
   * @param string $catalogue a catalogue to load
   * @return boolean true if loaded, false otherwise.
   */
  function load($catalogue = 'messages');

  /**
   * Gets the translation table. This includes all the loaded sections.
   * It must return a 2 level array of translation strings.
   * # "catalogue+variant" the catalogue and its variants.
   * # "source string" translation keys, and its translations.
   * <code>
   *   array('catalogue+variant' => 
   *       array('source string' => 'target string', ...)
   *             ...),
   *        ...);
   * </code>
   *
   * @return array 2 level array translation table.
   */
  function read();

  /**
   * Saves the list of untranslated blocks to the translation source. 
   * If the translation was not found, you should add those
   * strings to the translation source via the <b>append()</b> method.
   *
   * @param string $catalogue the catalogue to add to
   * @return boolean true if saved successfuly, false otherwise.
   */
  function save($catalogue = 'messages');

  /**
   * Adds a untranslated message to the source. Need to call save()
   * to save the messages to source.
   *
   * @param string $message message to add
   * @return void
   */
  function append($message);

  /**
   * Deletes a particular message from the specified catalogue.
   *
   * @param string $message   the source message to delete.
   * @param string $catalogue the catalogue to delete from.
   * @return boolean true if deleted, false otherwise. 
   */
  function delete($message, $catalogue = 'messages');

  /**
   * Updates the translation.
   *
   * @param string $text      the source string.
   * @param string $target    the new translation string.
   * @param string $comments  comments
   * @param string $catalogue the catalogue of the translation.
   * @return boolean true if translation was updated, false otherwise. 
   */
  function update($text, $target, $comments, $catalogue = 'messages');

  /**
   * Returns a list of catalogue as key and all it variants as value.
   *
   * @return array list of catalogues 
   */
  function catalogues();

  /**
   * Set the culture for this particular message source.
   *
   * @param string $culture the Culture name.
   */
  function setCulture($culture);

  /**
   * Get the culture identifier for the source.
   *
   * @return string culture identifier. 
   */
  function getCulture();

  /**
   * Returns a unique identifier for the current message source.
   */
  function getId();
}
