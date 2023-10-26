import { test, expect, Page } from "@playwright/test";
import { PimPage } from "../pages/PimPage";
import { LoginPage } from "../pages/LoginPage";
import { adminUserTestData, normalUserTestData } from "../data";
import { SubPage } from "../pages/BasePage";
import { generateRandomString } from "../utils/helper";
import { newEmployeeTestData } from "../data";

async function tableCellsGotByName(page: Page) {
  const pimPage = new PimPage(page);
  const adminUserCell = await pimPage.getLocatorByRandomNewEmplyeeName(
    adminUserTestData.userName.slice(0, adminUserTestData.userName.indexOf(" "))
  );
  const normalUserCell = await pimPage.getLocatorByRandomNewEmplyeeName(
    normalUserTestData.userName.slice(
      0,
      normalUserTestData.userName.indexOf(" ")
    )
  );
  return [adminUserCell, normalUserCell];
}

test.describe("Admin user should be able to manage on pim page", () => {
  let loginPage: LoginPage;
  let pimPage: PimPage;
  const randomNewEmployeeName = generateRandomString(3);
  const randomNewEmployeeNameForEditing = generateRandomString(3);
  const randomEditedEmployeeName = generateRandomString(3) + "Edited";
  const randomNameForMultiplyDelete1 = generateRandomString(3);
  const randomNameForMultipluDelete2 = generateRandomString(3);

  test.beforeEach(async ({ page }) => {
    loginPage = new LoginPage(page);
    pimPage = new PimPage(page);
    await loginPage.initialize();
    await loginPage.navigateToMainPage();
    await loginPage.loginUser(
      adminUserTestData.userName,
      adminUserTestData.password
    );
    await pimPage.navigateToSubPage(SubPage.PIM);
  });

  test.only("Admin user should add employee", async ({ page }) => {
    await pimPage.addEmployee(
      newEmployeeTestData.firstName + randomNewEmployeeName
    );
    await pimPage.navigateToSubPage(SubPage.PIM);
    await page.waitForLoadState("networkidle");
    await expect(
      pimPage.getLocatorByRandomNewEmplyeeName(randomNewEmployeeName)
    ).toBeVisible();
  });

  test.only("Admin user should edit previousely created employee with random name", async ({
    page,
  }) => {
    await pimPage.addEmployee(
      newEmployeeTestData.firstName + randomNewEmployeeNameForEditing
    );
    await pimPage.navigateToSubPage(SubPage.PIM);
    await pimPage
      .getEditIconByRandomEmployeeName(randomNewEmployeeNameForEditing)
      .click();
    await pimPage.editEmployee(randomEditedEmployeeName);
    await pimPage.navigateToSubPage(SubPage.PIM);
    await page.waitForLoadState("networkidle");
    await expect(
      pimPage.getLocatorByRandomNewEmplyeeName(randomEditedEmployeeName)
    ).toBeVisible();
  });

  test("Delete mulitiply users", async ({ page }) => {
    await pimPage.addEmployee(
      newEmployeeTestData.firstName + randomNameForMultiplyDelete1
    );
    await pimPage.navigateToSubPage(SubPage.PIM);
    await pimPage.addEmployee(
      newEmployeeTestData.firstName + randomNameForMultipluDelete2
    );
    await pimPage.navigateToSubPage(SubPage.PIM);
    await pimPage
      .getCheckIconByRandomEmployeeName(randomNameForMultiplyDelete1)
      .click();
    await pimPage
      .getCheckIconByRandomEmployeeName(randomNameForMultipluDelete2)
      .click();
    await pimPage.multiplyDelete.click();
    await pimPage.confirmDeleteButton.click();
    await expect(pimPage.getCheckIconByRandomEmployeeName(randomNameForMultiplyDelete1)).toHaveCount(0);
    await expect(pimPage.getCheckIconByRandomEmployeeName(randomNameForMultipluDelete2)).toHaveCount(0);
  });

  // test('User should search for employee by name', async ({ page }) => {
  //   const tableCells = await tableCellsGotByName(page);
  //   await pimPage.searchEmployeeByName(adminUserTestData.userName)
  //   await page.waitForLoadState('networkidle')
  //   await expect(page.locator(tableCells[0])).toBeVisible();
  //   await expect(page.locator(tableCells[1])).not.toBeVisible()
  // })

  // test('User should reset search that was done by name', async ({ page }) => {
  //   await pimPage.searchEmployeeByName(adminUserTestData.userName)
  //   const tableCells = await tableCellsGotByName(page);
  //   await page.waitForLoadState('networkidle')
  //   await expect.soft(page.locator(tableCells[0])).toBeVisible();
  //   await expect.soft(page.locator(tableCells[1])).not.toBeVisible()
  //   await pimPage.resetButton.click()
  //   await page.waitForLoadState('networkidle')
  //   await expect(page.locator(tableCells[0])).toBeVisible();
  //   await expect(page.locator(tableCells[1])).toBeVisible()
  // })

  // test('User should search for employee by id', async ({ page }) => {
  //   const tableCells = await tableCellsGotByName(page);
  //   await pimPage.searchEmployeeById(adminUserTestData.id)
  //   await page.waitForLoadState('networkidle')
  //   await expect(page.locator(tableCells[0])).toBeVisible();
  //   await expect(page.locator(tableCells[1])).not.toBeVisible()
  // })

  // test('User should reset search that was done by id', async ({ page }) => {
  //   await pimPage.searchEmployeeById(adminUserTestData.id)
  //   const tableCells = await tableCellsGotByName(page);
  //   await page.waitForLoadState('networkidle')
  //   await expect.soft(page.locator(tableCells[0])).toBeVisible();
  //   await expect.soft(page.locator(tableCells[1])).not.toBeVisible()
  //   await pimPage.resetButton.click()
  //   await page.waitForLoadState('networkidle')
  //   await expect(page.locator(tableCells[0])).toBeVisible();
  //   await expect(page.locator(tableCells[1])).toBeVisible()
  // })

  test.only("Admin user should delete employee//Clean all data after tests..", async ({
    page,
  }) => {
    const pimPage = new PimPage(page);
    await pimPage
      .getTrashBinByRandomEmployeeName(randomNewEmployeeName)
      .click();
    await pimPage.confirmDeleteButton.click();
    await pimPage
      .getTrashBinByRandomEmployeeName(randomEditedEmployeeName)
      .click();
    await pimPage.confirmDeleteButton.click();
    await expect(
      pimPage.getTrashBinByRandomEmployeeName(randomEditedEmployeeName)
    ).toHaveCount(0);
    await expect(
      pimPage.getTrashBinByRandomEmployeeName(randomNewEmployeeName)
    ).toHaveCount(0);
  });

  // test('Admin user should add employee from sub page: Add employee', async ({ page }) => {
  //   const randomNewEmployeeName = generateRandomString(3);
  //   await pimPage.addEmployee(newEmployeeTestData.firstName + randomNewEmployeeName, true);
  //   await pimPage.navigateToSubPage(SubPage.PIM);
  //   const element = await pimPage.getLocatorByRandomNewEmplyeeName(randomNewEmployeeName)
  //   await page.waitForLoadState('networkidle')
  //   await expect(page.locator(element)).toBeVisible();
  // });

  // test.only('Admin user should add employee from sub page Add employee with loging details', async ({ page }) => {
  //   const randomNewEmployeeName = generateRandomString(3);
  //   await pimPage.addEmployeeWIthLoginDetails(newEmployeeTestData.firstName + randomNewEmployeeName, true);
  //   await page.waitForTimeout(5000)
  // });
});
