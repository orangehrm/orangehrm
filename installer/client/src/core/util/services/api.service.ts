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

import axios, {
  AxiosError,
  AxiosInstance,
  AxiosRequestConfig,
  AxiosResponse,
} from 'axios';
import {ComponentInternalInstance, getCurrentInstance} from 'vue';

type ErrorResponse = {
  error: {message: string};
};

export class APIService {
  private _http: AxiosInstance;
  private _baseUrl: string;
  private _apiSection: string;

  constructor(baseUrl: string, path: string) {
    this._baseUrl = baseUrl;
    this._apiSection = path;
    this._http = axios.create({
      baseURL: this._baseUrl,
    });
    this.setupResponseInterceptors(getCurrentInstance());
  }

  getAll(params?: object): Promise<AxiosResponse> {
    const headers = {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      'Cache-Control':
        'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
    };
    return this._http.get(this._apiSection, {headers, params});
  }

  get(id: number, params?: object): Promise<AxiosResponse> {
    const headers = {
      'Content-Type': 'application/json',
    };
    return this._http.get(`${this._apiSection}/${id}`, {headers, params});
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  create(data: any): Promise<AxiosResponse> {
    const headers = {
      'Content-Type': 'application/json',
      Accept: 'application/json',
    };
    return this._http.post(this._apiSection, data, {headers});
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  update(id: number, data: any): Promise<AxiosResponse> {
    const headers = {
      'Content-Type': 'application/json',
    };
    return this._http.put(`${this._apiSection}/${id}`, data, {headers});
  }

  delete(id: number): Promise<AxiosResponse> {
    const headers = {
      'Content-Type': 'application/json',
    };
    return this._http.delete(`${this._apiSection}/${id}`, {headers});
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  deleteAll(data?: any): Promise<AxiosResponse> {
    const headers = {
      'Content-Type': 'application/json',
    };
    return this._http.delete(`${this._apiSection}`, {headers, data});
  }

  request(options: AxiosRequestConfig): Promise<AxiosResponse> {
    const headers = {
      'Content-Type': 'application/json',
    };
    return this._http.request({
      url: this._apiSection,
      headers,
      ...options,
    });
  }

  /**
   * ComponentInternalInstance is given to access $toast api.
   * will fail silently if $toast is not installed/NA
   */
  setupResponseInterceptors(vm: ComponentInternalInstance | null): void {
    this._http.interceptors.response.use(
      (response: AxiosResponse): AxiosResponse => {
        return response;
      },
      (error: AxiosError<ErrorResponse>): Promise<AxiosError> => {
        if (error.response?.status === 401) {
          return Promise.reject();
        }

        const $toast = vm?.appContext.config.globalProperties.$toast;
        if ($toast) {
          const message = error.response?.data?.error?.message;
          $toast.error({
            title: 'Error',
            message: message ?? 'Unexpected Error!',
          });
        }
        return Promise.reject(error);
      },
    );
  }

  public get http() {
    return this._http;
  }

  public get baseUrl() {
    return this._baseUrl;
  }

  public set apiSection(path: string) {
    this._apiSection = path;
  }
}
