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
      :loading="isLoading"
      :status="status"
      :due-date="dueDate"
      :employee="employee.details"
      :job-title="employee.jobTitle"
      :review-period-end="reviewPeriodEnd"
      :review-period-start="reviewPeriodStart"
      :final-rating="finalRating"
    />
    <br />
    <oxd-form ref="formRef" :loading="isLoading">
      <evaluation-form
        v-model="employeeReview"
        :kpis="kpis"
        :rules="rules"
        :editable="hasSupervisorUpdateAction"
        :collapsed="employee.status < 3"
        :collapsible="employee.status === 3"
        :employee="employee.details"
        :job-title="employee.jobTitle"
        :status="employee.status"
        :title="$t('performance.self_evaluation_by')"
      ></evaluation-form>
      <br />
      <evaluation-form
        v-model="supervisorReview"
        :kpis="kpis"
        :rules="rules"
        :editable="hasSaveAction || hasCompleteAction"
        :collapsible="true"
        :employee="supervisor.details"
        :job-title="supervisor.jobTitle"
        :status="supervisor.status"
        :title="$t('performance.supervisor_evaluation_by')"
      >
        <oxd-divider />
        <final-evaluation
          :key="isFinalizeRequired"
          v-model:final-rating="finalRating"
          v-model:final-comment="finalComment"
          v-model:completed-date="completedDate"
          :status="status"
          :is-required="isFinalizeRequired"
        />
        <oxd-divider />
        <oxd-form-actions>
          <div class="orangehrm-performance-review-actions">
            <oxd-button
              display-type="ghost"
              :label="$t('general.back')"
              @click="onClickBack"
            />
            <oxd-button
              v-show="hasSaveAction"
              display-type="ghost"
              type="button"
              :label="$t('general.save')"
              @click="onSubmit(false)"
            />
            <oxd-button
              v-show="hasCompleteAction"
              type="button"
              display-type="secondary"
              :label="$t('performance.complete')"
              @click="onSubmit(true)"
            />
          </div>
        </oxd-form-actions>
      </evaluation-form>
    </oxd-form>
  </div>
</template>

<script>
import {provide, readonly} from 'vue';
import useForm from '@ohrm/core/util/composable/useForm';
import useResponsive from '@ohrm/oxd/composables/useResponsive';
import {APIService} from '@/core/util/services/api.service';
import {navigate, reloadPage} from '@/core/util/helper/navigation';
import ReviewSummary from '@/orangehrmPerformancePlugin/components/ReviewSummary';
import FinalEvaluation from '@/orangehrmPerformancePlugin/components/FinalEvaluation';
import EvaluationForm from '@/orangehrmPerformancePlugin/components/EvaluationForm';
import useReviewEvaluation from '@/orangehrmPerformancePlugin/util/composable/useReviewEvaluation';

const reviewerModel = {
  details: {
    empNumber: null,
    firstName: '',
    lastName: '',
    terminationId: null,
  },
  jobTitle: '',
  status: 1,
  actions: new Map(),
};

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
  },
  setup() {
    const {formRef, invalid, validate} = useForm();
    const http = new APIService(window.appGlobal.baseUrl, '');

    const responsiveState = useResponsive();
    provide('screenState', readonly(responsiveState));

    const {
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
    } = useReviewEvaluation(http);

    return {
      http,
      invalid,
      formRef,
      validate,
      getAllKpis,
      generateRules,
      generateModel,
      generateReviewerData,
      generateAllowedActions,
      generateEvaluationFormData,
      getEmployeeReview,
      getSupervisorReview,
      getFinalReview,
      finalizeReview,
      saveEmployeeReview,
      saveSupervisorReview,
    };
  },
  data() {
    return {
      kpis: [],
      rules: [],
      employee: {...reviewerModel},
      employeeReview: {},
      supervisor: {...reviewerModel},
      supervisorReview: {},
      isLoading: false,
      finalRating: null,
      finalComment: null,
      completedDate: null,
      isFinalizeRequired: false,
    };
  },
  computed: {
    hasSupervisorUpdateAction() {
      return this.employee.actions.has('supervisorUpdate');
    },
    hasSaveAction() {
      return this.supervisor.actions.has('save');
    },
    hasCompleteAction() {
      return this.supervisor.actions.has('complete');
    },
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
        return this.getEmployeeReview(this.reviewId);
      })
      .then(response => {
        const {data} = response.data;
        const {meta} = response.data;
        this.employee = this.generateReviewerData(meta.reviewer);
        this.employee.actions = this.generateAllowedActions(
          meta.allowedActions,
        );
        this.employeeReview = this.generateEvaluationFormData(
          data,
          meta.generalComment,
        );
        return this.getSupervisorReview(this.reviewId);
      })
      .then(response => {
        const {data} = response.data;
        const {meta} = response.data;
        this.supervisor = this.generateReviewerData(meta.reviewer);
        this.supervisor.actions = this.generateAllowedActions(
          meta.allowedActions,
        );
        this.supervisorReview = this.generateEvaluationFormData(
          data,
          meta.generalComment,
        );
        return this.getFinalReview(this.reviewId);
      })
      .then(response => {
        const {data} = response.data;
        this.finalRating = data.finalRating;
        this.finalComment = data.finalComment;
        this.completedDate = data.completedDate;
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    onSubmit(complete = false) {
      this.isFinalizeRequired = complete;
      this.$nextTick()
        .then(() => this.validate())
        .then(() => {
          if (this.invalid === true) return;
          this.isLoading = true;
          this.saveSupervisorReview(this.reviewId, this.supervisorReview)
            .then(() => {
              if (this.hasSupervisorUpdateAction) {
                return this.saveEmployeeReview(
                  this.reviewId,
                  true,
                  this.employeeReview,
                );
              }
            })
            .then(() => {
              return this.finalizeReview(this.reviewId, {
                complete: complete,
                finalRating: this.finalRating,
                finalComment: this.finalComment,
                completedDate: this.completedDate,
              });
            })
            .then(() => {
              return this.$toast.saveSuccess();
            })
            .finally(() => {
              reloadPage();
            });
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
