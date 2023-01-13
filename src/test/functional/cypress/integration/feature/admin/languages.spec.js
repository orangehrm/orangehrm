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

describe('Admin - Languages', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.fixture('chars').as('strings');
    //cy.intercept('GET', '**/api/v2/admin/languages?*').as('getLanguage');
    cy.intercept('GET', '**/api/v2/admin/languages*').as('getLanguage');
    cy.intercept('POST', '**/api/v2/admin/languages').as('postLanguage');
    cy.intercept('PUT', '**/api/v2/admin/languages/*').as('updateLanguage');
    cy.intercept('DELETE', '**/api/v2/admin/languages').as('deleteLanguage');
    cy.fixture('user').then(({admin}) => {
      this.user = admin;
    });
  });

  // Read
  //include test case num row -3
  describe('list Language', function () {
    it('Language list is loaded', function () {
      cy.loginTo(this.user, '/admin/viewLanguages');
      cy.wait('@getLanguage');
      cy.get('.orangehrm-horizontal-padding > .oxd-text').contains(
        'No Records Found',
      );
      cy.toast('info', 'No Records Found');
    });
  });

  // Create
  describe('create Language', function () {
    //include test case num row -4, 7
    it('add Language', function () {
      cy.loginTo(this.user, '/admin/saveLanguages');
      cy.wait('@getLanguage');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars50.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@postLanguage');
      cy.toast('success', 'Successfully Saved');
      cy.get('.orangehrm-horizontal-padding > .oxd-text').contains(
        '(1) Record Found',
      );
      cy.get('[style="flex-basis: 80%;"] > div').contains(
        this.strings.chars50.text,
      );
      cy.task('db:snapshot', {name: 'Language'});
    });
    //test case row num -6
    it('add record increased by one', function () {
      cy.task('db:restore', {name: 'Language'});
      cy.loginTo(this.user, '/admin/saveLanguages');
      cy.wait('@getLanguage');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars30.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@postLanguage');
      cy.toast('success', 'Successfully Saved');
      cy.get('.orangehrm-horizontal-padding > .oxd-text').contains(
        '(2) Records Found',
      );
      cy.task('db:snapshot', {name: 'Language2'});
    });

    //test case row -5,17,16
    it('add Language form validations should work', function () {
      cy.task('db:restore', {name: 'Language'});
      cy.loginTo(this.user, '/admin/saveLanguages');
      cy.wait('@getLanguage');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name')
          .setValue(this.strings.chars200.text)
          .isInvalid('Should not exceed 120 characters');
        cy.getOXDInput('Name').setValue('').isInvalid('Required');
        cy.getOXDInput('Name')
          .setValue(this.strings.chars50.text)
          .isInvalid('Already exists');
      });
    });

    //test case row -19
    it('add Language click cancel', function () {
      cy.task('db:restore', {name: 'Language'});
      cy.loginTo(this.user, '/admin/saveLanguages');
      cy.wait('@getLanguage');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars30.text);
        cy.getOXD('button').contains('Cancel').click();
      });
      cy.wait('@getLanguage');
      cy.get('.orangehrm-horizontal-padding > .oxd-text').contains(
        '(1) Record Found',
      );
    });
  });

  // Update
  describe('update Language', function () {
    // test case row -8,9
    it('Edit Language', function () {
      cy.task('db:restore', {name: 'Language'});
      cy.loginTo(this.user, '/admin/saveLanguages/1');
      cy.wait('@getLanguage');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').clear().type(this.strings.chars30.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@updateLanguage');
      cy.toast('success', 'Successfully Updated');
      cy.get('.orangehrm-horizontal-padding > .oxd-text').contains(
        '(1) Record Found',
      );
    });

    //test case row -20
    it('Edit Language and cancel', function () {
      cy.task('db:restore', {name: 'Language'});
      cy.loginTo(this.user, '/admin/saveLanguages/1');
      cy.wait('@getLanguage');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').clear().type(this.strings.chars30.text);
        cy.getOXD('button').contains('Cancel').click();
      });
      cy.wait('@getLanguage');
      cy.get('.orangehrm-horizontal-padding > .oxd-text').contains(
        '(1) Record Found',
      );
      cy.get('[style="flex-basis: 80%;"] > div').contains(
        this.strings.chars50.text,
      );
    });
  });

  // Delete
  describe('delete Language', function () {
    //test case rows -10,11
    it('Delete a single Language', function () {
      cy.task('db:restore', {name: 'Language'});
      cy.loginTo(this.user, '/admin/viewLanguages');
      cy.wait('@getLanguage');
      cy.get(
        '.oxd-table-body > :nth-child(1) .oxd-table-cell-actions > :nth-child(1)',
      ).click();
      cy.getOXD('button').contains('Yes, Delete').click();
      cy.wait('@getLanguage');
      cy.toast('success', 'Successfully Deleted');
      cy.toast('info', 'No Records Found');
      cy.get('.orangehrm-horizontal-padding > .oxd-text').contains(
        'No Records Found',
      );
    });
    //bulk delete
    //test case row - 12,13
    it('Bulk Delete Language', function () {
      cy.task('db:restore', {name: 'Language2'});
      cy.loginTo(this.user, '/admin/viewLanguages');
      cy.wait('@getLanguage');
      cy.get('.oxd-table-header .oxd-checkbox-input').click();
      cy.getOXD('button').contains('Delete Selected').click();
      cy.getOXD('button').contains('Yes, Delete').click();
      cy.wait('@getLanguage');
      cy.toast('success', 'Successfully Deleted');
      cy.toast('info', 'No Records Found');
      cy.get('.orangehrm-horizontal-padding > .oxd-text').contains(
        'No Records Found',
      );
    });
  });
});
