import user from '../../../fixtures/user.json'

describe('', function () {

    it('Check job title view page', () => {
        cy.login(user.admin.userName, user.admin.password)

        cy.visit('/todo/items')

        cy.get('.oxd-text--h6').should('include.text', "View ToDo")

    })
})