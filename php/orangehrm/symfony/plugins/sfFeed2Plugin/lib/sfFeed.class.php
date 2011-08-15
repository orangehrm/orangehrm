<?php

/*
 * This file is part of the sfFeed2 package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004-2007 Francois Zaninotto <francois.zaninotto@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfFeed.
 *
 * based on feedgenerator.py from django project
 *
 * @package    sfFeed2
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Francois Zaninotto <francois.zaninotto@symfony-project.com>
 */
class sfFeed
{
  protected
    $items = array(),
    $image,
    $title,
    $link,
    $description,
    $language = 'en',
    $authorEmail,
    $authorName,
    $authorLink,
    $subtitle,
    $categories = array(),
    $feedUrl,
    $encoding = 'UTF-8';

  public function construct($feed_array = array())
  {
    if($feed_array)
    {
      $this->initialize($feed_array);
    }
  }

  /**
   * Defines the feed properties, based on an associative array.
   *
   * @param array an associative array of feed parameters
   *
   * @return sfFeed the current sfFeed object
   */
  public function initialize($feed_array)
  {
    $this->setItems(isset($feed_array['items']) ? $feed_array['items'] : '');
    $this->setImage(isset($feed_array['image']) ? $feed_array['image'] : '');
    $this->setTitle(isset($feed_array['title']) ? $feed_array['title'] : '');
    $this->setLink(isset($feed_array['link']) ? $feed_array['link'] : '');
    $this->setDescription(isset($feed_array['description']) ? $feed_array['description'] : '');
    $this->setLanguage(isset($feed_array['language']) ? $feed_array['language'] : $this->language);
    $this->setAuthorEmail(isset($feed_array['authorEmail']) ? $feed_array['authorEmail'] : '');
    $this->setAuthorName(isset($feed_array['authorName']) ? $feed_array['authorName'] : '');
    $this->setAuthorLink(isset($feed_array['authorLink']) ? $feed_array['authorLink'] : '');
    $this->setSubtitle(isset($feed_array['subtitle']) ? $feed_array['subtitle'] : '');
    $this->setCategories(isset($feed_array['categories']) ? $feed_array['categories'] : '');
    $this->setFeedUrl(isset($feed_array['feedUrl']) ? $feed_array['feedUrl'] : '');
    $this->setEncoding(isset($feed_array['encoding']) ? $feed_array['encoding'] : $this->encoding);

    return $this;
  }

  /**
   * Retrieves the feed items.
   *
   * @return array an array of sfFeedItem objects
   */
  public function getItems()
  {
    return $this->items;
  }

  /**
   * Defines the items of the feed.
   *
   * Caution: in previous versions, this method used to accept all kinds of objects.
   * Now only objects of class sfFeedItem are allowed.
   *
   * @param array an array of sfFeedItem objects
   *
   * @return sfFeed the current sfFeed object
   */
  public function setItems($items = array())
  {
    $this->items = array();
    $this->addItems($items);

    return $this;
  }

  /**
   * Adds one item to the feed.
   *
   * @param sfFeedItem an item object
   *
   * @return sfFeed the current sfFeed object
   */
  public function addItem($item)
  {
    if (!($item instanceof sfFeedItem))
    {
      // the object is of the wrong class
      $error = 'Parameter of addItem() is not of class sfFeedItem';

      throw new Exception($error);
    }
    $item->setFeed($this);
    $this->items[] = $item;

    return $this;
  }

  /**
   * Adds several items to the feed.
   *
   * @param array an array of sfFeedItem objects
   *
   * @return sfFeed the current sfFeed object
   */
  public function addItems($items)
  {
    if(is_array($items))
    {
      foreach($items as $item)
      {
        $this->addItem($item);
      }
    }

    return $this;
  }

  /**
   * Adds one item to the feed, based on an associative array.
   *
   * @param array an associative array
   *
   * @return sfFeed the current sfFeed object
   */
  public function addItemFromArray($item_array)
  {
    $this->items[] = new sfFeedItem($item_array);

    return $this;
  }

   /**
   * Removes the last items of the feed so that the total number doesn't bypass the limit defined as a parameter.
   *
   * @param integer the maximum number of items
   *
   * @return sfFeed the current sfFeed object
   */
  public function keepOnlyItems($count = 10)
  {
    if($count < count($this->items))
    {
      $this->items = array_slice($this->items, 0, $count);
    }
    return $this;
  }

  /**
   * Retrieves the feed image
   *
   * @return sfFeedImage actual sfFeedImage object
   */
  public function getImage()
  {
    return $this->image;
  }

  /**
   * Defines the image/icon of the feed
   *
   * @param image sfFeedImage object
   *
   * @return sfFeed the current sfFeed object
   */
  public function setImage($image)
  {
    $this->image = $image;
    
    return $this;
  }

  public function setTitle ($title)
  {
    $this->title = $title;
    //if an image is there that has no title yet set it as well
    if ($this->image instanceof sfFeedImage && !$this->image->getTitle())
    {
      $this->image->setTitle($title);
    }
  }

  public function getTitle ()
  {
    return $this->title;
  }

  public function setLink ($link)
  {
    $this->link = $link;
    //if an image is there that has no link yet set it as well
    if ($this->image instanceof sfFeedImage && !$this->image->getLink())
    {
      $this->image->setLink($link);
    }
  }

  public function getLink ()
  {
    return $this->link;
  }

  public function setDescription ($description)
  {
    $this->description = $description;
  }

  public function getDescription ()
  {
    return $this->description;
  }

  public function setLanguage ($language)
  {
    $this->language = $language;
  }

  public function getLanguage ()
  {
    return $this->language;
  }

  public function setAuthorEmail ($authorEmail)
  {
    $this->authorEmail = $authorEmail;
  }

  public function getAuthorEmail ()
  {
    return $this->authorEmail;
  }

  public function setAuthorName ($authorName)
  {
    $this->authorName = $authorName;
  }

  public function getAuthorName ()
  {
    return $this->authorName;
  }

  public function setAuthorLink ($authorLink)
  {
    $this->authorLink = $authorLink;
  }

  public function getAuthorLink ()
  {
    return $this->authorLink;
  }

  public function setSubtitle ($subtitle)
  {
    $this->subtitle = $subtitle;
  }

  public function getSubtitle ()
  {
    return $this->subtitle;
  }

  public function setCategories ($categories)
  {
    $this->categories = $categories;
  }

  public function getCategories ()
  {
    return $this->categories;
  }

  public function setFeedUrl ($feedUrl)
  {
    $this->feedUrl = $feedUrl;
  }

  public function getFeedUrl ()
  {
    return $this->feedUrl;
  }

  public function getEncoding()
  {
    return $this->encoding;
  }

  public function setEncoding($encoding)
  {
    $this->encoding = $encoding;
  }

  public function getLatestPostDate()
  {
    $updates = array();
    foreach ($this->getItems() as $item)
    {
      if ($item->getPubdate())
      {
        $updates[] = $item->getPubdate();
      }
    }

    if ($updates)
    {
      sort($updates);

      return array_pop($updates);
    }
    else
    {

      return time();
    }
  }

  public function asXml()
  {
    throw new sfException('You must use newInstance to get a real feed.');
  }

}
