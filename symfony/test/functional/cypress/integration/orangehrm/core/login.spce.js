import user from '../../../fixtures/admin-user.json'

describe('Automation Test Suite - Fixtures', function () {

    it('Visits the login page', () => {
        cy.visit('/auth/login')

        cy.get('input[name=username]').type(user.admin.userName)

        cy.get('input[name=password]').type(user.admin.password)

        cy.get('form').submit()

        cy.get('.oxd-userdropdown').should('include.text', user.admin.fullName)

    })

    it('Visits the login page and check validations', () => {
        cy.visit('/auth/login')

        cy.get('input[name=username]').type(' ')

        cy.get('input[name=password]').type(' ')

        cy.get('form').submit()

        cy.get('.oxd-text').should('include.text', 'Required')

    })
})
