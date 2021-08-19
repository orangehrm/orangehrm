import user from '../../../fixtures/user.json';
import viewport from '../../../fixtures/viewport.json';
import pimterminationreasons from '../../../fixtures/pim-termination-reasons-config.json';
import {terminationReasonsPage} from '../../../support/page_Objects/pim/termination-reasons-configPage';

describe('Termination Reason Configurations',() =>{
    beforeEach(()=>{
        cy.login(user.admin.userName, user.admin.password);
        cy.viewport(viewport.viewport1.width, viewport.viewport1.height);
        cy.visit('/pim/viewTerminationReasons');
    })
    afterEach(()=>{
        cy.visit('/pim/viewTerminationReasons');
    })
    it('Verify whether all the fields are available in termination reasons screen',()=>{
        terminationReasonsPage.returnTblHeaders().eq(1).should('contain',pimterminationreasons.TC01.tblHeaders.hName)
        terminationReasonsPage.returnTblHeaders().eq(2).should('contain',pimterminationreasons.TC01.tblHeaders.hActions)
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.returnTreminationReasonNameInputField().should('be.visible').and('be.enabled')
        terminationReasonsPage.returnTitleOfAddEditTerminationWizard().should('contain',pimterminationreasons.TC01.addEditHeaders.hAdd)
        cy.visit('/pim/viewTerminationReasons')
        terminationReasonsPage.returnTbleIndex(0).then(row =>{
            cy.wrap(row).find('.oxd-table-cell').eq(1).invoke('text').then(val=>{
                terminationReasonsPage.selectRecordToEdit(row)
                terminationReasonsPage.returnTreminationReasonNameInputField().should('be.visible').and('be.enabled')
                terminationReasonsPage.returnTitleOfAddEditTerminationWizard().should('contain',pimterminationreasons.TC01.addEditHeaders.hEdit)
                terminationReasonsPage.returnTreminationReasonNameInputField().should('have.value',val)
            })
        })
    })
    it('Verify admin ability to add new termination reasons, Toast Message and Row count', () =>{
        terminationReasonsPage.returnAllTbleRecords().then(row =>{
            let rowCount = row.length;
            terminationReasonsPage.clickAddTerminationReasonButton();
            terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC02.terminationReason)
            terminationReasonsPage.clickSaveButton()
            terminationReasonsPage.returnSuccessToastHeader().should('contain',pimterminationreasons.SuccessMessages.successToastHeader)
            terminationReasonsPage.returnSuccessToastMessage().should('contain',pimterminationreasons.SuccessMessages.successToastForSave)
            terminationReasonsPage.returnAllTbleRecords().should('have.length',(rowCount+1))
            terminationReasonsPage.deleteSelectedItrmFromTheList(pimterminationreasons.TC02.terminationReason)
        })
    })
    it('Verify maximun text length validation',() =>{
        terminationReasonsPage.clickAddTerminationReasonButton();
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC03.inputwith100val)
        terminationReasonsPage.clickSaveButton()
        terminationReasonsPage.returnFieldValidationElement().should('contain',pimterminationreasons.TC03.validationMessage)
        cy.visit('/pim/viewTerminationReasons');
    })
    it('Verify admin adding an already existing termination reasons name to add field',()=>{
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC04.terminationReason)
        terminationReasonsPage.clickSaveButton()
        cy.get('.orangehrm-container',{timeout: 100000}).should('be.visible')
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC04.terminationReason)
        terminationReasonsPage.clickSaveButton()
        terminationReasonsPage.returnFieldValidationElement().should('contain',pimterminationreasons.TC04.validationMessage)
        cy.visit('/pim/viewTerminationReasons');
        cy.get('.orangehrm-container',{timeout: 100000}).should('be.visible')
        terminationReasonsPage.deleteSelectedItrmFromTheList(pimterminationreasons.TC04.terminationReason)
    })
    it('Verify add termination reasons field by adding different characters.',()=>{
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC05.terminationReason)
        terminationReasonsPage.clickSaveButton()
        terminationReasonsPage.returnSuccessToastHeader().should('contain',pimterminationreasons.SuccessMessages.successToastHeader)
        terminationReasonsPage.returnSuccessToastMessage().should('contain',pimterminationreasons.SuccessMessages.successToastForSave)
        cy.get('.orangehrm-container',{timeout: 100000}).should('be.visible')
        terminationReasonsPage.deleteFirstElementOfList()
        cy.get('.orangehrm-container',{timeout: 100000}).should('be.visible')
    })
    it('Verify admin adding a deleted termination reasons back to the system',()=>{
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC06.terminationReason)
        terminationReasonsPage.clickSaveButton()
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        terminationReasonsPage.deleteSelectedItrmFromTheList(pimterminationreasons.TC06.terminationReason)
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC06.terminationReason)
        terminationReasonsPage.clickSaveButton()
        terminationReasonsPage.returnSuccessToastHeader().should('contain',pimterminationreasons.SuccessMessages.successToastHeader)
        terminationReasonsPage.returnSuccessToastMessage().should('contain',pimterminationreasons.SuccessMessages.successToastForSave)
        cy.get('.orangehrm-container',{timeout: 100000}).should('be.visible')
        terminationReasonsPage.deleteSelectedItrmFromTheList(pimterminationreasons.TC06.terminationReason)
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
    })
    it('Verify admin entering termination reasons name and clicking on the cancel button',()=>{
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC07.terminationReason)
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
        terminationReasonsPage.returnFieldValidationElement().should('contain',pimterminationreasons.TC08.validationMessage)
    })
    it('Verify admin ability to edit termination reasons and validate the success Toast message',()=>{
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC09.terminationReasonToEdit)
        terminationReasonsPage.clickSaveButton()
        cy.get('.orangehrm-container',{timeout: 100000}).should('be.visible')
        terminationReasonsPage.editSelectedItrmFromTheList(pimterminationreasons.TC09.terminationReasonToEdit)
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC09.terminationReasonNewName)
        terminationReasonsPage.clickSaveButton()
        terminationReasonsPage.returnSuccessToastHeader().should('contain',pimterminationreasons.SuccessMessages.successToastHeader)
        terminationReasonsPage.returnSuccessToastMessage().should('contain',pimterminationreasons.SuccessMessages.successToastForUpdate)
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        terminationReasonsPage.deleteSelectedItrmFromTheList(pimterminationreasons.TC09.terminationReasonNewName)
    })
    it('Verify admin editing to an already existing termination reasons name.',() =>{
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC10.terminationReasonToEdit)
        terminationReasonsPage.clickSaveButton()
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC10.terminationReasonNewName)
        terminationReasonsPage.clickSaveButton()
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        terminationReasonsPage.editSelectedItrmFromTheList(pimterminationreasons.TC10.terminationReasonNewName)
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC10.terminationReasonToEdit)
        terminationReasonsPage.clickSaveButton()
        terminationReasonsPage.returnFieldValidationElement().should('contain',pimterminationreasons.TC10.validationMessage)
        cy.visit('/pim/viewTerminationReasons')
        terminationReasonsPage.deleteSelectedItrmFromTheList(pimterminationreasons.TC10.terminationReasonToEdit)
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        terminationReasonsPage.deleteSelectedItrmFromTheList(pimterminationreasons.TC10.terminationReasonNewName)
    })
    it('Verify admin removing the termination reasons name when editing', ()=>{
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC11.terminationReason)
        terminationReasonsPage.clickSaveButton()
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        terminationReasonsPage.editSelectedItrmFromTheList(pimterminationreasons.TC11.terminationReason)
        terminationReasonsPage.fillTerminationReasoname('null')
        terminationReasonsPage.clickSaveButton()
        terminationReasonsPage.returnFieldValidationElement().should('contain',pimterminationreasons.TC11.validationMessage)
        cy.visit('/pim/viewTerminationReasons');
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        terminationReasonsPage.deleteSelectedItrmFromTheList(pimterminationreasons.TC11.terminationReason)
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
    })
    it('Verify admin ability to delete a single termination reasons, Delete confirmation modal messages and success Toast Messge',()=>{
        terminationReasonsPage.clickAddTerminationReasonButton()
        terminationReasonsPage.fillTerminationReasoname(pimterminationreasons.TC12.terminationReason)
        terminationReasonsPage.clickSaveButton()
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        terminationReasonsPage.clickDeleteButtonInParticularRow(pimterminationreasons.TC12.terminationReason)
        terminationReasonsPage.returnDeleteConfirmationMessageHeader().should('contain',pimterminationreasons.TC12.deleteModalHeader)
        terminationReasonsPage.returnDeleteConfirmationMessage().should('contain',pimterminationreasons.TC12.deleteModalMessage)
        terminationReasonsPage.clickDeleteConfirmButtonInDeleteModal()
        terminationReasonsPage.returnSuccessToastHeader().should('contain',pimterminationreasons.SuccessMessages.successToastHeader)
        terminationReasonsPage.returnSuccessToastMessage().should('contain',pimterminationreasons.SuccessMessages.successToastForDelete)
        cy.get('.orangehrm-container',{timeout: 10000}).should('be.visible')
        terminationReasonsPage.returnAllTbleRecords().each(tblRecords =>{
            terminationReasonsPage.returnAllTerminationReasonNamesInTable(tblRecords).invoke('text').then(textVal=>{
                if(textVal==pimterminationreasons.TC12.terminationReason){
                    expect(true).to.equal(false)
                }
            })
        })
    })
    it('Verify admin ability to delete multiple termination reasons, , Delete confirmation modal messages and success Toast Messge',()=>{
        const terminationReasonsArray = [pimterminationreasons.TC13.terminationReason1,pimterminationreasons.TC13.terminationReason2,pimterminationreasons.TC13.terminationReason3]
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
        terminationReasonsPage.returnDeleteConfirmationMessageHeader().should('contain',pimterminationreasons.TC13.deleteModalHeader)
        terminationReasonsPage.returnDeleteConfirmationMessage().should('contain',pimterminationreasons.TC13.deleteModalMessage)
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