# End-to-End Testing with testRigor

This directory contains the end-to-end testing infrastructure for the OrangeHRM project using testRigor.

## Overview

testRigor is an AI-powered end-to-end testing platform that allows you to write tests in plain English. This setup enables automated testing of the OrangeHRM application's user interface and functionality from a user's perspective.

## How it Works

2. **GitHub Actions**: Workflow job "testRigor" in `.github/workflows/end-to-end.yml` that:
   - Starts the OrangeHRM test app locally
   - Runs testRigor test suites against `localhost:8080`
   - Uses GitHub secrets for authentication (`AUTH_TOKEN` and `TEST_SUITE_ID`)
3. **testRigor CLI**: Executes test suites written in natural language on the testRigor platform

## Usage

### Local Testing
```bash
# Start the test app
cd tools/tests/end-to-end/test-app
OrangeHRM

# Run testRigor tests (requires valid tokens)
testrigor test-suite run <TEST_SUITE_ID> --token <AUTH_TOKEN> --localhost --url http://localhost:8080
```

### CI/CD Testing
Tests automatically run via GitHub Actions

## Configuration

Set these on the GitHub repository:
- `AUTH_TOKEN`: Your testRigor authentication token secret
- `TEST_SUITE_ID`: The testRigor test suite identifier variable

## Contribute with tests in plain english

Open source contributors can add new tests into the testcases directory and test it locally using the above instructions.

### Tips for creating and maintaining new test scenarios

- Generate new test cases using [testRigor AI](https://testrigor.com/prompt-engineering-in-software-testing/#howDoesItWork).
- Optimize maintenance of the existing test cases by using [AI self-healing functionalities](https://testrigor.com/ai-based-self-healing/#adapting_to_specification_changes).


## Learn More

- [testRigor Documentation](https://testrigor.com/docs/)
- [testRigor CLI Reference](https://testrigor.com/command-line)