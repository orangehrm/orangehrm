/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

describe('Core - Login Page', function () {
  before(function () {
    cy.task('db:reset');
    cy.intercept('POST', '**/auth/validate').as('postLogin');
    cy.fixture('user').then(({admin}) => {
      this.user = admin;
    });
  });

  it('should login as admin', function () {
    cy.visit('/auth/login');
    cy.getOXD('form').within(() => {
      cy.getOXDInput('Username').type(this.user.username);
      cy.getOXDInput('Password').type(this.user.password);
      cy.getOXD('button').contains('Login').click();
    });
    cy.wait('@postLogin')
      .its('response.headers')
      .should('have.property', 'location')
      .and('match', /dashboard\/index/);
  });

  it('login form validations should work', function () {
    cy.visit('/auth/login');
    cy.getOXD('button').contains('Login').click();
    cy.getOXDInput('Username').isInvalid('Required');
    cy.getOXDInput('Password').isInvalid('Required');
  });
});
