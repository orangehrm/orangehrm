import { Browser, chromium, Locator, Page } from '@playwright/test';

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

  public async navigateToSubPage(subPage: SubPage): Promise<void> {
    await this.page.getByRole('link', { name: subPage }).click();
  }

  protected getTextboxByPlaceholder(placeholder: string): Locator {
    return this.page.getByPlaceholder(placeholder);
  }

  protected getTextboxByTextLabel(label: string): Locator {
    console.log('działam ' + label);
    return this.page.getByText(label, { exact: true }).locator('xpath=../..').getByRole('textbox');
  }

  protected getDatePickerByLabel(label: string): Locator {
    return this.page
      .locator('form div')
      .filter({
        hasText: label,
      })
      .getByPlaceholder('yyyy-mm-dd');
  }

  protected async chooseOptionFromDropdown(label: string, option: string): Promise<void> {
    await this.page
      .getByText(label, { exact: true })
      .locator('xpath=../..')
      .getByText('-- Select --')
      .click();

    await this.page.getByRole('option', { name: option });
  }

  protected getRadiobuttonByLabel(label: string): Locator {
    return this.page.getByText(label, { exact: true }).locator('span');
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
