import { Locator, Page } from 'playwright';

import { newEmployeeData } from '../data';
import { randomTxt } from '../utils/helper';

import { BasePage } from './BasePage';

export class PimPage extends BasePage {
  readonly page: Page;
  readonly personalDetailsPage: Page;
  readonly addButton: Locator;
  readonly createLoginDetailsCheckbox: Locator;
  readonly saveButton: Locator;

  constructor(page: Page) {
    super(page);
    this.page = page;
    this.addButton = page.getByRole('button', { name: 'Add' });
    this.createLoginDetailsCheckbox = page
      .locator('div')
      .filter({ hasText: /^Create Login Details$/ })
      .locator('span');
    this.saveButton = page.getByRole('button', { name: 'Save' });
  }

  public async addEmployeeWithLoginCredentials(): Promise<void> {
    await this.addButton.click();
    await this.getTextboxByPlaceholder('First Name').fill(newEmployeeData.newEmployeeName);
    await this.getTextboxByPlaceholder('Middle Name').fill(newEmployeeData.newEmployeeMiddleName);
    await this.getTextboxByPlaceholder('Last Name').fill(newEmployeeData.newEmployeeLastName);
    await this.getTextboxByTextLabel('Employee Id').fill('' + randomTxt);
    await this.createLoginDetailsCheckbox.click();
    await this.getTextboxByTextLabel('Username').fill(newEmployeeData.newEmployeeName + randomTxt);
    await this.getTextboxByTextLabel('Password').fill(newEmployeeData.password);
    await this.getTextboxByTextLabel('Confirm Password').fill(newEmployeeData.password);
    await this.saveButton.click();
  }

  protected getTextboxByTextLabel(label: string): Locator {
    return this.page.getByText(label, { exact: true }).locator('xpath=../..').getByRole('textbox');
  }

  protected getTextboxByPlaceholder(placeholder: string): Locator {
    return this.page.getByPlaceholder(placeholder);
  }
}
