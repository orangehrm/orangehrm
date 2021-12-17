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

  // Read
  describe('list job title', function () {
    it('job title list is loaded', function () {
      cy.loginTo(this.user, '/admin/viewJobTitleList');
      cy.wait('@getJobTitles');
      cy.toast('info', 'No Records Found');
    });
  });

  // Update
  describe('update job title', function () {});

  // Delete
  describe('delete job title', function () {});
});
