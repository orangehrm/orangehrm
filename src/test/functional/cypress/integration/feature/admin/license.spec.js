describe('Admin - License', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.fixture('chars').as('strings');
    cy.intercept('GET', '**/api/v2/admin/licenses?*').as('getLicense');
    cy.intercept('POST', '**/api/v2/admin/licenses').as('postLicense');
    cy.intercept('PUT', '**/api/v2/admin/licenses/*').as('updateLicense');
    cy.intercept('DELETE', '**/api/v2/admin/licenses').as('deleteLicense');
    cy.fixture('user').then(({admin}) => {
      this.user = admin;
    });
  });

  // Read
  describe('List License', function () {
    it('Licen list is loaded', function () {
      cy.loginTo(this.user, '/admin/viewLicenses');
      cy.wait('@getLicense');
      cy.toast('info', 'No Records Found');
    });
  });

  // Create
  describe('create license', function () {
    it('add licence', function () {
      cy.loginTo(this.user, '/admin/saveLicenses');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars50.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@postLicense');
      cy.toast('success', 'Successfully Saved');
      cy.task('db:snapshot', {name: 'Licens'});
    });

    it('checkin length validation and required validation on license', function () {
      cy.task('db:restore', {name: 'Licens'});
      cy.loginTo(this.user, '/admin/saveLicenses');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name')
          .setValue(this.strings.chars120.text)
          .isInvalid('Should not exceed 100 characters');
        cy.getOXDInput('Name').setValue('').isInvalid('Required');
        cy.getOXDInput('Name')
          .setValue(this.strings.chars50.text)
          .isInvalid('Already exists');
      });
    });

    it('cancel button behaviour on add licens form', function () {
      cy.loginTo(this.user, '/admin/saveLicenses');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars50.text);
        cy.getOXD('button').contains('Cancel').click();
      });
      cy.wait('@getLicense');
      cy.getOXD('pageTitle').contains('Licenses');
    });
  });

  //Update
  describe('Update Licens', function () {
    it('Edit Licecs', function () {
      cy.task('db:restore', {name: 'Licens'});
      cy.loginTo(this.user, '/admin/saveLicenses/1');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name')
          .clear()
          .type('Updated the licens name with this text');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@updateLicense');
      cy.toast('success', 'Successfully Updated');
    });
    it('cancel button behaviour on edit licens', function () {
      cy.task('db:restore', {name: 'Licens'});
      cy.loginTo(this.user, '/admin/saveLicenses/1');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name')
          .clear()
          .type('Updated the licens name with this text');
        cy.getOXD('button').contains('Cancel').click();
      });
      cy.wait('@getLicense');
      cy.getOXD('pageTitle').contains('Licenses');
    });
  });

  //Delete
  describe('Delete License', function () {
    it('Delete a Single Licens', function () {
      cy.task('db:restore', {name: 'Licens'});
      cy.loginTo(this.user, '/admin/viewLicenses');
      cy.wait('@getLicense');
      cy.get(
        '.oxd-table-body > :nth-child(1) .oxd-table-cell-actions > :nth-child(1)',
      ).click();
      cy.getOXD('button').contains('Yes, Delete').click();
      cy.wait('@getLicense');
      cy.toast('success', 'Successfully Deleted');
    });
    it('Bulk Delete License', function () {
      cy.task('db:restore', {name: 'Licens'});
      cy.loginTo(this.user, '/admin/viewLicenses');
      cy.wait('@getLicense');
      cy.get('.oxd-table-header .oxd-checkbox-input').click();
      cy.getOXD('button').contains('Delete Selected').click();
      cy.getOXD('button').contains('Yes, Delete').click();
      cy.wait('@getLicense');
      cy.toast('success', 'Successfully Deleted');
    });
  });
});
