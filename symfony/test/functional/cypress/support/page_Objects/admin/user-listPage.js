import ComponenetsActions from '../../../integration/orangehrm/core/componenet_actions.spec';

const componenetAction = new ComponenetsActions()
const userNameLocator = ':nth-child(2) > .oxd-input'
const userRoleLocator= ':nth-child(2) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text'
const employeeNameLocator = '.oxd-autocomplete-text-input > input'
const employeeStatusLocator = ':nth-child(4) > .oxd-input-group > :nth-child(2) > .oxd-select-wrapper > .oxd-select-text'
const searchButtonLocator = '.oxd-form-actions > .oxd-button--secondary'

class UserListPage {
    fillSearchwizard(value){
        const array = [...value];
        if(array[0]!='null'){
            componenetAction.sendkeys(userNameLocator,array[0])
        }
        if(array[1]!='null'){
            componenetAction.selectElementfromDropdown(userRoleLocator,array[1])
        }
        if(array[2]!='null'){
            componenetAction.selectElementfromMultiSelect(employeeNameLocator,array[2])
        }
        if(array[3]!='null'){
            componenetAction.selectElementfromDropdown(employeeStatusLocator,array[3])
        }
    }
    clickOnSearchButton(){
        componenetAction.clickOnButton(searchButtonLocator)
    }
    
}
export default UserListPage
