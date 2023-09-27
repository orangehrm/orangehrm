import { BasePage } from "./BasePage";
import { Locator, Page } from 'playwright';

export class LoginPage extends BasePage {
  readonly userNameInput: Locator;
  readonly passwordInput: Locator;
  readonly loginButton: Locator
  readonly userNameAfterLogin: Locator

    
  constructor(page: Page) {
    super(page)
    this.userNameInput = page.locator('[placeholder="Username"]');
    this.passwordInput = page.locator('[placeholder="Password"]');
    this.loginButton = page.locator('button:has-text("Login")');
    this.userNameAfterLogin = page.locator(".oxd-userdropdown-name");
  }

    async loginUser(mail: string, password: string): Promise<void> {
        await this.userNameInput.type(mail);
        await this.passwordInput.type( password);
        await this.loginButton.click();
      }
}
