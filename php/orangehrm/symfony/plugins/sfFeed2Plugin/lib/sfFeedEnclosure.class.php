<?php

/*
 * This file is part of the sfFeed2 package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfFeedEnclosure.
 *
 * @package    sfFeed2
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class sfFeedEnclosure
{
  private
    $url,
    $length,
    $mimeType;

  /**
   * Defines the feed enclosure properties, based on an associative array.
   *
   * @param array an associative array of feed parameters
   *
   * @return sfFeedEnclosure the current sfFeedEnclosure object
   */
  public function initialize($feed_array)
  {
    $this->setUrl(isset($feed_array['url']) ? $feed_array['url'] : '');
    $this->setLength(isset($feed_array['length']) ? $feed_array['length'] : '');
    $this->setMimeType(isset($feed_array['mimeType']) ? $feed_array['mimeType'] : '');

    return $this;
  }

  public function __toString()
  {
    return sprintf('url=%s length=%s mimeType=%s', $this->getUrl(), $this->getLength(), $this->getMimeType());
  }

  public function setUrl ($url)
  {
    $this->url = $url;
  }

  public function getUrl ()
  {
    return $this->url;
  }

  public function setLength ($length)
  {
    $this->length = $length;
  }

  public function getLength ()
  {
    return $this->length;
  }

  public function setMimeType ($mimeType)
  {
    $this->mimeType = $mimeType;
  }

  public function getMimeType ()
  {
    return $this->mimeType;
  }
}
