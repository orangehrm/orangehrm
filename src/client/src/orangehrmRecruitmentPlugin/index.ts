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

import ViewJobVacancy from './pages/ViewJobVacancy.vue';
import AddJobVacancy from './pages/AddJobVacancy.vue';
import EditJobVacancy from './pages/EditJobVacancy.vue';
import HireAction from './pages/HireAction.vue';
import InterviewFailedAction from './pages/InterviewFailAction.vue';
import InterviewPassedAction from './pages/InterviewPassedAction.vue';
import InterviewScheduleAction from './pages/InterviewScheduleAction.vue';
import OfferJobAction from './pages/OfferJobAction.vue';
import OfferDeclinedAction from './pages/DeclineOfferAction.vue';
import RejectAction from './pages/RejectAction.vue';
import ShortlistAction from './pages/ShortlistAction.vue';
import SaveCandidate from './pages/SaveCandidate.vue';
import ViewCandidatesList from './pages/ViewCandidatesList.vue';
import ViewCandidateProfile from './pages/ViewCandidateProfile.vue';
import ViewActionHistory from './pages/ViewActionHistory.vue';
import VacancyList from './pages/VacancyList.vue';
import ApplyJobVacancy from './pages/ApplyJobVacancy.vue';
import ViewInterviewAttachments from './pages/ViewInterviewAttachments.vue';

export default {
  'view-job-vacancy': ViewJobVacancy,
  'add-job-vacancy': AddJobVacancy,
  'edit-job-vacancy': EditJobVacancy,
  'save-candidate': SaveCandidate,
  'view-candidates-list': ViewCandidatesList,
  'view-candidate-profile': ViewCandidateProfile,
  'shortlist-action': ShortlistAction,
  'reject-action': RejectAction,
  'interview-schedule-action': InterviewScheduleAction,
  'interview-passed-action': InterviewPassedAction,
  'interview-failed-action': InterviewFailedAction,
  'offer-job-action': OfferJobAction,
  'offer-decline-action': OfferDeclinedAction,
  'hire-action': HireAction,
  'view-action-history': ViewActionHistory,
  'vacancy-list': VacancyList,
  'apply-job-vacancy': ApplyJobVacancy,
  'view-interview-attachments': ViewInterviewAttachments,
};
