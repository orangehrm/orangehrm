import user from '../../../fixtures/user.json'

describe('Job title page', function () {

    it('Check job title view page', () => {
        cy.login(user.admin.userName, user.admin.password)

        cy.visit('/admin/viewJobTitleList')

        cy.get('.oxd-text--h6').should('include.text', "Job Title List")

    })
})
