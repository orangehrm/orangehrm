<!--
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
 -->

<template>
  <div class="orangehrm-background-container">
    <div class="orangehrm-card-container">
      <oxd-text tag="h5" class="orangehrm-performance-review-title">
        {{ $t('performance.performance_review') }}
      </oxd-text>
    </div>
    <br />
    <review-summary
      :employee="employee"
      :job-title="jobTitle"
      :status="status"
      :review-period-start="reviewPeriodStart"
      :review-period-end="reviewPeriodEnd"
      :due-date="dueDate"
    />
    <br />
    <oxd-form :loading="isLoading" @submitValid="onClickSave(true)">
      <evaluation-form
        :kpis="kpis"
        :rules="rules"
        :editable="false"
        :collapsible="false"
        :job-title="jobTitle"
        :employee="employee"
        title="Evaluation by Employee"
      ></evaluation-form>
      <br />
      <evaluation-form
        v-model="supervisorReview"
        :kpis="kpis"
        :rules="rules"
        :editable="true"
        :collapsible="true"
        :employee="supervisor"
        title="Evaluation by Supervisor"
      >
        <oxd-divider />
        <final-evaluation
          v-model:completed-date="completedDate"
          v-model:final-rating="finalRating"
          v-model:final-comment="finalComment"
          :status="status"
        />
        <oxd-divider />
        <oxd-form-actions>
          <oxd-button
            display-type="ghost"
            :label="$t('general.back')"
            @click="onClickBack"
          />
          <oxd-button
            v-show="!completed"
            display-type="ghost"
            class="orangehrm-left-space"
            :label="$t('general.save')"
            @click="onClickSave(false)"
          />
          <oxd-button
            v-show="!completed"
            display-type="secondary"
            class="orangehrm-left-space"
            :label="$t('performance.complete')"
            type="submit"
          />
        </oxd-form-actions>
      </evaluation-form>
    </oxd-form>
  </div>
</template>

<script>
import {computed} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import {navigate, reloadPage} from '@/core/util/helper/navigation';
import ReviewSummary from '@/orangehrmPerformancePlugin/components/ReviewSummary';
import FinalEvaluation from '@/orangehrmPerformancePlugin/components/FinalEvaluation';
import EvaluationForm from '@/orangehrmPerformancePlugin/components/EvaluationForm';
import useReviewEvaluation from '@/orangehrmPerformancePlugin/util/composable/useReviewEvaluation';

export default {
  components: {
    'review-summary': ReviewSummary,
    'final-evaluation': FinalEvaluation,
    'evaluation-form': EvaluationForm,
  },
  props: {
    reviewId: {
      type: Number,
      required: true,
    },
    jobTitle: {
      type: String,
      required: true,
    },
    status: {
      type: Number,
      required: true,
    },
    reviewPeriodStart: {
      type: String,
      required: true,
    },
    reviewPeriodEnd: {
      type: String,
      required: true,
    },
    dueDate: {
      type: String,
      required: true,
    },
    isReviewer: {
      type: Boolean,
      default: false,
    },
    employee: {
      type: Object,
      required: true,
    },
    supervisor: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const http = new APIService(window.appGlobal.baseUrl, '');
    const {
      getAllKpis,
      getFinalReview,
      generateRules,
      generateModel,
    } = useReviewEvaluation(http);
    // TODO workflow
    const completed = computed(() => props.status === 4);

    return {
      http,
      completed,
      getAllKpis,
      generateRules,
      generateModel,
      getFinalReview,
    };
  },
  data() {
    return {
      isLoading: false,
      completedDate: null,
      finalRating: null,
      finalComment: null,
      kpis: [],
      rules: [],
      employeeReview: [],
      supervisorReview: [],
    };
  },
  beforeMount() {
    this.isLoading = true;
    this.getAllKpis(this.reviewId)
      .then(response => {
        const {data} = response.data;
        this.kpis = [...data];
        this.rules = this.generateRules(data);
        this.employeeReview = this.generateModel(data);
        this.supervisorReview = this.generateModel(data);
        return this.getFinalReview(this.reviewId);
      })
      .then(response => {
        const {data} = response.data;
        this.completedDate = data.completedDate;
        this.finalRating = data.finalRating;
        this.finalComment = data.finalComment;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    onClickSave(complete = false) {
      this.isLoading = true;
      this.http
        .request({
          method: 'PUT',
          url: `/api/v2/performance/reviews/${this.reviewId}/evaluation/final`,
          data: {
            complete: complete,
            completedDate: this.completedDate,
            finalComment: this.finalComment,
            finalRating: this.finalRating,
          },
        })
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .finally(() => {
          reloadPage();
        });
    },
    onClickBack() {
      navigate(
        this.isReviewer
          ? '/performance/searchEvaluatePerformancReview'
          : '/performance/searchPerformanceReview',
      );
    },
  },
};
</script>

<style src="./review-evaluate.scss" lang="scss" scoped></style>
