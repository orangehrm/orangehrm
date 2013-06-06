<?php

class ohrmTreeViewComponentProperty {

    private $treeObject;
    private $rootLabel;
    private $deleteReistrictionLevels = array();
    private $addReistrictionLevels = array();

    public function getTreeObject() {
        return $this->treeObject;
    }

    public function setTreeObject($treeObject) {
        $this->treeObject = $treeObject;
    }

    public function getRootLabel() {
        return $this->rootLabel;
    }

    public function setRootLabel($rootLabel) {
        $this->rootLabel = $rootLabel;
    }

    public function getDeleteReistrictionLevels() {
        return $this->deleteReistrictionLevels;
    }

    public function setDeleteReistrictionLevels(array $levels) {
        $this->deleteReistrictionLevels = $levels;
    }

    public function getAddReistrictionLevels() {
        return $this->addReistrictionLevels;
    }

    public function setAddReistrictionLevels(array $levels) {
        $this->addReistrictionLevels = $levels;
    }

}

