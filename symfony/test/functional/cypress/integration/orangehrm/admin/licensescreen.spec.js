import user from '../../../fixtures/admin.json'
import promisify from 'cypress-promise'

//check license page
describe('License page', function () {
    it('license page', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewLicenses')
        cy.get('.oxd-text--h6').should('include.text', "License")
    })
})

//Add a new license file and check the toast
describe('Add a new license file and check the toast', function() {
    it ('add a new license file and check the toast', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewLicenses')
        cy.get('.oxd-button').click()
        cy.get(':nth-child(2) > .oxd-input').type("ITIL")
        cy.get('.oxd-button--secondary').click()
        cy.get('.oxd-toast-container--bottom').should('include.text', "Successfully Saved")
    })
})

//Required field verification
describe('Required field verification', function() {
    it('required field verification', () =>{
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewLicenses')
        cy.get('.oxd-button').click()
        cy.get(':nth-child(2) > .oxd-input').type(' ')
        cy.get('.oxd-button--secondary').click()
        cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Required')
    })
})

//Maximum length validation
describe('Maximum length validation', function() {
    it('maximum length validation', () =>{
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewLicenses')
        cy.get('.oxd-button').click()
        cy.get(':nth-child(2) > .oxd-input').type('Licencefieldcharacterlimitvalidationwithmorethan100charactersLicencefieldcharacterlimitvalidationwithmorethan100characters')
        cy.get('.oxd-button--secondary').click()
        cy.get('.oxd-input-group > .oxd-text').should('include.text', 'Should not exceed 100 characters')
    })
})

//Adding duplicate license file
describe('Adding a duplicate license file', function() {
    it ('adding a duplicate license file', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewLicenses')
        cy.get('.oxd-button').click()
        cy.get(':nth-child(2) > .oxd-input').type("ITIL")
        cy.get('.oxd-input-group__message').should('include.text', "Already exist")
    })
})

//Update an exising license file and check the toast
describe('Update an existing license file and check the toast', function (){
    it('update an existing license file and check the toast', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewLicenses')
        cy.get(':nth-child(1) > .oxd-table-row > .card-center > .card-header-slot > .--right > .oxd-table-cell > .oxd-table-cell-actions > :nth-child(2)').click()
        cy.get(':nth-child(2) > .oxd-input').click().clear().type('IESL')
        cy.get('.oxd-button--secondary').click()
        cy.get('.oxd-toast-container--bottom').should('include.text', "Successfully Updated")
    })
})


//Visiting edit license and clicking cancel
describe('Visiting edit license and clicking cancel', function(){
    it('visiting edit license and clicking cancel', () =>{
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewLicenses')
        cy.get(':nth-child(1) > .oxd-table-row > .card-center > .card-header-slot > .--right > .oxd-table-cell > .oxd-table-cell-actions > :nth-child(2) > .oxd-icon').click()
        cy.get('.oxd-button--ghost').click()
        cy.get('.oxd-text').should('include.text', 'Licenses')
    })
})

//List count increment
const getLicensecount = async () => {
    cy.wait(2000)
    let num = await promisify(cy.get('.oxd-text').contains('Found').invoke('text'))
    var line = num.match(/\((.*)\)/);
    return parseInt(line[1])
}
describe('list count increment', function () {
    it('list count increment',async () =>{
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewLicenses')
        const currentLicenseno = await getLicensecount()
        cy.get('.oxd-button').click()
        cy.get(':nth-child(2) > .oxd-input').type('CCNP')
        cy.get('.oxd-button--secondary').click()
        cy.viewport(1024, 768)
        cy.wait(3000)
        const newLicenseno = await getLicensecount()
        expect(newLicenseno).to.eq(currentLicenseno + 1)
    })
})

//Deleting a license file and check the toast
describe('Deleting a license file and check the toast', function (){
    it('deleting a license file and check the toast', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewLicenses')
        cy.get(':nth-child(1) > .oxd-table-row > .card-center > .card-header-slot > .--right > .oxd-table-cell > .oxd-table-cell-actions > :nth-child(1)').click()
        cy.get('.oxd-button--label-danger').click()
        cy.get('.oxd-toast-container--bottom').should('include.text', "Successfully Deleted")
    })
})

//Bulk delete license files and check the toast
describe('Bulk delete license files and check the toast', function (){
    it('bulk delete license files and check the toast', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewLicenses')
        cy.viewport(1024, 768)
        cy.get('.oxd-table-header > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon').click()
        cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click()
        cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click()
        cy.get('.oxd-toast').should('include.text', 'Successfully Deleted')
    })
})

//No records found message
describe('No records found message', function(){
    it ('no records found message', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewLicenses')
        cy.get('.oxd-text').should('include.text', 'No Records Found') 
    })   
})

//Visiting add new license and clicking cancel
describe('Visiting add new license and clicking cancel', function() {
    it('visiting add new license and clicking cancel', () =>{
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewLicenses')
        cy.get('.oxd-button').click()
        cy.get('.oxd-button--ghost').click()
        cy.get('.oxd-text').should('include.text', 'Licenses')
    })
})

//Verify header and field name
describe('Verify header and field name', function (){
    it('verify header', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewLicenses')
        cy.get('.orangehrm-header-container > .oxd-text').should('include.text', 'License')
    })
    it('field name', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/saveLicense')
        cy.get('.oxd-label').should('include.text', 'Name')
    })
})
