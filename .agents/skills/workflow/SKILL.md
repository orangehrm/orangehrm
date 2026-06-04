---
name: workflow
description: Reference for OrangeHRM's workflow state machine — the `WorkflowStateMachine` entity that models `(workflow, state, role, action) → resultingState` transitions in `ohrm_workflow_state_machine`, the eight `FLOW_*` constants for the workflow types (Leave, Recruitment, Timesheet, Attendance, Employee, Review, Self-Review, Claim), `AccessFlowStateMachineService` for querying allowed actions / states / next-state lookups, the per-flow `<ACTION>_*` integer constants on the entity (e.g. `LEAVE_ACTION_APPROVE`, `CLAIM_ACTION_SUBMIT`), and the typical pattern of dispatching an event after the state transition is persisted so subscribers can react (notifications, audits). Use whenever the user is adding a new workflow transition, debugging "why can this role not approve this leave", asking about state-machine state strings, or wiring a new approval-style feature. Companion to `events` (transitions dispatch events), `mail` (transitions are the primary trigger for notification emails), `authorization` (workflow access is layered on top of the data-group permission model — both apply).
---

# Workflow state machine

OrangeHRM has a **single, generic state machine table** that drives all approval-style flows: leave requests, timesheet submissions, recruitment applications, claim approvals, performance reviews, employee lifecycle events, attendance punches.

The table `ohrm_workflow_state_machine` stores rows of the form:

```
workflow:        which flow ('1' for Attendance, '4' for Leave, '7' for Claim, …)
state:           the current state of the thing being acted on ('PENDING APPROVAL')
role:            the user role taking the action ('ESS', 'Supervisor', 'Admin')
action:          the action ID (an integer specific to each flow)
resulting_state: the state after the action ('APPROVED')
```

A workflow run looks like: "I am role X, the thing is in state Y, can I do action Z?" The service queries the table, finds (if any) the matching row, and returns the resulting state. The application then transitions the entity and (usually) dispatches an event.

This skill covers the model, the service API, and the patterns. For the events that fire after transitions, see `events`. For the emails those events trigger, see `mail`.

## The model — `WorkflowStateMachine` entity

`OrangeHRM\Entity\WorkflowStateMachine` mapped to `ohrm_workflow_state_machine`:

```php
@ORM\Column(name="workflow", type="string", length=255)
$workflow;                  // FLOW_* constant (stored as the integer-as-string '4' for Leave, etc.)

@ORM\Column(name="state", type="string", length=255)
$state;                     // Current state, e.g. 'PENDING APPROVAL', 'APPROVED'

@ORM\Column(name="role", type="string", length=255)
$role;                      // User role, e.g. 'ESS', 'Supervisor', 'Admin'

@ORM\Column(name="action", type="string", length=255)
$action;                    // Action ID, e.g. '2' for LEAVE_ACTION_APPROVE

@ORM\Column(name="resulting_state", type="string", length=255)
$resultingState;            // State after the action
```

Plus the `id` primary key and the standard Decorator trait (see `entities` skill).

**Important: the values are strings**, even when conceptually integers. The action `2` is stored as `'2'`. Comparisons are string-equality. Use the entity's constants (which are integer-typed in PHP) and let the framework coerce them.

## The workflow type constants — `FLOW_*`

```php
class WorkflowStateMachine
{
    public const FLOW_TIME_TIMESHEET = 0;
    public const FLOW_ATTENDANCE     = 1;
    public const FLOW_RECRUITMENT    = 2;
    public const FLOW_EMPLOYEE       = 3;
    public const FLOW_LEAVE          = 4;
    public const FLOW_REVIEW         = 5;
    public const FLOW_SELF_REVIEW    = 6;
    public const FLOW_CLAIM          = 7;
}
```

Each flow has its own per-flow `<DOMAIN>_ACTION_*` constants. Examples from the entity:

```php
// Leave (FLOW_LEAVE = 4)
public const LEAVE_ACTION_APPROVE = 2;
public const LEAVE_ACTION_CANCEL  = 3;
public const LEAVE_ACTION_REJECT  = 4;
// (full list — see the entity file)

// Timesheet (FLOW_TIME_TIMESHEET = 0)
public const TIMESHEET_ACTION_VIEW    = 0;
public const TIMESHEET_ACTION_SUBMIT  = 1;
public const TIMESHEET_ACTION_APPROVE = 2;
public const TIMESHEET_ACTION_REJECT  = 3;
public const TIMESHEET_ACTION_RESET   = 4;
public const TIMESHEET_ACTION_MODIFY  = 5;
public const TIMESHEET_ACTION_CREATE  = 7;

// Claim (FLOW_CLAIM = 7)
public const CLAIM_ACTION_SUBMIT  = 1;
public const CLAIM_ACTION_APPROVE = 2;
public const CLAIM_ACTION_PAY     = 3;
public const CLAIM_ACTION_CANCEL  = 4;
public const CLAIM_ACTION_REJECT  = 5;

// Employee (FLOW_EMPLOYEE = 3)
public const EMPLOYEE_ACTION_ADD               = 1;
public const EMPLOYEE_ACTION_DELETE_ACTIVE     = 2;
public const EMPLOYEE_ACTION_TERMINATE         = 3;
public const EMPLOYEE_ACTION_REACTIVE          = 4;
public const EMPLOYEE_ACTION_DELETE_TERMINATED = 5;

// Recruitment (FLOW_RECRUITMENT = 2)
public const RECRUITMENT_APPLICATION_ACTION_ATTACH_VACANCY     = 1;
public const RECRUITMENT_APPLICATION_ACTION_SHORTLIST          = 2;
public const RECRUITMENT_APPLICATION_ACTION_REJECT             = 3;
// … many more

// Review (FLOW_REVIEW = 5)
public const REVIEW_INACTIVE_SAVE     = 1;
public const REVIEW_ACTIVATE          = 2;
public const REVIEW_IN_PROGRESS_SAVE  = 3;
public const REVIEW_COMPLETE          = 4;
```

States are **strings**, often domain-specific (e.g. `'PENDING APPROVAL'`, `'APPROVED'`, `'CANCELLED'`, `'REJECTED'`, `'TAKEN'`, `'SCHEDULED'`). The state vocabulary is defined per flow — leave has its set, claim has its set, etc.

When in doubt about what state strings exist for a flow, query the table:

```sql
SELECT DISTINCT state FROM ohrm_workflow_state_machine WHERE workflow = '4';
SELECT DISTINCT resulting_state FROM ohrm_workflow_state_machine WHERE workflow = '4';
```

## `AccessFlowStateMachineService` — querying the state machine

`OrangeHRM\Core\Service\AccessFlowStateMachineService`. **The only service that talks to `ohrm_workflow_state_machine` directly.** Domain services (LeaveRequestService, TimesheetService, etc.) wrap this for their specific flow.

```php
$svc = new AccessFlowStateMachineService();

// "What actions can this role take from this state?"
$actions = $svc->getAllowedActions(
    workflow: (string) WorkflowStateMachine::FLOW_LEAVE,
    state: 'PENDING APPROVAL',
    role: 'Supervisor',
);
// → ['2', '3', '4']  (APPROVE, CANCEL, REJECT — as strings)

// "Where does this take us?"
$next = $svc->getNextState(
    workflow: (string) WorkflowStateMachine::FLOW_LEAVE,
    state:    'PENDING APPROVAL',
    role:     'Supervisor',
    action:   (string) WorkflowStateMachine::LEAVE_ACTION_APPROVE,
);
// → 'APPROVED'

// "What WorkflowStateMachine rows match this combo?"
$workflow = $svc->getWorkflowItemByStateActionAndRole(
    (string) WorkflowStateMachine::FLOW_LEAVE,
    'PENDING APPROVAL',
    'Supervisor',
    (string) WorkflowStateMachine::LEAVE_ACTION_APPROVE,
);
// → WorkflowStateMachine entity with all fields populated

// "What workflow items exist for this flow, optionally filtered by role?"
$items = $svc->getWorkFlowStateMachineRecords(
    workflow: (string) WorkflowStateMachine::FLOW_LEAVE,
    role: 'Supervisor',
);
// → WorkflowStateMachine[]
```

The service has more flow-specific helpers (`getAllAlowedRecruitmentApplicationStates`, `getActionableStates`, etc.) — see the source for the full surface.

**Key methods**:

| Method | Returns | Use for |
|---|---|---|
| `getAllowedActions($workflow, $state, $role)` | `string[]` of action IDs | UI: which buttons to show |
| `getNextState($workflow, $state, $role, $action)` | `?string` (state) or `null` if action not allowed | Compute the new state after the user clicks |
| `getWorkflowItemByStateActionAndRole(...)` | `?WorkflowStateMachine` | When you need the full row (e.g. for notification routing — the Decorator has `getRolesToNotify()`) |
| `getActionableStates($workflow, $role, $actions)` | `?string[]` | "List items in any state where this role can do one of these actions" — used by approval queues |

All methods cache results in `$allowedWorkflowItemCache` per-request — the same query in two places doesn't re-query.

## The typical transition flow

A leave-approval flow walks like this:

```
1. Supervisor clicks "Approve" on a Leave entity in state 'PENDING APPROVAL'
   ↓
2. LeaveRequestService::approve(LeaveRequest $req)
   ↓
3. Compute next state via $accessFlowService->getNextState('4', 'PENDING APPROVAL', 'Supervisor', '2')
   → returns 'APPROVED'
   ↓
4. Update LeaveRequest entity: $req->setStatus('APPROVED')
   ↓
5. Persist via DAO
   ↓
6. Dispatch LeaveApprove event (see events skill)
   ↓
7. LeaveEventSubscriber catches it
   → queueEmailNotifications('leave.approve', ...) (see mail skill)
   ↓
8. Response returned to UI; on TERMINATE, email sent
```

The state machine is the **decision** ("can this transition happen and what's the new state?"). The domain service is the **executor** ("update the entity and persist, then dispatch an event").

## Two-layer authorization

Workflow access is **layered on top of** the data-group permission model (see `authorization` skill). Both have to pass:

1. **Data-group permission check** — does this user's role have `can_update` on `apiv2_leave_leave_requests`? If not → 403, no transition possible. Enforced by `ApiAuthorizationSubscriber`.

2. **Workflow transition check** — even with permission to update, the role might not have a row in `ohrm_workflow_state_machine` for `(workflow=4, state='PENDING APPROVAL', role='Supervisor', action=2)`. If no row → no allowed action, regardless of CRUD permission.

The two layers serve different concerns:
- **Permissions**: "in general, can this role touch this resource?"
- **Workflow**: "in this specific state, can this role take this specific action?"

A Supervisor has `can_update` on leave records always — that's a permission. But they can only *approve* a leave when it's in `PENDING APPROVAL` — that's a workflow transition. Trying to approve an already-`APPROVED` leave returns `null` from `getNextState`, and the service treats it as a no-op or error.

## Seeding workflow rows via migration

Workflow rows are seeded by **migrations**, not by code at runtime. When a new feature adds a flow (or a new transition to an existing flow), the migration inserts the appropriate rows into `ohrm_workflow_state_machine`.

There's no YAML helper for this in the current codebase — workflow seeds are typically raw `createQueryBuilder()->insert()` calls inside the migration. Look at the recruitment / leave / claim migrations for examples.

```php
// Representative pattern (paraphrased)
public function up(): void
{
    $rows = [
        // (workflow, state, role, action, resulting_state)
        ['4', 'PENDING APPROVAL', 'Supervisor', '2', 'APPROVED'],   // approve
        ['4', 'PENDING APPROVAL', 'Supervisor', '4', 'REJECTED'],   // reject
        ['4', 'PENDING APPROVAL', 'ESS',        '3', 'CANCELLED'],  // cancel (own)
        ['4', 'APPROVED',         'ESS',        '3', 'CANCELLED'],  // cancel after approval
        ['4', 'PENDING APPROVAL', 'Admin',      '2', 'APPROVED'],
        // …
    ];
    foreach ($rows as $r) {
        $this->createQueryBuilder()
            ->insert('ohrm_workflow_state_machine')
            ->values([
                'workflow' => ':workflow',
                'state'    => ':state',
                'role'     => ':role',
                'action'   => ':action',
                'resulting_state' => ':resulting_state',
            ])
            ->setParameter('workflow', $r[0])
            ->setParameter('state',    $r[1])
            ->setParameter('role',     $r[2])
            ->setParameter('action',   $r[3])
            ->setParameter('resulting_state', $r[4])
            ->executeStatement();
    }
}
```

See `migrations` skill for the migration mechanics.

## The Decorator — `WorkflowStateMachineDecorator`

`OrangeHRM\Entity\Decorator\WorkflowStateMachineDecorator` (see `entities` skill for the Decorator pattern in general). The leave subscriber uses it:

```php
$workflow = $allocateEvent->getWorkflow();
$recipientRoles = $workflow->getDecorator()->getRolesToNotify();
$performerRole  = strtolower($workflow->getRole());
```

`getRolesToNotify()` returns roles that should be notified when the workflow transitions — e.g., when a Supervisor approves a leave, the ESS who applied should be notified. This is computed from the workflow row's structure (out of scope for this skill, but the entity has the data needed).

## When to use the workflow vs. simple status field

**Use the workflow state machine** when:
- Multiple roles can take different actions from the same state
- The same action can have different effects depending on role (ESS cancel vs Supervisor reject)
- Transitions need to be auditable and inspectable from outside the code
- Operators / admins might want to configure transitions without a code change

**Don't use it** when:
- Status is purely informational, not transitionable (e.g. "active / inactive" toggle)
- Only one role ever touches the resource
- The state space is so small (2-3 states) that overhead exceeds value

The codebase reserves the state machine for the **complex multi-role approval flows** (leave, claim, recruitment, review). Simple status fields stay as enum strings on the entity.

---

# Recipes

## Recipe 1 — Query "can this user do this action?"

```php
class LeaveRequestService
{
    private ?AccessFlowStateMachineService $accessFlowService = null;

    public function getAccessFlowService(): AccessFlowStateMachineService
    {
        return $this->accessFlowService ??= new AccessFlowStateMachineService();
    }

    public function canApprove(LeaveRequest $req, string $userRole): bool
    {
        $allowedActions = $this->getAccessFlowService()->getAllowedActions(
            (string) WorkflowStateMachine::FLOW_LEAVE,
            $req->getStatus(),
            $userRole,
        );
        return in_array((string) WorkflowStateMachine::LEAVE_ACTION_APPROVE, $allowedActions ?? [], true);
    }
}
```

Use this from API endpoints / handlers to decide whether to show / hide the action button on the frontend.

## Recipe 2 — Execute a transition

```php
class LeaveRequestService
{
    use EventDispatcherTrait;

    public function approve(LeaveRequest $req, string $userRole): LeaveRequest
    {
        $nextState = $this->getAccessFlowService()->getNextState(
            (string) WorkflowStateMachine::FLOW_LEAVE,
            $req->getStatus(),
            $userRole,
            (string) WorkflowStateMachine::LEAVE_ACTION_APPROVE,
        );

        if ($nextState === null) {
            throw new BadRequestException('Approval not allowed in current state for this role');
        }

        $req->setStatus($nextState);
        $req = $this->getLeaveRequestDao()->saveLeaveRequest($req);

        $this->getEventDispatcher()->dispatch(
            new LeaveApprove($req, /* workflow */),
            LeaveEvent::APPROVE,
        );

        return $req;
    }
}
```

Standard pattern: **compute next state → mutate entity → persist → dispatch event**. The event picks up the rest (notifications, audits, downstream side effects).

## Recipe 3 — UI button visibility based on allowed actions

```ts
// Vue component for a leave-request row
const allowedActions = ref([]);

onMounted(async () => {
  const response = await http.get(leaveRequestId, { include: 'allowedActions' });
  allowedActions.value = response.data.data.allowedActions;
});
```

```vue
<oxd-button v-if="allowedActions.includes('2')" @click="onApprove" :label="$t('leave.approve')" />
<oxd-button v-if="allowedActions.includes('4')" @click="onReject"  :label="$t('leave.reject')" />
<oxd-button v-if="allowedActions.includes('3')" @click="onCancel"  :label="$t('leave.cancel')" />
```

Backend endpoint computes `allowedActions` via `AccessFlowStateMachineService::getAllowedActions()` for the current user role + leave state. Frontend just checks membership.

Compare to the `$can` approach (see `authorization`): `$can` is for **resource-level** permissions ("can this role touch leave at all?"), workflow actions are for **state-specific** ones ("is approval available right now?"). Both should pass for an action to actually work.

## Recipe 4 — Find "approval queue" items for a role

```php
// "Show me all leave requests this Supervisor can act on"
$states = $this->getAccessFlowService()->getActionableStates(
    (string) WorkflowStateMachine::FLOW_LEAVE,
    'Supervisor',
    [
        (string) WorkflowStateMachine::LEAVE_ACTION_APPROVE,
        (string) WorkflowStateMachine::LEAVE_ACTION_REJECT,
    ],
);
// → ['PENDING APPROVAL', ...]

$pendingForMe = $this->getLeaveRequestDao()->findInStates($states, $myEmpNumber);
```

`getActionableStates` is the inverse of `getAllowedActions`: "which states put items in my queue?" Used for approval-queue UIs and dashboard widgets ("3 items awaiting your action").

---

# Checklists

## Add a new transition to an existing workflow

- [ ] Identify the `FLOW_*` constant for the workflow
- [ ] Identify or define the action ID (a new `<DOMAIN>_ACTION_*` constant if needed)
- [ ] Migration inserts the row(s) into `ohrm_workflow_state_machine` with `(workflow, state, role, action, resulting_state)`
- [ ] Add the action constant to `WorkflowStateMachine` entity if it's new
- [ ] Wire the action in the domain service: compute next state, update entity, persist, dispatch event
- [ ] (Optional) Add a notification email — see `mail` skill (subscriber to the event)

## Add a new workflow entirely

- [ ] Add a new `FLOW_*` constant to `WorkflowStateMachine` (next integer)
- [ ] Define the action constants for the flow (`<DOMAIN>_ACTION_*`)
- [ ] Migration seeds the initial transitions (rows for each `(state, role, action) → resulting_state` combination)
- [ ] Define an Event base class for the flow (see `events`); each transition fires a specific subclass
- [ ] Domain service: methods for each transition, all using `AccessFlowStateMachineService` for permission/next-state logic
- [ ] Subscribers for notifications — see `mail`
- [ ] Optional: matching data-group permissions (`apiv2_<flow>_*`) so the data-group layer also gates the resource

## Debug "this user can't take this action"

- [ ] **Is the data-group permission granted?** Check `ohrm_user_role_data_group` for this role + data group (see `authorization` skill). 403 from the API authorization subscriber comes from here.
- [ ] **Is there a workflow row for this combo?** `SELECT * FROM ohrm_workflow_state_machine WHERE workflow=? AND state=? AND role=? AND action=?` — if no row, no transition allowed.
- [ ] **What's the effective role?** `BasicUserRoleManager` computes dynamic roles (Supervisor, ESS, etc.) per request. The user's *static* role might be Admin but they could also be an ESS (via the dynamic ESS addition). The workflow query is per-role — if there are rows for 'ESS' but not 'Admin', the Admin-with-ESS-role gets the ESS row.
- [ ] **Is the state string an exact match?** Workflow rows compare by string equality. `'PENDING APPROVAL'` ≠ `'Pending Approval'` ≠ `'PENDING_APPROVAL'`. Trim and case matter.

## Things that bite

- **All values are strings in the DB.** The action `2` is `'2'`. When passing constants, cast with `(string)` or compare carefully. `'2' == 2` is true loosely but `'2' === 2` is not. Use string comparisons throughout.
- **`getAllowedActions` returns `null` for "no rows matched"** — not an empty array. Check `is_null($actions) || in_array(...)` or default with `?? []`.
- **The cache is per-request, per-tuple.** `$allowedWorkflowItemCache` keys on `(workflow, state, role)`. Two calls in the same request reuse; a different request re-queries. Fine for typical use.
- **There's no schema-level FK** between `ohrm_workflow_state_machine.role` and `ohrm_user_role.name`. Misspelling a role in a seed silently makes the row dead — `getAllowedActions` for the typo'd role works but the typo never matches a real user's effective roles.
- **The same is true for `state` and `resulting_state`** — there's no enum constraint. Typos produce dead-ends or unreachable states. **Always seed via migrations and double-check** the strings match what the domain service emits.
- **`getNextState` returning `null` is the "not allowed" signal.** Don't accidentally treat `null` as "stay in current state" — that's wrong. The right interpretation is "this transition is forbidden; throw or refuse."
- **The workflow state and the entity's `status` column** are conceptually the same value — but the workflow doesn't write the column for you. The domain service is responsible for `entity->setStatus($nextState)` after the lookup. Forgetting this means the lookup says "next state is APPROVED" but the leave is still `PENDING APPROVAL` in the DB.
- **Multiple workflow rows for the same `(workflow, state, role, action)` is undefined behavior.** The DB doesn't have a unique constraint — duplicates lead to whichever row gets returned first. Always seed uniquely.
