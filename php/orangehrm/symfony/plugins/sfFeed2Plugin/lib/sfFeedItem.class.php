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
 * sfFeedItem.
 *
 * @package    sfFeed2
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class sfFeedItem
{
  private
   $title,
   $link,
   $description,
   $content,
   $authorEmail,
   $authorName,
   $authorLink,
   $pubdate,
   $comments,
   $uniqueId,
   $enclosure,
   $categories = array(),
   $feed;

  public function __construct($item_array = array())
  {
    if($item_array)
    {
      $this->initialize($item_array);
    }
  }

  /**
   * Sets the feed item parameters, based on an associative array.
   *
   * @param array an associative array
   *
   * @return sfFeedItem the current sfFeedItem object
   */
  public function initialize($item_array)
  {
    $this->setTitle(isset($item_array['title']) ? $item_array['title'] : '');
    $this->setLink(isset($item_array['link']) ? $item_array['link'] : '');
    $this->setDescription(isset($item_array['description']) ? $item_array['description'] : '');
    $this->setContent(isset($item_array['content']) ? $item_array['content'] : '');
    $this->setAuthorEmail(isset($item_array['authorEmail']) ? $item_array['authorEmail'] : '');
    $this->setAuthorName(isset($item_array['authorName']) ? $item_array['authorName'] : '');
    $this->setAuthorLink(isset($item_array['authorLink']) ? $item_array['authorLink'] : '');
    $this->setPubdate(isset($item_array['pubDate']) ? $item_array['pubDate'] : '');
    $this->setComments(isset($item_array['comments']) ? $item_array['comments'] : '');
    $this->setUniqueId(isset($item_array['uniqueId']) ? $item_array['uniqueId'] : '');
    $this->setEnclosure(isset($item_array['enclosure']) ? $item_array['enclosure'] : '');
    $this->setCategories(isset($item_array['categories']) ? $item_array['categories'] : '');
    $this->setFeed(isset($item_array['feed']) ? $item_array['feed'] : '');

    return $this;
  }

  public function setTitle ($title)
  {
    $this->title = $title;
  }

  public function getTitle ()
  {
    return $this->title;
  }

  public function setLink ($link)
  {
    $this->link = $link;
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
    if($this->description)
    {
      return $this->description;
    }
    else if($this->content)
    {
      $description = strip_tags($this->content);
      $description_max_length = sfConfig::get('app_feed_item_max_length', 100);
      if (strlen($description) > $description_max_length)
      {
        $description = substr($description, 0, $description_max_length - strlen($description)).'[...]';
      }
      return $description;
    }
  }

  public function setContent ($content)
  {
    $this->content = $content;
  }

  public function getContent ()
  {
    return $this->content;
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

  public function setPubdate ($pubdate)
  {
    $this->pubdate = $pubdate;
  }

  public function getPubdate ()
  {
    return $this->pubdate;
  }

  public function setComments ($comments)
  {
    $this->comments = $comments;
  }

  public function getComments ()
  {
    return $this->comments;
  }

  public function setUniqueId ($uniqueId)
  {
    $this->uniqueId = $uniqueId;
  }

  public function getUniqueId ()
  {
    return $this->uniqueId;
  }

  public function setEnclosure ($enclosure)
  {
    $this->enclosure = $enclosure;
  }

  public function getEnclosure ()
  {
    return $this->enclosure;
  }

  public function setCategories ($categories)
  {
    $this->categories = $categories;
  }

  public function getCategories ()
  {
    return $this->categories;
  }

  public function setFeed ($feed)
  {
    $this->feed = $feed;
  }

  public function getFeed ()
  {
    return $this->feed;
  }

}
