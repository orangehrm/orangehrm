import { Locator, Page } from 'playwright';

import { NewEmployeeData } from '../data';
import { randomTxt } from '../utils/helper';

import { BasePage } from './BasePage';

export class PimPage extends BasePage {
  readonly addButton: Locator;
  public readonly firstNameInput: Locator = this.getTextboxByPlaceholder('First Name');
  public readonly middleNameInput: Locator = this.getTextboxByPlaceholder('Middle Name');
  public readonly lastNameInput: Locator = this.getTextboxByPlaceholder('Last Name');
  public readonly employeeId: Locator = this.getTextboxByTextLabel('Employee Id');
  public readonly createLoginDetailsCheckbox: Locator;
  public readonly usernameInput: Locator = this.getTextboxByTextLabel('Username');
  public readonly passwordInput: Locator = this.getTextboxByTextLabel('Password');
  public readonly confirmPasswordInput: Locator = this.getTextboxByTextLabel('Confirm Password');
  readonly saveButton: Locator;

  constructor(page: Page) {
    super(page);
    this.addButton = page.getByRole('button', { name: 'Add' });
    this.createLoginDetailsCheckbox = page
      .locator('div')
      .filter({ hasText: /^Create Login Details$/ })
      .locator('span');
    this.saveButton = page.getByRole('button', { name: 'Save' });
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
