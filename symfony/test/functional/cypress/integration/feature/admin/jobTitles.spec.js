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

import faker from 'faker';

describe('Admin - Job Titles', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.intercept('GET', '**/api/v2/admin/job-titles?*').as('getJobTitles');
    cy.intercept('POST', '**/api/v2/admin/job-titles').as('postJobTitles');
    cy.intercept('PUT', '**/api/v2/admin/job-titles/*').as('updateJobTitles');
    cy.intercept('DELETE', '**/api/v2/admin/job-titles').as('deleteJobTitles');
    cy.fixture('user').then(({admin}) => {
      this.user = admin;
    });
  });

  const dummyText = faker.lorem.paragraph(20);
  const jobTitleModel = {
    title: faker.name.title(),
    titleInvalid: dummyText.substring(0, 101),
    description: dummyText.substring(0, 150),
    descriptionInvalid: dummyText.substring(0, 401),
    note: dummyText.substring(0, 150),
    noteInvalid: dummyText.substring(0, 401),
  };

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
        cy.getOXDInput('Job Title').type(jobTitleModel.title);
        cy.getOXDInput('Job Description').type(jobTitleModel.description);
        cy.getOXDInput('Note').type(jobTitleModel.note);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@postJobTitles');
      cy.toast('success', 'Successfully Saved');
    });

    it('add job title form validations should work', function () {
      cy.loginTo(this.user, '/admin/saveJobTitle');
      cy.wait('@getJobTitles');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Job Title')
          .setValue(jobTitleModel.titleInvalid)
          .isInvalid('Should be less than 100 characters');
        cy.getOXDInput('Job Title').setValue('').isInvalid('Required');
        cy.getOXDInput('Job Title')
          .setValue(jobTitleModel.title)
          .isInvalid('Already exists');
        cy.getOXDInput('Job Description')
          .setValue(jobTitleModel.descriptionInvalid)
          .isInvalid('Should be less than 400 characters');
        cy.getOXDInput('Note')
          .setValue(jobTitleModel.noteInvalid)
          .isInvalid('Should be less than 400 characters');
      });
    });
  });

  // Update
  describe('update job title', function () {});

  // Delete
  describe('delete job title', function () {});
});
