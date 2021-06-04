import user from '../../../fixtures/user.json'

//Verify No Records Found message
describe('No skills', function () {

    it('No records found', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('admin/viewSkills')
        cy.get('.oxd-text--span').should('include.text', "No Records Found")

    })
})

//Verify page heading
describe('Skill title page', function () {

    it('Check Qualification Skills title view page', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('admin/viewSkills')
        cy.get('.oxd-text--h6').should('include.text', "Qualification Skills List")

    })
})
//Verify Add skill
describe('Add Skill', function () {

    it('check adding a skill', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/saveSkills')
        cy.xpath('//*[@id="app"]/div[1]/div/div[2]/div/div/form/div[1]/div/div[2]/input').type('skill1')
        cy.xpath('//*[@id="app"]/div[1]/div/div[2]/div/div/form/div[2]/div/div[2]/textarea').type('Add description here2')
        cy.get('form').submit()
        cy.get('.oxd-toast').should('include.text', 'Success')
    })
})
//Verify Skill name should be unique validation
describe('Unique Skill name', function () {

    it('checking a duplicating skill', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/saveSkills')
        cy.xpath('//*[@id="app"]/div[1]/div/div[2]/div/div/form/div[1]/div/div[2]/input').type('skill1')
        cy.get('.orangehrm-left-space').click()
        cy.get('.oxd-input-group__message').should('include.text', 'Skill name should be unique')
    })
})
//Verify Edit skill
describe('Edit Skill', function () {

    it('check edit a skill', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewSkills')
        cy.xpath('//body[1]/div[1]/div[1]/div[1]/div[2]/div[1]/div[1]/div[3]/div[1]/div[2]/div[1]/div[1]/div[4]/div[1]/button[2]/i[1]').click()
        cy.xpath('//*[@id="app"]/div[1]/div/div[2]/div/div/form/div[1]/div/div[2]/input').type('edit neww')
        cy.get('form').submit()
        cy.get('.oxd-toast').should('include.text', 'Success')
    })
})
//Verify cancel when adding a skill
describe('Cancel when add Skill', function () {
    it('check cancel when adding a skill', () => {
                cy.login(user.admin.userName, user.admin.password)
                cy.visit('/admin/saveSkills')
                cy.xpath('//*[@id="app"]/div[1]/div/div[2]/div/div/form/div[1]/div/div[2]/input').type('skill11w')
                cy.get('.oxd-button--ghost').click()
                cy.get('.oxd-text--h6').should('include.text', "Qualification Skills List")
            })
    
})
//Verify cancel when edditing a skill
describe('Cancel when edit Skill', function () {
    it('check cancel when edditing a skill', () => {
                cy.login(user.admin.userName, user.admin.password)
                cy.visit('/admin/viewSkills')        
                cy.xpath('//body[1]/div[1]/div[1]/div[1]/div[2]/div[1]/div[1]/div[3]/div[1]/div[2]/div[1]/div[1]/div[4]/div[1]/button[2]/i[1]').click()
                cy.xpath('//*[@id="app"]/div[1]/div/div[2]/div/div/form/div[1]/div/div[2]/input').type('edit neww')
                cy.get('.oxd-button--ghost').click()
                cy.get('.oxd-text--h6').should('include.text', "Qualification Skills List")
            })
    
})
//Verify deleting a skill
describe('Delete Skill', function () {

    it('check delete a skill', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/viewSkills')
        cy.xpath('//*[@id="app"]/div[1]/div/div[2]/div/div/div[3]/div/div[2]/div[1]/div/div[4]/div/button[1]/i').click()
        cy.get('.oxd-button--label-danger').contains('Yes, Delete').click()
        cy.get('.oxd-toast').should('include.text', 'Success')
    })
})
//Verify required validation
describe('Required validation in Skill', function () {

        it('Required validation in Skill', () => {
            cy.login(user.admin.userName, user.admin.password)
            cy.visit('/admin/saveSkills')
            //cy.get('form').submit()
            cy.get('.orangehrm-left-space').click()
            cy.get('.oxd-input-group__message').should('include.text', 'Required')
        })
    })
//Verify Maximum allowed charachters validation
describe('Maximum allowed charachters validation in Skill', function () {

    it('Check Maximum allowed charachters validation in Skill', () => {
        cy.login(user.admin.userName, user.admin.password)
        cy.visit('/admin/saveSkills')
        cy.xpath('//*[@id="app"]/div[1]/div/div[2]/div/div/form/div[1]/div/div[2]/input').type('01234567890123456789012345678901234567890123456789')
        cy.get('.oxd-input-group__message').should('include.text', 'Should be less than 50 characters')
        cy.xpath('//*[@id="app"]/div[1]/div/div[2]/div/div/form/div[2]/div/div[2]/textarea').type('0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789')
        cy.get('.oxd-input-group__message').should('include.text', 'Should be less than 400 characters')

    })
})