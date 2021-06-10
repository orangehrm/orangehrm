/// <reference types="cypress" />

declare namespace Cypress {
  type FixtureEncoding =
    | 'ascii'
    | 'base64'
    | 'binary'
    | 'hex'
    | 'latin1'
    | 'utf8'
    | 'utf-8'
    | 'ucs2'
    | 'ucs-2'
    | 'utf16le'
    | 'utf-16le';

  type FixtureData =
    | string
    | {
        filePath?: string;
        fileContent?: Blob;
        fileName?: string;
        encoding?: FixtureEncoding;
        mimeType?: string;
        lastModified?: number;
      };

  interface FileProcessingOptions {
    subjectType?: 'input' | 'drag-n-drop';
    force?: boolean;
    allowEmpty?: boolean;
  }

  interface Chainable<Subject = any> {
    /**
     * Command to attach file(s) to given HTML element as subject
     * @param fixture file to attach
     * @param processingOpts affects the way of fixture processing
     */
    attachFile(fixture: FixtureData | FixtureData[], processingOpts?: FileProcessingOptions): Chainable<Subject>;
  }
}
