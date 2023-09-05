describe('Admin - Skills', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.fixture('chars').as('strings');
    cy.intercept('GET', '**/api/v2/admin/skills?*').as('getSkills');
    cy.intercept('POST', '**/api/v2/admin/skills').as('postSkills');
    cy.intercept('PUT', '**/api/v2/admin/skills/*').as('updateSkills');
    cy.intercept('DELETE', '**/api/v2/admin/skills').as('deleteSkills');
    cy.fixture('user').then(({admin}) => {
      this.user = admin;
    });
  });

  // Read
  describe('List Skills', function () {
    it('Skills list is loaded', function () {
      cy.loginTo(this.user, '/admin/viewSkills');
      cy.wait('@getSkills');
      cy.toast('info', 'No Records Found');
    });
  });

  // Create
  describe('create skills', function () {
    it('add skills', function () {
      cy.loginTo(this.user, '/admin/saveSkills');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars50.text);
        cy.getOXDInput('Description').type(this.strings.chars120.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@postSkills');
      cy.toast('success', 'Successfully Saved');
      cy.task('db:snapshot', {name: 'Skills'});
    });

    it('add skills form validations should work', function () {
      cy.task('db:restore', {name: 'Skills'});
      cy.loginTo(this.user, '/admin/saveSkills');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name')
          .setValue(this.strings.chars200.text)
          .isInvalid('Should not exceed 120 characters');
        cy.getOXDInput('Name').setValue('').isInvalid('Required');
        cy.getOXDInput('Name')
          .setValue(this.strings.chars50.text)
          .isInvalid('Already exists');
        cy.getOXDInput('Description')
          .setValue(this.strings.chars450.text)
          .isInvalid('Should not exceed 400 characters');
      });
    });

    it('cancel button behaviour on add skills form', function () {
      cy.loginTo(this.user, '/admin/saveSkills');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars50.text);
        cy.getOXD('button').contains('Cancel').click();
      });
      cy.wait('@getSkills');
      cy.getOXD('pageTitle').contains('Skills');
    });
  });

  //Update
  describe('Update Skills', function () {
    it('Edit Skills', function () {
      cy.task('db:restore', {name: 'Skills'});
      cy.loginTo(this.user, '/admin/saveSkills/1');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name')
          .clear()
          .type('Updated the skill name with this text');
        cy.getOXDInput('Description')
          .clear()
          .type('Updated the skil description with this text');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@updateSkills');
      cy.toast('success', 'Successfully Updated');
    });
    it('cancel button behaviour on edit skills', function () {
      cy.task('db:restore', {name: 'Skills'});
      cy.loginTo(this.user, '/admin/saveSkills/1');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name')
          .clear()
          .type('Updated the skill name with this text');
        cy.getOXDInput('Description')
          .clear()
          .type('Updated the skil description with this text');
        cy.getOXD('button').contains('Cancel').click();
      });
      cy.wait('@getSkills');
      cy.getOXD('pageTitle').contains('Skills');
    });
  });

  //Delete
  describe('Delete job Category', function () {
    it('Delete a Single skill', function () {
      cy.task('db:restore', {name: 'Skills'});
      cy.loginTo(this.user, '/admin/viewSkills');
      cy.wait('@getSkills');
      cy.get(
        '.oxd-table-body > :nth-child(1) .oxd-table-cell-actions > :nth-child(1)',
      ).click();
      cy.getOXD('button').contains('Yes, Delete').click();
      cy.wait('@getSkills');
      cy.toast('success', 'Successfully Deleted');
    });
    it('Bulk Delete Skills', function () {
      cy.task('db:restore', {name: 'Skills'});
      cy.loginTo(this.user, '/admin/viewSkills');
      cy.wait('@getSkills');
      cy.get('.oxd-table-header .oxd-checkbox-input').click();
      cy.getOXD('button').contains('Delete Selected').click();
      cy.getOXD('button').contains('Yes, Delete').click();
      cy.wait('@getSkills');
      cy.toast('success', 'Successfully Deleted');
    });
  });
});
