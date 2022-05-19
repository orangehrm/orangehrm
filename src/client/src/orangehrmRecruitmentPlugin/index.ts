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

import ApplicationInitiatedAction from './pages/ApplicationInitiatedAction.vue';
import HiredAction from './pages/HiredAction.vue';
import InterviewFailedAction from './pages/InterviewFailedAction.vue';
import InterviewPassedAction from './pages/InterviewPassedAction.vue';
import InterviewScheduleAction from './pages/InterviewScheduleAction.vue';
import JobOfferedAction from './pages/JobOfferedAction.vue';
import OfferDeclinedAction from './pages/OfferDeclinedAction.vue';
import RejectAction from './pages/RejectAction.vue';
import ShortlistAction from './pages/ShortlistAction.vue';
import ShortlistCandidateScreen from './pages/ShortlistCandidateScreen.vue';
import ShortlistHistoryScreen from './pages/ShortlistHistoryScreen.vue';
import ScheduleInterview from './pages/ScheduleInterview.vue';
import ScheduleInterviewHistory from './pages/ScheduleInterviewHistory.vue';
import ViewCandidate from './pages/ViewCandidate.vue';
import SaveCandidate from './pages/SaveCandidate.vue';

export default {
  'application-initiated-action': ApplicationInitiatedAction,
  'hired-action': HiredAction,
  'interview-failed-action': InterviewFailedAction,
  'interview-passed-action': InterviewPassedAction,
  'interview-schedule-action': InterviewScheduleAction,
  'job-offered-action': JobOfferedAction,
  'reject-action': RejectAction,
  'shortlist-action': ShortlistAction,
  'offer-declined-action': OfferDeclinedAction,
  'schedule-interview': ScheduleInterview,
  'schedule-interview-history': ScheduleInterviewHistory,
  'shortlist-candidate': ShortlistCandidateScreen,
  'shortlist-history': ShortlistHistoryScreen,
  'view-candidate': ViewCandidate,
  'save-candidate': SaveCandidate,
};
