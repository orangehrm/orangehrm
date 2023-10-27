import { BasePage, SubPage, Labels } from "./BasePage";
import { adminUserTestData, newEmployeeTestData } from "../data";
import { Locator } from "@playwright/test";

export class PimPage extends BasePage {
  protected readonly sharePhotosButton = this.page.getByRole("button", {
    name: "Share Photos",
  });
  protected readonly addEmployeeButton = this.page.getByRole("button", {
    name: "Add",
  });
  protected readonly firstNameInput = this.page.getByPlaceholder("First Name");
  protected readonly middleNameInput =
    this.page.getByPlaceholder("Middle Name");
  protected readonly lastNameInput = this.page.getByPlaceholder("Last Name");
  protected readonly saveUserButton = this.page.getByRole("button", {
    name: "Save",
  });
  public readonly confirmDeleteButton = this.page.getByRole("button", {
    name: "Yes, Delete",
  });
  public readonly multiplyDelete = this.page.getByRole("button", {
    name: "Delete Selected",
  });
  protected readonly employeeNameInput = this.page
    .getByPlaceholder("Type for hints...")
    .first();
  protected readonly employeeIdInput = this.page.getByRole("textbox").nth(2);
  protected readonly searchEmployeeButton = this.page.getByRole("button", {
    name: "Search",
  });
  public readonly resetButton = this.page.getByRole("button", {
    name: "Reset",
  });

  public getLocatorByRandomNewEmplyeeName(randomNewEmpoyeeName: string):Locator {
    return this.page.locator(`.oxd-table-cell:has(:text("${randomNewEmpoyeeName}"))`);
  } 
  
  

  // public getLocatorByRandomNewEmplyeeName(
  //   randomNewEmployeeName: string
  // ): Locator {
  //   return this.page.getByText(randomNewEmployeeName)
  // }
  // let's keep it in codebase for poor times


  // public getTrashBinByRandomEmployeeName(randomNewEmployeeName: string):Locator {
  //   return this.page.locator(`.oxd-table-card:has(:text("${randomNewEmployeeName}")) .oxd-icon.bi-trash`);
  // } 
  // this also works, let's keep it in codebase for poor times

  public getTrashBinByRandomEmployeeName(
    randomNewEmployeeName: string
  ): Locator {
    return this.page
      .getByText(randomNewEmployeeName)
      .locator("xpath=../..")
      .getByRole("button")
      .locator(".bi-trash");
  }

  // public  getEditIconByRandomEmployeeName(randomNewEmpoyeeName: string): Locator {
  //   return this.page.locator( `.oxd-table-card:has(:text("${randomNewEmpoyeeName}")) .oxd-icon.bi-pencil-fill`);
  // } 
  //this also works, let's keep it in codebase for poor times

  public getEditIconByRandomEmployeeName(randomNewEmployeeName: string): Locator {
    return this.page
    .getByText(randomNewEmployeeName)
    .locator('xpath=../..')
    .getByRole('button')
    .locator('.bi-pencil-fill')
  }

  // public  getCheckIconByRandomEmployeeName(randomNewEmpoyeeName: string):Locator {
  //   return this.page.locator(`.oxd-table-card:has(:text("${randomNewEmpoyeeName}")) .oxd-checkbox-input-icon`);
  // } 
  // this also works, let's keep it in codebase for poor times

  public getCheckIconByRandomEmployeeName(
    randomNewEmployeeName: string
  ): Locator {
    return this.page
      .getByText(randomNewEmployeeName)
      .locator("xpath=../..")
      .locator("span")
  }

  public getInputByLabelText(label: string): Locator {
    return this.page
      .getByText(label, { exact: true })
      .locator("xpath=../..")
      .locator("input");
  }

  public async addEmployee(firstRandomName: string, bySubtub?: boolean | null) {
    if (bySubtub === true) {
      await this.navigateToSubPage(SubPage.ADD_EMPLOYEE);
    } else {
      await this.addEmployeeButton.click();
    }

    await this.firstNameInput.type(firstRandomName);
    await this.middleNameInput.type(newEmployeeTestData.middleName);
    await this.lastNameInput.type(newEmployeeTestData.lastName);
    await this.saveUserButton.click();
  }

  public async addEmployeeWithLoginDetails(
    firstRandomName: string,
    bySubtub?: boolean | null
  ) {
    if (bySubtub === true) {
      await this.navigateToSubPage(SubPage.ADD_EMPLOYEE);
    } else {
      await this.addEmployeeButton.click();
    }
    await this.firstNameInput.type(firstRandomName);
    await this.middleNameInput.type(newEmployeeTestData.middleName);
    await this.lastNameInput.type(newEmployeeTestData.lastName);
    await this.getToggleByTextLabel(Labels.LOGIN_DETAILS).click();
    await this.getInputByLabelText(Labels.USERNAME).type(firstRandomName);
    await this.getInputByLabelText(Labels.PASSWORD).type(adminUserTestData.password);
    await this.getInputByLabelText(Labels.CONFIRM_PASSWORD).type(adminUserTestData.password);
    await this.getSaveButtonByHeadingSection(SubPage.ADD_EMPLOYEE).click();
  }

  public async editEmployee(randomEditedEmployeeName: string) {
    await this.page.waitForLoadState("load");
    await this.firstNameInput.fill(randomEditedEmployeeName);
    await this.saveUserButton.click();
  }

  public async searchEmployeeByName(employeeName: string) {
    await this.employeeNameInput.type(employeeName);
    await this.searchEmployeeButton.click();
  }

  public async searchEmployeeById(employeeId: string) {
    await this.employeeIdInput.type(employeeId);
    await this.searchEmployeeButton.click();
  }
}
