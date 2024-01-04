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

import {
  lessThanOrEqual,
  greaterThanOrEqual,
} from '@/core/util/validation/rules';
import usei18n from '@/core/util/composable/usei18n';
import {APIService} from '@/core/util/services/api.service';

export interface JobTitle {
  id: number;
  name: string;
  deleted: boolean;
}

export interface KPI {
  id: number;
  title: string;
  jobTitle: JobTitle;
  minRating: number;
  maxRating: number;
  isDefault: boolean;
}

export interface EvaluationData {
  id: number;
  rating: string;
  comment: string;
  kpi: KPI;
}

export interface Reviewer {
  empNumber: number;
  firstName: string;
  lastName: string;
  middleName: string;
  terminationId: number;
  jobTitle: JobTitle;
}

export interface ReviewerData {
  status: number;
  employee: Reviewer;
}

export interface AllowedAction {
  action: string;
  name: string;
}

export interface Review {
  kpis: Array<{
    kpiId: number;
    rating: number;
    comment: string;
  }>;
  generalComment: string;
}

export default function useReviewEvaluation(http: APIService) {
  const {$t} = usei18n();

  const getAllKpis = (reviewId: number) => {
    return http.request({
      method: 'GET',
      url: `/api/v2/performance/reviews/${reviewId}/kpis`,
    });
  };

  const getSupervisorReview = (reviewId: number) => {
    return http.request({
      method: 'GET',
      url: `/api/v2/performance/reviews/${reviewId}/evaluation/supervisor`,
    });
  };

  const getEmployeeReview = (reviewId: number) => {
    return http.request({
      method: 'GET',
      url: `/api/v2/performance/reviews/${reviewId}/evaluation/employee`,
    });
  };

  const getFinalReview = (reviewId: number) => {
    return http.request({
      method: 'GET',
      url: `/api/v2/performance/reviews/${reviewId}/evaluation/final`,
    });
  };

  const finalizeReview = (
    reviewId: number,
    reviewData: {
      complete: boolean;
      finalComment: string;
      finalRating: number;
      completedDate: string;
    },
  ) => {
    return http.request({
      method: 'PUT',
      url: `/api/v2/performance/reviews/${reviewId}/evaluation/final`,
      data: {
        ...reviewData,
        finalComment:
          reviewData.finalComment === '' ? null : reviewData.finalComment,
      },
    });
  };

  const saveEmployeeReview = (
    reviewId: number,
    complete: boolean,
    review: Review,
  ) => {
    return http.request({
      method: 'PUT',
      url: `/api/v2/performance/reviews/${reviewId}/evaluation/employee`,
      data: {
        complete,
        ratings: review.kpis,
        generalComment: review.generalComment,
      },
    });
  };

  const saveSupervisorReview = (reviewId: number, review: Review) => {
    return http.request({
      method: 'PUT',
      url: `/api/v2/performance/reviews/${reviewId}/evaluation/supervisor`,
      data: {
        ratings: review.kpis,
        generalComment: review.generalComment,
      },
    });
  };

  const generateRules = (kpis: KPI[]) => {
    return kpis.map((kpi) => [
      greaterThanOrEqual(
        kpi.minRating,
        $t('performance.rating_should_be_greater_than_or_equal_to_minValue', {
          minValue: kpi.minRating,
        }),
      ),
      lessThanOrEqual(
        kpi.maxRating,
        $t('performance.rating_should_be_less_than_or_equal_to_maxValue', {
          maxValue: kpi.maxRating,
        }),
      ),
    ]);
  };

  const generateModel = (kpis: KPI[]) => {
    return {
      kpis: kpis.map((kpi) => ({
        kpiId: kpi.id,
        rating: null,
        comment: null,
      })),
      generalComment: null,
    };
  };

  const generateEvaluationFormData = (
    evaluationData: EvaluationData[],
    generalComment: string,
    kpis: Array<{kpiId: number}>,
  ) => {
    return {
      kpis: kpis.map(({kpiId}) => {
        const _kpi = evaluationData.find((datum) => datum.kpi.id === kpiId);
        return {
          kpiId,
          rating: _kpi?.rating,
          comment: _kpi?.comment,
        };
      }),
      generalComment: generalComment,
    };
  };

  const generateReviewerData = (reviewerData: ReviewerData) => {
    return {
      details: {
        empNumber: reviewerData.employee.empNumber,
        firstName: reviewerData.employee.firstName,
        lastName: reviewerData.employee.lastName,
        middleName: reviewerData.employee.middleName,
        terminationId: reviewerData.employee.terminationId,
      },
      jobTitle: reviewerData.employee.jobTitle.name,
      status: reviewerData.status,
    };
  };

  const generateAllowedActions = (allowedActions: AllowedAction[] | null) => {
    return new Map(
      allowedActions?.map((action) => {
        return [action.action, action.name];
      }),
    );
  };

  return {
    getAllKpis,
    getEmployeeReview,
    getSupervisorReview,
    getFinalReview,
    generateRules,
    generateModel,
    generateReviewerData,
    generateAllowedActions,
    generateEvaluationFormData,
    finalizeReview,
    saveEmployeeReview,
    saveSupervisorReview,
  };
}
