---
name: frontend-platform
description: Reference for OrangeHRM's cross-cutting Vue app plugins — i18n (`$t(key, params)` with module-grouped keys matching backend lang-string groups, the `translate()` factory for non-component contexts), ACL (`$can.read/create/update/delete('data_group_name')` where the data group string matches `ohrm_data_group.name` from the backend permission seeds), toaster (`$toast.success/error/info/warn` plus the project's semantic shortcuts `saveSuccess`, `addSuccess`, `updateSuccess`, `deleteSuccess`, `cannotDelete`, `noRecordsFound`, `unexpectedError`), the global loader, the `navigate()` / `reloadPage()` helpers, and `useDateFormat` for the user's date format. Use whenever the user is translating a string, gating a button with `$can`, picking a toast variant, navigating between pages, formatting a date for display, or asking why an automatic toast appeared. Companion to `authorization` (backend side of the data-group strings used by `$can`), `rest-validation` (the i18n keys used by the validation rule messages), and `frontend-pages` (where this all gets composed).
---

# Frontend platform — i18n, ACL, toaster, navigation

These four cross-cutting Vue app plugins (plus a couple of helpers) are wired up in `src/client/src/main.ts` and available to every component. They're how a Vue page integrates with OrangeHRM's translation, permission, notification, and routing systems.

This skill covers what each one does and the conventions for using them. For the backend side of permissions, see `authorization`. For where this stuff gets used inside a typical Vue page, see `frontend-pages`.

## i18n — `$t()` for translations

`@/core/plugins/i18n/translate.ts`. The translator is installed as a Vue app plugin (`app.use(i18n)`) and exposed as `$t` on every component.

### How translations get loaded

On boot, `main.ts` calls `init()` on the i18n plugin, which fetches all current-locale strings in one request:

```
GET /core/i18n/messages           → { "general.required": { source: "Required", target: "…" }, … }
```

The response keys go straight into `IntlMessageFormat` instances, stored in a module-level map (`langStrings`). The Vue app mounts only after `init()` resolves — see the end of `main.ts`:

```ts
init().then(() => app.mount('#app'));
```

So **by the time any component renders, translations are loaded**. There's no flash-of-untranslated-content.

### Using `$t` in a template

```vue
<oxd-button :label="$t('general.save')" />
<oxd-text>{{ $t('pim.employee_information') }}</oxd-text>
```

`$t(key)` returns the translated string. If the key isn't in `langStrings`, **it returns the key as-is** (no error, no warning) — so a typo silently shows `general.svae` to the user. Run the test pack to catch missing keys.

### Parameters (ICU MessageFormat)

```vue
<oxd-text>
  {{ $t('pim.records_selected', {count: selectedCount}) }}
</oxd-text>
```

Backend lang string:
```yaml
pim.records_selected: '{count, plural, one {1 record selected} other {{count} records selected}}'
```

Full ICU syntax is supported via `intl-messageformat` — pluralization, select, number/date formatting. Errors during interpolation (missing required parameter) are logged to `console.error` and the key is returned instead of throwing.

### Using `$t` in `setup()` (Composition API)

`$t` is added via a `beforeCreate` mixin, which makes it available on `this` and in templates **but not** during `setup()`. To use translations in setup code, use the `usei18n` composable:

```ts
import usei18n from '@/core/util/composable/usei18n';

setup() {
  const {$t} = usei18n();
  const errorMessage = ref($t('general.required'));
  // …
}
```

Same translator under the hood — just packaged for the setup context.

### Outside Vue components — the `translate()` factory

For modules that aren't Vue components (validation rules, services, etc.), import the factory:

```ts
import {translate as translatorFactory} from '@/core/plugins/i18n/translate';

const translate = translatorFactory();
// …
const message = translate('general.already_exists');
```

This is how `@/core/util/validation/rules.ts` translates its error messages — see `frontend-data` skill.

### Module-grouped keys

Lang-string keys follow `<module>.<descriptor>` convention. The first segment matches the i18n group on the backend (see `migrations` skill's lang-string section — the `LangStringHelper::insertOrUpdateLangStrings($dir, $group)` call). Common groups: `general` (shared across the app), `pim`, `admin`, `leave`, `time`, `attendance`, `claim`, `auth`, `recruitment`, `performance`, `dashboard`, `buzz`, `directory`.

When adding a translation:
- New key in the appropriate `lang-string/<module>.yaml` file under a migration
- Match an existing group's naming style (`general.save`, not `general.saveTheRecord`)
- Run the migration with `migration:up` for local testing
- The Vue side picks it up on next page boot — no client code change

### Adding a translation for new code

Backend side (the canonical pattern; see `migrations` skill for the full mechanics):

```yaml
# installer/Migration/V5_9_0/lang-string/x.yaml
langStrings:
  - { value: 'Save Widget', unitId: save_widget }
  - { value: 'Widget {name} saved successfully', unitId: widget_saved }
```

```php
// In the migration up()
$this->getLangStringHelper()->insertOrUpdateLangStrings(__DIR__, 'x');
$this->updateLangStringVersion($this->getVersion());
```

Then in the Vue template:

```vue
<oxd-button :label="$t('x.save_widget')" />
<oxd-text>{{ $t('x.widget_saved', {name: widget.name}) }}</oxd-text>
```

The key (`x.save_widget`) is `<group>.<unitId>`.

## ACL — `$can` for permission checks

`@/core/plugins/acl/acl.ts`. Installed as a Vue mixin (`app.use(acl)`) that adds `$can` to every component.

### The data flow

```
1. Backend renders vue.html.twig with <oxd-layout :permissions="{...}">
2. OXD layout calls Vue's provide('permissions', { dataGroupName: { canRead, canCreate, ... } })
3. $can.read/create/update/delete inject('permissions') and look up the named data group
```

The permissions map is keyed by **`DataGroup.name`** (the string seeded in `permission/api.yaml` or `permission/screens.yaml` — see `authorization` skill). E.g. `'apiv2_pim_employees'`, `'job_titles'`, `'system_users'`.

### Usage

```vue
<oxd-button v-if="$can.create('apiv2_pim_employees')" :label="$t('general.add')" />
<oxd-icon-button v-if="$can.update('apiv2_pim_employees')" @click="onEdit" />
<oxd-icon-button v-if="$can.delete('apiv2_pim_employees')" @click="onDelete" />
```

Returns `boolean` — `true` if the current user's role(s) grant the named CRUD bit on the named data group, `false` otherwise. The boolean comes from the server-side computation in `BasicUserRoleManager` (OR-merged across effective roles — see `authorization` skill).

### Multiple data groups

`$can.read('a', 'b', 'c')` returns true only if all three pass. AND semantics across args. Use when a screen needs multiple permissions:

```vue
<div v-if="$can.read('apiv2_pim_employees', 'apiv2_pim_employees_personal_details')">
  …
</div>
```

### The four methods map to the same CRUD bits as the backend

| Frontend | Backend bit |
|---|---|
| `$can.read('x')` | `can_read` on `ohrm_user_role_data_group` |
| `$can.create('x')` | `can_create` |
| `$can.update('x')` | `can_update` |
| `$can.delete('x')` | `can_delete` |

The HTTP verb mapping is the same as `ApiAuthorizationSubscriber` (see `authorization`): GET→`canRead`, POST→`canCreate`, PUT→`canUpdate`, DELETE→`canDelete`.

### What `$can` does NOT do

- **It doesn't enforce permissions** — it just hides UI. **The backend still has to enforce it** via `ApiAuthorizationSubscriber` + the data-group permission rows. `$can` is for hiding buttons a user can't use; if they bypass the UI and hit the API directly, the backend rejects them.
- **It doesn't auto-update** — the permissions are computed at render time on the server and shipped to the client once. If permissions change mid-session (rare), the user needs a page reload to see the new state.
- **It doesn't help with row-level access** (the `self` flag — see `authorization`). That's enforced server-side; the frontend just shows the action and the server rejects if it's not their row.

### "What's the data group name for this endpoint?" — finding it

In a migration, search `permission/api.yaml` for the Endpoint FQCN, or `permission/screens.yaml` for the URL. The map key (e.g. `apiv2_pim_employees`) is the string to pass to `$can`. Quick query for an installed DB:

```sql
SELECT name FROM ohrm_data_group WHERE name LIKE '%pim_employees%';
SELECT name FROM ohrm_screen     WHERE action_url = 'viewEmployeeList';
```

## Toaster — `$toast` for notifications

`@/core/plugins/toaster/toaster.ts`. Installed via `app.use(toaster, options)`. The toast container is appended into the DOM as `<oxd-toaster>` next to `#app`.

### The generic API

```ts
this.$toast.success({ title: 'OK', message: 'Saved' });
this.$toast.error({   title: 'Oops', message: 'Something went wrong' });
this.$toast.info({    title: 'Info', message: '…' });
this.$toast.warn({    title: 'Warn', message: '…' });
this.$toast.show({    title: '…', message: '…' });   // neutral
this.$toast.notify(rawToast);                         // full Toast object
this.$toast.clear(id);
this.$toast.clearAll();
```

All return `Promise<string>` resolving to the toast ID (so you can clear specific ones later).

### Project semantic shortcuts (use these by default)

```ts
this.$toast.saveSuccess();        // "Successfully Saved" — for generic save flows
this.$toast.addSuccess();         // "Successfully Added"
this.$toast.updateSuccess();      // "Successfully Updated"
this.$toast.deleteSuccess();      // "Successfully Deleted"
this.$toast.cannotDelete();       // "Cannot Delete" — when a delete is rejected for business reasons
this.$toast.noRecordsFound();     // "No Records Found" — auto-fired by usePaginate when a search yields 0 (controllable via toastNoRecords option)
this.$toast.unexpectedError(msg); // "Unexpected Error" — auto-fired by APIService on non-401/422 errors
```

**Prefer the semantic shortcuts over hand-rolled toasts.** They're translated consistently across the app and match the user's experience on other pages.

### Auto-fired toasts (you don't call these manually)

- **`unexpectedError`** — fires automatically from `APIService` response interceptor on any non-401 error unless `setIgnorePath` matches the URL. Don't manually toast on every API failure; the interceptor already did.
- **`noRecordsFound`** — fires automatically from `usePaginate` when a query returns zero rows. Disable with `toastNoRecords: false` option if not appropriate for the page.

### In `setup()` — use `useToast` composable

```ts
import useToast from '@/core/util/composable/useToast';

setup() {
  const toast = useToast();
  const onSave = async () => {
    await http.create(payload);
    toast.saveSuccess();
  };
  return { onSave };
}
```

Same API surface as `$toast` — just packaged for the setup context.

## Loader — global loading indicator

`@/core/plugins/loader/`. A simple show/hide loader controlled globally. Not heavily used in modern pages (most use `isLoading` from `usePaginate` and pass it to OXD components' built-in loading states), but available for full-page-blocking spinners.

The included SCSS (`loader.scss`) is imported in `main.ts`. The plugin itself attaches an element to the DOM at app boot. If you need to show it programmatically, follow whatever existing example you find in a plugin (it's used sparingly).

## Navigation — `navigate()` and `reloadPage()`

`@/core/util/helper/navigation.ts`. Plain helpers, not Vue plugins.

```ts
import {navigate, reloadPage} from '@ohrm/core/util/helper/navigation';

navigate('/pim/viewEmployee/123');
navigate('/pim/viewEmployee/{id}', { id: 123 });
navigate('/pim/viewEmployee', {}, { tab: 'job' });    // → /pim/viewEmployee?tab=job
navigate('/x/listX', {}, { ids: [1, 2, 3] });          // → /x/listX?ids=1&ids=2&ids=3

reloadPage();                                          // window.location.reload()
```

Signature: `navigate(path, params?, query?)`. Both `params` (interpolated into `{placeholders}`) and `query` (appended as `?…`) accept primitives; query also accepts arrays.

Internally it calls `urlFor()` (from `@ohrm/core/util/helper/url`) and sets `window.location.href`. If you just need to build a URL without navigating, use `urlFor()` directly — same signature, returns a string.

**This is the only navigation mechanism in OrangeHRM's Vue layer.** No `vue-router`. Every page transition is a full reload — see `frontend-pages` for why.

## Date formatting — `useDateFormat`

`@/core/util/composable/useDateFormat.ts`. Returns the user's configured date format, translated from PHP format (`d-m-Y`) to JS-friendly format (`dd-MM-yyyy`).

```ts
import useDateFormat from '@/core/util/composable/useDateFormat';
import {formatDate} from '@/core/util/helper/datefns';

setup() {
  const {jsDateFormat, userDateFormat, jsTimeFormat, timeFormat} = useDateFormat();
  return {
    formattedDate: formatDate(new Date(), jsDateFormat),
  };
}
```

| Field | Use |
|---|---|
| `jsDateFormat` | `date-fns`-compatible format string (e.g. `dd-MM-yyyy`). Pass to `formatDate()` from `@/core/util/helper/datefns`. |
| `userDateFormat` | Human-readable label (e.g. `dd-mm-yyyy`). Show in UI when telling the user what format to use. |
| `timeFormat` | `'HH:mm'` (24-hour). |
| `jsTimeFormat` | `'hh:mm a'` (12-hour with AM/PM for display). |

The format originates from the user's preference, injected by the server into `vue.html.twig` via `<oxd-layout :date-format="…">` and exposed via Vue's `inject`. `useDateFormat` reads it; you don't.

**Don't hardcode date formats** — always go through `useDateFormat`. Different users will see different formats (`dd-mm-yyyy`, `mm-dd-yyyy`, `yyyy-mm-dd`, etc.).

---

# Recipes

## Recipe 1 — Translate + parameterize a string

Migration (see `migrations` skill):
```yaml
# installer/Migration/V5_9_0/lang-string/x.yaml
langStrings:
  - { value: '{count, plural, one {1 widget} other {# widgets}} found', unitId: widgets_found }
```

```php
$this->getLangStringHelper()->insertOrUpdateLangStrings(__DIR__, 'x');
$this->updateLangStringVersion($this->getVersion());
```

Vue:
```vue
<oxd-text>{{ $t('x.widgets_found', { count: total }) }}</oxd-text>
```

## Recipe 2 — Gate a button by permission

Backend (in a migration's `permission/api.yaml`):
```yaml
apiv2_x_widgets:
  description: 'X - Widgets'
  api: OrangeHRM\X\Api\WidgetAPI
  module: x
  allowed: { read: true, create: true, update: true, delete: true }
  permissions:
    - { role: Admin, permission: { read: true, create: true, update: true, delete: true } }
    - { role: ESS,   permission: { read: true, create: false, update: false, delete: false } }
```

Frontend:
```vue
<div v-if="$can.create('apiv2_x_widgets')">
  <oxd-button :label="$t('general.add')" icon-name="plus" @click="onClickAdd" />
</div>
<oxd-icon-button v-if="$can.delete('apiv2_x_widgets')" name="trash" @click="onDelete" />
```

The data-group name `apiv2_x_widgets` matches the YAML key exactly. The user's effective permissions on that group decide visibility.

## Recipe 3 — Save flow with toast + navigation

```ts
import useToast from '@/core/util/composable/useToast';
import {navigate} from '@ohrm/core/util/helper/navigation';

setup() {
  const toast = useToast();
  const http = new APIService(window.appGlobal.baseUrl, '/api/v2/x/widgets');

  const onSave = async () => {
    try {
      if (props.id) {
        await http.update(props.id, form.value);
        toast.updateSuccess();
      } else {
        await http.create(form.value);
        toast.addSuccess();
      }
      navigate('/x/widgets');
    } catch {
      // 422 already handled by interceptor unless setIgnorePath is on
    }
  };

  return { onSave };
}
```

**No manual error toast** on the `catch` — `APIService` already toasted `unexpectedError` for unexpected failures, and 422 validation errors should have been caught at the form-rule layer.

## Recipe 4 — Show a delete-rejected message

```ts
const onConfirmDelete = async () => {
  try {
    await http.deleteAll({ ids: selectedIds });
    toast.deleteSuccess();
  } catch (response) {
    if (response.status === 400 && response.data?.error?.message?.includes('in use')) {
      toast.cannotDelete();              // semantic message, not the raw backend string
    }
  }
};
```

`cannotDelete` is the canonical "this can't be deleted because something depends on it" UX. Use it instead of toasting the raw backend error.

## Recipe 5 — Format a date for display

```vue
<template>
  <oxd-text>{{ formattedJoinedDate }}</oxd-text>
</template>

<script>
import {computed} from 'vue';
import useDateFormat from '@/core/util/composable/useDateFormat';
import {formatDate, parseDate} from '@/core/util/helper/datefns';

export default {
  props: { employee: { type: Object, required: true } },
  setup(props) {
    const {jsDateFormat} = useDateFormat();
    const formattedJoinedDate = computed(() => {
      if (!props.employee.joinedDate) return '';
      return formatDate(parseDate(props.employee.joinedDate, 'yyyy-MM-dd'), jsDateFormat);
    });
    return { formattedJoinedDate };
  },
};
</script>
```

The API always returns dates in `yyyy-MM-dd` regardless of locale. The frontend reformats for display using the user's format. Don't try to do this in the backend — the user's date format isn't a backend concern.

---

# Checklists

## Add a translatable string

- [ ] Identify the right module group (`general`, `pim`, `admin`, etc.); `general` for cross-module strings, otherwise the module the string is specific to
- [ ] Add to `lang-string/<module>.yaml` in the next migration (see `migrations` skill)
- [ ] Call `$this->getLangStringHelper()->insertOrUpdateLangStrings(__DIR__, '<module>')` + `updateLangStringVersion()` in the migration's `up()`
- [ ] In Vue: `$t('<module>.<unit_id>')`
- [ ] For pluralization or parameters: use ICU MessageFormat syntax in the YAML value, pass params as the second arg to `$t`

## Gate UI by permission

- [ ] Identify the backend data-group name (from `permission/api.yaml` or `permission/screens.yaml`)
- [ ] `v-if="$can.<verb>('<data_group_name>')"` on the action element (button, link, menu item)
- [ ] Backend enforcement is **mandatory** — the frontend gate only hides UI, the API/screen subscriber still has to reject unauthorized requests (see `authorization`)
- [ ] For ANDing multiple permissions: `$can.read('a', 'b')`

## Show a success / failure notification

- [ ] Save / create / update / delete — use the semantic shortcut (`addSuccess`, `updateSuccess`, `deleteSuccess`, `saveSuccess`)
- [ ] Generic info/error — use `$toast.success/error/info/warn({title, message})`
- [ ] Don't manually toast on every API error — `APIService.unexpectedError` interceptor already does it for non-401/422 responses
- [ ] In `setup()`, use `useToast()` instead of `this.$toast`

## Navigate between pages

- [ ] `navigate('/path/to/page')` — full reload
- [ ] For URL params: `navigate('/path/{id}', { id: 123 })`
- [ ] For query string: `navigate('/path', {}, { foo: 'bar' })`
- [ ] **Don't** propose adding `vue-router` to enable client-side routing — see `frontend-pages` for why

## Things that bite

- **A missing translation key returns the key as-is** — the user sees `general.svae` literally. Never assume `$t` will throw or warn.
- **`$can` isn't security** — it's UX. Always have a matching backend permission rule in `permission/*.yaml`. A hidden button + open API endpoint is a security hole.
- **The data-group string in `$can('x')` must match `ohrm_data_group.name` exactly** — case-sensitive, no leading/trailing space, underscores not dashes. A typo silently returns `false` and the UI hides forever.
- **`$can` reads from `inject('permissions')`** which is set by `<oxd-layout>`. If you mount a Vue component outside the layout (rare but possible — e.g. login page), `$can` returns `false` for everything because no permissions are provided.
- **The toaster auto-fires `unexpectedError` from APIService** for any non-401/422 response. If you have an endpoint where errors are expected (like a validation-only endpoint), call `http.setIgnorePath(regex)` to suppress.
- **`navigate()` is `window.location.href = ...`** — it's not async; the page is leaving. Don't put code after a `navigate()` call expecting it to run.
- **Date formats: API uses `yyyy-MM-dd` always; display uses `useDateFormat`.** Never hardcode a format in templates; always go through `useDateFormat` + `formatDate()`.
- **`$t` is `undefined` in `setup()`** — use `usei18n()` to get it for setup-scope use.
- **Lang strings load once at app boot**, not on locale change. Switching locale requires a full page reload (which OHRM does — there's no in-app locale switcher mid-page).
