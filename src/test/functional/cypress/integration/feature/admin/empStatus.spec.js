describe('Admin - Employment Status', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.fixture('chars').as('strings');
    cy.intercept('GET', '**/api/v2/admin/employment-statuses*').as(
      'getEmpStatus',
    );
    cy.intercept('POST', '**/api/v2/admin/employment-statuses').as(
      'postEmpStatus',
    );
    cy.intercept('PUT', '**/api/v2/admin/employment-statuses/*').as(
      'putEmpStatus',
    );
    cy.intercept('DELETE', '**/api/v2/admin/employment-statuses').as(
      'deleteEmpStatus',
    );
    cy.fixture('user').then(({admin}) => {
      this.user = admin;
    });
  });

  describe('get emp status list', function () {
    it('load emp status list', function () {
      cy.loginTo(this.user, '/admin/employmentStatus');
      cy.wait('@getEmpStatus');
      cy.toast('info', 'No Records Found');
    });
  });

  describe('add emp status', function () {
    it('add an emp status and save', function () {
      cy.loginTo(this.user, '/admin/saveEmploymentStatus');
      cy.wait('@getEmpStatus');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars50.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@postEmpStatus');
      cy.toast('success', 'Successfully Saved');
      cy.task('db:snapshot', {name: 'empStatus'});
    });

    it('add an emp status and cancel', function () {
      cy.loginTo(this.user, '/admin/saveEmploymentStatus');
      cy.wait('@getEmpStatus');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars30.text);
        cy.getOXD('button').contains('Cancel').click();
      });
      cy.wait('@getEmpStatus');
      cy.getOXD('pageTitle').should('include.text', 'Employment Status');
    });

    it('add emp status form validations', function () {
      cy.task('db:restore', {name: 'empStatus'});
      cy.loginTo(this.user, '/admin/saveEmploymentStatus');
      cy.wait('@getEmpStatus');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name')
          .type(this.strings.chars100.text)
          .isInvalid('Should not exceed 50 characters');
        cy.getOXDInput('Name').setValue('').isInvalid('Required');
        cy.getOXDInput('Name')
          .type(this.strings.chars50.text)
          .isInvalid('Already exists');
      });
    });
  });

  describe('update emp status', function () {
    it('update emp status', function () {
      cy.task('db:restore', {name: 'empStatus'});
      cy.loginTo(this.user, '/admin/employmentStatus');
      cy.get(':nth-child(2) > .oxd-icon').click();
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').clear().type(this.strings.chars30.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@putEmpStatus');
      cy.toast('success', 'Successfully Updated');
    });
  });

  describe('delete emp status', function () {
    it('delete emp status', function () {
      cy.task('db:restore', {name: 'empStatus'});
      cy.loginTo(this.user, '/admin/employmentStatus');
      cy.get('.oxd-table-cell-actions > :nth-child(1)').click();
      cy.get('.oxd-button--label-danger').click();
      cy.wait('@deleteEmpStatus');
      cy.toast('success', 'Successfully Deleted');
    });
  });
});
