---
name: frontend-data
description: Reference for data flow in the OrangeHRM Vue frontend — the `APIService` axios wrapper that mirrors backend `/api/v2/...` routes 1:1, its 401/422 interceptors and ETag-based response caching in prod, the data-loading composables (`usePaginate` for list endpoints with reactive filter/sort, `useSort`, `useInfiniteScroll`), the form composables (`useForm`, `useServerValidation` for backend 422 handshakes, `useAutoFocus`, `usePasswordPolicy`), and the client-side validation rules in `@/core/util/validation/rules.ts` (~40 rules: required, length, dates, times, files, formats, comparisons). Use whenever the user is calling an API from a Vue component, paginating a list, building a form, debugging "why does my filter not re-fetch" or "why does a 401 reload the page", picking a validation rule, or wiring server-side validation back into form fields. Companion to `rest-endpoints`/`rest-validation`/`rest-serialization` (the backend surface), `frontend-pages` (where data hooks up to the UI), and `frontend-platform` (toaster + i18n + ACL).
---

# Frontend data flow

Every list, form, and API call in the OrangeHRM Vue layer goes through three building blocks:

1. **`APIService`** — the axios wrapper that knows the OHRM API conventions.
2. **Composables** — reactive wrappers (`usePaginate`, `useSort`, `useForm`, `useServerValidation`) that connect `APIService` to component state.
3. **Validation rules** — pure functions in `@/core/util/validation/rules.ts` for client-side form validation, plus the server-validation handshake for things that can only be checked on the backend.

This skill covers all three. For where they get used inside a page, see `frontend-pages`. For the backend's API surface, see `rest-endpoints`, `rest-validation`, and `rest-serialization`.

## `APIService` — the axios wrapper

`@/core/util/services/api.service.ts`. Constructed per-resource:

```ts
import {APIService} from '@/core/util/services/api.service';

const http = new APIService(
  window.appGlobal.baseUrl,      // ← always this; never hardcode the base
  '/api/v2/pim/employees',       // ← the resource path matching backend route
);
```

The `apiSection` string mirrors a backend `apiv2_*` route from `routes.yaml` exactly. **One APIService per resource**, typically declared inline in `setup()`.

### Methods

```ts
http.getAll(params?)                     // GET /api/v2/.../resources?…
http.get(id, params?)                    // GET /api/v2/.../resources/:id
http.create(data)                        // POST /api/v2/.../resources
http.update(id, data)                    // PUT /api/v2/.../resources/:id
http.delete(id)                          // DELETE /api/v2/.../resources/:id
http.deleteAll(data?)                    // DELETE /api/v2/.../resources       ← bulk
http.request(axiosOptions)               // arbitrary — for custom routes/methods
```

Each returns a `Promise<AxiosResponse>` — the typical pattern is `const {data} = await http.getAll({…})` then read `data.data` and `data.meta` (the backend's `{data, meta, rels}` envelope — see `rest-serialization`).

### Response interceptors — automatic behavior you should know about

Built into every `APIService` instance:

| Response | Interceptor behavior |
|---|---|
| **401 Unauthorized** | `reloadPage()` is called. The page reloads, and because the session is gone, the backend redirects to `/auth/login`. **You don't handle 401 manually** — let the interceptor work. |
| **400 / 422 on a validation endpoint** | Suppressed (no toast) **only if** the resource path matches a regex registered via `setIgnorePath()`. Used for "is this email unique?" style endpoints where 422 is the *expected* signal, not an error. |
| **Any other error with `$toast` available** | Calls `$toast.unexpectedError(message)` automatically. **You don't manually toast on error** — the interceptor already did. |
| **`ECONNABORTED`** (timeout) | Toast suppressed (the error still rejects). |

```ts
// Suppress toasts for validation requests
const validationHttp = new APIService(window.appGlobal.baseUrl, '/api/v2/pim/employees');
validationHttp.setIgnorePath('/api/v2/.*/validation');
```

After `setIgnorePath`, 400/422 responses matching the regex don't fire `unexpectedError`. Useful for validation-only endpoints that return 422 by design.

### ETag-based response caching (prod only)

When `NODE_ENV !== 'development'`:
- On request, if `localStorage` has an ETag for the URL, an `If-None-Match` header is added.
- On response, the ETag from the server is stored alongside the response body (in `localStorage`).
- A 304 response causes the interceptor to return the cached body as a 200.

**This is invisible to your code** — `getAll()` returns the response either way. But it explains why a network panel might show 304s instead of 200s in production. Dev disables the cache entirely so changes from the backend appear immediately.

## Data-loading composables

### `usePaginate` — list endpoints

`@/core/util/composable/usePaginate.ts`. The workhorse for list pages.

```ts
import usePaginate from '@ohrm/core/util/composable/usePaginate';

const http = new APIService(window.appGlobal.baseUrl, '/api/v2/x/widgets');

const filters = ref({ name: '', categoryId: null });
const serializedFilters = computed(() => ({
  name: filters.value.name || undefined,
  categoryId: filters.value.categoryId?.id,
}));

const {
  showPaginator,    // ref<boolean>     — show paginator? false if no records or single page
  total,            // ref<number>      — backend meta.total
  pages,            // ref<number>      — total page count
  currentPage,      // ref<number>      — active page (writable)
  pageSize,         // ref<number>      — limit per page (default 50)
  response,         // ref              — data array (post-normalizer)
  isLoading,        // ref<boolean>     — request in flight
  execQuery,        // () => void       — re-fetch with current query
} = usePaginate(http, {
  query: serializedFilters,                                    // ref or computed
  normalizer: (data) => data.map(item => ({ id: item.id, name: item.name })),
  prefetch: true,                                              // fetch on mount? default true
  toastNoRecords: true,                                        // toast "no records" on empty? default true
  pageSize: 50,                                                // initial page size
});
```

What it does for you:
- Watches the `query` ref/computed. **When the query value changes, re-fetches automatically.** This is the magic of the pattern — bind filters into a `computed`, and the table just re-fetches when filters change.
- Watches `currentPage`. When the user changes pages, re-fetches with the new offset.
- Calculates `pages` from `meta.total` and `pageSize`.
- Runs the optional `normalizer` to shape raw `data` rows for table display.

**The normalizer is where you reshape API rows for the table** — flatten nested objects, compose display strings, attach `isSelectable` flags, etc.

### `useSort` — sort field/order

```ts
import useSort from '@ohrm/core/util/composable/useSort';

const defaultSortOrder = {
  'employee.employeeId': 'DEFAULT',
  'employee.firstName':  'ASC',
  'employee.lastName':   'DEFAULT',
  // …
};

const {sortDefinition, sortField, sortOrder, onSort} = useSort({
  sortDefinition: defaultSortOrder,
});

// Wire up to usePaginate: sort change → re-fetch
onSort(execQuery);
```

`sortField` and `sortOrder` are refs. Include them in the `serializedFilters` computed so they end up in API params (matching backend `ALLOWED_SORT_FIELDS` from the DTO). `onSort(callback)` registers a watcher — when the user clicks a column header in `oxd-card-table`, the sort state updates and `callback` runs.

### `useInfiniteScroll` — for non-paginated lists

Used when the table is "load more as you scroll" rather than discrete pages. Same APIService underneath; different reactive shape. Most lists use `usePaginate`; reach for `useInfiniteScroll` only when the design specifically wants it.

## Form composables

### `useForm` — wrapper around `<oxd-form>`

`@/core/util/composable/useForm.ts`. Gives you programmatic access to the form's submit/reset/validate methods.

```ts
import useForm from '@/core/util/composable/useForm';

export default {
  setup() {
    const {formRef, submit, reset, validate, invalid, errorbag} = useForm();
    return {formRef, onSave: submit, invalid};
  },
};
```

```vue
<oxd-form :ref="formRef" @submit-valid="onSubmit">…</oxd-form>
<oxd-button :disabled="invalid" @click="onSave" />
```

`formRef` is the ref to attach to the `<oxd-form>`. `submit()` programmatically triggers `@submit-valid` (after validating rules); `reset()` resets all inputs; `validate()` runs rules without submitting. `invalid` is a reactive boolean of the form's validity.

### `useServerValidation` — async uniqueness checks

`@/core/util/composable/useServerValidation.ts`. Returns rule factories that hit a backend validation endpoint and return `true | errorMessage` async — usable directly inside the `rules` array.

```ts
import useServerValidation from '@/core/util/composable/useServerValidation';
import {required, shouldNotExceedCharLength} from '@/core/util/validation/rules';

const http = new APIService(window.appGlobal.baseUrl, '/api/v2/...');
http.setIgnorePath('/api/v2/core/validation/unique');                // suppress error toasts

const serverValidation = useServerValidation(http);

const uniqueRule = serverValidation.createUniqueValidator(
  'JobTitle',                                                        // entityName (PHP class short name)
  'jobTitleName',                                                    // attributeName (entity property)
  {
    entityId: this.editId,                                           // ignore self when updating
    translateKey: 'general.already_exists',                          // optional error message key
  },
);

return {
  rules: {
    title: [required, shouldNotExceedCharLength(100), uniqueRule],
  },
};
```

It hits the generic `/api/v2/core/validation/unique?value=…&entityName=…&attributeName=…&entityId=…` endpoint and resolves with `true` (valid) or a translated error string. **Debounced 500ms by default** — typing fast doesn't fire one request per keystroke.

Backend side: see `rest-validation` skill, specifically the `ENTITY_UNIQUE_PROPERTY` rule and `EntityUniquePropertyOption` for the "ignore self on update" pattern.

### Smaller form composables

| Composable | Use |
|---|---|
| `useAutoFocus` | Auto-focus a ref on mount. |
| `usePasswordPolicy` | Fetches the configured password policy + returns a rule that enforces it. |

## Client-side validation rules (`@/core/util/validation/rules.ts`)

~40 pure functions. Each accepts the input value, returns `true | string` — `true` when valid, the error string (already translated) when not. The `<oxd-form>` collects these via `:rules="rules.fieldName"` on each input and displays the returned string inline.

### Usage pattern

```ts
data() {
  return {
    rules: {
      name:        [required, shouldNotExceedCharLength(100)],
      email:       [required, validEmailFormat, shouldNotExceedCharLength(255)],
      phoneNumber: [validPhoneNumberFormat],
      startDate:   [required, validDateFormat, beforeDate(this.endDate)],
      endDate:     [required, validDateFormat, afterDate(this.startDate)],
    },
  };
}
```

```vue
<oxd-input-field v-model="form.name" :rules="rules.name" :label="$t('general.name')" />
```

### The catalog

**Presence / size**

| Rule | Use |
|---|---|
| `required` | Non-empty string, non-NaN number, non-empty array, non-null object. |
| `shouldNotExceedCharLength(N)` | String length ≤ N. |
| `shouldNotLessThanCharLength(N)` | String length ≥ N. |
| `max(N)` | Numeric ≤ N. |
| `maxCurrency(N)` | Currency-formatted numeric ≤ N. |
| `greaterThanOrEqual(N)` | Numeric ≥ N. |
| `lessThanOrEqual(N, message?)` | Numeric ≤ N. |
| `maxValueShouldBeGreaterThanMinValue` / `minValueShouldBeLowerThanMaxValue` | Range pair validators. |
| `numberShouldBeBetweenMinAndMaxValue(min, max)` | In range. |

**Numbers**

| Rule | Use |
|---|---|
| `digitsOnly` | Integer-like string (no sign, no decimal). |
| `numericOnly` | Numeric-like string. |
| `digitsOnlyWithDecimalPoint` | Positive decimal. |
| `digitsOnlyWithDecimalPointAndMinusSign` | Signed decimal. |
| `digitsOnlyWithTwoDecimalPoints` | Up to two decimal places (currency-style). |

**Dates / times**

| Rule | Use |
|---|---|
| `validDateFormat(format?)` | Parses against the user's date format. |
| `validTimeFormat` | `HH:mm` 24-hour. |
| `shouldBeCurrentOrPreviousDate()` | Date is today or earlier. |
| `beforeDate(otherDate)` / `afterDate(otherDate)` / `sameDate(otherDate)` | Comparison. |
| `beforeTime(otherTime)` / `afterTime(otherTime)` / `sameTime(otherTime)` | Comparison. |
| `startDateShouldBeBeforeEndDate(...)` / `endDateShouldBeAfterStartDate(...)` | Convenient paired-field rules. |
| `startTimeShouldBeBeforeEndTime(...)` / `endTimeShouldBeAfterStartTime(...)` | Same for times. |

**Files**

| Rule | Use |
|---|---|
| `maxFileSize(bytes)` | File ≤ N bytes. |
| `validFileTypes([mime,...])` | MIME type in allowed list. |
| `imageShouldHaveDimensions(width, height)` | Exact image dimensions. |

**Formats**

| Rule | Use |
|---|---|
| `validEmailFormat` | OHRM email regex (matches backend `Rules::EMAIL`). |
| `validPhoneNumberFormat` | OHRM phone regex. |
| `validHexFormat` | `#xxx` / `#xxxxxx`. |
| `validHostnameFormat` | Hostname per RFC. |
| `validPortRange(min?, max?)` | TCP port in 1–65535 (or narrower). |
| `validVideoURL` | Common video provider URL. |
| `validLangString` | A lang-string key (matches backend `LangStringHelper` keys). |

**Misc**

| Rule | Use |
|---|---|
| `validSelection` | Required dropdown — must select an object, not a typed-but-not-selected string. |

For anything not covered: **write a custom rule function inline**. Rules are just functions; you don't need to add them to `rules.ts`. Reserve `rules.ts` for genuinely reusable rules.

```ts
data() {
  return {
    rules: {
      foo: [
        required,
        (value) => /^[A-Z]{3}-\d{4}$/.test(value) || this.$t('x.invalid_foo_code'),
      ],
    },
  };
}
```

## Server-side validation handshake

For checks the client can't do alone (uniqueness, FK existence, complex business rules) the backend validates and returns **HTTP 422** with the offending param keys in `error.message`. See `rest-validation` skill for the server side.

Two ways the frontend handles this:

1. **Async rule via `useServerValidation.createUniqueValidator`** — the rule itself fires the request. The user sees the error inline as soon as the field is checked. Use for uniqueness checks where instant feedback matters.

2. **Catch on submit** — if you don't pre-validate, the API call returns 422; the interceptor would normally toast `unexpectedError`, **but** if you called `http.setIgnorePath` on a regex that matches your save endpoint, you can catch the rejection yourself:

   ```ts
   http.setIgnorePath('/api/v2/x/widgets$');
   try {
     await http.create(payload);
   } catch (response) {
     if (response.status === 422) {
       this.formError = response.data.error.message;
     }
   }
   ```

The async-rule approach is preferred for uniqueness; the catch-on-submit approach is the fallback for everything else.

---

# Recipes

## Recipe 1 — List page with filters, sort, paging

```ts
import {computed, ref} from 'vue';
import usePaginate from '@ohrm/core/util/composable/usePaginate';
import useSort from '@ohrm/core/util/composable/useSort';
import {APIService} from '@/core/util/services/api.service';

export default {
  setup() {
    const filters = ref({ name: '', categoryId: null });

    const {sortField, sortOrder, onSort, sortDefinition} = useSort({
      sortDefinition: {
        'w.name':        'ASC',
        'w.createdAt':   'DEFAULT',
      },
    });

    const serializedFilters = computed(() => ({
      name:       filters.value.name || undefined,
      categoryId: filters.value.categoryId?.id,
      sortField:  sortField.value,
      sortOrder:  sortOrder.value,
    }));

    const http = new APIService(window.appGlobal.baseUrl, '/api/v2/x/widgets');
    const {showPaginator, total, pages, currentPage, response, isLoading, execQuery} =
      usePaginate(http, {
        query: serializedFilters,
        normalizer: (rows) => rows.map(r => ({ id: r.id, name: r.name, category: r.category?.name })),
      });
    onSort(execQuery);

    return { showPaginator, total, pages, currentPage, items: response, isLoading, filters, sortDefinition };
  },
};
```

The `serializedFilters` `computed` is the single source of truth for query params. Any change to it (filter input, sort change) triggers `usePaginate` to re-fetch. **Don't try to call `execQuery()` manually from a filter handler** — let the watch do it.

## Recipe 2 — Create / update form

```ts
import {ref} from 'vue';
import useForm from '@/core/util/composable/useForm';
import useServerValidation from '@/core/util/composable/useServerValidation';
import {APIService} from '@/core/util/services/api.service';
import {required, shouldNotExceedCharLength} from '@/core/util/validation/rules';
import {navigate} from '@ohrm/core/util/helper/navigation';

export default {
  props: { id: { type: Number, default: 0 } },

  setup(props) {
    const http = new APIService(window.appGlobal.baseUrl, '/api/v2/x/widgets');
    http.setIgnorePath('/api/v2/core/validation/unique');
    const validation = useServerValidation(http);
    const {formRef, submit, invalid} = useForm();

    const form = ref({ name: '', description: '' });
    const isLoading = ref(false);

    if (props.id) {
      isLoading.value = true;
      http.get(props.id).then(r => { form.value = r.data.data; isLoading.value = false; });
    }

    const onSubmit = async () => {
      try {
        if (props.id) await http.update(props.id, form.value);
        else          await http.create(form.value);
        navigate('/x/widgets');
      } catch {
        // 422 already rejected; the interceptor toasted if no setIgnorePath matched
      }
    };

    return {
      formRef, invalid, form, onSubmit, isLoading,
      onSave: submit,                                      // triggers @submit-valid
      rules: {
        name: [
          required,
          shouldNotExceedCharLength(100),
          validation.createUniqueValidator('Widget', 'name', { entityId: props.id }),
        ],
        description: [shouldNotExceedCharLength(500)],
      },
    };
  },
};
```

Template:

```vue
<oxd-form :ref="formRef" @submit-valid="onSubmit">
  <oxd-input-field v-model="form.name"        :rules="rules.name"        :label="$t('general.name')" />
  <oxd-input-field v-model="form.description" :rules="rules.description" :label="$t('general.description')" />
  <oxd-form-actions>
    <oxd-button :label="$t('general.cancel')" type="reset" display-type="ghost" />
    <oxd-button :label="$t('general.save')" type="submit" :disabled="invalid" display-type="secondary" />
  </oxd-form-actions>
</oxd-form>
```

## Recipe 3 — File upload

```vue
<template>
  <oxd-form @submit-valid="onSubmit">
    <file-upload-input
      v-model="form.attachment"
      :rules="rules.attachment"
      :button-label="$t('general.choose_file')"
    />
    <oxd-button type="submit" :label="$t('general.upload')" />
  </oxd-form>
</template>

<script>
import {ref} from 'vue';
import FileUploadInput from '@/core/components/inputs/FileUploadInput';
import {APIService} from '@/core/util/services/api.service';
import {required, maxFileSize, validFileTypes} from '@/core/util/validation/rules';

export default {
  components: { 'file-upload-input': FileUploadInput },

  setup() {
    const http = new APIService(window.appGlobal.baseUrl, '/api/v2/x/upload');
    const form = ref({ attachment: null });

    const onSubmit = async () => {
      // form.attachment is { name, type, size, base64 } — matches backend Base64Attachment
      await http.create({ file: form.value.attachment });
    };

    return {
      form, onSubmit,
      rules: {
        attachment: [
          required,
          maxFileSize(1024 * 1024 * 5),                    // 5MB
          validFileTypes(['image/png', 'image/jpeg', 'application/pdf']),
        ],
      },
    };
  },
};
</script>
```

The `FileUploadInput` produces a `{name, type, size, base64}` object — the exact shape the backend `Base64Attachment` rule expects (see `rest-validation`). No manual base64 encoding needed.

## Recipe 4 — Bulk delete with confirmation dialog

```ts
import {ref} from 'vue';
import {APIService} from '@/core/util/services/api.service';
import DeleteConfirmationDialog from '@ohrm/components/dialogs/DeleteConfirmationDialog';

export default {
  components: { 'delete-confirmation': DeleteConfirmationDialog },

  setup() {
    const http = new APIService(window.appGlobal.baseUrl, '/api/v2/x/widgets');
    const checkedItems = ref([]);
    const dialog = ref(null);

    const onClickDeleteSelected = () => { dialog.value = 'delete'; };

    const onConfirmDelete = async () => {
      const ids = checkedItems.value.map(idx => /* … resolve indices to IDs */);
      await http.deleteAll({ ids });
      dialog.value = null;
      // refetch the list (e.g. via execQuery from usePaginate)
    };

    return { checkedItems, dialog, onClickDeleteSelected, onConfirmDelete };
  },
};
```

Backend (covered in `rest-endpoints`): bulk delete expects `{ ids: [int...] }` in the body — exactly what `http.deleteAll({ids})` sends.

## Recipe 5 — Custom one-off request (non-CRUD endpoint)

`http.request()` is the escape hatch for endpoints that don't fit the standard CRUD shape:

```ts
const http = new APIService(window.appGlobal.baseUrl, '/api/v2/core/i18n/messages');

const response = await http.request({
  method: 'GET',
  params: { locale: 'fr' },
});
```

Pass the same axios options shape you'd pass to `axios()`. The interceptors still apply (401, error toasts, ETag caching).

---

# Checklists

## Wire up a new list page

- [ ] `APIService(window.appGlobal.baseUrl, '/api/v2/...')` matching the backend route exactly
- [ ] `filters = ref({...})` + `serializedFilters = computed(() => ({...}))` — undefined for "no filter," not empty string
- [ ] `usePaginate(http, { query: serializedFilters, normalizer })` — normalizer reshapes API rows for the table
- [ ] `useSort` if columns are sortable; `onSort(execQuery)` to re-fetch on sort change
- [ ] Don't manually call `execQuery` from filter inputs — let the watch on `query` do it
- [ ] Backend's `DTO::ALLOWED_SORT_FIELDS` must include every `sortField` you send

## Wire up a new form page

- [ ] `useForm()` returning `formRef`, `submit`, `invalid`
- [ ] `<oxd-form :ref="formRef" @submit-valid="onSubmit">` — never `@submit`
- [ ] Form data in a `ref({})`; submit button `:disabled="invalid"`
- [ ] `rules: { field: [required, shouldNotExceedCharLength(N), …] }` — each rule from `@/core/util/validation/rules`
- [ ] Uniqueness checks via `useServerValidation().createUniqueValidator(...)` — pass `entityId` when updating to "ignore self"
- [ ] `http.setIgnorePath('/api/v2/core/validation/unique')` on the APIService used for the unique check, to suppress error toasts on 422
- [ ] On save success: `navigate(...)` to the list page (no in-place state preservation across pages)

## Add a custom validation rule

- [ ] **Inline** if used in one place: `(v) => /regex/.test(v) || this.$t('x.invalid')`
- [ ] **In `rules.ts`** if reusable: export a function returning `boolean | string`; use `translatorFactory()` for translated messages
- [ ] Match the existing style — rules are pure functions, no side effects, return `true` or an already-translated string

## Debug "my list doesn't update when I change a filter"

- [ ] `serializedFilters` is a `computed`, not a `ref` (refs containing objects don't trigger watch on nested change)
- [ ] The filter input's `v-model` is correctly bound to a field in the `filters` `ref` (typo in property name = silent failure)
- [ ] The query value isn't always identical — `JSON.stringify(filters)` between two changes should be different. `usePaginate`'s watch is deep enough but the value still has to actually change
- [ ] The backend route's `getValidationRuleForGetAll()` accepts the params you're sending — a 422 would be toasted (check Network tab)

## Debug "form save returns 422 but user sees no error"

- [ ] The save APIService doesn't have an `setIgnorePath` covering its own path — if it did, you need to handle the rejection yourself
- [ ] Check Network tab: response body's `error.message` shows the offending param key
- [ ] Either map the per-param error to the field (manual catch), or pre-validate with a `useServerValidation` rule so the error shows inline

## Things that bite

- **`APIService` is constructed per resource, not per page.** Don't create one and store it in a global — instantiate fresh in each page's `setup()`. The 401 interceptor depends on `getCurrentInstance()` finding the current Vue instance.
- **`http.setIgnorePath(regex)` suppresses error toasts but not rejections** — the promise still rejects, you still need to `.catch()` if you want to react.
- **`usePaginate`'s `query` must be a `ref` or `computed`** — passing a plain object doesn't trigger refetch on change.
- **Filter values: empty string vs `undefined`** — the backend's validator may reject empty strings (`""`) when it expects either absent or a real value. The `usePaginate` source has `params[key] = value === null || value === '' ? undefined : value` — that strips empties, so `serializedFilters` returning `''` for an unset filter gets normalized to absent.
- **`useServerValidation` debounces 500ms by default.** Tests that fire validation synchronously and expect a result need to await the debounce.
- **The form's `@submit-valid` event fires after all rules pass (including async ones).** Don't bind to `@submit` — that's the raw form-submit event and runs before validation.
- **`http.deleteAll({ids})` matches the backend's bulk DELETE convention** (`{ids: [int...]}` body — see `rest-endpoints`). Don't invent a custom shape.
- **ETag cache in prod can mask backend changes during testing.** If you're testing prod-mode locally and seeing stale data, clear localStorage or do a hard refresh.
