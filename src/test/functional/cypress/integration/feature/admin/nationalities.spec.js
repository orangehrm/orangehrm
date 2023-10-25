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

describe('Admin - Nationalities', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.fixture('viewport').then(({HD}) => {
      cy.viewport(HD.width, HD.height);
    });
    cy.fixture('chars').as('strings');
    cy.intercept('GET', '**/api/v2/admin/nationalities?*').as('getNational');
    cy.intercept('POST', '**/api/v2/admin/nationalities').as('postNational');
    cy.intercept('PUT', '**/api/v2/admin/nationalities/*').as('updateNational');
    cy.intercept('DELETE', '**/api/v2/admin/nationalities').as(
      'deleteNational',
    );
    cy.fixture('user').then(({admin}) => {
      this.user = admin;
    });
  });

  // Create
  describe('Add job Category', function () {
    it('add Nationality', function () {
      cy.loginTo(this.user, '/admin/saveNationality');
      cy.wait('@getNational');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars50.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@postNational');
      cy.toast('success', 'Successfully Saved');
    });
    it('Nationalities form validations', function () {
      cy.loginTo(this.user, '/admin/saveNationality');
      cy.wait('@getNational');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name')
          .type(this.strings.chars120.text)
          .isInvalid('Should not exceed 100 characters');
        cy.getOXDInput('Name').setValue('').isInvalid('Required');
        cy.getOXDInput('Name').type('Albanian').isInvalid('Already exists');
      });
    });
    it('add a Nationality and click cancel', function () {
      cy.loginTo(this.user, '/admin/saveNationality');
      cy.wait('@getNational');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars30.text);
        cy.getOXD('button').contains('Cancel').click();
      });
      cy.wait('@getNational');
      cy.getOXD('pageTitle').contains('Nationalities');
    });
  });

  //Update
  describe('Update Nationality', function () {
    it('Edit Nationality', function () {
      cy.loginTo(this.user, '/admin/saveNationality/1');
      cy.wait('@getNational');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').clear().type('Updated with this Name');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@updateNational');
      cy.toast('success', 'Successfully Updated');
    });
  });

  //Delete
  describe('Delete Nationality', function () {
    it('Delete a single Nationality', function () {
      cy.loginTo(this.user, '/admin/nationality');
      cy.wait('@getNational');
      cy.get(
        '.oxd-table-body > :nth-child(1) .oxd-table-cell-actions > :nth-child(1)',
      ).click();
      cy.getOXD('button').contains('Yes, Delete').click();
      cy.wait('@deleteNational');
      cy.toast('success', 'Successfully Deleted');
    });
  });
});
