class UserListPageClass{

    fillUserName(value){
        cy.get(':nth-child(2) > .oxd-input').type(value)
    }
    selectUserRoles(value){
        cy.get(':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text').click()
        cy.get('.oxd-select-dropdown').contains(value).click()
    }
    selectEmployeeStatus(value){
        cy.get(':nth-child(4) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text').click()
        cy.get('.oxd-select-dropdown').contains(value).click()
    }
    selectElementfromMultiSelect(value){
        cy.get('.oxd-autocomplete-text-input > input').type(value)
        cy.wait(3000)
        cy.get('.oxd-autocomplete-text-input > input').first().click()
    }
    clickSearchButton(){
        cy.get('.oxd-form-actions > .oxd-button--secondary').click()
    }
    returnTableRowCount(){
        return cy.get('.oxd-table-body .oxd-table-row ')
    }
}

export default UserListPageClass