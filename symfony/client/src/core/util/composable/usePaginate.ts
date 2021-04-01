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

import {onBeforeMount, reactive, toRefs, watch} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import {AxiosResponse} from 'axios';

interface ServerResponse {
  data?: any;
  meta?: any;
  error?: boolean;
  message?: string;
}

interface State {
  showPaginator: boolean;
  isLoading: boolean;
  response: ServerResponse;
  total: number;
  pages: number;
  pageSize: number;
  currentPage: number;
}

async function fetchData(
  http: APIService,
  params: object,
): Promise<ServerResponse> {
  try {
    const response: AxiosResponse = await http.getAll(params);
    return {
      data: response.data.data,
      meta: response.data.meta,
      error: false,
    };
  } catch (error) {
    return {
      error: true,
      message: error.message,
    };
  }
}

function getPageParams(pageSize: number, currentPage: number) {
  const offset = pageSize * (currentPage - 1);
  return {
    limit: pageSize,
    offset,
  };
}

export default function usePaginate(apiPath: string) {
  // @ts-expect-error
  const http: APIService = new APIService(window.appGlobal.baseUrl, apiPath);

  const state = reactive<State>({
    showPaginator: false,
    isLoading: false,
    response: {},
    total: 0,
    pages: 0,
    pageSize: 5,
    currentPage: 1,
  });

  const execQuery = async () => {
    state.isLoading = true;
    const params = getPageParams(state.pageSize, state.currentPage);
    state.response = await fetchData(http, params);
    if (state.response.meta) {
      state.total = state.response.meta.total;
      if (state.total > state.pageSize) {
        state.showPaginator = true;
        state.pages = Math.ceil(state.total / state.pageSize);
      } else {
        state.currentPage = 1;
        state.pages = 1;
        state.showPaginator = false;
      }
    }
    state.isLoading = false;
  };

  onBeforeMount(execQuery);

  watch(() => state.currentPage, execQuery);

  return {
    ...toRefs(state),
    execQuery,
  };
}
