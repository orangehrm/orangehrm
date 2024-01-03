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

import ClaimEvent from '@/orangehrmClaimPlugin/pages/ClaimEvent.vue';
import SaveClaimEvent from '@/orangehrmClaimPlugin/pages/SaveClaimEvent.vue';
import EditClaimEvent from '@/orangehrmClaimPlugin/pages/EditClaimEvent.vue';
import ClaimExpenseType from '@/orangehrmClaimPlugin/pages/claimExpenseTypes/ClaimExpenseType.vue';
import SaveClaimExpenseType from '@/orangehrmClaimPlugin/pages/claimExpenseTypes/SaveClaimExpenseType.vue';
import EditClaimExpenseType from '@/orangehrmClaimPlugin/pages/claimExpenseTypes/EditClaimExpenseType.vue';
import SubmitClaimRequest from '@/orangehrmClaimPlugin/pages/submitClaim/SubmitClaimRequest.vue';
import SubmitClaim from '@/orangehrmClaimPlugin/pages/submitClaim/SubmitClaim.vue';
import MyClaims from '@/orangehrmClaimPlugin/pages/myClaims/MyClaims.vue';
import AssignClaimRequest from '@/orangehrmClaimPlugin/pages/assignClaim/AssignClaimRequest.vue';
import AssignClaim from '@/orangehrmClaimPlugin/pages/assignClaim/AssignClaim.vue';
import EmployeeClaims from '@/orangehrmClaimPlugin/pages/employeeClaims/EmployeeClaims.vue';

export default {
  'claim-event': ClaimEvent,
  'claim-event-create': SaveClaimEvent,
  'claim-event-edit': EditClaimEvent,
  'claim-expense-types': ClaimExpenseType,
  'claim-expense-type-create': SaveClaimExpenseType,
  'claim-expense-type-edit': EditClaimExpenseType,
  'submit-claim-request': SubmitClaimRequest,
  'submit-claim': SubmitClaim,
  'my-claim': MyClaims,
  'assign-claim-request': AssignClaimRequest,
  'assign-claim': AssignClaim,
  'employee-claim': EmployeeClaims,
};
