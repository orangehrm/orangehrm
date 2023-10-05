import { Page } from 'playwright';

import { UserData } from '../data';

import { BasePage } from './BasePage';

export class PersonalDetailsPage extends BasePage {
  public readonly nickname = this.getTextboxByTextLabel('Nickname');
  public readonly otherId = this.getTextboxByTextLabel('Other Id');
  public readonly driversLicense = this.getTextboxByTextLabel("Driver's License Number");
  public readonly licenseExpiryDate = this.getDatePickerByTextLabel('License Expiry Date');
  public readonly ssnNumber = this.getTextboxByTextLabel('SSN Number');
  public readonly sinNumber = this.getTextboxByTextLabel('SIN Number');
  public readonly dateOfBirthDatepicker = this.getDatePickerByTextLabel('Date of Birth');
  public readonly nationalityDropdown = this.getDropdownByTextLabel('Nationality');
  public readonly maritalStatusDropdown = this.getDropdownByTextLabel('Marital Status');
  public readonly genderRadioButtons = this.getRadiobuttonByTextLabel('Gender');
  public readonly militaryServiceInput = this.getTextboxByTextLabel('Military Service');
  public readonly saveButton = this.getSaveButtonByHeadingSection('Personal Details');

  constructor(page: Page) {
    super(page);
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
    await this.genderRadioButtons.setChecked(true, { force: true });
    await this.genderRadioButtons
      .getByText(userData.personalDetails.gender, { exact: true })
      .setChecked(true, { force: true });
    await this.saveForm();
  }

  public async saveForm(): Promise<void> {
    await this.saveButton.click();
  }
}
