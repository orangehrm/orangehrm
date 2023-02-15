/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

describe('Admin - Organization Structure', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.fixture('viewport').then(({HD}) => {
      cy.viewport(HD.width, HD.height);
    });
    cy.fixture('chars').as('strings');
    cy.intercept('GET', '**/api/v2/admin/subunits*').as('getOrgStructure');
    cy.intercept('POST', '**/api/v2/admin/subunits').as('postOrgStructure');
    cy.intercept('PUT', '**/api/v2/admin/subunits/*').as('updateOrgStructure');
    cy.intercept('DELETE', '**/api/v2/admin/subunits/*').as(
      'deleteOrgStructure',
    );
    cy.fixture('user').then(({admin}) => {
      this.user = admin;
    });
  });

  describe('Verify the edit button behaviour', function () {
    it('switch the edit button and view the Add button', function () {
      cy.loginTo(this.user, '/admin/viewCompanyStructure');
      cy.wait('@getOrgStructure');
      cy.getOXD('pageTitle').contains('Organization Structure');
      cy.getOXD('switch').click();
      cy.getOXD('button').contains(' Add ');
    });
  });

  describe('Verify the Add button bahaviour and verify the add organization unit modal', function () {
    it('verify the add button behaviour', function () {
      cy.loginTo(this.user, '/admin/viewCompanyStructure');
      cy.wait('@getOrgStructure');
      cy.getOXD('pageTitle').contains('Organization Structure');
      cy.getOXD('switch').click();
      cy.getOXD('button').contains(' Add ').click();
      cy.wait('@getOrgStructure');
      cy.get('.orangehrm-modal-header > .oxd-text').contains(
        'Add Organization Unit',
      );
    });

    it('verify the field validation in the add organization unit modal', function () {
      cy.loginTo(this.user, '/admin/viewCompanyStructure');
      cy.wait('@getOrgStructure');
      cy.getOXD('pageTitle').contains('Organization Structure');
      cy.getOXD('switch').click();
      cy.getOXD('button').contains(' Add ').click();
      cy.wait('@getOrgStructure');
      cy.get(':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input')
        .type(this.strings.chars120.text)
        .isInvalid('Should not exceed 100 characters');
      cy.get(':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input')
        .type(this.strings.chars120.text)
        .isInvalid('Should not exceed 100 characters');
      cy.get('.oxd-textarea')
        .type(this.strings.chars450.text)
        .isInvalid('Should not exceed 400 characters');
      cy.get(':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input')
        .setValue('')
        .isInvalid('Required');
    });

    it('verify the cancel button behaviour in Add Organization unit modal', function () {
      cy.loginTo(this.user, '/admin/viewCompanyStructure');
      cy.wait('@getOrgStructure');
      cy.getOXD('pageTitle').contains('Organization Structure');
      cy.getOXD('switch').click();
      cy.getOXD('button').contains(' Add ').click();
      cy.wait('@getOrgStructure');
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type(this.strings.chars50.text);
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type(this.strings.chars50.text);
      cy.get('.oxd-textarea').type(this.strings.chars100.text);
      cy.get('.oxd-button--ghost').click();
      cy.wait('@getOrgStructure');
      cy.getOXD('pageTitle').contains('Organization Structure');
    });
  });

  describe('Verify adding a unit for organaizational structure successfully and verify the Updating scenarios', function () {
    it('adding a unit with Unit ID and check the behaviour', function () {
      cy.loginTo(this.user, '/admin/viewCompanyStructure');
      cy.wait('@getOrgStructure');
      cy.getOXD('switch').click();
      cy.getOXD('button').contains(' Add ').click();
      cy.wait('@getOrgStructure');
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type(this.strings.chars50.text);
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type(this.strings.chars50.text);
      cy.get('.oxd-textarea').type(this.strings.chars100.text);
      cy.get('.oxd-form-actions > .oxd-button--secondary').click();
      cy.wait('@postOrgStructure');
      cy.toast('success', 'Successfully Saved');
      // eslint-disable-next-line cypress/no-unnecessary-waiting
      cy.wait(3000);
      cy.task('db:snapshot', {name: 'addedEventOnOrg'});
    });

    it('check the duplicate validation for unit', function () {
      cy.task('db:restore', {name: 'addedEventOnOrg'});
      cy.loginTo(this.user, '/admin/viewCompanyStructure');
      cy.wait('@getOrgStructure');
      cy.getOXD('switch').click();
      cy.getOXD('button').contains(' Add ').click();
      cy.wait('@getOrgStructure');
      cy.get(':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input')
        .setValue(this.strings.chars50.text)
        .isInvalid('Organization unit name should be unique');
    });

    it('Verifying edit modal header', function () {
      cy.task('db:restore', {name: 'addedEventOnOrg'});
      cy.loginTo(this.user, '/admin/viewCompanyStructure');
      cy.wait('@getOrgStructure');
      cy.getOXD('switch').click();
      cy.get('.org-action > :nth-child(2)').click();
      cy.wait('@getOrgStructure');
      cy.get('.orangehrm-modal-header > .oxd-text').contains(
        'Edit Organization Unit',
      );
    });

    it('Verifying cancel button in edit modal', function () {
      cy.task('db:restore', {name: 'addedEventOnOrg'});
      cy.loginTo(this.user, '/admin/viewCompanyStructure');
      cy.wait('@getOrgStructure');
      cy.getOXD('switch').click();
      cy.get('.org-action > :nth-child(2)').click();
      cy.wait('@getOrgStructure');
      cy.get('.oxd-button--ghost').click();
      cy.wait('@getOrgStructure');
      cy.getOXD('pageTitle').contains('Organization Structure');
    });

    it('Verifying editing an unit successfully', function () {
      cy.task('db:restore', {name: 'addedEventOnOrg'});
      cy.loginTo(this.user, '/admin/viewCompanyStructure');
      cy.wait('@getOrgStructure');
      cy.getOXD('switch').click();
      cy.get('.org-action > :nth-child(2)').click();
      cy.wait('@getOrgStructure');
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('Updated Unit ID');
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type('Updated Name');
      cy.get('.oxd-textarea').type('Updated description');
      cy.get('.oxd-form-actions > .oxd-button--secondary').click();
      cy.wait('@updateOrgStructure');
      cy.toast('success', 'Successfully Updated');
      //eslint-disable-next-line cypress/no-unnecessary-waiting
      cy.wait(3000);
    });
  });

  describe('Verifying deleting scenario', function () {
    it('adding a unit with Unit ID and check the behaviour', function () {
      cy.loginTo(this.user, '/admin/viewCompanyStructure');
      cy.wait('@getOrgStructure');
      cy.getOXD('switch').click();
      cy.getOXD('button').contains(' Add ').click();
      cy.wait('@getOrgStructure');
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type(this.strings.chars50.text);
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type(this.strings.chars50.text);
      cy.get('.oxd-textarea').type(this.strings.chars100.text);
      cy.get('.oxd-form-actions > .oxd-button--secondary').click();
      cy.wait('@postOrgStructure');
      cy.toast('success', 'Successfully Saved');
      // eslint-disable-next-line cypress/no-unnecessary-waiting
      cy.wait(3000);
      cy.task('db:snapshot', {name: 'addedEventOnOrgForDelete'});
      // eslint-disable-next-line cypress/no-unnecessary-waiting
      cy.wait(3000);
    });

    it('verifying "no delete" button on delete pop up message', function () {
      cy.task('db:restore', {name: 'addedEventOnOrgForDelete'});
      cy.loginTo(this.user, '/admin/viewCompanyStructure');
      cy.wait('@getOrgStructure');
      cy.getOXD('switch').click();
      cy.get('.org-action > :nth-child(1)').click();
      cy.get('.oxd-button--text').click();
      //cy.wait('@getOrgStructure');
      cy.getOXD('pageTitle').contains('Organization Structure');
      // eslint-disable-next-line cypress/no-unnecessary-waiting
      cy.wait(3000);
    });

    it('verifying yes delete button on delete pop up message and successfully deletion', function () {
      cy.task('db:restore', {name: 'addedEventOnOrgForDelete'});
      cy.loginTo(this.user, '/admin/viewCompanyStructure');
      cy.wait('@getOrgStructure');
      cy.getOXD('switch').click();
      cy.get('.org-action > :nth-child(1)').click();
      cy.get('.oxd-button--label-danger').click();
      cy.wait('@deleteOrgStructure');
      cy.toast('success', 'Successfully Deleted');
      // eslint-disable-next-line cypress/no-unnecessary-waiting
      cy.wait(3000);
    });
  });

  describe('Verifying adding units to added unit', function () {
    it('adding a unit with Unit ID and check the behaviour', function () {
      cy.loginTo(this.user, '/admin/viewCompanyStructure');
      cy.wait('@getOrgStructure');
      cy.getOXD('switch').click();
      cy.getOXD('button').contains(' Add ').click();
      cy.wait('@getOrgStructure');
      cy.get(
        ':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type(this.strings.chars50.text);
      cy.get(
        ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input',
      ).type(this.strings.chars50.text);
      cy.get('.oxd-textarea').type(this.strings.chars100.text);
      cy.get('.oxd-form-actions > .oxd-button--secondary').click();
      cy.wait('@postOrgStructure');
      cy.toast('success', 'Successfully Saved');
      // eslint-disable-next-line cypress/no-unnecessary-waiting
      cy.wait(3000);
      cy.task('db:snapshot', {name: 'addedEventOnOrgForAddingAnotherUnit'});
      // eslint-disable-next-line cypress/no-unnecessary-waiting
      cy.wait(3000);
    });

    it('checking field level validation for added child event', function () {
      cy.task('db:restore', {name: 'addedEventOnOrgForAddingAnotherUnit'});
      cy.loginTo(this.user, '/admin/viewCompanyStructure');
      cy.wait('@getOrgStructure');
      cy.getOXD('switch').click();
      cy.get('.org-action > :nth-child(3)').click();
      cy.wait('@getOrgStructure');
      cy.get(':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input')
        .type(this.strings.chars120.text)
        .isInvalid('Should not exceed 100 characters');
      cy.get(':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input')
        .type(this.strings.chars120.text)
        .isInvalid('Should not exceed 100 characters');
      cy.get('.oxd-textarea')
        .type(this.strings.chars450.text)
        .isInvalid('Should not exceed 400 characters');
      cy.get(':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input')
        .setValue('')
        .isInvalid('Required');
      // eslint-disable-next-line cypress/no-unnecessary-waiting
      cy.wait(3000);
    });

    it('checking cancel button added child event', function () {
      cy.task('db:restore', {name: 'addedEventOnOrgForAddingAnotherUnit'});
      cy.loginTo(this.user, '/admin/viewCompanyStructure');
      cy.wait('@getOrgStructure');
      cy.getOXD('switch').click();
      cy.get('.org-action > :nth-child(3)').click();
      cy.wait('@getOrgStructure');
      cy.get(':nth-child(1) > .oxd-input-group > :nth-child(2) > .oxd-input')
        .type(this.strings.chars120.text)
        .isInvalid('Should not exceed 100 characters');
      cy.get(':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input')
        .type(this.strings.chars120.text)
        .isInvalid('Should not exceed 100 characters');
      cy.get('.oxd-textarea')
        .type(this.strings.chars450.text)
        .isInvalid('Should not exceed 400 characters');
      cy.get(':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-input')
        .setValue('')
        .isInvalid('Required');
      cy.get('.oxd-button--ghost').click();
      cy.wait('@getOrgStructure');
      cy.getOXD('pageTitle').contains('Organization Structure');
    });
  });
});
