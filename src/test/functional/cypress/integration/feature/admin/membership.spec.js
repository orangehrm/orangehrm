describe('Admin - Membership', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.fixture('chars').as('strings');
    cy.intercept('GET', '**/api/v2/admin/memberships?*').as('getMembership');
    cy.intercept('POST', '**/api/v2/admin/memberships').as('postMembership');
    cy.intercept('PUT', '**/api/v2/admin/memberships/*').as('updateMembership');
    cy.intercept('DELETE', '**/api/v2/admin/memberships').as(
      'deleteMembership',
    );
    cy.fixture('user').then(({admin}) => {
      this.user = admin;
    });
  });

  // Read
  describe('List Memberships', function () {
    it('Membership list is loaded', function () {
      cy.loginTo(this.user, '/admin/membership');
      cy.wait('@getMembership');
      cy.toast('info', 'No Records Found');
    });
  });

  // Create
  describe('create membership', function () {
    it('add membership', function () {
      cy.loginTo(this.user, '/admin/saveMemberships');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars30.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@postMembership');
      cy.toast('success', 'Successfully Saved');
      cy.task('db:snapshot', {name: 'Membership'});
    });

    it('checking length validation and required validation on membership', function () {
      cy.task('db:restore', {name: 'Membership'});
      cy.loginTo(this.user, '/admin/saveMemberships');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name')
          .setValue(this.strings.chars51.text)
          .isInvalid('Should not exceed 50 characters');
        cy.getOXDInput('Name').setValue('').isInvalid('Required');
        cy.getOXDInput('Name')
          .setValue(this.strings.chars30.text)
          .isInvalid('Already exists');
      });
    });

    it('cancel button behaviour on add membership form', function () {
      cy.loginTo(this.user, '/admin/saveMemberships');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars30.text);
        cy.getOXD('button').contains('Cancel').click();
      });
      cy.wait('@getMembership');
      cy.getOXD('pageTitle').contains('Memberships');
    });
  });

  //Update
  describe('Update Memberships', function () {
    it('Edit Memberships', function () {
      cy.task('db:restore', {name: 'Membership'});
      cy.loginTo(this.user, '/admin/saveMemberships/1');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').clear().type('Updated Name');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@updateMembership');
      cy.toast('success', 'Successfully Updated');
      // eslint-disable-next-line cypress/no-unnecessary-waiting
      cy.wait(3000);
    });
    it('cancel button behaviour on edit membership', function () {
      cy.task('db:restore', {name: 'Membership'});
      cy.loginTo(this.user, '/admin/saveMemberships/1');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').clear().type('Updated Membership Name');
        cy.getOXD('button').contains('Cancel').click();
      });
      cy.wait('@getMembership');
      cy.getOXD('pageTitle').contains('Memberships');
    });
  });

  //Delete
  describe('Delete Membership', function () {
    it('Delete a Single Membership', function () {
      cy.task('db:restore', {name: 'Membership'});
      cy.loginTo(this.user, '/admin/membership');
      cy.wait('@getMembership');
      cy.get(
        '.oxd-table-body > :nth-child(1) .oxd-table-cell-actions > :nth-child(1)',
      ).click();
      cy.getOXD('button').contains('Yes, Delete').click();
      cy.wait('@getMembership');
      cy.toast('success', 'Successfully Deleted');
    });
    it('Bulk Delete Membership', function () {
      cy.task('db:restore', {name: 'Membership'});
      cy.loginTo(this.user, '/admin/membership');
      cy.wait('@getMembership');
      cy.get('.oxd-table-header .oxd-checkbox-input').click();
      cy.getOXD('button').contains('Delete Selected').click();
      cy.getOXD('button').contains('Yes, Delete').click();
      cy.wait('@getMembership');
      cy.toast('success', 'Successfully Deleted');
    });
  });
});
