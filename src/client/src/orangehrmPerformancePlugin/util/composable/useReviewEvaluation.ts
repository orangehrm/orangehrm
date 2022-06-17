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

export default function useReviewEvaluation(http: APIService) {
  const {$t} = usei18n();

  const getAllKpis = (reviewId: number) => {
    return http.request({
      method: 'GET',
      url: `/api/v2/performance/reviews/${reviewId}/kpis`,
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
      data: {...reviewData},
    });
  };

  const saveSupervisorReview = (
    reviewId: number,
    ratings: Array<{
      kpiId: number;
      rating: number;
      comment: string;
    }>,
  ) => {
    return http.request({
      method: 'PUT',
      url: `/api/v2/performance/reviews/${reviewId}/evaluation/supervisor`,
      data: {
        ratings,
      },
    });
  };

  const generateRules = (kpis: KPI[]) => {
    return kpis.map(kpi => [
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
    return kpis.map(kpi => ({
      kpiId: kpi.id,
      rating: null,
      comment: null,
    }));
  };

  return {
    getAllKpis,
    getFinalReview,
    generateRules,
    generateModel,
    finalizeReview,
    saveSupervisorReview,
  };
}
