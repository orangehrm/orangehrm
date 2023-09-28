import { expect } from '@playwright/test';
import { Locator, Page } from 'playwright';

const randomNumber = (Math.random() * 1000).toString().substring(0, 4);

const nationality = 'Algerian',
  nickname = 'NicknameTest',
  maritalStatus = 'Married',
  gender = 'Female',
  smoker = 'Yes',
  bloodType = 'A+';

export class PersonalDetailsPage {
  readonly page: Page;
  readonly pageHeader: Locator;
  readonly selectDropdown: Locator;

  constructor(page: Page) {
    this.page = page;
    this.pageHeader = page.getByRole('heading', { name: 'Personal Details' });
    this.selectDropdown = page.getByText('-- Select --');
  }

  public async personalDetailsPageIsOpened(): Promise<void> {
    await expect(this.pageHeader).toBeVisible();
  }

  public async fillPersonalDetails(): Promise<void> {
    await this.fillTextboxField('Nickname', 3, nickname);
    await this.fillTextboxField('Other Id', 1, randomNumber);
    await this.fillTextboxField("Driver's License Number", 2, randomNumber);
    await this.fillDatePicker('License Expiry Date', '2020-02-02');
    // await this.fillTextboxField('SSN Number', 1, '1234'); //ToFix later
    // await this.fillTextboxField('SIN Number', 1, '1224'); //ToFix later
    await this.fillDatePicker('Date of Birth', '2000-01-01');
    await this.selectDropdown.nth(1).click();
    await this.chooseOptionFromDropdown(maritalStatus);
    await this.clickCheckbox(gender, 'span');
    await this.selectDropdown.first().click();
    await this.chooseOptionFromDropdown(nationality);
    await this.clickCheckbox(smoker, 'i');
    await this.selectDropdown.nth(2).click();
    await this.chooseOptionFromDropdown(bloodType);
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

  public async fillTextboxField(label: string, nth: number, text: string): Promise<void> {
    await this.page
      .locator('form div')
      .filter({ hasText: label })
      .getByRole('textbox')
      .nth(nth)
      .fill(text);
  }

  public async chooseOptionFromDropdown(option: string): Promise<void> {
    await this.page.getByRole('option', { name: option }).click();
  }

  public async clickCheckbox(label: string, locator: string): Promise<void> {
    await this.page.locator('label').filter({ hasText: label }).locator(locator).click();
  }
}
