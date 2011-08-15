<?php



class DependentsAddView extends AddView
{
    public function  __construct($selenium) {
        
        $editPane=new DependentsEditPane($selenium);
        $list = new TitledList($selenium, "//table[@id='dependent_list']", False);
        parent::__construct($selenium, $list, $editPane);

    }
/**
 *
 * @param <type> $depName
 * @param <type> $depLabel
 * @param <type> $depDateOfBirth
 * @return DependentsListView
 */
    public function saveDetails($depName,$depLabel,$depDateOfBirth){
       return $this->editPane->saveDetails($depName, $depLabel, $depDateOfBirth);
    }

}
?>
