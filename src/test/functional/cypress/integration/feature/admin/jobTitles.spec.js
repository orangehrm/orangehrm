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

describe('Admin - Job Titles', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.fixture('chars').as('strings');
    cy.intercept('GET', '**/api/v2/admin/job-titles?*').as('getJobTitles');
    cy.intercept('POST', '**/api/v2/admin/job-titles').as('postJobTitles');
    cy.intercept('PUT', '**/api/v2/admin/job-titles/*').as('updateJobTitles');
    cy.intercept('DELETE', '**/api/v2/admin/job-titles').as('deleteJobTitles');
    cy.fixture('user').then(({admin}) => {
      this.user = admin;
    });
  });

  // Read
  describe('list job title', function () {
    it('job title list is loaded', function () {
      cy.loginTo(this.user, '/admin/viewJobTitleList');
      cy.wait('@getJobTitles');
      cy.toast('info', 'No Records Found');
    });
  });

  // Create
  describe('create job title', function () {
    it('add job title', function () {
      cy.loginTo(this.user, '/admin/saveJobTitle');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Job Title').type(this.strings.chars50.text);
        cy.getOXDInput('Job Description').type(this.strings.chars120.text);
        cy.getOXDInput('Note').type(this.strings.chars120.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@postJobTitles');
      cy.toast('success', 'Successfully Saved');
      cy.task('db:snapshot', {name: 'jobTitle'});
    });

    it('add job title form validations should work', function () {
      cy.task('db:restore', {name: 'jobTitle'});
      cy.loginTo(this.user, '/admin/saveJobTitle');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Job Title')
          .setValue(this.strings.chars120.text)
          .isInvalid('Should not exceed 100 characters');
        cy.getOXDInput('Job Title').setValue('').isInvalid('Required');
        cy.getOXDInput('Job Title')
          .setValue(this.strings.chars50.text)
          .isInvalid('Already exists');
        cy.getOXDInput('Job Description')
          .setValue(this.strings.chars450.text)
          .isInvalid('Should not exceed 400 characters');
        cy.getOXDInput('Note')
          .setValue(this.strings.chars450.text)
          .isInvalid('Should not exceed 400 characters');
      });
    });
  });

  // Update
  describe('update job title', function () {});

  // Delete
  describe('delete job title', function () {});
});
