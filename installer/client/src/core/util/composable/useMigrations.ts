/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

import {APIService} from '@/core/util/services/api.service';
import {AxiosResponse} from 'axios';

export default function useMigrations(http: APIService) {
  const getVersionList = (): Promise<AxiosResponse<string[]>> => {
    return http.request({
      method: 'GET',
      url: 'upgrader/api/versions',
    });
  };

  const getCurrentVersion = (): Promise<
    AxiosResponse<{version: string} | null>
  > => {
    return http.request({
      method: 'GET',
      url: 'upgrader/api/current-version',
    });
  };

  const migrateToVersion = (fromVersion: string | null, toVersion: string) => {
    let payload;
    if (fromVersion) {
      payload = {
        fromVersion,
        toVersion,
      };
    } else {
      payload = {
        version: toVersion,
      };
    }
    return http.request({
      method: 'POST',
      url: 'upgrader/api/migration',
      data: payload,
    });
  };

  function* versionGenerator(
    versions: string[],
    currentVersion: string | null,
  ) {
    let index = versions.findIndex((_version) => _version === currentVersion);
    if (index === -1) {
      return null;
    }
    yield {
      fromVersion: versions[index],
      toVersion: versions[index + 1],
    };
    index++;
  }

  const runAllMigrations = () => {
    let versions = [];
    let currentVersion = null;
    const getVersions = Promise.all([getVersionList(), getCurrentVersion()]);
    return new Promise<Promise<boolean>>((resolve, reject) => {
      getVersions.then((responses) => {
        const [versionResponse, currentVersionResponse] = responses;
        versions = [...versionResponse.data];
        currentVersion =
          currentVersionResponse.data !== null
            ? currentVersionResponse.data.version
            : null;
        if (currentVersion === null) reject('version not detected');
        for (const v of versionGenerator(versions, currentVersion)) {
          migrateToVersion(v.fromVersion, v.toVersion)
            .then((resolve) => console.log(resolve))
            .catch((error) => reject(error));
        }
      });
    });
  };

  return {
    getVersionList,
    getCurrentVersion,
    migrateToVersion,
    runAllMigrations,
  };
}
