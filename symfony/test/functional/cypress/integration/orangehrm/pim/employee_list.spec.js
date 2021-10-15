import adminUser from '../../../fixtures/admin.json';
import viewport from '../../../fixtures/viewport.json';
import ComponenetsActions from '../core/componenet_actions.spec';
describe('Termination Reason Configurations',() =>{
    const componenetAction = new ComponenetsActions();
    beforeEach(()=>{
        cy.login(adminUser.admin.userName, adminUser.admin.password);
        cy.viewport(viewport.viewport1.width, viewport.viewport1.height);
        cy.visit('/pim/viewEmployeeList');
    })
    it('Test', ()=>{
        console.log("Test")
    })
    it.only('Fill Employee information wizard',function() {
        componenetAction.sendkeys(':nth-child(2) > .oxd-input','TestValue')
        componenetAction.selectElementfromDropdown(':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text','Admin')
        componenetAction.selectElementfromMultiSelect('.oxd-autocomplete-text-input > input',"Linda")
        componenetAction.selectElementfromDropdown(':nth-child(4) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text','Enabled')
    })
})