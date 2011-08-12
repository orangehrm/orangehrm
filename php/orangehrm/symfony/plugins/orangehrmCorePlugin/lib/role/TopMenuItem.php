<?php

class TopMenuItem {

    private $displayName;
    private $link;

    /**
     * Get display name
     * @return displayName
     */
    public function getDisplayName() {

        return $this->displayName;
    }

    /**
     * Set display name
     * @param displayName
     * @return void
     */
    public function setDisplayName($displayName) {

        $this->displayName = $displayName;
    }

    /**
     * Get link
     * @return link
     */
    public function getLink() {

        return $this->link;
    }

    /**
     * Set link
     * @param link
     * @return void
     */
    public function setLink($link) {

        $this->link = $link;
    }

}
