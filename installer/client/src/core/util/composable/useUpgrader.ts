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

export default function useUpgrader(http: APIService) {
  const getVersionList = (
    excludeLatest = true,
  ): Promise<AxiosResponse<string[]>> => {
    return http.request({
      method: 'GET',
      url: 'upgrader/api/versions',
      params: {excludeLatest},
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

  const createConfigFiles = (): Promise<AxiosResponse> => {
    return http.request({
      method: 'POST',
      url: 'upgrader/api/config-file',
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
    if (index === -1) return null;
    while (versions[index + 1]) {
      yield versions[index + 1];
      index++;
    }
  }

  const runAllMigrations = async () => {
    let versions = [];
    let currentVersion = null;
    const getVersions = Promise.all([
      getVersionList(false),
      getCurrentVersion(),
    ]);
    const [versionResponse, currentVersionResponse] = await getVersions;
    versions = [...versionResponse.data];
    currentVersion = currentVersionResponse.data?.version;
    if (!currentVersion) throw new Error('version not detected');
    for (const nextVersion of versionGenerator(versions, currentVersion)) {
      await migrateToVersion(null, nextVersion);
    }
  };

  return {
    getVersionList,
    getCurrentVersion,
    migrateToVersion,
    runAllMigrations,
    createConfigFiles,
    versionGenerator,
  };
}
