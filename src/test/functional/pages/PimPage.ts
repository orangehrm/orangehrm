import { Locator, Page } from 'playwright';

export class PimPage {
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

  constructor(page: Page) {
    this.page = page;
    this.addButton = page.getByRole('button', { name: 'Add' });
    this.nameInput = page.getByPlaceholder('First Name');
    this.middleNameInput = page.getByPlaceholder('Middle Name');
    this.lastNameInput = page.getByPlaceholder('Last Name');
    this.employeeId = page.locator('form').getByRole('textbox').nth(4);
    this.createLoginDetailsCheckbox = page.locator('form span');
    this.username = page.locator(
      'div:nth-child(4) > .oxd-grid-2 > div > .oxd-input-group > div:nth-child(2) > .oxd-input',
    );
    this.password = page.locator('input[type="password"]').first();
    this.passwordConfirmed = page.locator('input[type="password"]').nth(1);
    this.saveButton = page.getByRole('button', { name: 'Save' });
    this.cancelButton = page.getByRole('button', { name: 'Cancel' });
  }

  public async addEmployee(): Promise<void> {
    await this.addButton.click();
    await this.nameInput.type('name input 1');
    await this.middleNameInput.type('middle');
    await this.lastNameInput.type('last name');
    await this.employeeId.fill('123456');
    await this.createLoginDetailsCheckbox.click();
    await this.username.fill('username003');
    await this.password.fill('Test02!');
    await this.passwordConfirmed.fill('Test02!');
    // await this.cancelButton.click();
    // await this.saveButton.click();
  }
}
