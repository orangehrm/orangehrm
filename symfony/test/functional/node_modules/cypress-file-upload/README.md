# cypress-file-upload

[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/abramenal/cypress-file-upload/blob/master/LICENSE) [![npm version](https://img.shields.io/npm/v/cypress-file-upload.svg?style=flat&color=red)](https://www.npmjs.com/package/cypress-file-upload) ![build](https://github.com/abramenal/cypress-file-upload/workflows/build/badge.svg) [![All Contributors](https://img.shields.io/badge/all_contributors-33-yellow.svg?style=flat&color=9cf)](#contributors) [![monthly downloads](https://img.shields.io/npm/dm/cypress-file-upload.svg?style=flat&color=orange&label=monthly%20downloads)](https://www.npmjs.com/package/cypress-file-upload) [![downloads all time](https://img.shields.io/npm/dt/cypress-file-upload.svg?style=flat&color=black&label=lifetime%20downloads)](https://www.npmjs.com/package/cypress-file-upload)

File upload testing made easy.

This package adds a custom [Cypress][cypress] command that allows you to make an abstraction on how exactly you upload files through HTML controls and focus on testing user workflows.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
  - [HTML5 file input](#html5-file-input)
  - [Drag-n-drop component](#drag-n-drop-component)
  - [Attaching multiple files](#attaching-multiple-files)
  - [Working with file encodings](#working-with-file-encodings)
  - [Working with raw file contents](#working-with-raw-file-contents)
  - [Override the file name](#override-the-file-name)
  - [Working with empty fixture file](#working-with-empty-fixture-file)
  - [I wanna see some real-world examples](#i-wanna-see-some-real-world-examples)
- [API](#api)
- [Recipes](#recipes)
- [Caveats](#caveats)
- [It isn't working! What else can I try?](#it-isnt-working-what-else-can-i-try)
- [Contributors](#contributors)
- [License](#license)

## Installation

The package is distributed via [npm][npm] and should be installed as one of your project's `devDependencies`:

```bash
npm install --save-dev cypress-file-upload
```

If you are using TypeScript, ensure your `tsconfig.json` contains commands' types:

```json
"compilerOptions": {
  "types": ["cypress", "cypress-file-upload"]
}
```

To be able to use any custom command you need to add it to `cypress/support/commands.js` like this:

```javascript
import 'cypress-file-upload';
```

Then, make sure this `commands.js` is imported in `cypress/support/index.js` (it might be commented):

```javascript
// Import commands.js using ES2015 syntax:
import './commands';
```

All set now! :boom:

## Usage

Now, let's see how we can actually test something. Exposed command has signature like:

```javascript
cySubject.attachFile(fixture, optionalProcessingConfig);
```

It is a common practice to put all the files required for Cypress tests inside `cypress/fixtures` folder and call them as fixtures (or a fixture).  The command recognizes [`cy.fixture`][cy.fixture] format, so usually this is just a file name.

### HTML5 file input

```javascript
cy.get('[data-cy="file-input"]')
  .attachFile('myfixture.json');
```

### Drag-n-drop component

```javascript
cy.get('[data-cy="dropzone"]')
  .attachFile('myfixture.json', { subjectType: 'drag-n-drop' });
```

### Attaching multiple files

```javascript
cy.get('[data-cy="file-input"]')
  .attachFile(['myfixture1.json', 'myfixture2.json']);
```
_Note: in previous version you could also attach it chaining the command. It brought flaky behavior with redundant multiple event triggers, and was generally unstable. It might be still working, but make sure to use array instead._
### Working with file encodings

In some cases you might need more than just plain JSON [`cy.fixture`][cy.fixture]. If your file extension is supported out of the box, it should all be just fine.

In case your file comes from some 3rd-party tool, or you already observed some errors in console, you likely need to tell Cypress how to treat your fixture file.

```javascript
cy.get('[data-cy="file-input"]')
  .attachFile({ filePath: 'test.shp', encoding: 'utf-8' });
```

**Trying to upload a file that does not supported by Cypress by default?** Make sure you pass `encoding` property (see [API](#api)).

### Working with raw file contents

Normally you do not need this. But what the heck is normal anyways :neckbeard:

If you need some custom file preprocessing, you can pass the raw file content:

```javascript
const special = 'file.spss';

cy.fixture(special, 'binary')
  .then(Cypress.Blob.binaryStringToBlob)
  .then(fileContent => {
    cy.get('[data-cy="file-input"]').attachFile({
      fileContent,
      filePath: special,
      encoding: 'utf-8',
      lastModified: new Date().getTime()
    });
  });
```

You still need to provide `filePath` in order to get file's metadata and encoding. For sure this is optional, and you can do it manually:

```javascript
cy.fixture('file.spss', 'binary')
  .then(Cypress.Blob.binaryStringToBlob)
  .then(fileContent => {
    cy.get('[data-cy="file-input"]').attachFile({
      fileContent,
      fileName: 'whatever',
      mimeType: 'application/octet-stream',
      encoding: 'utf-8',
      lastModified: new Date().getTime(),
    });
  });
```

### Override the file name

```javascript
cy.get('[data-cy="file-input"]')
  .attachFile({ filePath: 'myfixture.json', fileName: 'customFileName.json' });
```

### Working with empty fixture file

Normally you have to provide non-empty fixture file to test something. If your case isn't normal in that sense, here is the code snippet for you:

```javascript
cy.get('[data-cy="file-input"]')
  .attachFile({ filePath: 'empty.txt', allowEmpty: true });
```

### Waiting for the upload to complete

Cypress' [`cy.wait`][cy.wait] command allows you to pause code execution until some asyncronous action is finished. In case you are testing file upload, you might want to wait until the upload is complete:

```javascript
// start watching the POST requests
cy.server({ method:'POST' });
// and in particular the one with 'upload_endpoint' in the URL
cy.route({
  method: 'POST',
  url: /upload_endpoint/
}).as('upload');


const fileName = 'upload_1.xlsx';

cy.fixture(fileName, 'binary')
    .then(Cypress.Blob.binaryStringToBlob)
    .then(fileContent => {
      cy.get('#input_upload_file').attachFile({
        fileContent,
        fileName,
        mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        encoding:'utf8',
        lastModified: new Date().getTime()
      })
    })

// wait for the 'upload_endpoint' request, and leave a 2 minutes delay before throwing an error
cy.wait('@upload', { requestTimeout: 120000 });

// stop watching requests
cy.server({ enable: false })

// keep testing the app
// e.g. cy.get('.link_file[aria-label="upload_1"]').contains('(xlsx)');
```

### I wanna see some real-world examples

There is a set of [recipes](./recipes) that demonstrates some framework setups along with different test cases. Make sure to check it out when in doubt.

## API

Exposed command in a nutshell:

```javascript
cySubject.attachFile(fixture, processingOpts);
```

**Familiar with TypeScript?** It might be easier for you to just look at [type definitions](./types/index.d.ts).

`fixture` can be a string path (or array of those), or object (or array of those) that represents your local fixture file and contains following properties:

- {string} `filePath` - file path (with extension)
- {string} `fileName` - the name of the file to be attached, this allows to override the name provided by `filePath`
- {Blob} `fileContent` - the binary content of the file to be attached
- {string} `mimeType` - file [MIME][mime] type. By default, it gets resolved automatically based on file extension. Learn more about [mime](https://github.com/broofa/node-mime)
- {string} `encoding` - normally [`cy.fixture`][cy.fixture] resolves encoding automatically, but in case it cannot be determined you can provide it manually. For a list of allowed encodings, see [here](https://github.com/abramenal/cypress-file-upload/blob/master/lib/file/constants.js#L1)
- {number} `lastModified` - The unix timestamp of the lastModified value for the file.  Defaults to current time. Can be generated from `new Date().getTime()` or `Date.now()`

`processingOpts` contains following properties:

- {string} `subjectType` - target (aka subject) element kind: `'drag-n-drop'` component or plain HTML `'input'` element. Defaults to `'input'`
- {boolean} `force` - same as for [`cy.trigger`][cy.trigger], it enforces the event triggers on HTML subject element. Usually this is necessary when you use hidden HTML controls for your file upload. Defaults to `false`
- {boolean} `allowEmpty` - when true, do not throw an error if `fileContent` is zero length. Defaults to `false`

## Recipes

There is a set of [recipes](./recipes) that demonstrates some framework setups along with different test cases. Make sure to check it out when in doubt.

Any contributions are welcome!

## Caveats

During the lifetime plugin faced some issues you might need to be aware of:

- Chrome 73 changes related to HTML file input behavior: [#34][#34]
- Force event triggering (same as for [`cy.trigger`][cy.trigger]) should happen when you use hidden HTML controls: [#41][#41]
- Binary fixture has a workarounded encoding: [#70][#70]
- Video fixture has a workarounded encoding: [#136][#136]
- XML encoded files: [#209][#209]
- Shadow DOM compatibility: [#74][#74]
- Reading file content after upload: [#104][#104]

## It isn't working! What else can I try?

Here is step-by-step guide:

1. Check [Caveats](#caveats) - maybe there is a tricky thing about exactly your setup
1. Submit the issue and let us know about you problem
1. In case you're using a file with encoding and/or extension that is not yet supported by Cypress, make sure you've tried to explicitly set the `encoding` property (see [API](#api))
1. Comment your issue describing what happened after you've set the `encoding`

## I want to contribute

You have an idea of improvement, or some bugfix, or even a small typo fix? That's :cool:

We really appreciate that and try to share ideas and best practices. Make sure to check out [CONTRIBUTING.md](./CONTRIBUTING.md) before start!

Have something on your mind? Drop an issue or a message in [Discussions](https://github.com/abramenal/cypress-file-upload/discussions).

## Contributors

Thanks goes to these wonderful people ([emoji key](https://github.com/all-contributors/all-contributors#emoji-key)):

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore-start -->
<!-- markdownlint-disable -->
<table>
  <tr>
    <td align="center"><a href="https://github.com/allout58"><img src="https://avatars0.githubusercontent.com/u/2939703?v=4?s=100" width="100px;" alt=""/><br /><sub><b>James Hollowell</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=allout58" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/lunxiao"><img src="https://avatars1.githubusercontent.com/u/17435809?v=4?s=100" width="100px;" alt=""/><br /><sub><b>lunxiao</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/issues?q=author%3Alunxiao" title="Bug reports">🐛</a></td>
    <td align="center"><a href="http://www.ollie-odonnell.com"><img src="https://avatars2.githubusercontent.com/u/5886107?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Oliver O'Donnell</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/issues?q=author%3Aoliverodaa" title="Bug reports">🐛</a> <a href="https://github.com/abramenal/cypress-file-upload/commits?author=oliverodaa" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/virtuoushub"><img src="https://avatars0.githubusercontent.com/u/4303638?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Peter Colapietro</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=virtuoushub" title="Documentation">📖</a></td>
    <td align="center"><a href="https://github.com/km333"><img src="https://avatars1.githubusercontent.com/u/37389351?v=4?s=100" width="100px;" alt=""/><br /><sub><b>km333</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/issues?q=author%3Akm333" title="Bug reports">🐛</a></td>
    <td align="center"><a href="http://pages.cs.wisc.edu/~mui/"><img src="https://avatars2.githubusercontent.com/u/17896701?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Kevin Mui</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=kmui2" title="Code">💻</a> <a href="#ideas-kmui2" title="Ideas, Planning, & Feedback">🤔</a> <a href="https://github.com/abramenal/cypress-file-upload/pulls?q=is%3Apr+reviewed-by%3Akmui2" title="Reviewed Pull Requests">👀</a></td>
    <td align="center"><a href="http://www.benwurth.com/"><img src="https://avatars0.githubusercontent.com/u/2358786?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Ben Wurth</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/issues?q=author%3Abenwurth" title="Bug reports">🐛</a> <a href="https://github.com/abramenal/cypress-file-upload/commits?author=benwurth" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="http://tomskjs.ru"><img src="https://avatars2.githubusercontent.com/u/1303845?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Andreev Sergey</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=DragorWW" title="Tests">⚠️</a> <a href="#question-DragorWW" title="Answering Questions">💬</a> <a href="#example-DragorWW" title="Examples">💡</a> <a href="https://github.com/abramenal/cypress-file-upload/commits?author=DragorWW" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/GuillaumeDind"><img src="https://avatars1.githubusercontent.com/u/45589123?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Guts</b></sub></a><br /><a href="#question-GuillaumeDind" title="Answering Questions">💬</a></td>
    <td align="center"><a href="https://github.com/maple-leaf"><img src="https://avatars3.githubusercontent.com/u/3980995?v=4?s=100" width="100px;" alt=""/><br /><sub><b>maple-leaf</b></sub></a><br /><a href="#question-maple-leaf" title="Answering Questions">💬</a> <a href="https://github.com/abramenal/cypress-file-upload/commits?author=maple-leaf" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/daniula"><img src="https://avatars3.githubusercontent.com/u/91628?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Daniel Mendalka</b></sub></a><br /><a href="#question-daniula" title="Answering Questions">💬</a></td>
    <td align="center"><a href="http://www.stickypixel.com"><img src="https://avatars1.githubusercontent.com/u/12176122?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Chris Sargent</b></sub></a><br /><a href="#question-ChrisSargent" title="Answering Questions">💬</a></td>
    <td align="center"><a href="https://ronakchovatiya.glitch.me/"><img src="https://avatars1.githubusercontent.com/u/16197756?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Ronak Chovatiya</b></sub></a><br /><a href="#question-rchovatiya88" title="Answering Questions">💬</a></td>
    <td align="center"><a href="https://geromekevin.com"><img src="https://avatars0.githubusercontent.com/u/31096420?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Jan Hesters</b></sub></a><br /><a href="#question-janhesters" title="Answering Questions">💬</a> <a href="https://github.com/abramenal/cypress-file-upload/issues?q=author%3Ajanhesters" title="Bug reports">🐛</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/skjnldsv"><img src="https://avatars0.githubusercontent.com/u/14975046?v=4?s=100" width="100px;" alt=""/><br /><sub><b>John Molakvoæ</b></sub></a><br /><a href="#question-skjnldsv" title="Answering Questions">💬</a></td>
    <td align="center"><a href="http://psjones.co.uk"><img src="https://avatars1.githubusercontent.com/u/677167?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Phil Jones</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/issues?q=author%3Aphiljones88" title="Bug reports">🐛</a></td>
    <td align="center"><a href="https://github.com/NicolasGehring"><img src="https://avatars3.githubusercontent.com/u/38431471?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Nicolas Gehring</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/issues?q=author%3ANicolasGehring" title="Bug reports">🐛</a></td>
    <td align="center"><a href="https://www.pertiller.tech"><img src="https://avatars3.githubusercontent.com/u/1514111?v=4?s=100" width="100px;" alt=""/><br /><sub><b>David Pertiller</b></sub></a><br /><a href="#question-Mobiletainment" title="Answering Questions">💬</a> <a href="https://github.com/abramenal/cypress-file-upload/commits?author=Mobiletainment" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/xiaomeidan"><img src="https://avatars1.githubusercontent.com/u/5284575?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Amy</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/issues?q=author%3Axiaomeidan" title="Bug reports">🐛</a></td>
    <td align="center"><a href="https://github.com/kammerer"><img src="https://avatars0.githubusercontent.com/u/14025?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Tomasz Szymczyszyn</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=kammerer" title="Documentation">📖</a></td>
    <td align="center"><a href="http://nitzel.github.io/"><img src="https://avatars0.githubusercontent.com/u/8362046?v=4?s=100" width="100px;" alt=""/><br /><sub><b>nitzel</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=nitzel" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/stefanbrato"><img src="https://avatars2.githubusercontent.com/u/4852275?v=4?s=100" width="100px;" alt=""/><br /><sub><b>dirk</b></sub></a><br /><a href="#ideas-stefanbrato" title="Ideas, Planning, & Feedback">🤔</a></td>
    <td align="center"><a href="https://github.com/0xADD1E"><img src="https://avatars1.githubusercontent.com/u/38090404?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Addie Morrison</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/issues?q=author%3A0xADD1E" title="Bug reports">🐛</a></td>
    <td align="center"><a href="https://blog.alec.coffee"><img src="https://avatars2.githubusercontent.com/u/6475934?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Alec Brunelle</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/issues?q=author%3Aaleccool213" title="Bug reports">🐛</a></td>
    <td align="center"><a href="https://glebbahmutov.com/"><img src="https://avatars1.githubusercontent.com/u/2212006?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Gleb Bahmutov</b></sub></a><br /><a href="#ideas-bahmutov" title="Ideas, Planning, & Feedback">🤔</a></td>
    <td align="center"><a href="https://github.com/JesseDeBruijne"><img src="https://avatars1.githubusercontent.com/u/29858373?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Jesse de Bruijne</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=JesseDeBruijne" title="Documentation">📖</a></td>
    <td align="center"><a href="https://github.com/justinlittman"><img src="https://avatars1.githubusercontent.com/u/588335?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Justin Littman</b></sub></a><br /><a href="#question-justinlittman" title="Answering Questions">💬</a></td>
    <td align="center"><a href="https://github.com/harrison9149"><img src="https://avatars0.githubusercontent.com/u/41189790?v=4?s=100" width="100px;" alt=""/><br /><sub><b>harrison9149</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/issues?q=author%3Aharrison9149" title="Bug reports">🐛</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/jdcl32"><img src="https://avatars1.githubusercontent.com/u/17127746?v=4?s=100" width="100px;" alt=""/><br /><sub><b>jdcl32</b></sub></a><br /><a href="#question-jdcl32" title="Answering Questions">💬</a> <a href="https://github.com/abramenal/cypress-file-upload/commits?author=jdcl32" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/ds300"><img src="https://avatars2.githubusercontent.com/u/1242537?v=4?s=100" width="100px;" alt=""/><br /><sub><b>David Sheldrick</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=ds300" title="Code">💻</a></td>
    <td align="center"><a href="https://macwright.org/"><img src="https://avatars2.githubusercontent.com/u/32314?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Tom MacWright</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=tmcw" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/ajhoddinott"><img src="https://avatars3.githubusercontent.com/u/771460?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Andrew Hoddinott</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=ajhoddinott" title="Code">💻</a></td>
    <td align="center"><a href="http://nisgrak.me"><img src="https://avatars3.githubusercontent.com/u/19597708?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Eneko Rodríguez</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=Nisgrak" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/the-owl"><img src="https://avatars1.githubusercontent.com/u/11090288?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Dmitry Nikulin</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=the-owl" title="Code">💻</a></td>
    <td align="center"><a href="https://www.linkedin.com/in/thiago-brezinski-5a4b30125/"><img src="https://avatars3.githubusercontent.com/u/26878038?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Thiago Brezinski</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/issues?q=author%3Athiagobrez" title="Bug reports">🐛</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://github.com/jackguoAtJogg"><img src="https://avatars1.githubusercontent.com/u/56273621?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Jack</b></sub></a><br /><a href="#question-jackguoAtJogg" title="Answering Questions">💬</a></td>
    <td align="center"><a href="https://github.com/yonigibbs"><img src="https://avatars3.githubusercontent.com/u/39593145?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Yoni Gibbs</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/issues?q=author%3Ayonigibbs" title="Bug reports">🐛</a></td>
    <td align="center"><a href="https://github.com/benowenssonos"><img src="https://avatars2.githubusercontent.com/u/44402951?v=4?s=100" width="100px;" alt=""/><br /><sub><b>benowenssonos</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/issues?q=author%3Abenowenssonos" title="Bug reports">🐛</a></td>
    <td align="center"><a href="http://blog.kodono.info"><img src="https://avatars2.githubusercontent.com/u/946315?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Aymeric</b></sub></a><br /><a href="#question-Aymkdn" title="Answering Questions">💬</a></td>
    <td align="center"><a href="https://github.com/asumaran"><img src="https://avatars1.githubusercontent.com/u/1025173?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Alfredo Sumaran</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/issues?q=author%3Aasumaran" title="Bug reports">🐛</a></td>
    <td align="center"><a href="https://github.com/x-yuri"><img src="https://avatars0.githubusercontent.com/u/730588?v=4?s=100" width="100px;" alt=""/><br /><sub><b>x-yuri</b></sub></a><br /><a href="#ideas-x-yuri" title="Ideas, Planning, & Feedback">🤔</a></td>
    <td align="center"><a href="http://triqtran.com"><img src="https://avatars1.githubusercontent.com/u/2232035?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Tri Q. Tran</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=triqi" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://francischartrand.com"><img src="https://avatars0.githubusercontent.com/u/1503758?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Francis Chartrand</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=chartrandf" title="Documentation">📖</a></td>
    <td align="center"><a href="https://github.com/emilong"><img src="https://avatars2.githubusercontent.com/u/1090771?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Emil Ong</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=emilong" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/Ebazhanov"><img src="https://avatars2.githubusercontent.com/u/13170022?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Evgenii</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=Ebazhanov" title="Documentation">📖</a></td>
    <td align="center"><a href="https://github.com/josephzidell"><img src="https://avatars0.githubusercontent.com/u/1812443?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Joseph Zidell</b></sub></a><br /><a href="#maintenance-josephzidell" title="Maintenance">🚧</a></td>
    <td align="center"><a href="https://github.com/danielcaballero"><img src="https://avatars1.githubusercontent.com/u/1639333?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Daniel Caballero</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=danielcaballero" title="Code">💻</a></td>
    <td align="center"><a href="https://adrienjoly.com/now"><img src="https://avatars3.githubusercontent.com/u/531781?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Adrien Joly</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=adrienjoly" title="Documentation">📖</a></td>
    <td align="center"><a href="http://www.hypercubed.com"><img src="https://avatars1.githubusercontent.com/u/509946?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Jayson Harshbarger</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=Hypercubed" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="http://www.andri.co"><img src="https://avatars0.githubusercontent.com/u/17087167?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Andrico</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=andrico1234" title="Documentation">📖</a></td>
    <td align="center"><a href="https://github.com/paulblyth"><img src="https://avatars0.githubusercontent.com/u/692357?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Paul Blyth</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=paulblyth" title="Code">💻</a></td>
    <td align="center"><a href="https://github.com/zephraph"><img src="https://avatars1.githubusercontent.com/u/3087225?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Justin Bennett</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=zephraph" title="Documentation">📖</a></td>
    <td align="center"><a href="http://www.bennettjones.com"><img src="https://avatars0.githubusercontent.com/u/62298251?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Shafiq Jetha</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=jethas-bennettjones" title="Documentation">📖</a></td>
    <td align="center"><a href="https://github.com/anonkey"><img src="https://avatars1.githubusercontent.com/u/6380129?v=4?s=100" width="100px;" alt=""/><br /><sub><b>tt rt</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=anonkey" title="Code">💻</a></td>
    <td align="center"><a href="https://www.edhollinghurst.com"><img src="https://avatars.githubusercontent.com/u/2844785?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Ed Hollinghurst</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=edhollinghurst" title="Documentation">📖</a></td>
    <td align="center"><a href="https://github.com/anark"><img src="https://avatars.githubusercontent.com/u/101184?v=4?s=100" width="100px;" alt=""/><br /><sub><b>anark</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=anark" title="Tests">⚠️</a> <a href="https://github.com/abramenal/cypress-file-upload/commits?author=anark" title="Code">💻</a></td>
  </tr>
  <tr>
    <td align="center"><a href="https://www.michaeljaltamirano.com/"><img src="https://avatars.githubusercontent.com/u/13544620?v=4?s=100" width="100px;" alt=""/><br /><sub><b>Michael Altamirano</b></sub></a><br /><a href="https://github.com/abramenal/cypress-file-upload/commits?author=michaeljaltamirano" title="Code">💻</a> <a href="#question-michaeljaltamirano" title="Answering Questions">💬</a></td>
  </tr>
</table>

<!-- markdownlint-restore -->
<!-- prettier-ignore-end -->

<!-- ALL-CONTRIBUTORS-LIST:END -->

This project follows the [all-contributors](https://github.com/all-contributors/all-contributors) specification. Contributions of any kind welcome!

## License

[MIT][mit]

[cypress]: https://cypress.io/
[cy.fixture]: https://docs.cypress.io/api/commands/fixture.html
[cy.trigger]: https://docs.cypress.io/api/commands/trigger.html#Arguments
[cy.wait]: https://docs.cypress.io/api/commands/wait.html
[npm]: https://www.npmjs.com/
[mime]: https://developer.mozilla.org/en-US/docs/Web/HTTP/Basics_of_HTTP/MIME_types/Complete_list_of_MIME_types
[mit]: https://opensource.org/licenses/MIT
[#34]: https://github.com/abramenal/cypress-file-upload/issues/34
[#41]: https://github.com/abramenal/cypress-file-upload/issues/41
[#70]: https://github.com/abramenal/cypress-file-upload/issues/70
[#74]: https://github.com/abramenal/cypress-file-upload/issues/74
[#104]: https://github.com/abramenal/cypress-file-upload/issues/104
[#136]: https://github.com/abramenal/cypress-file-upload/issues/136
[#209]: https://github.com/abramenal/cypress-file-upload/issues/209
