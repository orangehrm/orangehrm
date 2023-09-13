import { PlaywrightTestConfig } from '@playwright/test';

const config: PlaywrightTestConfig = {
  projects: [
    {
      name: 'desktop-chromium',
      use: {         
        launchOptions: {
            headless: false
      }, },
    },
  ],
};

export default config;
