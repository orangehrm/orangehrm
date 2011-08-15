<?php


class CommonListPane
{
    private $selenium = "";
    private $pimList="";

    function  __construct($selenium) {

      $this->pimList = new PIMList($selenium,$xpathOfList,"checkAll");
      $this->selenium=$selenium;
      $this->xpath=$xpathOfList;
    }
    //Get the heading of each sub page
    function getHeading()
    {
        return $this->selenium->getText("//form[@id='frmEmpDelEmgContacts']/div[1]/h2");
    }

    function verifyItemPresentAndClickOnIt($header,$itemName)
    {
        return $this->pimList->clickOntheItem($header, $itemName);
    }

    function clickOnAddButton($addButton)
    {
       $this->selenium->selectFrame("relative=up");
       return $this->selenium->click($addButton);
    }
//select one item and delete
/**
 *
 * @param <type> $deleteButton
 * @param <type> $header
 * @param <type> $itemName
 */
    public  function clickDelete($deleteButton,$header,$itemName)
    {
        $this->pimList->select($header,$itemName);
        $this->selenium->selectFrame("relative=up");
        $this->selenium->click($deleteButton);
    }

//select all and delete
/**
 *
 * @param <type> $deleteButton 
 */
    function selectAllAndDelete($deleteButton){
        $this->pimList->selectAllInTheList();
        $this->selenium->selectFrame("relative=up");
        $this->selenium->click($deleteButton);
    }
}
?>
