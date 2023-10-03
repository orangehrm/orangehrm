import { Locator, Page } from 'playwright';

import { UserData } from '../data';

import { BasePage } from './BasePage';

export class PersonalDetailsPage extends BasePage {
  readonly pageHeader: Locator;
  public readonly nickname: Locator = this.getTextboxByTextLabel('Nickname');
  public readonly otherId: Locator = this.getTextboxByTextLabel('Other Id');
  public readonly driversLicense: Locator = this.getTextboxByTextLabel("Driver's License Number");
  public readonly licenseExpiryDate: Locator = this.getDatePickerByLabel('License Expiry Date');
  public readonly ssnNumber: Locator = this.getTextboxByTextLabel('SSN Number');
  public readonly sinNumber: Locator = this.getTextboxByTextLabel('SIN Number');
  public readonly dateOfBirthDatepicker: Locator = this.getDatePickerByLabel('Date of Birth');
  public readonly nationalityDropdown: Locator = this.getDropdownByLabel('Nationality');
  public readonly maritalStatusDropdown: Locator = this.getDropdownByLabel('Marital Status');
  public readonly genderMaleRadiobutton: Locator = this.getRadiobuttonByLabel('Male');
  public readonly militaryServiceInput: Locator = this.getTextboxByTextLabel('Military Service');
  readonly saveButton: Locator = this.getSaveButtonByHeadingSection('Personal Details');

  constructor(page: Page) {
    super(page);
    this.pageHeader = page.getByRole('heading', { name: 'Personal Details' });
  }

  public async fillNewEmployeePersonalDetails(userData: UserData): Promise<void> {
    await this.nickname.click();
    await this.nickname.fill(userData.personalDetails.nickname);
    await this.otherId.fill(userData.personalDetails.otherId);
    await this.driversLicense.fill(userData.personalDetails.driverLicenseNumber);
    await this.licenseExpiryDate.fill(userData.personalDetails.licenseExpiryDate);
    await this.ssnNumber.fill(userData.personalDetails.ssnNumber);
    await this.sinNumber.fill(userData.personalDetails.sinNumber);
    await this.dateOfBirthDatepicker.fill(userData.personalDetails.dateOfBirth);
    await this.nationalityDropdown.click();
    await this.chooseDropdownOptionByText(userData.personalDetails.nationality);
    await this.maritalStatusDropdown.click();
    await this.chooseDropdownOptionByText(userData.personalDetails.maritalStatus);
    await this.militaryServiceInput.fill(userData.personalDetails.militaryService);
    await this.genderMaleRadiobutton.click();
    await this.page.waitForTimeout(3000);
    await this.saveForm();
    await this.page.waitForTimeout(3000);
  }

  public async saveForm(): Promise<void> {
    await this.saveButton.click();
  }
}
