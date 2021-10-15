class ComponenetsActions{

    sendkeys(elementLocator,value){
        cy.get(elementLocator).clear()
        if (value != 'null') {
            cy.get(elementLocator).type(value)
        }
    }
    selectElementfromDropdown(elementLocator,value){
        cy.get(elementLocator).click()
        cy.get('.oxd-select-dropdown').contains(value).click()
    }
    selectElementfromMultiSelect(elementLocator,value){
        cy.get(elementLocator).type(value)
        cy.wait(3000)
        cy.get('.oxd-autocomplete-option').first().click()
    }
    clickOnButton(elementLocator){
        cy.get(elementLocator).click()
    }
    returnTableRowCount(elementLocator){
        return cy.get(elementLocator)
    }
    waituntillTableContainerLoad(){
        cy.get('.orangehrm-container',{timeout: 100000}).should('be.visible')
    }
}


export default ComponenetsActions