describe('Admin - Job Titles', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.intercept('GET', '**/api/v2/admin/job-titles?*').as('getJobTitles');
    cy.fixture('user').then(({admin}) => {
      this.user = admin;
    });
  });

  it('Job Title List page should correctly render screen', function () {
    cy.loginTo(this.user, '/admin/viewJobTitleList');
    cy.wait('@getJobTitles');
    cy.getOXD('pageContext').within(() => {
      cy.getOXD('pageTitle').should('include.text', 'Job Titles');
      cy.getOXD('button').should('include.text', 'Add');
    });
  });
});
