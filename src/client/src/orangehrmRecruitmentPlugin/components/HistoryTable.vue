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
  <div class="orangehrm-paper-container">
    <div class="orangehrm-header-container">
      <oxd-text tag="h6" class="orangehrm-main-title">
        {{ $t('recruitment.candidate_history') }}
      </oxd-text>
    </div>
    <table-header
      :selected="0"
      :total="total"
      :loading="isLoading"
    ></table-header>
    <div class="orangehrm-container">
      <oxd-card-table
        :headers="headers"
        :items="items?.data"
        :clickable="false"
        :selectable="false"
        :loading="isLoading"
        row-decorator="oxd-table-decorator-card"
      />
    </div>
    <div class="orangehrm-bottom-container">
      <oxd-pagination
        v-if="showPaginator"
        v-model:current="currentPage"
        :length="pages"
      />
    </div>
  </div>
</template>

<script>
import {APIService} from '@/core/util/services/api.service';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import useLocale from '@/core/util/composable/useLocale';
import useDateFormat from '@/core/util/composable/useDateFormat';
import useEmployeeNameTranslate from '@/core/util/composable/useEmployeeNameTranslate';
import {formatDate, parseDate} from '@/core/util/helper/datefns';
import {navigate} from '@/core/util/helper/navigation';
import usei18n from '@/core/util/composable/usei18n';

const ACTION_ASSIGNED_VACANCY = 1;
const ACTION_SHORTLISTED = 2;
const ACTION_REJECTED = 3;
const ACTION_INTERVIEW_SCHEDULED = 4;
const ACTION_INTERVIEW_PASSED = 5;
const ACTION_INTERVIEW_FAILED = 6;
const ACTION_JOB_OFFERED = 7;
const ACTION_OFFER_DECLINED = 8;
const ACTION_HIRED = 9;
const ACTION_REMOVED = 15;
const ACTION_ADDED = 16;
const ACTION_APPLIED = 17;

export default {
  name: 'HistoryTable',
  props: {
    candidate: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const {$t} = usei18n();
    const {locale} = useLocale();
    const {jsDateFormat} = useDateFormat();
    const {$tEmpName} = useEmployeeNameTranslate();

    const http = new APIService(
      window.appGlobal.baseUrl,
      `/api/v2/recruitment/candidates/${props.candidate?.id}/history`,
    );

    const historyDataNormalizer = (data) => {
      const candidateName = `${props.candidate?.firstName} ${
        props.candidate?.middleName || ''
      } ${props.candidate?.lastName}`;
      return data.map((item) => {
        let description = null;
        const interview = {
          name: '',
          data: '',
          interviewers: '',
        };
        const performerName = $tEmpName(item.performedBy, {
          includeMiddle: true,
          excludePastEmpTag: false,
        });
        if (item.interview) {
          interview.name = item.interview.name;
          interview.date = item.interview.date;
          const interviewers = item.interview.interviewers.map(
            (interviewer) => {
              return $tEmpName(interviewer, {
                includeMiddle: true,
                excludePastEmpTag: false,
              });
            },
          );
          interview.interviewers = interviewers.join(', ');
        }

        switch (item?.action.id) {
          case ACTION_APPLIED:
            description = $t('recruitment.candidate_applied_for_the_vacancy', {
              candidate: candidateName,
              vacancy: item.vacancyName,
            });
            break;
          case ACTION_ADDED:
            description = $t('recruitment.employee_added_candidate', {
              employee: performerName,
              candidate: candidateName,
            });
            break;
          case ACTION_ASSIGNED_VACANCY:
            description = $t(
              'recruitment.employee_assigned_vacancy_to_candidate',
              {
                employee: performerName,
                vacancy: item.vacancyName,
              },
            );
            break;
          case ACTION_SHORTLISTED:
            description = $t(
              'recruitment.candidate_shortlisted_for_vacancy_by_employee',
              {
                vacancy: item.vacancyName,
                employee: performerName,
              },
            );
            break;
          case ACTION_REJECTED:
            description = $t('recruitment.employee_rejected_the_candidate', {
              employee: performerName,
              candidate: candidateName,
              vacancy: item.vacancyName,
            });
            break;
          case ACTION_INTERVIEW_SCHEDULED:
            description = $t(
              'recruitment.employee_schedule_interview_with_interviewers',
              {
                employee: performerName,
                interview: interview.name,
                interviewDate: interview.date,
                interviewers: interview.interviewers,
                vacancy: item.vacancyName,
              },
            );
            break;
          case ACTION_INTERVIEW_PASSED:
            description = $t('recruitment.employee_marked_interveiw_passed', {
              employee: performerName,
              interview: interview.name,
              vacancy: item.vacancyName,
            });
            break;
          case ACTION_INTERVIEW_FAILED:
            description = $t('recruitment.employee_marked_interveiw_failed', {
              employee: performerName,
              interview: interview.name,
              vacancy: item.vacancyName,
            });
            break;
          case ACTION_JOB_OFFERED:
            description = $t('recruitment.employee_offered_the_job', {
              employee: performerName,
              vacancy: item.vacancyName,
            });
            break;
          case ACTION_OFFER_DECLINED:
            description = $t('recruitment.employee_marked_the_offer_declined', {
              employee: performerName,
              vacancy: item.vacancyName,
            });
            break;
          case ACTION_HIRED:
            description = $t('recruitment.employee_hired_the_candidate', {
              employee: performerName,
              candidate: candidateName,
              vacancy: item.vacancyName,
            });
            break;
          case ACTION_REMOVED:
            description = $t(
              'recruitment.employee_removed_candidate_from_vacancy',
              {
                employee: performerName,
                candidate: candidateName,
                vacancy: item.vacancyName,
              },
            );
            break;
        }

        return {
          ...item,
          description: description,
          performedDate: formatDate(
            parseDate(item.performedDate),
            jsDateFormat,
            {
              locale,
            },
          ),
        };
      });
    };

    const {
      showPaginator,
      currentPage,
      total,
      pages,
      pageSize,
      response,
      isLoading,
      execQuery,
    } = usePaginate(http, {normalizer: historyDataNormalizer});

    return {
      http,
      showPaginator,
      currentPage,
      isLoading,
      total,
      pages,
      pageSize,
      execQuery,
      items: response,
    };
  },

  data() {
    return {
      headers: [
        {
          name: 'performedDate',
          slot: 'title',
          title: this.$t('recruitment.performed_date'),
          style: {flex: '20%'},
        },
        {
          name: 'description',
          title: this.$t('general.description'),
          style: {flex: '65%'},
        },
        {
          name: 'actions',
          slot: 'action',
          title: this.$t('general.actions'),
          style: {flex: '15%'},
          cellType: 'oxd-table-cell-actions',
          cellRenderer: this.cellRenderer,
        },
      ],
    };
  },
  watch: {
    candidate() {
      this.execQuery();
    },
  },
  methods: {
    cellRenderer(...[, , , row]) {
      const cellConfig = {};

      if (
        row.action?.id != ACTION_ASSIGNED_VACANCY &&
        row.action?.id != ACTION_ADDED &&
        row.action?.id != ACTION_REMOVED &&
        row.action?.id != ACTION_APPLIED &&
        row.editable
      ) {
        cellConfig.edit = {
          onClick: this.onClickEdit,
          props: {
            name: 'pencil-fill',
          },
        };
      }

      if (
        (row.action?.id === ACTION_INTERVIEW_SCHEDULED ||
          row.action?.id === ACTION_INTERVIEW_PASSED ||
          row.action?.id === ACTION_INTERVIEW_FAILED) &&
        row.editable
      ) {
        cellConfig.attachment = {
          onClick: this.onClickAttachment,
          props: {
            name: 'paperclip',
          },
        };
      }

      return {
        props: {
          header: {
            cellConfig,
          },
        },
      };
    },

    onClickEdit(item) {
      navigate('/recruitment/candidateHistory/{candidateId}/{historyId}', {
        candidateId: this.candidate.id,
        historyId: item.id,
      });
    },

    onClickAttachment(item) {
      navigate('/recruitment/interviewAttachments/{interviewId}', {
        interviewId: item.interview?.id,
      });
    },
  },
};
</script>
<style lang="scss" scoped>
.orangehrm-card-container {
  padding: 1.2rem 0;
}
</style>
