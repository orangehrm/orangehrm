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
 *
 * Adds support for icons/images for atom and rss feeds in an unified extensible way.
 * Both (rss and atom) specs are quite limited, but this class should cope with possible enhancements.
 * Note that not everything is used. Atom at the moment only takes favicon (1:1 ratio) and
 * image (approx 2:1 ratio) image urls.
 * Rss only takes the image, but expects additionally the size of that image.
 *
 * @package    sfFeed2
 * @author     Fabian Lange <fabian.lange@web.de>
 */
class sfFeedImage
{
  private
   $favicon,
   $image,
   $faviconX,
   $faviconY,
   $imageX,
   $imageY,
   $title,
   $link,
   $feed;

  public function __construct($item_array = array())
  {
    if($item_array)
    {
      $this->initialize($item_array); 
    }
  }
  
  /**
   * Sets the feed image parameters, based on an associative array
   *
   * @param array an associative array
   *
   * @return sfFeedImage the current sfFeedImage object
   */
  public function initialize($item_array)
  {
    $this->setFavicon(isset($item_array['favicon']) ? $item_array['favicon'] : '');
    $this->setImage(isset($item_array['image']) ? $item_array['image'] : '');
    $this->setFaviconX(isset($item_array['faviconX']) ? $item_array['faviconX'] : '');
    $this->setFaviconY(isset($item_array['faviconY']) ? $item_array['faviconY'] : '');
    $this->setImageX(isset($item_array['imageX']) ? $item_array['imageX'] : '');
    $this->setImageY(isset($item_array['imageY']) ? $item_array['imageY'] : '');
    $this->setTitle(isset($item_array['title']) ? $item_array['title'] : '');
    $this->setLink(isset($item_array['link']) ? $item_array['link'] : '');
    $this->setFeed(isset($item_array['feed']) ? $item_array['feed'] : '');

    return $this;
  }

  public function setFavicon ($favicon)
  {
    $this->favicon = $favicon;
  }

  public function getFavicon ()
  {
    return $this->favicon;
  }

  public function setImage ($image)
  {
    $this->image = $image;
  }

  public function getImage ()
  {
    return $this->image;
  }

  public function setFaviconX ($faviconX)
  {
    $this->faviconX = $faviconX;
  }

  public function getFaviconX ()
  {
    return $this->faviconX;
  }

  public function setFaviconY ($faviconY)
  {
    $this->faviconY = $faviconY;
  }

  public function getFaviconY ()
  {
    return $this->faviconY;
  }

  public function setImageX ($imageX)
  {
    $this->imageX = $imageX;
  }

  public function getImageX ()
  {
    return $this->imageX;
  }

  public function setImageY ($imageY)
  {
    $this->imageY = $imageY;
  }

  public function getImageY ()
  {
    return $this->imageY;
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

  public function setFeed ($feed)
  {
    $this->feed = $feed;
  }

  public function getFeed ()
  {
    return $this->feed;
  }

}
