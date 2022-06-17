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
        v-model="employeeReview"
        :kpis="kpis"
        :rules="rules"
        :editable="false"
        :collapsed="true"
        :collapsible="false"
        :employee="employee"
        :job-title="jobTitle"
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
        :job-title="jobTitle"
        title="Evaluation by Supervisor"
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
import useForm from '@ohrm/core/util/composable/useForm';
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
    const {formRef, invalid, validate} = useForm();
    const http = new APIService(window.appGlobal.baseUrl, '');
    const {
      getAllKpis,
      getFinalReview,
      generateRules,
      generateModel,
      finalizeReview,
      saveSupervisorReview,
    } = useReviewEvaluation(http);
    // TODO workflow
    const completed = computed(() => props.status === 4);

    return {
      http,
      invalid,
      formRef,
      validate,
      completed,
      getAllKpis,
      generateRules,
      generateModel,
      getFinalReview,
      finalizeReview,
      saveSupervisorReview,
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
      isFinalizeRequired: false,
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
              return complete === true
                ? this.finalizeReview(this.reviewId, {
                    complete: true,
                    finalRating: this.finalRating,
                    finalComment: this.finalComment,
                    completedDate: this.completedDate,
                  })
                : null;
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
