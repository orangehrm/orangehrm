import { Locator, Page } from 'playwright';

import { newEmployeeData } from '../data';

import { BasePage } from './BasePage';
import { PersonalDetailsPage } from './PersonalDetailsPage';

const randomNumber = (Math.random() * 1000).toString().substring(0, 3);
export class PimPage extends BasePage {
  readonly page: Page;
  readonly pimPageButton: Locator;
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
    this.pimPageButton = page.getByRole('link', { name: 'PIM' });
    this.addButton = page.getByRole('button', { name: 'Add' });
    this.nameInput = page.getByPlaceholder('First Name');
    this.middleNameInput = page.getByPlaceholder('Middle Name');
    this.lastNameInput = page.getByPlaceholder('Last Name');
    this.employeeId = page.locator('input:below(:text("Employee Id"))').first();
    this.createLoginDetailsCheckbox = page
      .locator('input:below(:text("Create Login Details"))')
      .first();
    this.username = page.locator('div:has-text(“Username”) + div').first();
    this.passwordConfirmed = page.locator('input[type="password"]').nth(1);
    this.saveButton = page.getByRole('button', { name: 'Save' });
    this.cancelButton = page.getByRole('button', { name: 'Cancel' });
  }

  public async addEmployeeWithLoginCredentials(): Promise<void> {
    await this.addButton.click();
    await this.nameInput.fill(newEmployeeData.newEmployeeName + ' ' + randomNumber);
    await this.middleNameInput.fill(newEmployeeData.newEmployeeMiddleName);
    await this.lastNameInput.fill(newEmployeeData.newEmployeeLastName);
    await this.employeeId.fill('' + randomNumber);
    await this.createLoginDetailsCheckbox.click();
    await this.username.fill(newEmployeeData.newEmployeeName + ' ' + randomNumber);
    await this.password.fill(newEmployeeData.password);
    await this.passwordConfirmed.fill(newEmployeeData.password);
    await this.saveButton.click();
  }

  public async fillNewEmployeePersonalDetails(): Promise<void> {
    const personalDetailsPage = new PersonalDetailsPage(this.page);
    await personalDetailsPage.verifyIfPersonalDetailsPageIsOpened();
    await personalDetailsPage.fillPersonalDetails();
    await this.saveButton.click();
    await this.page.getByText('Success').isVisible();
  }
}
