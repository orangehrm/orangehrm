import { BasePage } from './BasePage';

export class LoginPage extends BasePage {

  protected readonly userNameInput = this.page.getByPlaceholder('Username');
  protected readonly passwordInput = this.page.getByPlaceholder('Password');
  protected readonly loginButton = this.page.getByRole('button', { name: 'Login' });

  public chooseDropdownOptionByText(option: string) {
    return this.page.getByRole('banner').getByText(option)
  }

  async loginUser(mail: string, password: string): Promise<void> {
      await this.userNameInput.type(mail);
      await this.passwordInput.type( password);
      await this.loginButton.click();
  }
}
