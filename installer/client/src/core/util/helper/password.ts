export function getPassLevel(password: string): number[] {
  const level1 = new RegExp(/[a-z]/);
  const level2 = new RegExp(/[A-Z]/);
  const level3 = new RegExp(/[0-9]/);
  const level4 = new RegExp(/[@#\\/\-!$%^&*()_+|~=`{}[\]:";'<>?,.]/);
  return [level1, level2, level3, level4].map((level) => {
    return level.test(password) ? 1 : 0;
  });
}

export function checkPassword(password: string): string | boolean {
  if (password.length >= 8) {
    const pwdLevel = getPassLevel(password);
    if (RegExp(/\s/).test(password)) {
      return 'Your password should not contain spaces.';
    }
    if (pwdLevel.reduce((acc, curr) => acc + curr, 0) < 4) {
      return 'Your password must contain a lower-case letter, an upper-case letter, a digit and a special character. Try a different password';
    } else {
      return true;
    }
  } else {
    return 'Should have at least 8 characters';
  }
}
