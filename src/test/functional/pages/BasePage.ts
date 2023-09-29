import { Browser, chromium, Page } from '@playwright/test';

import config from '../../playwright.config';

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
    await this.page.goto(config.baseUrl);
    await this.page.waitForURL(config.baseUrl);
  }

  public async navigateToSubPage(pageTab: string): Promise<void> {
    await this.page.getByRole('link', { name: pageTab }).click();
  }
}

export enum SubPage {
  ADMIN = 'Admin',
  PIM = 'PIM',
  LEAVE = 'Leave',
  TIME = 'Time',
  RECRUITMENT = 'Recruitment',
  MY_INFO = 'My Info',
  PERFORMANCE = 'Performance',
  DASHBOARD = 'Dashboard',
  DIRECTORY = 'Directory',
  MAINTENANCE = 'Maintenance',
  CLAIM = 'Claim',
  BUZZ = 'BUZZ',
}
