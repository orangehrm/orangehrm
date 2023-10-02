import { Locator, Page } from 'playwright';

import { NewEmployeeData } from '../data';

import { BasePage } from './BasePage';

export class PimPage extends BasePage {
  readonly personalDetailsPage: Page;
  readonly addButton: Locator;
  readonly createLoginDetailsCheckbox: Locator;
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
    await this.getTextboxByPlaceholder('First Name').fill(newEmployeeData.firstName);
    await this.getTextboxByPlaceholder('Middle Name').fill(newEmployeeData.middleName);
    await this.getTextboxByPlaceholder('Last Name').fill(newEmployeeData.lastName);
    await this.getTextboxByTextLabel('Employee Id').fill(newEmployeeData.employeeId);
    await this.createLoginDetailsCheckbox.click();
    await this.getTextboxByTextLabel('Username').fill(newEmployeeData.loginDetail.username);
    await this.getTextboxByTextLabel('Password').fill(newEmployeeData.loginDetail.password);
    await this.getTextboxByTextLabel('Confirm Password').fill(newEmployeeData.loginDetail.password);
    await this.saveButton.click();
  }
}
