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

describe('Admin - Education', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.fixture('chars').as('strings');
    //cy.intercept('GET', '**/api/v2/admin/educations?*').as('getEducation');
    cy.intercept('GET', '**/api/v2/admin/educations*').as('getEducation');
    cy.intercept('POST', '**/api/v2/admin/educations').as('postEducation');
    cy.intercept('PUT', '**/api/v2/admin/educations/*').as('updateEducation');
    cy.intercept('DELETE', '**/api/v2/admin/educations').as('deleteEducation');
    cy.fixture('user').then(({admin}) => {
      this.user = admin;
    });
  });

  // Read
  //include test case num row - 2,3
  describe('list education', function () {
    it('education list is loaded', function () {
      cy.loginTo(this.user, '/admin/viewEducation');
      cy.wait('@getEducation');
      cy.get('.orangehrm-horizontal-padding > .oxd-text').contains(
        'No Records Found',
      );
      cy.toast('info', 'No Records Found');
    });
  });

  // Create
  describe('create education', function () {
    //include test case num row - 4,6
    it('add education', function () {
      cy.loginTo(this.user, '/admin/saveEducation');
      cy.wait('@getEducation');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Level').type(this.strings.chars50.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@postEducation');
      cy.toast('success', 'Successfully Saved');
      cy.get('.orangehrm-horizontal-padding > .oxd-text').contains(
        '(1) Record Found',
      );
      cy.get('[style="flex-basis: 80%;"] > div').contains(
        this.strings.chars50.text,
      );
      cy.task('db:snapshot', {name: 'education'});
    });
    //test case row num -5
    it('add record increased by one', function () {
      cy.task('db:restore', {name: 'education'});
      cy.loginTo(this.user, '/admin/saveEducation');
      cy.wait('@getEducation');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Level').type(this.strings.chars30.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@postEducation');
      cy.toast('success', 'Successfully Saved');
      cy.get('.orangehrm-horizontal-padding > .oxd-text').contains(
        '(2) Records Found',
      );
      cy.task('db:snapshot', {name: 'education2'});
    });

    //test case row - 7,8,9
    it('add education form validations should work', function () {
      cy.task('db:restore', {name: 'education'});
      cy.loginTo(this.user, '/admin/saveEducation');
      cy.wait('@getEducation');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Level')
          .setValue(this.strings.chars120.text)
          .isInvalid('Should not exceed 100 characters');
        cy.getOXDInput('Level').setValue('').isInvalid('Required');
        cy.getOXDInput('Level')
          .setValue(this.strings.chars50.text)
          .isInvalid('Already exists');
      });
    });

    //test case row -21
    it('add education click cancel', function () {
      cy.task('db:restore', {name: 'education'});
      cy.loginTo(this.user, '/admin/saveEducation');
      cy.wait('@getEducation');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Level').type(this.strings.chars30.text);
        cy.getOXD('button').contains('Cancel').click();
      });
      cy.wait('@getEducation');
      cy.get('.orangehrm-horizontal-padding > .oxd-text').contains(
        '(1) Record Found',
      );
    });
  });

  // Update
  describe('update education', function () {
    // test case row - 10,11, 12
    it('Edit education', function () {
      cy.task('db:restore', {name: 'education'});
      cy.loginTo(this.user, '/admin/saveEducation/1');
      cy.wait('@getEducation');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Level').clear().type(this.strings.chars30.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@updateEducation');
      cy.toast('success', 'Successfully Updated');
      cy.get('.orangehrm-horizontal-padding > .oxd-text').contains(
        '(1) Record Found',
      );
    });

    //test case row - 22
    it('Edit education and cancel', function () {
      cy.task('db:restore', {name: 'education'});
      cy.loginTo(this.user, '/admin/saveEducation/1');
      cy.wait('@getEducation');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Level').clear().type(this.strings.chars30.text);
        cy.getOXD('button').contains('Cancel').click();
      });
      cy.wait('@getEducation');
      cy.get('.orangehrm-horizontal-padding > .oxd-text').contains(
        '(1) Record Found',
      );
      cy.get('[style="flex-basis: 80%;"] > div').contains(
        this.strings.chars50.text,
      );
    });
  });

  // Delete
  describe('delete education', function () {
    //test case rows - 13, 14, 15
    it('Delete a single education', function () {
      cy.task('db:restore', {name: 'education'});
      cy.loginTo(this.user, '/admin/viewEducation');
      cy.wait('@getEducation');
      cy.get(
        '.oxd-table-body > :nth-child(1) .oxd-table-cell-actions > :nth-child(1)',
      ).click();
      cy.getOXD('button').contains('Yes, Delete').click();
      cy.wait('@getEducation');
      cy.toast('success', 'Successfully Deleted');
      cy.toast('info', 'No Records Found');
      cy.get('.orangehrm-horizontal-padding > .oxd-text').contains(
        'No Records Found',
      );
    });
    //bulk delete
    //test case row - 16, 17
    it('Bulk Delete education', function () {
      cy.task('db:restore', {name: 'education2'});
      cy.loginTo(this.user, '/admin/viewEducation');
      cy.wait('@getEducation');
      cy.get('.oxd-table-header .oxd-checkbox-input').click();
      cy.getOXD('button').contains('Delete Selected').click();
      cy.getOXD('button').contains('Yes, Delete').click();
      cy.wait('@getEducation');
      cy.toast('success', 'Successfully Deleted');
      cy.toast('info', 'No Records Found');
      cy.get('.orangehrm-horizontal-padding > .oxd-text').contains(
        'No Records Found',
      );
    });
  });
});
