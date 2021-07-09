
import user from '../../../fixtures/admin.json'
import promisify from 'cypress-promise'

//check employment status page
describe('employment status page', function () {
    it('check employemnt status list page', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/employmentStatus')
        cy.get('.oxd-text--h6').should('include.text', "Employment Status")
    })
})

//Add a new employment status   
describe('add new employment status', function () {
    it('check add new employment status', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/saveEmploymentStatus')
        cy.get(':nth-child(2) > .oxd-input').type('Part Time')
        cy.get('form').submit()
    })
})

//Validate add employment status field by exceeding the character no
describe('validate add employment status field by exceeding the character no', function () {
    it('validate add employment status by exceeding the character no', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/saveEmploymentStatus')
        cy.get(':nth-child(2) > .oxd-input').type('validateaddingemploymentstatusfieldbyexceedingthemaximumcharacterlimit')
        cy.get('form').submit()
        cy.get('.oxd-input-group__message').should('include.text', 'Should not exceed 50 characters')
    })
})

//Validate add employment status required field
describe('validate add employment status required field', function () {
    it('validate add employment status required field', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/saveEmploymentStatus')
        cy.get(':nth-child(2) > .oxd-input').type(' ')
        cy.get('form').submit()
        cy.get('.oxd-input-group__message').should('include.text', 'Required')
    })
})

//Adding duplicate employment status
describe('adding a duplicate employment status', function () {
    it('adding a duplicate employment status', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/saveEmploymentStatus')
        cy.get(':nth-child(2) > .oxd-input').type('Part Time')
        cy.get('form').submit()
        cy.get('.oxd-input-group__message').should('include.text', 'Already exist')
    })
})

//Updating an employment status and the toast message 
describe('updating an employment status and the toast message', function () {
    it('updating an employment status and the toast message', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/employmentStatus')
        cy.get(':nth-child(1) > .oxd-table-row > .card-center > .card-header-slot > .--right > .oxd-table-cell > .oxd-table-cell-actions > :nth-child(2) > .oxd-icon').click()
        cy.get(':nth-child(2) > .oxd-input').click().clear().type('Contract')
        cy.get('form').submit()
        cy.get('.oxd-toast').should('include.text', 'Successfully Updated')
    })
})

//Delete employment status and the toast message
describe('delete an employment status and the toast message', function () {
    it('delete an employment status and the toast message', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/employmentStatus')
        cy.get(':nth-child(1) > .oxd-table-row > .card-center > .card-header-slot > .--right > .oxd-table-cell > .oxd-table-cell-actions > :nth-child(1)').click()
        cy.get('.oxd-button--label-danger').click()
        cy.get('.oxd-toast').should('include.text', 'Successfully Deleted')
    })
})

//Add a new employment status and check the success toast.
describe('add new employment status and check the success toast', function () {
    it('check add new employment status and check the success toast', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/saveEmploymentStatus')
        cy.get(':nth-child(2) > .oxd-input').type('Fulltime')
        cy.get('form').submit()
        cy.wait(2000)
        cy.get('.oxd-toast').should('include.text', "Successfully Saved")
    })
})

//Visiting update an employment status and click cancel 
describe('visiting update an employment status and click cancel', function () {
    it('visiting update an employment status and click cancel', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/employmentStatus')
        cy.get(':nth-child(1) > .oxd-table-row > .card-center > .card-header-slot > .--right > .oxd-table-cell > .oxd-table-cell-actions > :nth-child(2)').click()
        cy.get('.oxd-button--ghost').click()
    })
})

//Visiting add a new employment status and click cancel 
describe('visiting add a new employment status and click cancel', function () {
    it('visiting add a new employment status and click cancel', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/employmentStatus')
        cy.get('.oxd-button').click()
        cy.get('.oxd-button--ghost').click()
    })
})

describe('add new employment status and check the success toast', function () {
    it('check add new employment status and check the success toast', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/saveEmploymentStatus')
        cy.get(':nth-child(2) > .oxd-input').type('Fulltime2')
        cy.get('form').submit()
    })
})

//List count increment
const getStatuscount = async () => {
    cy.wait(2000)
    let num = await promisify(cy.get('.oxd-text').contains('Records Found').invoke('text'))
    var line = num.match(/\((.*)\)/);
    return parseInt(line[1])
}
describe('list count increment', function () {
    it('list count increment', async () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/employmentStatus')
        const currentStatusno = await getStatuscount()
        cy.log(currentStatusno)
        cy.visit('/admin/saveEmploymentStatus')
        cy.get(':nth-child(2) > .oxd-input').type('probation')
        cy.get('form').submit()
        cy.viewport(1024, 768)
        cy.wait(2000)
        const newStatusno = await getStatuscount()
        expect(newStatusno).to.eq(currentStatusno + 1)
    })
})

//Bulk delete and check the success toast.
describe('Bulk delete check the success toast', function () {
    it('Bulk delete check the success toast', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/employmentStatus')
        cy.viewport(1024, 768)
        cy.get('.oxd-table-header > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon').click()
        cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click()
        cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click()
        cy.get('.oxd-toast').should('include.text', 'Successfully Deleted')
    })
})

//Employment status list no results found
describe('Employment status list no results found', function () {
    it('check employment status list page', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/employmentStatus')
        cy.get('.oxd-text--span').should('include.text', "No Records Found")
    })
})

//Verify header and field name
describe('verify header and field name', function () {
    it('verify header', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/employmentStatus')
        cy.get('.orangehrm-header-container > .oxd-text').should('include.text', 'Employment Status')
    })
    it('field name', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/saveEmploymentStatus')
        cy.get('.oxd-label').should('include.text', 'Name')
    })
})
