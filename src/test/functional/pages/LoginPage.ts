import { BasePage } from "./BasePage";
import { loginPageLocator } from "../locators";

export class LoginPage extends BasePage {

    async loginUser(mail: string, password: string): Promise<void> {
        await this.page.type(loginPageLocator.nameInput, mail);
        await this.page.type(loginPageLocator.passwordInput, password);
        await this.page.click(loginPageLocator.loginButton);
      }
}
