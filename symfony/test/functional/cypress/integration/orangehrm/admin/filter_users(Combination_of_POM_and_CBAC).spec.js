import adminUser from '../../../fixtures/admin.json';
import viewport from '../../../fixtures/viewport.json';
import UserListPage from '../../../support/page_Objects/admin/user-listPage';
// import ComponenetsActions from '../core/componenet_actions.spec';
describe('User List Page',() =>{
    const userListPage = new UserListPage();
    // const componenetAction = new ComponenetsActions();
    beforeEach(()=>{
        cy.login(adminUser.admin.userName, adminUser.admin.password);
        cy.viewport(viewport.viewport1.width, viewport.viewport1.height);
        cy.visit('/admin/viewSystemUsers');
    })
    it('Search User with all values',function() {
        const detailsArray = ['Admin','Admin','Shenali','Enabled']
        userListPage.fillSearchwizard(detailsArray)
        userListPage.clickOnSearchButton();
        componenetAction.returnTableRowCount('.oxd-table-body .oxd-table-row ').then(rows =>{
            expect(rows.length).to.equal(1)
        })
    })
    it('Search User with User role value',function() {
        const detailsArray2 = ['null','ESS','null','Enabled']
        userListPage.fillSearchwizard(detailsArray2)
        userListPage.clickOnSearchButton();
        componenetAction.returnTableRowCount('.oxd-table-body .oxd-table-row ').then(rows =>{
            expect(rows.length).to.equal(5)
        })
    })
})
