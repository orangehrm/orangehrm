<?php





class DependentsEditView extends EditView {

    public function __construct($selenium) {
        $editPane = new DependentsEditPane($selenium);
        $list = new TitledList($selenium, "//table[@id='dependent_list']", False);
        parent::__construct($selenium, $list, $editPane);

        }

    public function saveDetails($depName, $depLabel, $depDateOfBirth) {
        return $this->editPane->saveDetails($depName, $depLabel, $depDateOfBirth);
    }

 

}

?>
