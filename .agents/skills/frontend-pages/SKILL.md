---
name: frontend-pages
description: Entry-point reference for OrangeHRM's Vue 3 frontend — the per-page mini-SPA architecture (no vue-router; each backend page-controller mounts one Vue root component into the Twig shell), the four-step page registration flow, plugin-frontend layout under `src/client/src/orangehrm{X}Plugin/`, the three import aliases (`@/`, `@ohrm/core`, `@ohrm/components`) + the external `@ohrm/oxd` design system, the catalog of OXD components actually used in the codebase, the custom `@ohrm/components` layer (DateInput, EmployeeAutocomplete, FileUploadInput, etc.), the typical Vue page file anatomy (mixed Composition + Options API, scoped external SCSS), and full-page navigation via `navigate(url)`. Use whenever the user is adding a new Vue page, editing an existing one, asking "where do I register this component", asking about the OXD design system, debugging "why doesn't my new page render", or working out which import alias to use. Companion skills: `frontend-data` (APIService + composables + validation), `frontend-platform` (i18n + ACL + toaster + navigation), `authorization` (the page-controller side that mounts these Vue components).
---

# Vue frontend — pages and components

OrangeHRM's frontend looks like a single Vue 3 app on first glance, but it isn't structured like a typical SPA. This skill covers the architecture and the per-page anatomy. The sibling skills cover data flow (`frontend-data`) and cross-cutting platform plugins (`frontend-platform`).

## The architecture — many small SPAs, not one big one

**There is no `vue-router` in this codebase.** Every page navigation is a **full browser navigation** to a new URL; the server renders the next page's Twig template, which mounts a fresh Vue app rendering one root component.

```
HTTP request to /pim/viewEmployeeList
  → AbstractVueController::handle (see authorization skill)
     → preRender(): $this->setComponent(new Component('employee-list', [...props]))
  → vue.html.twig renders <oxd-layout>…<employee-list :prop1="…" :prop2="…" /></oxd-layout>
  → src/client/src/main.ts boots Vue, mounts at #app
     → all page components are globally registered (pages.ts spreads each plugin's index.ts)
     → the one named 'employee-list' actually renders; the rest are unused
  → user clicks "Add" → navigate('/pim/addEmployee') → window.location.href set → next page-load starts
```

What this means in practice:

- **No client-side route tables, no router views, no programmatic route guards.** Permission checks happen server-side (see `authorization` skill) and via `$can.read('data_group')` template gates (see `frontend-platform`).
- **State doesn't survive navigation.** Each page is a fresh Vue app. If you need to pass data between pages, do it via URL params, server-side session, or the API.
- **Page boot is slightly heavy** — Vue bootstraps, fetches translations from `/core/i18n/messages` on init, then mounts. The Twig template preloads vendor + app bundles to make this fast.
- **The single app instance still registers every page component globally** via `pages.ts` + `components.ts` — they're all in the bundle, but only one mounts per page.

If you've worked on a typical Vue SPA: nothing about a `<router-view>` or `useRouter()` applies here. Don't propose adding vue-router — it'd require rewriting the entire page-controller layer.

## The four-step page registration

Adding a new page is **always these four pieces, in this order**:

### Step 1 — Write the Vue component

`src/client/src/orangehrm{X}Plugin/pages/<feature>/<Page>.vue`:

```vue
<template>
  <div class="orangehrm-background-container">
    <!-- … OXD components + ACL gates + …  -->
  </div>
</template>

<script>
import {ref} from 'vue';
import usei18n from '@/core/util/composable/usei18n';
// …
export default {
  setup() { /* … */ },
};
</script>

<style src="./my-feature.scss" lang="scss" scoped></style>
```

### Step 2 — Import in the plugin's `index.ts`

`src/client/src/orangehrm{X}Plugin/index.ts`:

```ts
import MyFeaturePage from './pages/myFeature/MyFeaturePage.vue';

export default {
  'my-feature-page': MyFeaturePage,   // ← kebab-case key — this is the global component name
  // … existing entries
};
```

### Step 3 — Plugin's index.ts must be spread in `pages.ts`

`src/client/src/pages.ts` already spreads every plugin's `index.ts`. **You don't need to touch it** unless you're adding a brand-new plugin. For a plugin that already exists, Step 2 is enough — the spread picks up the new entry.

### Step 4 — Backend page controller references the same name

In the backend (covered in `rest-endpoints` + `authorization`):

```php
class MyFeatureController extends AbstractVueController
{
    public function preRender(Request $request): void
    {
        $this->setComponent(new Component('my-feature-page'));   // ← same string as Step 2 key
    }
}
```

The string `'my-feature-page'` is the contract between backend and frontend. If they disagree, the Twig template renders `<my-feature-page>` and Vue silently doesn't render anything (no error — just an empty page).

## Plugin frontend layout

Mirror of the backend layout, one Vue plugin dir per backend plugin:

```
src/client/src/orangehrm{X}Plugin/
  index.ts              ← default-exports { 'component-name': Component, ... }
  pages/                ← Vue page components — one folder per feature
    <feature>/
      <Page>.vue
      <page>.scss       ← scoped SCSS for the page
      <SubComponent>.vue  ← page-private components (optional)
  components/           ← plugin-wide reusable components (e.g. JobtitleDropdown for PIM)
  util/                 ← plugin-specific helpers (rare)
```

Examples from existing plugins:
- `orangehrmPimPlugin/pages/employee/Employee.vue` + `employee.scss`
- `orangehrmPimPlugin/components/JobtitleDropdown.vue`
- `orangehrmAdminPlugin/pages/user/SystemUser.vue`

The convention isn't enforced — there are minor variations across plugins — but new code should follow it.

## Import aliases

`vue.config.js` defines three webpack aliases plus the external `@ohrm/oxd` package:

| Alias | Resolves to | Use for |
|---|---|---|
| `@/` | `src/client/src/` | Standard Vue CLI alias. `@/orangehrmPimPlugin`, `@/core`, etc. |
| `@ohrm/core` | `@/core` (= `src/client/src/core`) | Composables, plugins, helpers, services, util. Short alias. |
| `@ohrm/components` | `@/core/components` (= `src/client/src/core/components`) | Custom reusable components (DateInput, EmployeeAutocomplete, etc.) |
| `@ohrm/oxd` | external `@ohrm/oxd` npm package | The OXD design system (button, form, table, layout, etc.) |

**Both `@/core/…` and `@ohrm/core/…` work** — they resolve to the same path. The codebase mixes them. Don't get pedantic about which one to use; match what's nearby. Same for `@/core/components/…` vs `@ohrm/components/…`.

## OXD design system (`@ohrm/oxd`) — what's actually used

The OXD package is an external repo; `2.0.3` is pinned in `src/client/package.json`. These are the components OrangeHRM uses most often (all globally registered in `src/client/src/components.ts`):

### Layout & structure

| Component | Use |
|---|---|
| `<oxd-layout>` | The whole-page shell with sidepanel + topbar. Rendered by Twig (`vue.html.twig`), not directly in page components. |
| `<oxd-grid :cols="N">` + `<oxd-grid-item>` | N-column responsive grid. Standard layout for filter rows and forms. |
| `<oxd-divider>` | Horizontal rule. |
| `<oxd-card-table>` | The standard data table — accepts `:headers`, `:items`, sortable, has slots for action buttons per row. |
| `<oxd-table-filter :filter-title="…">` | Wraps a search/filter form above a table. |
| `<oxd-pagination>` | Page navigation for tables. |

### Forms

| Component | Use |
|---|---|
| `<oxd-form>` | Form wrapper. Emits `@submit-valid` (after rules pass) and `@reset`. Validates child `oxd-input-field` rules. |
| `<oxd-form-row>` | Group inputs in a row. |
| `<oxd-form-actions>` | Bottom action bar — Save/Cancel buttons go here. |
| `<oxd-input-field v-model="…" :rules="[…]" :label="…">` | The standard text input. Pass validation rules via `:rules` (see `frontend-data` skill). |
| `<oxd-input-group>` | Group inputs that should appear together. |

### Buttons & labels

| Component | Use |
|---|---|
| `<oxd-button :label="…" display-type="…" type="…" icon-name="…" @click="…">` | All buttons. `display-type` is `secondary` (filled), `ghost` (outline), `text`. `type` is `submit` / `reset` / `button`. |
| `<oxd-icon-button>` | Icon-only button. |
| `<oxd-text>` | Typography wrapper. |

### Custom `@ohrm/components` (project-specific layer)

These wrap OXD with OHRM-specific behavior. Globally registered:

| Component | What it does |
|---|---|
| `<date-input v-model="…">` | Date picker tied to the user's date format from `useDateFormat`. |
| `<time-input v-model="…">` | Time picker. |
| `<submit-button>` | Standard form submit button with loading state. |
| `<table-header :selected="N" :total="T" :loading="…" @delete="…">` | Common header for a paginated table — shows total count + selected count + bulk delete. |
| `<required-text>` | "*" indicator next to required form labels. |

Not globally registered — import per-component:
- `EmployeeAutocomplete` (from `@/core/components/inputs/`) — autocomplete that hits `/api/v2/pim/employees` for suggestions. Accepts `v-model`, `:rules`, `:params`.
- `FileUploadInput` — wraps the OXD file input with project-standard validation hooks.
- `PasswordInput` — password input with policy display.
- `DeleteConfirmationDialog`, `ConfirmationDialog` (from `@/core/components/dialogs/`) — modal dialogs. Slot-based confirm/cancel.

Plus plugin-specific dropdowns (e.g. `JobtitleDropdown`, `SubunitDropdown`, `EmploymentStatusDropdown` from `orangehrmPimPlugin/components/`) — these wrap an OXD dropdown with the API call to fetch options.

## Component file anatomy

This is the shape every page follows. Mixed Composition (`setup()`) + Options API (`data`, `computed`, `methods`) — **this is the project convention, don't refactor to one or the other.**

```vue
<template>
  <!-- OXD-composed UI -->
  <div class="orangehrm-background-container">
    <oxd-table-filter :filter-title="$t('pim.employee_information')">
      <oxd-form @submit-valid="filterItems" @reset="filterItems">
        <oxd-form-row>
          <oxd-grid :cols="4" class="orangehrm-full-width-grid">
            <oxd-grid-item>
              <employee-autocomplete v-model="filters.employee" :rules="rules.employee" />
            </oxd-grid-item>
            <!-- … -->
          </oxd-grid>
        </oxd-form-row>
        <oxd-form-actions>
          <oxd-button :label="$t('general.reset')" type="reset" display-type="ghost" />
          <oxd-button :label="$t('general.search')" type="submit" display-type="secondary" />
        </oxd-form-actions>
      </oxd-form>
    </oxd-table-filter>

    <div v-if="$can.create('employee_list')">                <!-- ACL gate -->
      <oxd-button :label="$t('general.add')" icon-name="plus" @click="onClickAdd" />
    </div>

    <oxd-card-table :headers="headers" :items="items" :loading="isLoading" />
    <oxd-pagination v-if="showPaginator" v-model:current="currentPage" :total="pages" />
  </div>
</template>

<script>
import {computed, ref} from 'vue';
import usei18n from '@/core/util/composable/usei18n';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import useSort from '@ohrm/core/util/composable/useSort';
import {navigate} from '@ohrm/core/util/helper/navigation';
import {APIService} from '@/core/util/services/api.service';
import {shouldNotExceedCharLength, validSelection} from '@/core/util/validation/rules';

export default {
  components: {
    // locally registered components (the global ones don't need this)
  },

  props: {
    // server-injected props arrive here — see authorization skill / rest-endpoints
    someProp: { type: Number, default: 0 },
  },

  setup(props) {
    const {$t} = usei18n();                                  // translation in setup context

    const filters = ref({ employee: null, /* ... */ });
    const {sortField, sortOrder, onSort} = useSort({ sortDefinition: { /* ... */ } });

    const serializedFilters = computed(() => ({              // serialize for the API
      empNumber: filters.value.employee?.id,
      sortField: sortField.value,
      sortOrder: sortOrder.value,
    }));

    const http = new APIService(window.appGlobal.baseUrl, '/api/v2/pim/employees');
    const {showPaginator, total, pages, response, isLoading, execQuery} = usePaginate(http, {
      query: serializedFilters,
      normalizer: (data) => data.map(/* shape for table */),
    });
    onSort(execQuery);

    return { showPaginator, total, pages, items: response, isLoading, filters, currentPage: /* … */ };
  },

  data() {
    return {
      checkedItems: [],
      rules: {                                               // client-side validation
        employee: [shouldNotExceedCharLength(100)],
      },
    };
  },

  computed: {
    headers() {                                              // table column defs use $t()
      return [
        { name: 'employeeId', title: this.$t('general.id'), sortField: 'employee.employeeId' },
        // …
        { name: 'actions',    title: this.$t('general.actions'), slot: 'action' },
      ];
    },
  },

  methods: {
    onClickAdd() { navigate('/pim/addEmployee'); },
  },
};
</script>

<style src="./employee.scss" lang="scss" scoped></style>
```

Key conventions visible in this anatomy:

- **Composables live in `setup()`**. Everything that returns reactive state from a composable (`usePaginate`, `useSort`, `useToast`, `usei18n`) goes there.
- **Local UI state lives in `data()`**. Form rules, selection state, modal-open flags — anywhere Options API is more readable.
- **Computed properties live in `computed`** (Options API) for non-setup-based derivations; in `setup` they're `computed(() => …)` refs.
- **Methods on event handlers live in `methods`**. Easier to reference as `this.onClickAdd` from the template.
- **External SCSS via `<style src="…" lang="scss" scoped>`** — never inline CSS in a page component. The global `@import "@/core/styles";` happens via `vue.config.js` `additionalData`, so SCSS variables are available without explicit imports.
- **Template uses translation everywhere** (`:label="$t('general.save')"`) — never hardcoded text. See `frontend-platform` for translation specifics.
- **ACL gates everywhere they apply** (`v-if="$can.create('data_group_name')"`) — see `frontend-platform`.

## Navigation between pages

Pages don't share state; transition is a full reload via `navigate()`:

```ts
import {navigate} from '@ohrm/core/util/helper/navigation';

navigate('/pim/viewEmployee/123');                          // → window.location.href
navigate('/pim/viewEmployee', {}, { tab: 'job' });          // → /pim/viewEmployee?tab=job
navigate('/pim/viewEmployee/{id}', { id: 123 });            // → /pim/viewEmployee/123
```

For URL-building without navigating, `urlFor()` from `@ohrm/core/util/helper/url` does the same param/query interpolation.

`reloadPage()` is the explicit "redo the same page" — used internally by the APIService 401 handler.

## Where state comes from

Three sources, each with its own lifetime:

| Source | Lifetime | Examples |
|---|---|---|
| **Server-injected props** | The page render | URL params, current employee being viewed, edit-mode flags. Set in PHP `preRender()` via `Component($name)->addProp(...)` and arrive as Vue `props`. |
| **`window.appGlobal`** | The page render | `baseUrl` for API calls. Set in the Twig template, read by the APIService constructor. |
| **Vue `inject`** | The page render | `permissions` (drives `$can`), `dateFormat`, locale, breadcrumb. Set by `<oxd-layout>` and consumed by plugins (`acl`) or composables (`useDateFormat`). |

There's no Pinia / Vuex / global state store. **Don't introduce one** — it'd be at odds with the per-page-mini-SPA model (state doesn't survive nav, so a store wouldn't either).

## Testing

The frontend testing surface is light. Jest is configured (`yarn test:unit`) with `@vue/vue3-jest` for SFC transform, but **most existing tests cover util functions** (`rules.spec.ts`, `datefns.spec.ts`, `url.spec.ts`, `year-range.spec.ts`, `filesize.spec.ts`) — Vue component tests are rare.

If you need to test a component, the setup works (Vue Test Utils + Jest), but the precedent in the codebase is to extract testable logic into util/composable functions and unit-test those instead.

---

# Recipes

## Recipe 1 — A read-only list page

Backend (covered in `rest-endpoints` + `authorization`): a Vue page controller, an `EndpointCollectionResult` API endpoint, `ohrm_screen` row + permissions.

Frontend:

```vue
<template>
  <div class="orangehrm-background-container">
    <oxd-table-filter :filter-title="$t('x.widget_filter')">
      <oxd-form @submit-valid="filterItems" @reset="filterItems">
        <oxd-grid :cols="2">
          <oxd-grid-item>
            <oxd-input-field v-model="filters.name" :label="$t('general.name')" />
          </oxd-grid-item>
        </oxd-grid>
        <oxd-form-actions>
          <oxd-button :label="$t('general.reset')" type="reset" display-type="ghost" />
          <oxd-button :label="$t('general.search')" type="submit" display-type="secondary" />
        </oxd-form-actions>
      </oxd-form>
    </oxd-table-filter>

    <table-header :total="total" :loading="isLoading" />
    <oxd-card-table :headers="headers" :items="items" :loading="isLoading" />
    <oxd-pagination v-if="showPaginator" v-model:current="currentPage" :total="pages" />
  </div>
</template>

<script>
import {computed, ref} from 'vue';
import usei18n from '@/core/util/composable/usei18n';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import {APIService} from '@/core/util/services/api.service';

export default {
  setup() {
    const {$t} = usei18n();
    const filters = ref({ name: '' });
    const serializedFilters = computed(() => ({ name: filters.value.name || undefined }));

    const http = new APIService(window.appGlobal.baseUrl, '/api/v2/x/widgets');
    const {showPaginator, total, pages, response, isLoading, currentPage} =
      usePaginate(http, { query: serializedFilters });

    return { showPaginator, total, pages, currentPage, items: response, isLoading, filters };
  },
  computed: {
    headers() {
      return [
        { name: 'id',   title: this.$t('general.id'), style: { flex: 1 } },
        { name: 'name', title: this.$t('general.name'), style: { flex: 1 } },
      ];
    },
  },
};
</script>

<style src="./widget-list.scss" lang="scss" scoped></style>
```

Then `index.ts`: `'widget-list': WidgetList`. Then backend `preRender()`: `new Component('widget-list')`.

## Recipe 2 — Page with ACL gate around an action

```vue
<template>
  <!-- … -->
  <div v-if="$can.create('apiv2_x_widgets')" class="orangehrm-header-container">
    <oxd-button
      :label="$t('general.add')"
      icon-name="plus"
      display-type="secondary"
      @click="onClickAdd"
    />
  </div>
</template>

<script>
import {navigate} from '@ohrm/core/util/helper/navigation';
export default {
  methods: {
    onClickAdd() { navigate('/x/saveWidget'); },
  },
};
</script>
```

The string `'apiv2_x_widgets'` must match a `DataGroup.name` in `ohrm_data_group` (see `authorization` skill). The `permissions` injected by the server tells `$can` what each data group's CRUD bits are for the current user.

## Recipe 3 — Page-private modal dialog

```vue
<template>
  <div>
    <oxd-button :label="$t('general.delete')" @click="onClickDelete" />
    <delete-confirmation
      v-if="dialog === 'delete'"
      @confirm="onConfirmDelete"
      @cancel="dialog = null"
    />
  </div>
</template>

<script>
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';
export default {
  components: { 'delete-confirmation': DeleteConfirmationDialog },
  data() {
    return { dialog: null };
  },
  methods: {
    onClickDelete() { this.dialog = 'delete'; },
    async onConfirmDelete() { /* call API */ this.dialog = null; },
  },
};
</script>
```

The dialog is mounted/unmounted via `v-if`, not toggled with a `visible` prop. Cancel returns to no-dialog state; confirm runs the action then closes.

## Recipe 4 — A new plugin's frontend

For a brand-new plugin (rare but worth documenting):

1. Create `src/client/src/orangehrmFooPlugin/` with `index.ts`, `pages/`, `components/`.
2. Add to `src/client/src/pages.ts`:
   ```ts
   import fooPages from '@/orangehrmFooPlugin';
   export default { …, ...fooPages };
   ```
3. Pages register inside the plugin's own `index.ts`, same as existing plugins.

---

# Checklists

## Add a new Vue page

- [ ] Create `<Page>.vue` under `src/client/src/orangehrm{X}Plugin/pages/<feature>/`
- [ ] External `<style src="./<feature>.scss" lang="scss" scoped></style>` — don't inline CSS
- [ ] Import in the plugin's `index.ts` with kebab-case key
- [ ] Backend page controller's `preRender()` calls `new Component('that-same-kebab-key')`
- [ ] All visible text wrapped in `$t(...)` — no hardcoded strings
- [ ] ACL gates (`v-if="$can.read/create/update/delete('data_group')"`) around any action that's permission-controlled
- [ ] Composables (`usePaginate`, `useSort`, `useToast`) in `setup()`; everything else can use Options API
- [ ] Validation rules from `@/core/util/validation/rules` for any form inputs

## Add a new plugin's frontend

- [ ] Create `src/client/src/orangehrm{X}Plugin/` with `index.ts`, `pages/`, optionally `components/`, `util/`
- [ ] Import + spread the plugin's index into `src/client/src/pages.ts`
- [ ] Add a `yarn build` to confirm webpack picks up the new alias paths

## Debug "my new page doesn't render"

- [ ] **Component name mismatch** — open the rendered HTML, look for the literal `<my-component>` element. If it's there as plain HTML (no inner content), Vue couldn't find the component. Check the kebab-case key in `index.ts` exactly matches the PHP `new Component('…')` string.
- [ ] **Plugin's index.ts not imported in pages.ts** — only an issue when adding a new plugin.
- [ ] **Browser console errors** — Vue renders silently when a global component is missing; SCSS or JS import errors will be in DevTools.
- [ ] **Stale build** — running `yarn dev` (watch) or rebuilding via `yarn build` after a new file is needed; the `web/dist/` artifacts must be current.
- [ ] **Vue 3 reactivity gotchas** — if the page renders but a value isn't updating, check whether you reassigned a `ref` (need `.value` on write) or mutated a nested object without using `reactive`/`ref`.

## Things that bite

- **There is no `vue-router`.** Don't propose adding it; rewriting all the page-controller dispatch isn't worth it. Use `navigate(url)` for transitions.
- **`<style>` blocks are scoped via external `src=`** — inline `<style scoped>` works but is unidiomatic. Stick to external SCSS for consistency.
- **The mixed Composition + Options API is intentional.** Don't refactor an entire page to one or the other; match the surrounding style.
- **Global components are bundle-wide** — all plugins ship in the same bundle. There's no per-plugin code splitting in the current build.
- **`window.appGlobal.baseUrl` is the only safe way to know the deploy path.** Don't hardcode `/api/v2/...` directly; always go through `new APIService(window.appGlobal.baseUrl, '/api/v2/…')` so subdir deploys work.
- **A typo in a page-name key produces an empty page, no console error.** Vue treats unknown component names as raw HTML; the browser then renders nothing because `<unknown-name>` has no native definition.
- **State doesn't survive navigation.** If you find yourself wanting to keep filter state across pages, push it into the URL (`navigate(..., {}, queryParams)`).
- **`@/core/components/…` and `@ohrm/components/…` are the same path** — pick one for a new file and stay consistent within that file; don't mix in a single file.
