import { Page, Browser, chromium, Locator } from "@playwright/test";
import config from "../../playwright.config";

export class BasePage {
  private browser: Browser | null = null;
  protected page: Page;

  constructor(page: Page) {
    this.page = page;
  }

  async initialize(): Promise<void> {
    this.browser = await chromium.launch();
  }

  async close(): Promise<void> {
    if (this.browser) {
      await this.browser.close();
    }
  }

  async navigateToMainPage(): Promise<void> {
    const navigationPromise = this.page.waitForNavigation();
    await this.page.goto(config.baseUrl);
    await navigationPromise;
  }

  async navigateToSubPage(locator: string): Promise<void> {
    await this.page.locator(locator).click();
  }
}
