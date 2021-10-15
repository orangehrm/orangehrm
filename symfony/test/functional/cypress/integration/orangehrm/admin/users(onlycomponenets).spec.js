import adminUser from '../../../fixtures/admin.json';
import viewport from '../../../fixtures/viewport.json';
import ComponenetsActions from '../core/componenet_actions.spec';
describe('User List Page',() =>{
    const componenetAction = new ComponenetsActions();
    beforeEach(()=>{
        cy.login(adminUser.admin.userName, adminUser.admin.password);
        cy.viewport(viewport.viewport1.width, viewport.viewport1.height);
        cy.visit('/admin/viewSystemUsers');
    })
    it('Search User with all values',function() {
        componenetAction.sendkeys(':nth-child(2) > .oxd-input','Admin')
        componenetAction.selectElementfromDropdown(':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text','Admin')
        componenetAction.selectElementfromMultiSelect('.oxd-autocomplete-text-input > input',"Shenali")
        componenetAction.selectElementfromDropdown(':nth-child(4) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text','Enabled')
        componenetAction.clickOnButton('.oxd-form-actions > .oxd-button--secondary')
        componenetAction.returnTableRowCount('.oxd-table-body .oxd-table-row ').then(rows =>{
            expect(rows.length).to.equal(1)
        })
    })
    it('Search User with User role value',function() {
        componenetAction.selectElementfromDropdown(':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text','ESS')
        componenetAction.selectElementfromDropdown(':nth-child(4) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text','Enabled')
        componenetAction.clickOnButton('.oxd-form-actions > .oxd-button--secondary')
        cy.get('.oxd-table-body',{timeout: 100000}).should('be.visible')
        componenetAction.returnTableRowCount('.oxd-table-body .oxd-table-row ').then(rows =>{
            expect(rows.length).to.equal(5)
        })
    })
})
