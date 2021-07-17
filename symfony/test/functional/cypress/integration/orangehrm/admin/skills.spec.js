<<<<<<< e0e330c4cc79b7dc0e369215f90e5aad5e705f8a
import user from '../../../fixtures/admin-user.json'
=======
import user from '../../../fixtures/admin.json'
import charLength from '../../../fixtures/charLength.json'
import promisify from 'cypress-promise'
>>>>>>> OHRM5X-271 : Skills screen test scripts

//check skills page
describe('Skills page', function () {
    it('skills page', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewSkills')
        cy.get('.oxd-text--h6').should('include.text', "Skills")
    })
})

//Verify Add skill and toast message
describe('Add Skill and check toask message', function () {
    it('Add Skill and check toask message', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewSkills')
        cy.get('.oxd-button').click()
        cy.get(':nth-child(2) > .oxd-input').type("html")
        cy.get('.oxd-textarea').type("This is the description for html skill")
        cy.get('form').submit()
        cy.get('.oxd-toast').should('include.text', 'Successfully Saved')
    })
})

//Verify required field validation
describe('Required field validation in Skill', function () {
    it('required field validation in Skill', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/saveSkills')
        cy.get(':nth-child(2) > .oxd-input').click()
        cy.get('.oxd-button--secondary').click()
        cy.get('.oxd-input-group__message').should('include.text', 'Required')
    })
})

//Verify Maximum allowed charachters validation
describe('Maximum allowed charachters validation in Skill', function () {
    it('maximum allowed charachters validation in Skill', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/saveSkills')
        cy.get(':nth-child(2) > .oxd-input').type(charLength.chars120.text)
        cy.get('.oxd-textarea').type(charLength.chars400.text)
        cy.get('.oxd-input-group__message').should('include.text', 'Should not exceed 120 characters')
        cy.get('.oxd-input-group__message').should('include.text', 'Should not exceed 400 characters')
    })
})

//List count iccrement
const getSkillcount = async () => {
    cy.wait(3000)
    let num = await promisify(cy.get('.oxd-text').contains('Found').invoke('text'))
    var line = num.match(/\((.*)\)/);
    return parseInt(line[1])
}
describe('list count increment', function () {
    it('list count increment', async () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewSkills')
        const currentSkillno = await getSkillcount()
        cy.log(currentSkillno)
        cy.get('.oxd-button').click()
        cy.get(':nth-child(2) > .oxd-input').type('Springboot')
        cy.get('.oxd-button--secondary').click()
        cy.viewport(1024, 768)
        cy.wait(2000)
        const newSkillno = await getSkillcount()
        expect(newSkillno).to.eq(currentSkillno + 1)
    })
})

//Duplicate record validation
describe('Duplicate record validation', function () {
    it('duplicate record validation', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/saveSkills')
        cy.get(':nth-child(2) > .oxd-input').type("html")
        cy.get('.oxd-input-group__message').should('include.text', 'Already exist')
    })
})

//Update a skill and check toast message
describe('Update a skill and check toast message', function () {
    it('update a skill and check toast message', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewSkills')
        cy.get(':nth-child(1) > .oxd-table-row > .card-center > .card-header-slot > .--right > .oxd-table-cell > .oxd-table-cell-actions > :nth-child(2)').click()
        cy.get(':nth-child(2) > .oxd-input').click().clear().type("java")
        cy.get('form').submit()
        cy.get('.oxd-toast').should('include.text', 'Successfully Updated')
    })
})

//Visiting edit skill and clicking cancel
describe('Visiting edit skill and clicking cancel', function () {
    it('visiting edit skill and clicking cancel', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewSkills')
        cy.get(':nth-child(1) > .oxd-table-row > .card-center > .card-header-slot > .--right > .oxd-table-cell > .oxd-table-cell-actions > :nth-child(2)').click()
        cy.get('.oxd-button--ghost').click()
        cy.get('.oxd-text--h6').should('include.text', "Skills")
    })
})

//Deleting a skill and checking toast message
describe('Deleting a skill and checking toast message', function () {
    it('deleting a skill and checking toast message', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewSkills')
        cy.get(':nth-child(1) > .oxd-table-row > .card-center > .card-header-slot > .--right > .oxd-table-cell > .oxd-table-cell-actions > :nth-child(1)').click()
        cy.get('.oxd-button--label-danger').click()
        cy.get('.oxd-toast').should('include.text', 'Successfully Deleted')
    })
})

//Bulk delete skills and check toast message
describe('Bulk delete skills and check toast message', function () {
    it('bulk delete skills and check toast message', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewSkills')
        cy.viewport(1024, 768)
        cy.get('.oxd-table-header > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon').click()
        cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click()
        cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click()
        cy.get('.oxd-toast').should('include.text', 'Successfully Deleted')
    })
})

//Verify No Records Found message
describe('No Records Found verification', function () {
    it('No records found', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('admin/viewSkills')
        cy.get('.oxd-text--span').should('include.text', "No Records Found")
    })
})

//Visiting add skill and clicking cancel
describe('Visiting add skill and clicking cancel', function () {
    it('visiting add skill and clicking cancel', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/saveSkills')
        cy.get('.oxd-button--ghost').click()
        cy.get('.oxd-text--h6').should('include.text', "Skills")
    })
})

//Verify header and field name
describe('Verify header and field name', function () {
    it('verify header', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('admin/viewSkills')
        cy.get('.orangehrm-header-container > .oxd-text').should('include.text', "Skills")
    })
    it('verify field name', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('admin/viewSkills')
        cy.get('.oxd-button').click()
        cy.get(':nth-child(1) > .oxd-input-group > .oxd-input-group__label-wrapper > .oxd-label').should('include.text', "Name")
        cy.get(':nth-child(2) > .oxd-input-group > .oxd-input-group__label-wrapper > .oxd-label').should('include.text', "Description")
    })
})
