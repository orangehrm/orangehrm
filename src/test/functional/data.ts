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
  employeeId: string;
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
  employeeId: '9870',
  loginDetail: {
    username: 'usernameTest7',
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
    employeeId: string;
    otherId: string;
    driverLicenseNumber: string;
    licenseExpiryDate: string;
    nationality: string;
    maritalStatus: string;
    dateOfBirth: string;
    genderFemale: string;
    smoker: string;
  };
}

export const userData: UserData = {
  personalDetails: {
    firstName: 'FirstNameTest9',
    middleName: 'MiddleNameTest',
    lastName: 'LastNameTest',
    nickname: 'NicknameTest',
    employeeId: '12431',
    otherId: '1234',
    driverLicenseNumber: '4567',
    licenseExpiryDate: '2020-02-02',
    nationality: 'Algerian',
    maritalStatus: 'Married',
    dateOfBirth: '2000-01-01',
    genderFemale: 'Female',
    smoker: 'Yes',
  },
};
