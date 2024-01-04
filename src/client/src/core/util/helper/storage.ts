/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

class TempStorage {
  private _tempStorage: {[key: string]: string} = {};

  clear(): void {
    this._tempStorage = {};
  }

  getItem(name: string): string | null {
    return this._tempStorage[name] || null;
  }

  key(index: number): string | null {
    return Object.keys(this._tempStorage)[index] || null;
  }

  removeItem(name: string): void {
    delete this._tempStorage[name];
  }

  setItem(name: string, value: string): void {
    this._tempStorage[name] = value;
  }
}

/**
 * Check storage API available
 * https://developer.mozilla.org/en-US/docs/Web/API/Web_Storage_API/Using_the_Web_Storage_API#testing_for_availability
 */
function isSupported(storage: Storage): boolean {
  try {
    const x = '__storage_test__';
    storage.setItem(x, x);
    storage.removeItem(x);
    return true;
  } catch (e) {
    return (
      e instanceof DOMException &&
      // everything except Firefox
      (e.code === 22 ||
        // Firefox
        e.code === 1014 ||
        // test name field too, because code might not be present
        // everything except Firefox
        e.name === 'QuotaExceededError' ||
        // Firefox
        e.name === 'NS_ERROR_DOM_QUOTA_REACHED') &&
      // acknowledge QuotaExceededError only if there's something already stored
      storage &&
      storage.length !== 0
    );
  }
}

export class WebStorage {
  private _storage;

  constructor(storage: Storage) {
    if (isSupported(storage)) {
      this._storage = storage;
    } else {
      this._storage = new TempStorage();
    }
  }

  clear(): void {
    this._storage.clear();
  }

  getItem(name: string): string | null {
    return this._storage.getItem(name);
  }

  key(index: number): string | null {
    return this._storage.key(index);
  }

  removeItem(name: string): void {
    this._storage.removeItem(name);
  }

  setItem(name: string, value: string): void {
    this._storage.setItem(name, value);
  }
}
