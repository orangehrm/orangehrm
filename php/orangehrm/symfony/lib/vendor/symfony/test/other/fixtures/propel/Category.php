<?php

class Category extends BaseCategory
{
  public function __toString()
  {
    return $this->name;
  }
}