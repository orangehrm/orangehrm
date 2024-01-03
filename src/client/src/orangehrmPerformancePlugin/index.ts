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

import MyTracker from './pages/MyTracker.vue';
import EmployeeTrackers from './pages/EmployeeTrackers.vue';
import KpiEdit from './pages/KpiEdit.vue';
import KpiSave from './pages/KpiSave.vue';
import KpiSearch from './pages/KpiSearch.vue';
import AddReview from './pages/AddReview.vue';
import EditReview from './pages/EditReview.vue';
import PerformanceTrackerList from './pages/PerformanceTrackers.vue';
import SavePerformanceTracker from './pages/SavePerformanceTracker.vue';
import EditPerformanceTracker from './pages/EditPerformanceTracker.vue';
import MyReviews from './pages/MyReviews.vue';
import ReviewList from './pages/ReviewList.vue';
import ReviewSearch from './pages/ReviewSearch.vue';
import EmployeeTrackerLogs from './pages/EmployeeTrackerLogs.vue';
import AdminEvaluation from './pages/AdminEvaluation.vue';
import SelfEvaluation from './pages/SelfEvaluation.vue';

export default {
  'my-tracker': MyTracker,
  'employee-trackers': EmployeeTrackers,
  'kpi-search': KpiSearch,
  'kpi-save': KpiSave,
  'kpi-edit': KpiEdit,
  'add-review': AddReview,
  'edit-review': EditReview,
  'performance-tracker-list': PerformanceTrackerList,
  'performance-tracker-save': SavePerformanceTracker,
  'performance-tracker-edit': EditPerformanceTracker,
  'my-reviews': MyReviews,
  'review-list': ReviewList,
  'review-search': ReviewSearch,
  'employee-tracker-logs': EmployeeTrackerLogs,
  'admin-evaluation': AdminEvaluation,
  'self-evaluation': SelfEvaluation,
};
