module.exports = {
  env: {
    node: true,
  },
  plugins: ['cypress', 'jest'],
  extends: [
    'eslint:recommended',
    'plugin:cypress/recommended',
    'plugin:prettier/recommended',
  ],
  parserOptions: {
    ecmaVersion: 2020,
  },
  rules: {
    'no-console': 'warn',
    'no-debugger': 'error',
    'no-var': 'error',
    'prefer-const': ['error'],
    'lines-between-class-members': [
      'warn',
      'always',
      {exceptAfterSingleLine: true},
    ],
    'no-multiple-empty-lines': ['error', {max: 2, maxEOF: 0}],
    'prettier/prettier': 'error',
    'cypress/no-assigning-return-values': 'error',
    'cypress/no-unnecessary-waiting': 'error',
    'cypress/assertion-before-screenshot': 'warn',
    'cypress/no-force': 'warn',
    'cypress/no-async-tests': 'error',
    'jest/no-disabled-tests': 'warn',
    'jest/no-focused-tests': 'error',
    'jest/no-identical-title': 'error',
    'jest/prefer-to-have-length': 'warn',
  },
};
