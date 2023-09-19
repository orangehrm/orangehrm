import { Locator, Page } from 'playwright';

export class PimPage {
  readonly page: Page;
  readonly addButton: Locator;
  readonly nameInput: Locator;
  readonly middleNameInput: Locator;
  readonly lastNameInput: Locator;
  readonly employeeId: Locator;

  constructor(page: Page) {
    this.page = page;
    this.addButton = page.getByRole('button', { name: 'Add' });
    this.nameInput = page.getByPlaceholder('First Name');
    this.middleNameInput = page.getByPlaceholder('Middle Name');
    this.lastNameInput = page.getByPlaceholder('Last Name');
    this.employeeId = page.locator('form').getByRole('textbox').nth(4);
  }

  public async addEmployee(): Promise<void> {
    await this.addButton.click();
    await this.nameInput.type('name input 1');
    await this.middleNameInput.type('middle');
    await this.lastNameInput.type('last name');
    await this.employeeId.type('123456');
  }
}
