import { Page, Browser, chromium } from "@playwright/test";
import { userTestData } from "../data";

export class BasePage {
  private browser: Browser;
  protected page: Page;

  constructor(page: Page) {
    this.page = page;
    this.browser = this.browser;
  }

  async initialize(): Promise<void> {
    this.browser = await chromium.launch();
  }

  async close(): Promise<void> {
    await this.page.close();
  }

  async navigateToMainPage(): Promise<void> {
    const navigationPromise = this.page.waitForNavigation();
    await this.page.goto(userTestData.host);
    await navigationPromise;
  }
}
