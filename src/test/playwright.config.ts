import { PlaywrightTestConfig } from '@playwright/test';

// Define your custom configuration properties
interface CustomConfig extends PlaywrightTestConfig {
  baseUrl: string;
}

const config: CustomConfig = {
  projects: [
    {
      name: 'desktop-chromium',
      use: {
        headless: false,
        viewport: { width: 1280, height: 720 },
        screenshot: 'only-on-failure',
        video: 'retain-on-failure',
      },
    },
  ],
  reporter: 'html',
  outputDir: 'test-results',
  expect: {
    // Maximum time expect() should wait for the condition to be met.
    timeout: 5000,

    toHaveScreenshot: {
      // An acceptable amount of pixels that could be different, unset by default.
      maxDiffPixels: 10,
    },

    toMatchSnapshot: {
      // An acceptable ratio of pixels that are different to the
      // total amount of pixels, between 0 and 1.
      maxDiffPixelRatio: 0.1,
    },
  },
  baseUrl: 'http://localhost:8888/web/index.php/auth/login'
};

export default config;
