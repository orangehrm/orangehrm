<!--
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
 -->

<template>
  <div class="orangehrm-background-container">
    <review-confirm-modal ref="confirmDialog"> </review-confirm-modal>
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
      <div v-if="status < 4">
        <evaluation-form
          v-model="supervisorReview"
          :kpis="kpis"
          :rules="rules"
          :editable="false"
          :collapsible="false"
          :collapsed="true"
          :employee="supervisor.details"
          :job-title="supervisor.jobTitle"
          :status="supervisor.status"
          :title="$t('performance.supervisor_evaluation_by')"
        ></evaluation-form>
        <br />
        <evaluation-form
          v-model="employeeReview"
          :kpis="kpis"
          :rules="rules"
          :editable="employee.status < 3"
          :collapsed="false"
          :collapsible="true"
          :employee="employee.details"
          :job-title="employee.jobTitle"
          :status="employee.status"
          :title="$t('performance.self_evaluation_by')"
        >
          <oxd-form-actions v-show="hasActions">
            <oxd-divider />
            <div class="orangehrm-performance-review-actions">
              <oxd-button
                v-if="hasCancelAction"
                display-type="ghost"
                :label="$t('general.cancel')"
                @click="onClickCancel"
              />
              <oxd-button
                v-if="hasSaveAction"
                display-type="ghost"
                type="button"
                :label="$t('general.save')"
                @click="onSubmit(false)"
              />
              <oxd-button
                v-if="hasCompleteAction"
                type="button"
                display-type="secondary"
                :label="$t('performance.complete')"
                @click="onSubmit(true)"
              />
            </div>
          </oxd-form-actions>
        </evaluation-form>
      </div>
      <div v-if="status === 4">
        <evaluation-form
          v-model="employeeReview"
          :kpis="kpis"
          :rules="rules"
          :editable="false"
          :collapsed="false"
          :collapsible="true"
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
          :editable="false"
          :collapsible="true"
          :collapsed="false"
          :employee="supervisor.details"
          :job-title="supervisor.jobTitle"
          :status="supervisor.status"
          :title="$t('performance.supervisor_evaluation_by')"
        >
          <oxd-divider />
          <final-evaluation
            v-model:final-rating="finalRating"
            v-model:final-comment="finalComment"
            v-model:completed-date="completedDate"
            :status="status"
            :is-required="false"
          />
        </evaluation-form>
      </div>
    </oxd-form>
  </div>
</template>

<script>
import {navigate, reloadPage} from '@/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import ReviewSummary from '../components/ReviewSummary';
import FinalEvaluation from '../components/FinalEvaluation';
import EvaluationForm from '../components/EvaluationForm';
import useForm from '@ohrm/core/util/composable/useForm';
import useReviewEvaluation from '@/orangehrmPerformancePlugin/util/composable/useReviewEvaluation';
import ReviewConfirmModal from '@/orangehrmPerformancePlugin/components/ReviewConfirmModal';

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
  name: 'SelfEvaluation',
  components: {
    'review-summary': ReviewSummary,
    'final-evaluation': FinalEvaluation,
    'evaluation-form': EvaluationForm,
    'review-confirm-modal': ReviewConfirmModal,
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
  },
  setup() {
    const {formRef, invalid, validate} = useForm();
    const http = new APIService(window.appGlobal.baseUrl, '');

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
    };
  },
  computed: {
    hasSaveAction() {
      return this.employee.actions.has('save');
    },
    hasCompleteAction() {
      return this.employee.actions.has('complete');
    },
    hasCancelAction() {
      return !(this.status === 4 || this.employee?.status === 3);
    },
    hasActions() {
      return (
        this.hasSaveAction || this.hasCancelAction || this.hasCompleteAction
      );
    },
  },
  beforeMount() {
    this.isLoading = true;
    this.getAllKpis(this.reviewId)
      .then((response) => {
        const {data} = response.data;
        this.kpis = [...data];
        this.rules = this.generateRules(data);
        this.employeeReview = this.generateModel(data);
        this.supervisorReview = this.generateModel(data);
        return this.getEmployeeReview(this.reviewId);
      })
      .then((response) => {
        const {data} = response.data;
        const {meta} = response.data;
        this.employee = this.generateReviewerData(meta.reviewer);
        this.employee.actions = this.generateAllowedActions(
          meta.allowedActions,
        );
        this.employeeReview = this.generateEvaluationFormData(
          data,
          meta.generalComment,
          this.employeeReview.kpis,
        );
        return this.getSupervisorReview(this.reviewId);
      })
      .then((response) => {
        const {data} = response.data;
        const {meta} = response.data;
        this.supervisor = this.generateReviewerData(meta.reviewer);
        this.supervisor.actions = this.generateAllowedActions(
          meta.allowedActions,
        );
        this.supervisorReview = this.generateEvaluationFormData(
          data,
          meta.generalComment,
          this.supervisorReview.kpis,
        );
        return this.status === 4 ? this.getFinalReview(this.reviewId) : {};
      })
      .then((response) => {
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
        .then(async () => {
          if (this.invalid === true) return;
          if (complete) {
            this.$refs.confirmDialog.showDialog().then((confirmation) => {
              if (confirmation === 'ok') {
                this.submitReview(true);
              }
            });
          } else {
            this.submitReview(false);
          }
        });
    },
    submitReview(complete = false) {
      this.isLoading = true;
      this.saveEmployeeReview(this.reviewId, complete, this.employeeReview)
        .then(() => {
          return this.$toast.saveSuccess();
        })
        .finally(() => {
          reloadPage();
        });
    },
    onClickCancel() {
      navigate('/performance/myPerformanceReview');
    },
  },
};
</script>

<style src="./review-evaluate.scss" lang="scss" scoped></style>
