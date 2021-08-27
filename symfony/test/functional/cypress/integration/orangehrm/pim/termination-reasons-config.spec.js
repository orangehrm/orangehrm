import adminUser from '../../../fixtures/admin.json';
import viewport from '../../../fixtures/viewport.json';
import pimterminationreasons from '../../../fixtures/pim-termination-reasons-config.json';
import TerminationReasonPage from '../../../support/page_Objects/pim/termination-reasons-configPage';

describe('Termination Reason Configurations',() =>{
    const terminationReasonsPage = new TerminationReasonPage();
    beforeEach(()=>{
        cy.login(adminUser.admin.userName, adminUser.admin.password);
        cy.viewport(viewport.viewport1.width, viewport.viewport1.height);
        cy.visit('/pim/viewTerminationReasons');
    })
    afterEach(()=>{
        cy.visit('/pim/viewTerminationReasons');
    })
    it('Verify whether all the fields are available in termination reasons screen',()=>{
        terminationReasonsPage.returnTblHeaders().eq(1).should('contain',pimterminationreasons.TC01.Tbl_Headers.Header_Name)
        terminationReasonsPage.returnTblHeaders().eq(2).should('contain',pimterminationreasons.TC01.Tbl_Headers.Header_Action)
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.returnTreminationReasonNameInputField().should('be.visible').and('be.enabled')
        terminationReasonsPage.returnTitleOfAddEditTerminationWizard().should('contain',pimterminationreasons.TC01.Add_Edit_Headers.Wizard_Header_Add)
        cy.visit('/pim/viewTerminationReasons')
        terminationReasonsPage.returnTbleIndex(0).then(row =>{
            cy.wrap(row).find('.oxd-table-cell').eq(1).invoke('text').then(val=>{
                terminationReasonsPage.selectRecordToEdit(row)
                terminationReasonsPage.returnTreminationReasonNameInputField().should('be.visible').and('be.enabled')
                terminationReasonsPage.returnTitleOfAddEditTerminationWizard().should('contain',pimterminationreasons.TC01.Add_Edit_Headers.Wizard_Header_Edit)
                terminationReasonsPage.returnTreminationReasonNameInputField().should('have.value',val)
            })
        })
    })
    it('Verify admin ability to add new termination reasons, Toast Message and Row count', () =>{
        terminationReasonsPage.returnAllTbleRecords().then(row =>{
            let rowCount = row.length;
            terminationReasonsPage.clickAddTerminationReasonButton();
            terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC02.TerminationReason)
            terminationReasonsPage.clickSaveButton()
            terminationReasonsPage.returnSuccessToastHeader().should('contain',pimterminationreasons.SuccessMessages.successToastHeader)
            terminationReasonsPage.returnSuccessToastMessage().should('contain',pimterminationreasons.SuccessMessages.successToastForSave)
            terminationReasonsPage.returnAllTbleRecords().should('have.length',(rowCount+1))
            terminationReasonsPage.deleteSelectedItrmFromTheList(pimterminationreasons.TC02.TerminationReason)
        })
    })
    it('Verify maximun text length validation',() =>{
        terminationReasonsPage.clickAddTerminationReasonButton();
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC03.InputValueWith100Chars)
        terminationReasonsPage.clickSaveButton()
        terminationReasonsPage.returnFieldValidationElement().should('contain',pimterminationreasons.TC03.ValidationMessage)
        cy.visit('/pim/viewTerminationReasons');
    })
    it('Verify admin adding an already existing termination reasons name to add field',()=>{
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC04.TerminationReason)
        terminationReasonsPage.clickSaveButton()
        cy.get('.orangehrm-container',{timeout: 100000}).should('be.visible')
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC04.TerminationReason)
        terminationReasonsPage.clickSaveButton()
        terminationReasonsPage.returnFieldValidationElement().should('contain',pimterminationreasons.TC04.ValidationMessage)
        cy.visit('/pim/viewTerminationReasons');
        cy.get('.orangehrm-container',{timeout: 100000}).should('be.visible')
        terminationReasonsPage.deleteSelectedItrmFromTheList(pimterminationreasons.TC04.TerminationReason)
    })
    it('Verify add termination reasons field by adding different characters.',()=>{
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC05.TerminationReason)
        terminationReasonsPage.clickSaveButton()
        terminationReasonsPage.returnSuccessToastHeader().should('contain',pimterminationreasons.SuccessMessages.successToastHeader)
        terminationReasonsPage.returnSuccessToastMessage().should('contain',pimterminationreasons.SuccessMessages.successToastForSave)
        cy.get('.orangehrm-container',{timeout: 100000}).should('be.visible')
        terminationReasonsPage.deleteFirstElementOfList()
        cy.get('.orangehrm-container',{timeout: 100000}).should('be.visible')
    })
    it('Verify admin adding a deleted termination reasons back to the system',()=>{
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC06.TerminationReason)
        terminationReasonsPage.clickSaveButton()
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        terminationReasonsPage.deleteSelectedItrmFromTheList(pimterminationreasons.TC06.TerminationReason)
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC06.TerminationReason)
        terminationReasonsPage.clickSaveButton()
        terminationReasonsPage.returnSuccessToastHeader().should('contain',pimterminationreasons.SuccessMessages.successToastHeader)
        terminationReasonsPage.returnSuccessToastMessage().should('contain',pimterminationreasons.SuccessMessages.successToastForSave)
        cy.get('.orangehrm-container',{timeout: 100000}).should('be.visible')
        terminationReasonsPage.deleteSelectedItrmFromTheList(pimterminationreasons.TC06.TerminationReason)
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
    })
    it('Verify admin entering termination reasons name and clicking on the cancel button',()=>{
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC07.TerminationReason)
        terminationReasonsPage.clickCancelButton()
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        terminationReasonsPage.returnAllTbleRecords().each(tblRecords =>{
            terminationReasonsPage.returnAllTerminationReasonNamesInTable(tblRecords).invoke('text').then(textVal=>{
                if(textVal=='Test Cancel Button'){
                    expect(true).to.equal(false)
                }
            })
        })
    })
    it('Verify clicking save without entering termination reasons name',()=>{
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.clickSaveButton()
        terminationReasonsPage.returnFieldValidationElement().should('contain',pimterminationreasons.TC08.ValidationMessage)
    })
    it('Verify admin ability to edit termination reasons and validate the success Toast message',()=>{
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC09.TerminationReasonToEdit)
        terminationReasonsPage.clickSaveButton()
        cy.get('.orangehrm-container',{timeout: 100000}).should('be.visible')
        terminationReasonsPage.editSelectedItrmFromTheList(pimterminationreasons.TC09.TerminationReasonToEdit)
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC09.NewTerminationReason)
        terminationReasonsPage.clickSaveButton()
        terminationReasonsPage.returnSuccessToastHeader().should('contain',pimterminationreasons.SuccessMessages.successToastHeader)
        terminationReasonsPage.returnSuccessToastMessage().should('contain',pimterminationreasons.SuccessMessages.successToastForUpdate)
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        terminationReasonsPage.deleteSelectedItrmFromTheList(pimterminationreasons.TC09.NewTerminationReason)
    })
    it('Verify admin editing to an already existing termination reasons name.',() =>{
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC10.TerminationReasonToEdit)
        terminationReasonsPage.clickSaveButton()
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC10.NewTerminationReason)
        terminationReasonsPage.clickSaveButton()
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        terminationReasonsPage.editSelectedItrmFromTheList(pimterminationreasons.TC10.NewTerminationReason)
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC10.TerminationReasonToEdit)
        terminationReasonsPage.clickSaveButton()
        terminationReasonsPage.returnFieldValidationElement().should('contain',pimterminationreasons.TC10.validationMessage)
        cy.visit('/pim/viewTerminationReasons')
        terminationReasonsPage.deleteSelectedItrmFromTheList(pimterminationreasons.TC10.TerminationReasonToEdit)
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        terminationReasonsPage.deleteSelectedItrmFromTheList(pimterminationreasons.TC10.NewTerminationReason)
    })
    it('Verify admin removing the termination reasons name when editing', ()=>{
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC11.TerminationReason)
        terminationReasonsPage.clickSaveButton()
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        terminationReasonsPage.editSelectedItrmFromTheList(pimterminationreasons.TC11.TerminationReason)
        terminationReasonsPage.fillTerminationReasoname('null')
        terminationReasonsPage.clickSaveButton()
        terminationReasonsPage.returnFieldValidationElement().should('contain',pimterminationreasons.TC11.ValidationMessage)
        cy.visit('/pim/viewTerminationReasons');
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        terminationReasonsPage.deleteSelectedItrmFromTheList(pimterminationreasons.TC11.TerminationReason)
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
    })
    it('Verify admin ability to delete a single termination reasons, Delete confirmation modal messages and success Toast Messge',()=>{
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC12.TerminationReason)
        terminationReasonsPage.clickSaveButton()
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        terminationReasonsPage.clickDeleteButtonInParticularRow(pimterminationreasons.TC12.TerminationReason)
        terminationReasonsPage.returnDeleteConfirmationMessageHeader().should('contain',pimterminationreasons.DeleteModal.deleteModalHeader)
        terminationReasonsPage.returnDeleteConfirmationMessage().should('contain',pimterminationreasons.DeleteModal.deleteModalMessage)
        terminationReasonsPage.clickDeleteConfirmButtonInDeleteModal()
        terminationReasonsPage.returnSuccessToastHeader().should('contain',pimterminationreasons.SuccessMessages.successToastHeader)
        terminationReasonsPage.returnSuccessToastMessage().should('contain',pimterminationreasons.SuccessMessages.successToastForDelete)
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        terminationReasonsPage.returnAllTbleRecords().each(tblRecords =>{
            terminationReasonsPage.returnAllTerminationReasonNamesInTable(tblRecords).invoke('text').then(textVal=>{
                if(textVal==pimterminationreasons.TC12.TerminationReason){
                    expect(true).to.equal(false)
                }
            })
        })
    })
    it('Verify admin ability to delete multiple termination reasons, , Delete confirmation modal messages and success Toast Messge',()=>{
        const terminationReasonsArray = [pimterminationreasons.TC13.TerminationReason1,pimterminationreasons.TC13.TerminationReason2,pimterminationreasons.TC13.TerminationReason3]
        cy.wrap(terminationReasonsArray).each(item =>{
            terminationReasonsPage.clickAddTerminationReasonButton()
            terminationReasonsPage.fillTerminationReasoname(item)
            terminationReasonsPage.clickSaveButton()
            cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        })
        cy.wrap(terminationReasonsArray).each(item =>{
            terminationReasonsPage.selectItemsFromTheLits(item)
        })
        terminationReasonsPage.clickDeleteSelectedItemFromTheListButton()
        terminationReasonsPage.returnDeleteConfirmationMessageHeader().should('contain',pimterminationreasons.DeleteModal.deleteModalHeader)
        terminationReasonsPage.returnDeleteConfirmationMessage().should('contain',pimterminationreasons.DeleteModal.deleteModalMessage)
        terminationReasonsPage.clickDeleteConfirmButtonInDeleteModal()
        terminationReasonsPage.returnSuccessToastHeader().should('contain',pimterminationreasons.SuccessMessages.successToastHeader)
        terminationReasonsPage.returnSuccessToastMessage().should('contain',pimterminationreasons.SuccessMessages.successToastForDelete)
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        cy.wrap(terminationReasonsArray).each(item=>{
            terminationReasonsPage.returnAllTbleRecords().each(tblRecords =>{
                terminationReasonsPage.returnAllTerminationReasonNamesInTable(tblRecords).invoke('text').then(textVal=>{
                    if(textVal==item){
                        expect(true).to.equal(false)
                    }
                })
            })
        })
        

    })
})
