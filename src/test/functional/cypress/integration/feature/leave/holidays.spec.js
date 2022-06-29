describe('Leave - Holidays', function () {
  beforeEach(function () {
    cy.task('db:reset');
    cy.fixture('chars').as('strings');
    cy.intercept(
      'GET',
      '**/api/v2/leave/holidays?fromDate=2022-01-01&toDate=2022-12-31',
    ).as('getHoliday');
    cy.intercept('PUT', '**/api/v2/leave/leave-period').as('putLeavePeriod');
    cy.intercept('GET', '**/api/v2/leave/leave-period').as('getLeavePeriod');
    cy.intercept('POST', '**/api/v2/leave/holidays').as('postHolidays');
    cy.intercept('PUT', '**/api/v2/leave/holidays/*').as('putHolidays');
    cy.intercept('DELETE', '**/api/v2/leave/holidays').as('deleteHolidays');
    cy.fixture('user').then(({admin}) => {
      this.user = admin;
    });
  });

  describe('create snapshot with leave period', function () {
    it('create snapshot with leave period', function () {
      cy.loginTo(this.user, '/leave/defineLeavePeriod');
      cy.getOXD('form').within(() => {
        cy.wait('@getLeavePeriod');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@putLeavePeriod').then(function () {
        cy.task('db:snapshot', {name: 'leavePeriod'});
      });
    });
  });

  describe('get holidays list', function () {
    it('get holidays list', function () {
      cy.task('db:restore', {name: 'leavePeriod'});
      cy.loginTo(this.user, '/leave/viewHolidayList');
      cy.wait('@getHoliday');
      cy.toast('info', 'No Records Found');
    });
  });

  describe('create snapshot with holiday', function () {
    it.only('create snapshot with holiday', function () {
      cy.loginTo(this.user, '/leave/saveHolidays');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars10.text);
        cy.getOXDInput('Date').type('2021-10-03');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@postHolidays').then(function () {
        cy.task('db:snapshot', {name: 'holidays'});
      });
    });
  });

  describe('add new holiday', function () {
    it('add new holiday and save', function () {
      cy.task('db:restore', {name: 'holidays'});
      cy.loginTo(this.user, '/leave/saveHolidays');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars30.text);
        cy.getOXDInput('Date').type('2021-10-11');
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@postHolidays');
      cy.toast('success', 'Successfully Saved');
    });

    it('add new holiday and cancel', function () {
      cy.loginTo(this.user, '/leave/saveHolidays');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').type(this.strings.chars50.text);
        cy.getOXDInput('Date').type('2021-01-25');
        cy.getOXD('button').contains('Cancel').click();
      });
      cy.wait('@getHoliday');
      cy.getOXD('filterTitle').should('include.text', 'Holidays');
    });

    it('add holiday form validations', function () {
      cy.task('db:restore', {name: 'holidays'});
      cy.loginTo(this.user, '/leave/saveHolidays');
      cy.wait('@getHoliday');
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name')
          .type(this.strings.chars250.text)
          .isInvalid('Should not exceed 200 characters');
        cy.getOXDInput('Name').setValue('').isInvalid('Required');
        //cy.getOXDInput('Date').blur().setValue('').isInvalid('Required');
        cy.getOXDInput('Full Day/Half Day')
          .selectOption('-- Select --')
          .isInvalid('Required');
        cy.getOXDInput('Date').type('2021-10-03').isInvalid('Already exists');
      });
    });
  });

  describe('update holiday', function () {
    it('update holiday', function () {
      cy.task('db:restore', {name: 'leavePeriod'});
      cy.task('db:restore', {name: 'holidays'});
      cy.loginTo(this.user, '/leave/viewHolidayList');
      cy.get('.oxd-table-cell-actions > :nth-child(2)').click();
      cy.getOXD('form').within(() => {
        cy.getOXDInput('Name').clear().type(this.strings.chars50.text);
        cy.getOXD('button').contains('Save').click();
      });
      cy.wait('@putHolidays');
      cy.toast('success', 'Successfully Updated');
    });
  });

  describe('delete holiday', function () {
    it('delete holiday', function () {
      cy.task('db:restore', {name: 'holidays'});
      cy.loginTo(this.user, '/leave/viewHolidayList');
      cy.get('.oxd-table-cell-actions > :nth-child(1)').click();
      cy.get('.oxd-button--label-danger').click();
      cy.wait('@deleteHolidays');
      cy.toast('success', 'Successfully Deleted');
    });
  });
});
