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
      cy.wait('@getJobTitles');
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
      cy.wait('@getJobTitles');
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

    it('cancel button behaviour on jobTitle form', function () {
      cy.loginTo(this.user, '/admin/saveJobTitle');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Job Title').type(this.strings.chars50.text);
        cy.getOXD('button').contains('Cancel').click();
      });
      cy.wait('@getJobTitles');
      cy.getOXD('pageTitle').contains('Job Titles');
    });
  });

  // Update
  describe('update job title', function () {
    it('Edit Job Title', function () {
      cy.task('db:restore', {name: 'jobTitle'});
      cy.loginTo(this.user, '/admin/saveJobTitle/1');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Job Title')
          .clear()
          .type('Updated the Job Title name with this text');
        cy.getOXDInput('Job Description')
          .clear()
          .type('Updated the Job Title description with this text');
        cy.getOXDInput('Note')
          .clear()
          .type('Updated the Job Title Note with this text');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@updateJobTitles');
      cy.toast('success', 'Successfully Updated');
    });

    it('cancel button behaviour on edit Job Title', function () {
      cy.task('db:restore', {name: 'jobTitle'});
      cy.loginTo(this.user, '/admin/saveJobTitle/1');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Job Title')
          .clear()
          .type('Updated the Job Title name with this text');
        cy.getOXDInput('Job Description')
          .clear()
          .type('Updated the Job title description with this text');
        cy.getOXD('button').contains('Cancel').click();
      });
      cy.wait('@getJobTitles');
      cy.getOXD('pageTitle').contains('Job Titles');
    });
  });

  // Delete
  describe('delete job title', function () {
    it('Delete a Single Job Title', function () {
      cy.task('db:restore', {name: 'jobTitle'});
      cy.loginTo(this.user, '/admin/viewJobTitleList');
      cy.wait('@getJobTitles');
      cy.get(
        '.oxd-table-body > :nth-child(1) .oxd-table-cell-actions > :nth-child(1)',
      ).click();
      cy.getOXD('button').contains('Yes, Delete').click();
      cy.wait('@getJobTitles');
      cy.toast('success', 'Successfully Deleted');
    });

    it('Bulk Delete Job Title', function () {
      cy.task('db:restore', {name: 'jobTitle'});
      cy.loginTo(this.user, '/admin/viewJobTitleList');
      cy.wait('@getJobTitles');
      cy.get('.oxd-table-header .oxd-checkbox-input').click();
      cy.getOXD('button').contains('Delete Selected').click();
      cy.getOXD('button').contains('Yes, Delete').click();
      cy.wait('@getJobTitles');
      cy.toast('success', 'Successfully Deleted');
    });
  });
});
