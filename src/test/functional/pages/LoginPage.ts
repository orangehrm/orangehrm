import { BasePage } from "./BasePage";

export class LoginPage extends BasePage {

  protected readonly userNameInput = this.page.getByPlaceholder("Username");
  protected readonly passwordInput = this.page.getByPlaceholder("Password");
  protected readonly loginButton = this.page.locator('button:has-text("Login")');
  public readonly userNameAfterLogin = this.page.locator(".oxd-userdropdown-name");

    async loginUser(mail: string, password: string): Promise<void> {
        await this.userNameInput.type(mail);
        await this.passwordInput.type( password);
        await this.loginButton.click();
    }
}
