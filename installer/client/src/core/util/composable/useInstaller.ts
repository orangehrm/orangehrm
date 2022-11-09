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
import useUpgrader from './useUpgrader';
import useDiagnostics from './useDiagnostics';

export default function useInstaller(http: APIService) {
  const {versionGenerator} = useUpgrader(http);
  const {notifyInstallerStart} = useDiagnostics(http);

  const getVersionList = (
    excludeLatest = false,
  ): Promise<AxiosResponse<string[]>> => {
    return http.request({
      method: 'GET',
      url: 'installer/api/versions',
      params: {excludeLatest},
    });
  };

  const createDatabase = (): Promise<AxiosResponse[]> => {
    return Promise.all([
      notifyInstallerStart(),
      http.request({
        method: 'POST',
        url: '/installer/api/installation/database',
      }),
    ]);
  };

  const createDatabaseUser = (): Promise<AxiosResponse> => {
    return http.request({
      method: 'POST',
      url: 'installer/api/installation/database-user',
    });
  };

  const runMigrations = async (): Promise<void> => {
    const doMigration = (version: string): Promise<AxiosResponse> => {
      return http.request({
        method: 'POST',
        url: 'installer/api/installation/migration',
        data: {
          version,
        },
      });
    };

    const versionResponse = await getVersionList();
    const versions = ['0.0', ...versionResponse.data];
    const currentVersion = Array.isArray(versions) ? versions[0] : null;
    if (!currentVersion) throw new Error('version not detected');
    for (const nextVersion of versionGenerator(versions, currentVersion)) {
      await doMigration(nextVersion);
    }
  };

  const createInstance = (): Promise<AxiosResponse> => {
    return http.request({
      method: 'POST',
      url: 'installer/api/installation/instance',
    });
  };

  const createConfigFiles = (): Promise<AxiosResponse> => {
    return http.request({
      method: 'POST',
      url: 'installer/api/installation/config-file',
    });
  };

  const runCleanup = (): Promise<AxiosResponse> => {
    return http.request({
      method: 'POST',
      url: 'installer/api/clean-up-install',
    });
  };

  return {
    runCleanup,
    runMigrations,
    createInstance,
    createDatabase,
    createConfigFiles,
    createDatabaseUser,
  };
}
