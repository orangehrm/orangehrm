import { expect } from '@playwright/test';
import { Locator, Page } from 'playwright';

import { newEmployeeData } from '../data';

import { BasePage } from './BasePage';

export class PersonalDetailsPage extends BasePage {
  readonly page: Page;
  readonly pageHeader: Locator;
  readonly dropdown: Locator;
  readonly saveButton: Locator;

  constructor(page: Page) {
    super(page);
    this.page = page;
    this.pageHeader = page.getByRole('heading', { name: 'Personal Details' });
    this.dropdown = page.locator('.oxd-select-text');
    this.saveButton = page.locator('form').filter({ hasText: 'Save' }).getByRole('button').first();
  }

  public async assertIfPersonalDetailsPageIsOpened(): Promise<void> {
    await expect(this.pageHeader).toBeVisible();
  }

  public async fillNewEmployeePersonalDetails(): Promise<void> {
    await this.getTextboxByTextLabel('Nickname').fill(newEmployeeData.nickname);
    await this.getTextboxByTextLabel('Other Id').fill(newEmployeeData.otherId);
    await this.getTextboxByTextLabel("Driver's License Number").fill(
      newEmployeeData.driverLicenseNumber,
    );
    await this.getDatePickerByLabel('License Expiry Date').fill(newEmployeeData.licenseExpiryDate);
    await this.getDatePickerByLabel('Date of Birth').fill(newEmployeeData.dateOfBirth);
    await this.dropdown.nth(1).click();
    await this.getDropdownByRole(newEmployeeData.maritalStatus).click();
    await this.getCheckbox(newEmployeeData.gender, 'span').click();
    await this.dropdown.first().click();
    await this.getDropdownByRole(newEmployeeData.nationality).click();
    await this.getCheckbox(newEmployeeData.smoker, 'i').click();
  }

  public async savePersonalDetails(): Promise<void> {
    await this.saveButton.click();
  }

  protected getDatePickerByLabel(label: string): Locator {
    return this.page
      .locator('form div')
      .filter({
        hasText: label,
      })
      .getByPlaceholder('yyyy-mm-dd');
  }

  protected getTextboxByTextLabel(label: string): Locator {
    return this.page.getByText(label, { exact: true }).locator('xpath=../..').getByRole('textbox');
  }

  protected getDropdownByRole(option: string): Locator {
    return this.page.getByRole('option', { name: option });
  }

  protected getCheckbox(label: string, locator: string): Locator {
    return this.page.locator('label').filter({ hasText: label }).locator(locator);
  }
}
