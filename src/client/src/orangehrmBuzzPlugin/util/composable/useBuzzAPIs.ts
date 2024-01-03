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

export interface Post {
  text: string;
  employee: Employee;
  createdDate: string;
  createdTime: string;
}

export interface PostBody {
  text: string;
  type: string;
  link?: string;
  photos?: Array<object>;
  deletedPhotos?: Array<number>;
}

type Capability = 'canRead' | 'canCreate' | 'canUpdate' | 'canDelete';

type Permission = {
  [key in Capability]: boolean;
};

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

interface PostsResponse {
  data: Array<{
    id: number;
    text: string;
    type: string;
    liked: boolean;
    employee: Employee;
    createdDate: string;
    createdTime: string;
    permission: Permission;
    originalPost: Post | null;
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
      url: `/api/v2/buzz/shares/${postId}/comments`,
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
      url: `/api/v2/buzz/shares/${postId}/comments`,
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
      url: `/api/v2/buzz/shares/${postId}/comments/${commentId}`,
      data: {text: comment},
    });
  };

  const deletePostComment = (
    postId: number,
    commentId: number,
  ): Promise<AxiosResponse> => {
    return http.request({
      method: 'DELETE',
      url: `/api/v2/buzz/shares/${postId}/comments/${commentId}`,
    });
  };

  const fetchPostLikes = (postId: number): Promise<AxiosResponse> => {
    return http.request({
      method: 'GET',
      url: `/api/v2/buzz/shares/${postId}/likes`,
    });
  };

  const fetchPosts = (
    limit: number,
    offset: number,
    sortOrder: 'ASC' | 'DESC',
    sortField:
      | 'share.createdAtUtc'
      | 'share.numOfLikes'
      | 'share.numOfComments',
  ): Promise<AxiosResponse<PostsResponse>> => {
    return http.request({
      method: 'GET',
      url: '/api/v2/buzz/feed',
      params: {
        limit,
        offset,
        sortOrder,
        sortField,
      },
    });
  };

  const updatePostLike = (
    postId: number,
    like: boolean,
  ): Promise<AxiosResponse> => {
    return http.request({
      method: like ? 'DELETE' : 'POST',
      url: `/api/v2/buzz/shares/${postId}/likes`,
    });
  };

  const updateCommentLike = (
    commentId: number,
    like: boolean,
  ): Promise<AxiosResponse> => {
    return http.request({
      method: like ? 'DELETE' : 'POST',
      url: `/api/v2/buzz/comments/${commentId}/likes`,
    });
  };

  const deletePost = (postId: number): Promise<AxiosResponse> => {
    return http.request({
      method: 'DELETE',
      url: `/api/v2/buzz/shares/${postId}`,
    });
  };

  const updatePost = (
    postId: number,
    post: PostBody,
  ): Promise<AxiosResponse> => {
    if (post.type === 'photo') {
      delete post['link'];
    }
    if (post.type === 'video') {
      delete post['photos'];
      delete post['deletedPhotos'];
    }
    if (post.type === 'text') {
      delete post['link'];
      delete post['photos'];
    }
    return http.request({
      method: 'PUT',
      url: `/api/v2/buzz/posts/${postId}`,
      data: {...post},
      params: {model: 'detailed'},
    });
  };

  const updateSharedPost = (
    postId: number,
    text: string,
  ): Promise<AxiosResponse> => {
    return http.request({
      method: 'PUT',
      url: `/api/v2/buzz/shares/${postId}`,
      data: {text},
      params: {model: 'detailed'},
    });
  };

  return {
    fetchPosts,
    updatePost,
    deletePost,
    updatePostLike,
    fetchPostLikes,
    savePostComment,
    updateSharedPost,
    updatePostComment,
    deletePostComment,
    fetchPostComments,
    updateCommentLike,
  };
}
