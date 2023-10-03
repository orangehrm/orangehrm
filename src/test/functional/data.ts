export const normalUserTestData = {
  password: 'QAtpx123#',
  userName: 'Normal User',
};

export const adminUserTestData = {
  password: 'QAtpx123#',
  userName: 'Admin User',
};

export interface NewEmployeeData {
  firstName: string;
  middleName: string;
  lastName: string;
  loginDetail: {
    username: string;
    statusEnabled: boolean;
    password: string;
  };
}

export const newEmployeeData: NewEmployeeData = {
  firstName: 'FirstNameTest',
  middleName: 'MiddleNameTest',
  lastName: 'LastNameTest',
  loginDetail: {
    username: 'usernameTest',
    statusEnabled: true,
    password: 'Password123@',
  },
};

export interface UserData {
  personalDetails: {
    firstName: string;
    middleName: string;
    lastName: string;
    nickname: string;
    otherId: string;
    ssnNumber: string;
    sinNumber: string;
    driverLicenseNumber: string;
    licenseExpiryDate: string;
    nationality: string;
    maritalStatus: string;
    dateOfBirth: string;
    gender: string;
    militaryService: string;
  };
}

export const userData: UserData = {
  personalDetails: {
    firstName: 'FirstNameTest',
    middleName: 'MiddleNameTest',
    lastName: 'LastNameTest',
    nickname: 'NicknameTest',
    otherId: '1234',
    ssnNumber: '456',
    sinNumber: '678',
    driverLicenseNumber: '4567',
    licenseExpiryDate: '2020-02-02',
    nationality: 'Afghan',
    maritalStatus: 'Married',
    dateOfBirth: '2000-01-01',
    gender: 'Male',
    militaryService: 'military',
  },
};
