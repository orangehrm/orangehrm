import { Page } from 'playwright';

import { NewEmployeeData } from '../data';
import { randomTxt } from '../utils/helper';

import { BasePage } from './BasePage';

export class PimPage extends BasePage {
  readonly addButton = this.page.getByRole('button', { name: 'Add' });
  public readonly firstNameInput = this.getTextboxByPlaceholder('First Name');
  public readonly middleNameInput = this.getTextboxByPlaceholder('Middle Name');
  public readonly lastNameInput = this.getTextboxByPlaceholder('Last Name');
  public readonly employeeId = this.getTextboxByTextLabel('Employee Id');
  public readonly createLoginDetailsCheckbox = this.getToggleByTextLabel('Create Login Details');
  public readonly usernameInput = this.getTextboxByTextLabel('Username');
  public readonly passwordInput = this.getTextboxByTextLabel('Password');
  public readonly confirmPasswordInput = this.getTextboxByTextLabel('Confirm Password');
  readonly saveButton = this.page.getByRole('button', { name: 'Save' });

  constructor(page: Page) {
    super(page);
  }

  public async addEmployeeWithLoginCredentials(newEmployeeData: NewEmployeeData): Promise<void> {
    await this.addButton.click();
    await this.firstNameInput.fill(newEmployeeData.firstName + randomTxt);
    await this.middleNameInput.fill(newEmployeeData.middleName);
    await this.lastNameInput.fill(newEmployeeData.lastName);
    await this.employeeId.fill(randomTxt);
    await this.createLoginDetailsCheckbox.click();
    await this.usernameInput.fill(newEmployeeData.loginDetail.username + randomTxt);
    await this.passwordInput.fill(newEmployeeData.loginDetail.password);
    await this.confirmPasswordInput.fill(newEmployeeData.loginDetail.password);
    await this.saveButton.click();
  }

  public async navigateToPimTab(tab: PIMTAB): Promise<void> {
    await this.page.getByRole('link', { name: tab }).click();
  }
}

export enum PIMTAB {
  PERSONAL_DETAILS = 'Personal Details',
  CONTACT_DETAILS = 'Contact Details',
}
