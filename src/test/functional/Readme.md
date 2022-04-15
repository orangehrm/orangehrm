## Introduction

Testing OrangeHRM 5x via cypress framework. cypress execute test on native browser without any middleware server.
This is the main advantage of cypress over selenium.

## How to set up the project

- Install Yarn locally in your computer. pls refer https://yarnpkg.com/getting-started/install
- Install project dependencies via `yarn install`
- Modify test/cypress.json fie base url to working instance url

```
     {
        "baseUrl": "http://php80/orangehrm/symfony/web/index.php",
     }
```

- Modify your instance admin user details on `fixtures/user.json` file

```
   # Admin user details are included here
    "admin": {
       "userName": "Admin",
       "password": "Admin@123",
       "fullName": "Samantha Jayasinghe"
   },
```

## How to execute the cypress tests on cli

```bash
yarn test
```

## How to execute the cypress on browser

```bash
yarn open
```
