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
      :status="status"
      :due-date="dueDate"
      :employee="employee"
      :job-title="jobTitle"
      :review-period-end="reviewPeriodEnd"
      :review-period-start="reviewPeriodStart"
    />
    <br />
    <oxd-form ref="formRef" :loading="isLoading">
      <evaluation-form
        v-model="supervisorReview"
        :kpis="kpis"
        :rules="rules"
        :editable="false"
        :collapsible="status === 4"
        :collapsed="status < 4"
        :employee="supervisor"
        :job-title="jobTitle"
        :status="supervisorStatus"
        :title="$t('performance.supervisor_evaluation_by')"
      ></evaluation-form>
      <br />
      <evaluation-form
        v-model="employeeReview"
        :kpis="kpis"
        :rules="rules"
        :editable="employeeStatus < 3"
        :collapsed="false"
        :collapsible="true"
        :employee="employee"
        :job-title="jobTitle"
        :status="employeeStatus"
        :title="$t('performance.self_evaluation_by')"
      >
        <oxd-divider v-show="status === 4" />
        <final-evaluation
          v-show="status === 4"
          v-model:final-rating="finalRating"
          v-model:final-comment="finalComment"
          v-model:completed-date="completedDate"
          :status="status"
          :is-required="false"
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
            type="button"
            class="orangehrm-left-space"
            :label="$t('general.save')"
            @click="onSubmit(false)"
          />
          <oxd-button
            v-show="!completed"
            type="button"
            display-type="secondary"
            class="orangehrm-left-space"
            :label="$t('performance.complete')"
            @click="onSubmit(true)"
          />
        </oxd-form-actions>
      </evaluation-form>
    </oxd-form>
  </div>
</template>

<script>
import {computed} from 'vue';
import {navigate, reloadPage} from '@/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import ReviewSummary from '../components/ReviewSummary';
import FinalEvaluation from '../components/FinalEvaluation';
import EvaluationForm from '../components/EvaluationForm';
import useForm from '@ohrm/core/util/composable/useForm';
import useReviewEvaluation from '@/orangehrmPerformancePlugin/util/composable/useReviewEvaluation';

export default {
  name: 'SelfEvaluation',
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
    empNumber: {
      type: Number,
      required: true,
    },
    employeeName: {
      type: String,
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
    employee: {
      type: Object,
      required: true,
    },
    supervisor: {
      type: Object,
      required: true,
    },
    employeeStatus: {
      type: Number,
      required: true,
    },
    supervisorStatus: {
      type: Number,
      required: true,
    },
  },
  setup(props) {
    const {formRef, invalid, validate} = useForm();
    const http = new APIService(window.appGlobal.baseUrl, '');
    // TODO workflow
    const completed = computed(() => props.status === 4);

    const {
      getAllKpis,
      getEmployeeReview,
      getSupervisorReview,
      getFinalReview,
      generateRules,
      generateModel,
      generateEvaluationFormData,
      saveEmployeeReview,
    } = useReviewEvaluation(http);

    return {
      http,
      invalid,
      formRef,
      validate,
      completed,
      getAllKpis,
      getEmployeeReview,
      getSupervisorReview,
      getFinalReview,
      generateRules,
      generateModel,
      generateEvaluationFormData,
      saveEmployeeReview,
    };
  },
  data() {
    return {
      kpis: [],
      rules: [],
      employeeReview: [],
      supervisorReview: [],
      isLoading: false,
      finalRating: null,
      finalComment: null,
      completedDate: null,
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
        return this.getEmployeeReview(this.reviewId);
      })
      .then(response => {
        const {data} = response.data;
        this.employeeReview = this.generateEvaluationFormData(data);
        return this.completed ? this.getSupervisorReview(this.reviewId) : {};
      })
      .then(response => {
        if (Object.keys(response).length !== 0) {
          const {data} = response.data;
          this.supervisorReview = this.generateEvaluationFormData(data);
        }
        return this.completed ? this.getFinalReview(this.reviewId) : {};
      })
      .then(response => {
        if (Object.keys(response).length !== 0) {
          const {data} = response.data;
          this.finalRating = data.finalRating;
          this.finalComment = data.finalComment;
          this.completedDate = data.completedDate;
        }
      })
      .finally(() => {
        this.isLoading = false;
      });
  },
  methods: {
    onSubmit(complete = false) {
      this.$nextTick()
        .then(() => this.validate())
        .then(() => {
          if (this.invalid === true) return;
          this.isLoading = true;
          this.saveEmployeeReview(this.reviewId, complete, this.employeeReview)
            .then(() => {
              return this.$toast.saveSuccess();
            })
            .finally(() => {
              reloadPage();
            });
        });
    },
    onClickBack() {
      navigate('/performance/myPerformanceReview');
    },
  },
};
</script>

<style src="./review-evaluate.scss" lang="scss" scoped></style>
