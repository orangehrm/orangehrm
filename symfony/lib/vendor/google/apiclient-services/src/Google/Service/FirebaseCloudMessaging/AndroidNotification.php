<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

class Google_Service_FirebaseCloudMessaging_AndroidNotification extends Google_Collection
{
  protected $collection_key = 'titleLocArgs';
  public $body;
  public $bodyLocArgs;
  public $bodyLocKey;
  public $channelId;
  public $clickAction;
  public $color;
  public $icon;
  public $sound;
  public $tag;
  public $title;
  public $titleLocArgs;
  public $titleLocKey;

  public function setBody($body)
  {
    $this->body = $body;
  }
  public function getBody()
  {
    return $this->body;
  }
  public function setBodyLocArgs($bodyLocArgs)
  {
    $this->bodyLocArgs = $bodyLocArgs;
  }
  public function getBodyLocArgs()
  {
    return $this->bodyLocArgs;
  }
  public function setBodyLocKey($bodyLocKey)
  {
    $this->bodyLocKey = $bodyLocKey;
  }
  public function getBodyLocKey()
  {
    return $this->bodyLocKey;
  }
  public function setChannelId($channelId)
  {
    $this->channelId = $channelId;
  }
  public function getChannelId()
  {
    return $this->channelId;
  }
  public function setClickAction($clickAction)
  {
    $this->clickAction = $clickAction;
  }
  public function getClickAction()
  {
    return $this->clickAction;
  }
  public function setColor($color)
  {
    $this->color = $color;
  }
  public function getColor()
  {
    return $this->color;
  }
  public function setIcon($icon)
  {
    $this->icon = $icon;
  }
  public function getIcon()
  {
    return $this->icon;
  }
  public function setSound($sound)
  {
    $this->sound = $sound;
  }
  public function getSound()
  {
    return $this->sound;
  }
  public function setTag($tag)
  {
    $this->tag = $tag;
  }
  public function getTag()
  {
    return $this->tag;
  }
  public function setTitle($title)
  {
    $this->title = $title;
  }
  public function getTitle()
  {
    return $this->title;
  }
  public function setTitleLocArgs($titleLocArgs)
  {
    $this->titleLocArgs = $titleLocArgs;
  }
  public function getTitleLocArgs()
  {
    return $this->titleLocArgs;
  }
  public function setTitleLocKey($titleLocKey)
  {
    $this->titleLocKey = $titleLocKey;
  }
  public function getTitleLocKey()
  {
    return $this->titleLocKey;
  }
}
