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

import {AxiosResponse} from 'axios';
import {APIService} from '@/core/util/services/api.service';

export interface Employee {
  empNumber: number;
  lastName: string;
  firstName: string;
  middleName: string;
  employeeId: string;
  terminationId?: number;
}

interface CommentsResponse {
  data: Array<{
    comment: {
      id: number;
      createdDate: string;
      createdTime: string;
    };
    employee: Employee;
  }>;
  meta: {
    total: number;
  };
}

export default function useBuzzAPIs(http: APIService) {
  const fetchPostComments = (
    postId: number,
    limit = 0,
    detailed = false,
  ): Promise<AxiosResponse<CommentsResponse>> => {
    return http.request({
      method: 'GET',
      url: `api/v2/buzz/shares/${postId}/comments`,
      params: {
        limit: limit,
        ...(detailed && {model: 'detailed'}),
      },
    });
  };

  const savePostComment = (
    postId: number,
    comment: string,
  ): Promise<AxiosResponse> => {
    return http.request({
      method: 'POST',
      url: `api/v2/buzz/shares/${postId}/comments`,
      data: {text: comment},
    });
  };

  const updatePostComment = (
    postId: number,
    commentId: number,
    comment: string,
  ): Promise<AxiosResponse> => {
    return http.request({
      method: 'PUT',
      url: `api/v2/buzz/shares/${postId}/comments/${commentId}`,
      data: {text: comment},
    });
  };

  const deletePostComment = (
    postId: number,
    commentId: number,
  ): Promise<AxiosResponse> => {
    return http.request({
      method: 'DELETE',
      url: `api/v2/buzz/shares/${postId}/comments/${commentId}`,
    });
  };

  return {
    savePostComment,
    updatePostComment,
    deletePostComment,
    fetchPostComments,
  };
}
