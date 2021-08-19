export class TerminationReasonPage {
    returnTblHeaders() {
        return cy.get('.oxd-table-header-cell')
    }
    returnTreminationReasonNameInputField() {
        return cy.get('.oxd-input-group .oxd-input')
    }
    returnTitleOfAddEditTerminationWizard() {
        return cy.get('.orangehrm-main-title')
    }
    returnTbleIndex(rowIndex) {
        return cy.get('.oxd-table-body .oxd-table-card').eq(rowIndex)
    }
    returnAllTbleRecords() {
        return cy.get('.oxd-table-card')
    }
    returnAllTerminationReasonNamesInTable(tablRow) {
        return cy.wrap(tablRow).find('.oxd-table-cell').eq(1)
    }
    returnSuccessToastHeader() {
        return cy.get('.oxd-text--toast-title')
    }
    returnSuccessToastMessage() {
        return cy.get('.oxd-text--toast-message')
    }
    returnFieldValidationElement() {
        return cy.get('.oxd-input-field-error-message')
    }
    returnDeleteConfirmationMessageHeader(){
        return cy.get('.oxd-text--card-title')
    }
    returnDeleteConfirmationMessage(){
        return cy.get('.orangehrm-text-center-align .oxd-text--p')
    }
    editSelectedItrmFromTheList(selectedItem) {
        cy.contains('.oxd-table-card', selectedItem)
            .then(tblRow => {
                cy.wrap(tblRow).find('.oxd-icon-button').eq(1).click()
            })
    }
    selectItemsFromTheLits(itemToSelect){
        cy.contains('.oxd-table-card', itemToSelect)
        .then(tblRow => {
            cy.wrap(tblRow).find('.oxd-checkbox-input-icon').click()
        })
    }
    clickDeleteButtonInParticularRow(selectedItem){
        cy.contains('.oxd-table-card', selectedItem)
        .then(tblRow => {
            cy.wrap(tblRow).find('.oxd-icon-button').eq(0).click()
            cy.get('.oxd-sheet', { timeout: 10000 }).should('be.visible')
        })
    }
    clickDeleteConfirmButtonInDeleteModal(){
        cy.contains('Yes, Delete').click()
    }
    deleteSelectedItrmFromTheList(selectedItem) {
        cy.contains('.oxd-table-card', selectedItem)
            .then(tblRow => {
                cy.wrap(tblRow).find('.oxd-icon-button').eq(0).click()
                cy.get('.oxd-sheet', { timeout: 10000 }).should('be.visible')
                cy.contains('Yes, Delete').click()
            })
    }
    deleteFirstElementOfList() {
        cy.get('.oxd-table-card').then(row => {
                cy.wrap(row).eq(0).find('.oxd-icon-button').eq(0).click()
                cy.get('.oxd-sheet', { timeout: 10000 }).should('be.visible')
                cy.contains('Yes, Delete').click()
            })
    }
    clickAddTerminationReasonButton() {
        cy.get('.oxd-button').click()
    }
    selectRecordToEdit(row) {
        cy.wrap(row).find('.oxd-icon-button').eq(1).click()
    }
    selectRecordToDelete(row) {
        cy.wrap(row).find('.oxd-icon-button').eq(0).click()
    }
    fillTerminationReasoname(reason) {
        cy.get('.oxd-input-group .oxd-input').clear()
        if (reason != 'null') {
            cy.get('.oxd-input-group .oxd-input').type(reason)
        }
    }
    clickSaveButton() {
        cy.get('.oxd-button--secondary').click()
    }
    clickCancelButton() {
        cy.get('.oxd-button--ghost').click()
    }
    clickDeleteSelectedItemFromTheListButton(){
        cy.get('.orangehrm-horizontal-padding .oxd-button--medium').click();
    }
}
export const terminationReasonsPage = new TerminationReasonPage();