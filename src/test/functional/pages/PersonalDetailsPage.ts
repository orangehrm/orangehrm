import { expect } from '@playwright/test';
import { Locator, Page } from 'playwright';

import { UserData } from '../data';

import { BasePage } from './BasePage';

export class PersonalDetailsPage extends BasePage {
  readonly pageHeader: Locator;
  readonly smokerCheckbox: Locator;
  readonly saveButton: Locator;

  constructor(page: Page) {
    super(page);
    this.pageHeader = page.getByRole('heading', { name: 'Personal Details' });
    this.smokerCheckbox = page.locator(
      'xpath=//span[contains(@class,"oxd-checkbox-input--active")]',
    );
    this.saveButton = page.locator('form').filter({ hasText: 'Save' }).getByRole('button').first();
  }

  public async assertIfPersonalDetailsPageIsOpened(): Promise<void> {
    await expect(this.pageHeader).toBeVisible();
  }

  public async fillNewEmployeePersonalDetails(userData: UserData): Promise<void> {
    console.log(userData.personalDetails.nickname);
    await this.getTextboxByTextLabel('Nickname').fill(userData.personalDetails.nickname);
    // await this.getTextboxByTextLabel('Other Id').fill(userData.personalDetails.otherId);
    // await this.getTextboxByTextLabel("Driver's License Number").fill(
    //   userData.personalDetails.driverLicenseNumber,
    // );
    // await this.getDatePickerByLabel('License Expiry Date').fill(
    //   userData.personalDetails.licenseExpiryDate,
    // );
    // await this.getDatePickerByLabel('Date of Birth').fill(userData.personalDetails.dateOfBirth);
    // await this.chooseOptionFromDropdown('Nationality', userData.personalDetails.nationality);
    // await this.chooseOptionFromDropdown('Martial Status', userData.personalDetails.maritalStatus);
    // await this.getRadiobuttonByLabel(userData.personalDetails.genderFemale).click();
    // await this.chooseOptionFromDropdown('Marital Status', userData.personalDetails.maritalStatus);
    // await this.smokerCheckbox.check();
  }

  public async savePersonalDetails(): Promise<void> {
    await this.saveButton.click();
  }
}
