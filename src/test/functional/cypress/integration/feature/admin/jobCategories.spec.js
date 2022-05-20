describe('Admin - Job Category', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.viewport(1280, 720);
    cy.fixture('chars').as('strings');
    cy.intercept('GET', '**/api/v2/admin/job-categories?*').as(
      'getJobCategories',
    );
    cy.intercept('POST', '**/api/v2/admin/job-categories').as(
      'postJobCategories',
    );
    cy.intercept('PUT', '**/api/v2/admin/job-categories/*').as(
      'updateJobCategories',
    );
    cy.intercept('DELETE', '**/api/v2/admin/job-categories').as(
      'deleteJobCategories',
    );
    cy.fixture('user').then(({admin}) => {
      this.user = admin;
    });
  });

  // Read
  describe('list job Categories', function () {
    it('job title list is loaded', function () {
      cy.loginTo(this.user, '/admin/jobCategory');
      cy.wait('@getJobCategories');
      cy.getOXD('numRecords').contains('(9) Records Found');
    });
  });
  // Create
  describe('Add job Category', function () {
    it('add job category', function () {
      cy.loginTo(this.user, '/admin/saveJobCategory');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars50.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@postJobCategories');
      cy.toast('success', 'Successfully Saved');
    });
    it('Job Category form validations', function () {
      cy.loginTo(this.user, '/admin/saveJobCategory');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name')
          .type(this.strings.chars100.text)
          .isInvalid('Should not exceed 50 characters');
        cy.getOXDInput('Name').setValue('').isInvalid('Required');
        cy.getOXDInput('Name')
          .type('Craft Workers')
          .isInvalid('Already exists');
      });
    });
    it('add a job category and click cancel', function () {
      cy.loginTo(this.user, '/admin/saveJobCategory');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars50.text);
        cy.getOXD('button').contains('Cancel').click();
      });
      cy.wait('@getJobCategories');
      cy.getOXD('pageTitle').contains('Job Categories');
    });
  });
  //Update
  describe('Update job Category', function () {
    it('Edit job category', function () {
      cy.loginTo(this.user, '/admin/saveJobCategory/1');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').clear().type(this.strings.chars50.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@updateJobCategories');
      cy.toast('success', 'Successfully Updated');
    });
  });
  describe('Delete job Category', function () {
    it('Delete a single job category', function () {
      cy.loginTo(this.user, '/admin/jobCategory');
      cy.wait('@getJobCategories');
      cy.get(
        ':nth-child(1) > .oxd-table-row > [style="flex-shrink: 1;"] > .oxd-table-cell-actions > :nth-child(1)',
      ).click();
      cy.getOXD('button').contains('Yes, Delete').click();
      cy.wait('@getJobCategories');
      cy.toast('success', 'Successfully Deleted');
    });
    it('Bulk Delete job categories', function () {
      cy.loginTo(this.user, '/admin/jobCategory');
      cy.wait('@getJobCategories');
      cy.get(
        '.oxd-table-header > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
      ).click();
      cy.getOXD('button').contains('Delete Selected').click();
      cy.getOXD('button').contains('Yes, Delete').click();
      cy.wait('@getJobCategories');
      cy.toast('success', 'Successfully Deleted');
    });
  });
});
