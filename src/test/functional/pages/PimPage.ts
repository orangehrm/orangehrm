import { Locator, Page } from 'playwright';

import { newEmployeeData } from '../data';

import { BasePage } from './BasePage';
import { PersonalDetailsPage } from './PersonalDetailsPage';

const randomNumber = (Math.random() * 1000).toString().substring(0, 3);
export class PimPage extends BasePage {
  readonly page: Page;
  readonly addButton: Locator;
  readonly nameInput: Locator;
  readonly middleNameInput: Locator;
  readonly lastNameInput: Locator;
  readonly employeeId: Locator;
  readonly createLoginDetailsCheckbox: Locator;
  readonly username: Locator;
  readonly password: Locator;
  readonly passwordConfirmed: Locator;
  readonly saveButton: Locator;
  readonly cancelButton: Locator;
  readonly personalDetailsPage: Page;

  constructor(page: Page) {
    super(page);
    this.page = page;
    this.addButton = page.getByRole('button', { name: 'Add' });
    this.nameInput = page.getByPlaceholder('First Name');
    this.middleNameInput = page.getByPlaceholder('Middle Name');
    this.lastNameInput = page.getByPlaceholder('Last Name');
    this.employeeId = page.locator('form').getByRole('textbox').nth(4);
    this.createLoginDetailsCheckbox = page
      .locator('div')
      .filter({ hasText: /^Create Login Details$/ })
      .locator('span');
    this.username = page.locator(
      'div:nth-child(4) > .oxd-grid-2 > div > .oxd-input-group > div:nth-child(2) > .oxd-input',
    );
    this.password = page.locator('input[type="password"]').first();
    this.passwordConfirmed = page.locator('input[type="password"]').nth(1);
    this.saveButton = page.getByRole('button', { name: 'Save' });
    this.cancelButton = page.getByRole('button', { name: 'Cancel' });
  }

  public async addEmployeeWithLoginCredentails(): Promise<void> {
    await this.addButton.click();
    await this.nameInput.type(newEmployeeData.nameInput + ' ' + randomNumber);
    await this.middleNameInput.type(newEmployeeData.middleNameInput);
    await this.lastNameInput.type(newEmployeeData.lastNameInput);
    await this.employeeId.fill('' + randomNumber);
    // await this.createLoginDetailsCheckbox.click();
    // await this.username.fill(newEmployeeData.username + ' ' + randomNumber);
    // await this.password.fill(newEmployeeData.password);
    // await this.passwordConfirmed.fill(newEmployeeData.password);
    await this.saveButton.click();
  }

  public async fillNewEmployeePersonalDetails(): Promise<void> {
    const personalDetailsPage = new PersonalDetailsPage(this.page);
    await personalDetailsPage.personalDetailsPageIsOpened();
    await personalDetailsPage.fillPersonalDetails();
    // await await this.saveButton.click();
  }
}
