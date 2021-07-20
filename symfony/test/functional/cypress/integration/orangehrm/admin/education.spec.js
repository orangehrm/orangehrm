import user from '../../../fixtures/admin.json'
import charLength from '../../../fixtures/charLength.json'
import promisify from 'cypress-promise'

describe('Add education and Field validations testing', function () {
    //Add Education record to the list and toast message 
    it('Add Education record to the list and toast message', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewEducation')
        cy.get('.oxd-button').click()
        cy.get(':nth-child(2) > .oxd-input').type("Degree")
        cy.get('.oxd-button--secondary').click()
        cy.get('.oxd-toast').should('include.text', 'Successfully Saved')
    })
    //Validation check for the duplicated records
    it('Duplicate Records Validation', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewEducation')
        cy.get('.oxd-button--medium').should('include.text', "Add").click()
        cy.get('.oxd-input-group').type("Degree")
        cy.get('.oxd-button--secondary').click()
        cy.get('.oxd-input-group__message').should('include.text', 'Already exist')
        cy.get('.oxd-button--secondary').click()
    })
    //Validate required fields in Save Education Screen
    it('Required field validation', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewEducation')
        cy.get('.oxd-button--medium').should('include.text', "Add").click()
        cy.get('.oxd-button--secondary').click()
        cy.get('.oxd-input-group__message').should('include.text', 'Required')
    })
    //Validate maximum character length of the fields in Save Education screen
    it('Maximum Length validation', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewEducation')
        cy.get('.oxd-button--medium').should('include.text', "Add").click()
        cy.get('.oxd-input-group').type(charLength.chars100.text)
        cy.get('.oxd-input-group__message').should('include.text', 'Should not exceed 100 characters')
        cy.get('.oxd-button--secondary').click()
    })
    //Get the current education list count
    const getEducationListCount = async () => {
        cy.wait(2000)
        let txt = await promisify(cy.get('.oxd-text').contains('Found').invoke('text'))
        var line = txt.match(/\((.*)\)/);
        return parseInt(line[1])
    }
    //Check whether count of the list has increased by 1 after addding a new record to the list
    it('Education list increment', async () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewEducation')
        const previousCount = await getEducationListCount()
        cy.get('.oxd-text--span').should('include.text', "Found")
        cy.get('.oxd-button--medium').should('include.text', "Add").click()
        cy.get('.oxd-input-group').type("Certificate Level")
        cy.get('.oxd-button--secondary').click()
        cy.wait(3000)
        const updatedCount = await getEducationListCount()
        expect(updatedCount).to.eq(previousCount + 1)
    })
})

describe('Update education testing', function () {
    //Update an existing education record from the list and toast message
    it('Edit education and toast message', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewEducation')
        cy.get(':nth-child(1) > .oxd-table-row > .card-center > .card-header-slot > .--right > .oxd-table-cell > .oxd-table-cell-actions > :nth-child(2)').click()
        cy.get(':nth-child(2) > .oxd-input').click().clear().type("Advanced Level")
        cy.get('.oxd-button--secondary').click()
        cy.get('.oxd-toast').should('include.text', 'Successfully Updated')
    })
})

describe('Cancel button testing', function () {
    //Visiting edit education and clicking cancel
    it('Visiting edit education and clicking cancel', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewEducation')
        cy.get(':nth-child(1) > .oxd-table-row > .card-center > .card-header-slot > .--right > .oxd-table-cell > .oxd-table-cell-actions > :nth-child(2) > .oxd-icon').click()
        cy.get('.oxd-button--ghost').click()
        cy.get('.oxd-text').should('include.text', 'Education')
    })
    //Visiting add new education and clicking cancel
    it('Visiting add new education and clicking cancel', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewEducation')
        cy.get('.oxd-button').click()
        cy.get('.oxd-button--ghost').click()
        cy.get('.oxd-text').should('include.text', 'Education')
    })
})

describe('Delete education testing', function () {
    const getEducationListCount = async () => {
        cy.wait(2000)
        let txt = await promisify(cy.get('.oxd-text').contains('Found').invoke('text'))
        var line = txt.match(/\((.*)\)/);
        return parseInt(line[1])
    }
    //Delete an Education record from the list and toast message
    it('Delete education and toast message', async () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewEducation')
        const previousCount = await getEducationListCount()
        cy.get('.bi-trash').first().click()
        cy.get('.oxd-button--label-danger').click()
        cy.get('.oxd-toast').should('include.text', 'Successfully Deleted')
        cy.wait(3000)
        const updatedCount = await getEducationListCount()
        expect(updatedCount).to.eq(previousCount - 1)
    })
    //Bulk Delete records from the list
    it('Bulk Delete education', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewEducation')
        cy.viewport(1024, 768)
        cy.get('.oxd-table-header > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon').click()
        cy.get('.oxd-button--label-danger').should('include.text', 'Selected').click()
        cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click()
        cy.get('.oxd-toast').should('include.text', 'Successfully Deleted')
    })
    //No records found text validation
    it('No records found text validation', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewEducation')
        cy.get('.oxd-text').should('include.text', 'No Records Found')
    })
})

describe('UI testing', function () {
    //View Education List Page
    it('Check education view page', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewEducation')
        cy.get('.oxd-text--h6').should('include.text', "Education")
    })
    //Verify header and field name
    it('Verify header', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewEducation')
        cy.get('.orangehrm-header-container > .oxd-text').should('include.text', 'Education')
    })
    it('Verify field name', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/saveEducation')
        cy.get('.oxd-label').should('include.text', 'Level')
    })
})
