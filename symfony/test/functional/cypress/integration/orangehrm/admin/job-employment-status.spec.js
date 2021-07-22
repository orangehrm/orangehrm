import user from '../../../fixtures/admin-user.json';

describe('validate add employment status field by exceeding the character no', () => {
  beforeEach(() => {
    cy.loginTo(
      user.admin.userName,
      user.admin.password,
      '/admin/employmentStatus',
    );
  });

  it('check employment status list page', () => {
    cy.visit('/admin/employmentStatus');
    cy.get('.orangehrm-main-title').should('include.text', 'Employment Status');
  });

  it('check add new employment status', () => {
    cy.visit('/admin/saveEmploymentStatus');
    cy.get(':nth-child(2) > .oxd-input').type('Part Time');
    cy.get('form').submit();
  });

  it('validate add employment status by exceeding the character no', () => {
    cy.visit('/admin/saveEmploymentStatus');
    cy.get(':nth-child(2) > .oxd-input').type(
      'validateaddingemploymentstatusfieldbyexceedingthemaximumcharacterlimit',
    );
    cy.get('form').submit();
    cy.get('.oxd-input-group__message').should(
      'include.text',
      'Should not exceed 50 characters',
    );
  });

  it('validate add employment status required field', () => {
    cy.visit('/admin/saveEmploymentStatus');
    cy.get(':nth-child(2) > .oxd-input').type(' ');
    cy.get('form').submit();
    cy.get('.oxd-input-group__message').should('include.text', 'Required');
  });

  it('adding a duplicate employment status', () => {
    cy.visit('/admin/saveEmploymentStatus');
    cy.get(':nth-child(2) > .oxd-input').type('Part Time');
    cy.get('form').submit();
    cy.get('.oxd-input-group__message').should('include.text', 'Already exist');
  });

  it('updating an employment status and the toast message', () => {
    cy.visit('/admin/employmentStatus');
    cy.get(
      ':nth-child(1) > .oxd-table-row > .card-center > .card-header-slot > .--right > .oxd-table-cell > .oxd-table-cell-actions > :nth-child(2) > .oxd-icon',
    ).click();
    cy.get(':nth-child(2) > .oxd-input').click().clear().type('Contract');
    cy.get('form').submit();
    cy.get('.oxd-toast').should('include.text', 'Successfully Updated');
  });

  it('delete an employment status and the toast message', () => {
    cy.visit('/admin/employmentStatus');
    cy.get(
      ':nth-child(1) > .oxd-table-row > .card-center > .card-header-slot > .--right > .oxd-table-cell > .oxd-table-cell-actions > :nth-child(1)',
    ).click();
    cy.get('.oxd-button--label-danger').click();
    cy.get('.oxd-toast').should('include.text', 'Successfully Deleted');
  });

  it('check add new employment status and check the success toast', () => {
    cy.visit('/admin/saveEmploymentStatus');
    cy.get(':nth-child(2) > .oxd-input').type('Fulltime');
    cy.get('form').submit();
    cy.get('.oxd-toast').should('include.text', 'Successfully Saved');
  });

  it('visiting update an employment status and click cancel', () => {
    cy.visit('/admin/employmentStatus');
    cy.get(
      ':nth-child(1) > .oxd-table-row > .card-center > .card-header-slot > .--right > .oxd-table-cell > .oxd-table-cell-actions > :nth-child(2)',
    ).click();
    cy.get('.oxd-button--ghost').click();
  });

  it('visiting add a new employment status and click cancel', () => {
    cy.visit('/admin/employmentStatus');
    cy.get('.oxd-button').click();
    cy.get('.oxd-button--ghost').click();
  });

  it('check add a new employment status and check the success toast', () => {
    cy.visit('/admin/saveEmploymentStatus');
    cy.get(':nth-child(2) > .oxd-input').type('Fulltime2');
    cy.get('form').submit();
  });

  it('list count increment', () => {
    cy.visit('/admin/employmentStatus');
    cy.get('.orangehrm-horizontal-padding > .oxd-text')
      .contains('Records Found')
      .invoke('text')
      .then((line) => {
        const num = line.match(/\((.*)\)/);
        const currentNum = parseInt(num[1]);

        cy.visit('/admin/saveEmploymentStatus');
        cy.get(':nth-child(2) > .oxd-input').type('probation');
        cy.get('form').submit();
        cy.viewport(1024, 768);

        cy.get('.orangehrm-horizontal-padding > .oxd-text')
          .contains('Records Found')
          .invoke('text')
          .then((line) => {
            const num = line.match(/\((.*)\)/);
            const newNum = parseInt(num[1]);
            expect(newNum).to.eq(currentNum + 1);
          });
      });
  });

  it('Bulk delete check the success toast', () => {
    cy.visit('/admin/employmentStatus');
    cy.viewport(1024, 768);
    cy.get(
      '.oxd-table-header > .oxd-table-row > :nth-child(1) > .oxd-checkbox-wrapper > label > .oxd-checkbox-input > .oxd-icon',
    ).click();
    cy.get('.orangehrm-horizontal-padding > div > .oxd-button').click();
    cy.get('.orangehrm-modal-footer > .oxd-button--label-danger').click();
    cy.get('.oxd-toast').should('include.text', 'Successfully Deleted');
    cy.get('.oxd-text--span').should('include.text', 'No Records Found');
  });

  it('verify header', () => {
    cy.visit('/admin/employmentStatus');
    cy.get('.orangehrm-header-container > .oxd-text').should(
      'include.text',
      'Employment Status',
    );
  });

  it('field name', () => {
    cy.visit('/admin/saveEmploymentStatus');
    cy.get('.oxd-label').should('include.text', 'Name');
  });
});
