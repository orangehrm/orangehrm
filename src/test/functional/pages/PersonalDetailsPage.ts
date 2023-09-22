import { expect } from '@playwright/test';
import { Locator, Page } from 'playwright';

const randomNumber = (Math.random() * 1000).toString().substring(0, 4);
const nationality = 'Algerian';
const status = 'Married';
export class PersonalDetailsPage {
  readonly page: Page;
  readonly pageHeader: Locator;
  readonly otherId: Locator;
  readonly driverLicenseNumber: Locator;
  readonly selectDropdown: Locator;
  readonly nationalityOption: Locator;
  readonly statusOption: Locator;
  readonly dateOfBirth: Locator;

  constructor(page: Page) {
    this.page = page;
    this.pageHeader = page.getByRole('heading', { name: 'Personal Details' });
    this.otherId = page
      .locator('form div')
      .filter({ hasText: 'Other Id' })
      .getByRole('textbox')
      .nth(1);
    this.driverLicenseNumber = page
      .locator('form div')
      .filter({
        hasText: "Driver's License Number",
      })
      .getByRole('textbox')
      .nth(2);
    this.selectDropdown = page.getByText('-- Select --');
    this.nationalityOption = page.getByRole('option', { name: nationality });
    this.statusOption = page.getByRole('option', { name: status });
  }

  public async personalDetailsPageIsOpened(): Promise<void> {
    // await expect(this.page).toHaveURL('/pim/viewPersonalDetails');
    await expect(this.pageHeader).toBeVisible();
  }

  public async fillPersonalDetails(): Promise<void> {
    await this.otherId.fill(randomNumber);
    await this.driverLicenseNumber.fill(randomNumber);
    await this.fillDatePicker('License Expiry Date', '2020-02-02');
    await this.selectDropdown.first().click();
    await this.nationalityOption.click();
    await this.selectDropdown.click();
    await this.statusOption.click();
    await this.fillDatePicker('Date of Birth', '2000-01-01');
    await this.chooseGenderCheckbox('Female');
  }

  public async fillDatePicker(text: string, date: string): Promise<void> {
    await this.page
      .locator('form div')
      .filter({
        hasText: text,
      })
      .getByPlaceholder('yyyy-mm-dd')
      .fill(date);
  }

  public async chooseGenderCheckbox(label: string): Promise<void> {
    await this.page.locator('label').filter({ hasText: label }).locator('span').click();
  }
}
