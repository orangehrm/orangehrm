---
name: menus
description: Reference for OrangeHRM's menu system — the `MenuItem` entity in `ohrm_menu_item` (hierarchical via `parent_id` + `level`, ordered by `order_hint`, linked to a `Screen` via `screen_id`), the side panel + top menu rendering pipeline (server-computed in `MenuService::getMenuItems()`, passed to the Twig layout as JSON, consumed by `<oxd-layout :sidepanel-menu-items="…" :topbar-menu-items="…">`), the `MenuConfigurator` interface for runtime menu customization (referenced by `Screen.menu_configurator`, lets a screen mutate which menu item highlights as active or even substitute an entirely different screen), permission gating via `ScreenPermission` rows (menu items linked to screens the user can't see get hidden), `additional_params` JSON for icon/URL extras, and the standard pattern of seeding `ohrm_menu_item` rows via migrations. Use whenever the user is adding a menu entry alongside a new page, restructuring the navigation hierarchy, debugging "my new page exists but doesn't appear in the menu", or writing a MenuConfigurator to override the active-menu logic on a special-case screen. Companion to `frontend-pages` (the page that the menu links to), `authorization` (screen permissions decide visibility), `migrations` (where menu rows get seeded), `entities` (MenuItem entity shape).
---

# Menus — side panel + top menu navigation

OrangeHRM's navigation chrome (the dark side panel + the top horizontal menu inside each section) is **server-computed per request, rendered by Vue from JSON injected via Twig**. Menu items live in `ohrm_menu_item` and are visibility-gated by the user's role-screen permissions.

This skill covers: the entity model, how menus get computed and rendered, the `MenuConfigurator` extension hook, and the standard pattern for adding a menu entry when shipping a new page.

## The two-level structure

```
┌──────────────────────────────────────────────┐
│ Side panel    │ Top menu within selected section │
├───────────────┼──────────────────────────────┤
│ Admin    ◉   │ Configuration ▾  Users  Job ▾  Organization ▾  …  │   ← top menu
│ PIM           │                                  │
│ Leave         │  [page content here]             │
│ Time          │                                  │
│ Recruitment   │                                  │
│ …             │                                  │
└───────────────┴──────────────────────────────┘
   ↑ level 1                ↑ level 2 + children
```

| Layer | Stored as | Renders as |
|---|---|---|
| **Side panel** | `MenuItem` rows with `level=1` and `parent_id=NULL` | The vertical icon-list on the left (PIM, Leave, Time, etc.) |
| **Top menu** | `MenuItem` rows with `level=2` under a selected side-panel item | The horizontal menu bar at the top of the section (within Admin: User Management, Job, Organization, Qualifications, …) |
| **Sub-menu (level 3+)** | `MenuItem` rows with `level=3`, parented to a top menu item | The dropdown items under a top-menu hover |

Only side panel + top menu are typically active at once; the side panel determines the section, and the top menu shows the screens of that section.

## The `MenuItem` entity

```php
@ORM\Table(name="ohrm_menu_item")
class MenuItem
{
    private int $id;
    private string $menuTitle;                                   // shown to the user (translatable via $t)
    private ?MenuItem $parent = null;                            // null = top-level (side panel)
    private int $level;                                          // 1 = side panel, 2 = top menu, 3+ = nested
    private int $orderHint;                                      // sort order within siblings
    private bool $status;                                        // false = hidden (e.g. when a module is disabled)
    private ?Screen $screen = null;                              // the destination page (or null = group only)
    private ?array $additionalParams = [];                       // JSON blob; usually {icon: 'pim', class: '...'}
}
```

Six fields shape the menu:

1. **`menuTitle`** — display string. **Not translated in the DB** — the value matches a lang-string key (or sometimes the literal English fallback). The Vue side runs it through `$t()` to translate (see `frontend-platform`).

2. **`parent`** — self-FK. Root items have `parent=NULL`. Children point at their parent's `id`.

3. **`level`** — `1` (side panel), `2` (top menu), `3` (top menu dropdown items). Defined by where in the tree the item sits; redundantly stored so queries don't have to walk the tree.

4. **`orderHint`** — sort key within siblings. Lower number = appears first.

5. **`status`** — boolean toggle. `false` hides the item without deleting the row. Used when a module is disabled (see `enableModuleMenuItems()` below).

6. **`screen`** — FK to `ohrm_screen`. Two purposes:
   - **Destination**: clicking the menu item navigates to `Screen.action_url` within `Screen.module_id`.
   - **Permission gate**: the user must have `can_read` on this `ohrm_user_role_screen` row to see the menu item (see `authorization` skill). If they lack it, the menu service filters this item out.
   - A menu item with `screen=NULL` is a **group only** — it appears as a label, may have children, but can't be clicked to navigate.

7. **`additionalParams`** — `json` column. Typically `{"icon": "user", "class": "admin-icon"}` for side-panel items, or extras specific to the rendering. The OXD layout reads these in the Vue side.

## How menu rendering works

End-to-end flow on every request that hits an `AbstractVueController`:

```
1. Page controller's preRender() runs (see frontend-pages skill)
   ↓
2. AbstractVueController calls VueControllerHelper::getContextParams()
   ↓
3. VueControllerHelper calls MenuService::getMenuItems(baseUrl)
   ↓
4. MenuService:
   - Looks up the current Screen from URL (via ModuleScreenHelper)
   - If the Screen has a MenuConfigurator, instantiates and calls configure()
     → may return a MenuItem chain that overrides active-detection
     → may override which Screen is considered "current" via overrideScreen()
   - Loads all level-1 (side panel) items + caches result
   - Walks each to determine the active side-panel item
   - For the active side-panel, loads its level-2 (top menu) items + caches
   - Walks each top-menu item to determine which is active
   - Filters out items the user lacks screen permission for
   - Filters out items where status=false (disabled modules)
   - Normalizes each into a JSON-friendly dict
   ↓
5. Returns [sidePanelMenuItems, topMenuItems] arrays
   ↓
6. Twig template renders:
   <oxd-layout
     :sidepanel-menu-items="{{ sidePanelMenuItems | json_encode() }}"
     :topbar-menu-items="{{ topMenuItems | json_encode() }}"
     …
   />
   ↓
7. Vue's <oxd-layout> renders the menu chrome using those props
```

Two takeaways:

- **All menu logic is server-side.** The frontend (`<oxd-layout>`) is a dumb renderer — it gets JSON, displays it. There is no client-side menu construction.
- **Visibility = permission AND status.** A menu item shows only if (a) the user's effective roles include `can_read` on the linked screen (see `authorization`), and (b) the item's `status=true` (the module isn't disabled).

## The cache layer

`MenuService` caches the **detailed side panel menu items** and **top menu items per side-panel** in the OHRM cache (`CacheTrait` — see `helpers`):

```php
// MenuService internals
private function getDetailedSidePanelMenuItemsAlongWithCache(): array
{
    return $this->getCache()->get('detailed_side_panel_menu_items', /* compute callback */);
}

private function getTopMenuItemsAlongWithCache(int $sidePanelMenuItemId): array
{
    $key = "top_menu_items.{$sidePanelMenuItemId}";
    return $this->getCache()->get($key, /* compute callback */);
}
```

**Implications:**
- Menu structure is cached **globally**, not per user. Permission filtering happens *after* the cached fetch, on the filtered list per-request.
- After a migration that adds/removes/renames menu items: **`MenuService::invalidateCachedMenuItems()`** must run. Or `bin/console cache:clear` (see `console-commands`).
- Adding a menu item without invalidating the cache means existing prod instances won't show it until the next cache wipe.

`enableModuleMenuItems(string $moduleName, array $menuTitles = [])` is the canonical "enable / disable module's menu items" call. Used by the Dashboard subscribers when a module's status toggles (see `events`). It updates `status` on the relevant rows and invalidates the cache.

## `MenuConfigurator` — the runtime extension hook

```php
namespace OrangeHRM\Core\Menu;

interface MenuConfigurator
{
    public function configure(Screen $screen): ?MenuItem;
}
```

Per-screen runtime customization. **Stored on `Screen.menu_configurator`** as the FQCN string of an implementing class. When `MenuService::getMenuItems()` resolves the current screen, if a configurator is set, it's instantiated and called.

The configurator returns either:
- A `MenuItem` — the menu service walks its parent chain and uses that as the "active menu" highlight (overrides the normal active-detection). Used when a page should highlight a different menu entry than the screen-based default.
- `null` — the configurator only needed to side-effect (e.g. call `getCurrentModuleAndScreen()->overrideScreen('viewJobTitleList')` to make the active-menu computation use a different screen).

Examples in the codebase (`*/Menu/*MenuConfigurator.php`):

- `JobTitleMenuConfigurator` — on the "Add Job Title" screen, override the current screen to `viewJobTitleList` so the list-page menu item highlights (otherwise "Add" would have no menu entry).
- `PIMLeftMenuItemConfigurator` — on an Employee detail screen, if the viewing user is *themselves*, highlight "My Info" in the side panel; otherwise treat it as the employee list view.
- `PayGradeConfigurator`, `LocationMenuConfigurator`, etc. — same "override active" pattern for various entity-add/edit screens.

**When to write one:**
- A "create" or "edit" page where the matching menu item is the parent list ("Add User" should highlight "User Management → Users")
- A polymorphic page that should highlight different menu items based on who's viewing or what they're viewing
- Anything where the menu item to highlight isn't directly the screen the user is on

**When not to:**
- The screen has its own menu entry → no configurator needed, default active-detection works
- You want to globally hide a menu item → that's `status` or permissions, not a configurator

### Configurator template

```php
namespace OrangeHRM\X\Menu;

use OrangeHRM\Core\Menu\MenuConfigurator;
use OrangeHRM\Core\Traits\ModuleScreenHelperTrait;
use OrangeHRM\Core\Traits\Service\MenuServiceTrait;
use OrangeHRM\Entity\MenuItem;
use OrangeHRM\Entity\Screen;

class WidgetEditMenuConfigurator implements MenuConfigurator
{
    use ModuleScreenHelperTrait;
    use MenuServiceTrait;

    public function configure(Screen $screen): ?MenuItem
    {
        // Option A: pretend we're on a different screen for active-menu purposes
        $this->getCurrentModuleAndScreen()->overrideScreen('viewWidgetList');
        return null;
    }
}
```

Or to point at a specific menu item directly:

```php
public function configure(Screen $screen): ?MenuItem
{
    return $this->getMenuService()->getMenuDao()->getMenuItemByTitle('Widget Management', 1);
}
```

Configurators can compose any traits (auth user, request, services) and make decisions based on context — see `PIMLeftMenuItemConfigurator` which checks `$this->getAuthUser()->getEmpNumber()` against a URL param.

## Permission gating

Menu items linked to a `Screen` are filtered by the user's role-screen permissions (the `ohrm_user_role_screen` table — see `authorization` skill). The filter happens in `MenuService` after the cache fetch:

```
For each menu item:
  if (item->screen != null)
    if (no row in ohrm_user_role_screen for user's effective roles × this screen with can_read=true)
      filter out
```

**Group-only items** (`screen=NULL`) aren't permission-gated directly — they show if any of their *children* show. An empty group is filtered out.

This means: **a permission seed without a matching menu item gives the user access to the screen via URL but no menu entry to reach it**. Conversely, a menu item without a permission seed shows for nobody and is dead. Always seed both together (see `authorization` skill for permission seeding).

## `additionalParams` — the JSON extras

The column carries layout-specific extras the Vue layer wants:

```json
{
  "icon": {"name": "pim"},
  "class": "menu-pim"
}
```

The OXD `<oxd-layout>` component reads `icon` to render the side-panel icon, and may use `class` for styling. **Exact shape depends on which OXD layout version you're on** — to know what's supported, check existing menu rows that work and mirror them.

For new side-panel items, the most important extra is `icon` — pick from the OXD icon set (the icons used by existing items: `user`, `dashboard`, `pim`, `time`, `leave`, etc.).

For top-menu and child items, `additionalParams` is usually empty — they don't need icons or extra styling beyond the title.

## Seeding via migrations

Menu items are seeded by **migrations** (see `migrations` skill), inserted into `ohrm_menu_item`. There's no YAML helper specifically for this; use `createQueryBuilder()->insert()` directly:

```php
// Inside a migration's up()
public function up(): void
{
    // First create/find the parent (e.g. the "Admin" side panel item)
    $parentId = $this->createQueryBuilder()
        ->select('id')
        ->from('ohrm_menu_item')
        ->where('menu_title = :title AND level = 1')
        ->setParameter('title', 'Admin')
        ->fetchOne();

    // Get the screen this menu item links to
    $screenId = $this->createQueryBuilder()
        ->select('id')
        ->from('ohrm_screen')
        ->where('action_url = :url')
        ->setParameter('url', 'viewWidgetList')
        ->fetchOne();

    // Insert the menu item
    $this->createQueryBuilder()
        ->insert('ohrm_menu_item')
        ->values([
            'menu_title' => ':title',
            'screen_id'  => ':screenId',
            'parent_id'  => ':parentId',
            'level'      => ':level',
            'order_hint' => ':order',
            'status'     => ':status',
        ])
        ->setParameter('title', 'Widgets')
        ->setParameter('screenId', $screenId)
        ->setParameter('parentId', $parentId)
        ->setParameter('level', 2)            // top menu within Admin
        ->setParameter('order', 500)          // adjust to slot it where you want
        ->setParameter('status', true)
        ->executeStatement();

    // Invalidate the cache so existing instances see the new item
    // (caches don't persist across migration runs typically, but defensive)
}
```

**Always seed in this order:**
1. The screen first (`ohrm_screen`) — see `authorization` skill
2. The screen permissions (`ohrm_user_role_screen`) — see `authorization` skill
3. The menu item (`ohrm_menu_item`) — this skill

The migration is one atomic operation; failures roll back. Don't add the menu item before the screen exists or you'll have a dangling FK.

## When a module is disabled

OrangeHRM has module-level on/off (`ohrm_module.status`). When a module gets disabled:
- The `ModuleStatusChange` event fires (see `events` skill)
- Plugin-specific subscribers (e.g. `BuzzModuleStatusChangeSubscriber`, `TimeModuleStatusChangeSubscriber`, `LeaveModuleStatusChangeSubscriber` in Dashboard) call `MenuService::enableModuleMenuItems($moduleName, [])` with empty titles to flip every related menu item's `status` to false
- The cache is invalidated
- On the next request, the menu items are gone

`enableModuleMenuItems(string $moduleName, array $menuTitles = [])`:
- `$menuTitles` empty = disable all
- `$menuTitles` non-empty = enable only those specific titles, disable the rest

You don't typically call this manually — the module status-change subscriber handles it. But it's why a menu item suddenly disappearing usually correlates with a module toggle.

---

# Recipes

## Recipe 1 — Add a menu entry alongside a new page

Assuming the new page (Widget List) already has:
- A Vue component registered (`frontend-pages` skill)
- A backend page controller (`authorization` skill)
- A `Screen` row seeded with `action_url='viewWidgetList'`, `module=admin`
- A `ScreenPermission` row for Admin role with `can_read=true`

Now add the menu item:

```php
// In a migration's up()
$screenId = $this->createQueryBuilder()
    ->select('id')->from('ohrm_screen')
    ->where('action_url = :url')->setParameter('url', 'viewWidgetList')
    ->fetchOne();

$parentId = $this->createQueryBuilder()
    ->select('id')->from('ohrm_menu_item')
    ->where('menu_title = :title AND level = 1')
    ->setParameter('title', 'Admin')
    ->fetchOne();

$this->createQueryBuilder()
    ->insert('ohrm_menu_item')
    ->values([
        'menu_title' => ':title',
        'screen_id'  => ':screenId',
        'parent_id'  => ':parentId',
        'level'      => ':level',
        'order_hint' => ':order',
        'status'     => ':status',
    ])
    ->setParameter('title', 'Widgets')
    ->setParameter('screenId', $screenId)
    ->setParameter('parentId', $parentId)
    ->setParameter('level', 2)
    ->setParameter('order', 600)
    ->setParameter('status', true)
    ->executeStatement();
```

Existing instances need `bin/console cache:clear` (or wait for the cache to expire) before the new menu item appears.

## Recipe 2 — Add a side-panel section for a new module

A new top-level section (level 1) gets its own icon:

```php
$this->createQueryBuilder()
    ->insert('ohrm_menu_item')
    ->values([
        'menu_title' => ':title',
        'screen_id'  => ':screenId',
        'parent_id'  => ':parentId',                            // NULL for side panel
        'level'      => ':level',
        'order_hint' => ':order',
        'status'     => ':status',
        'additional_params' => ':params',
    ])
    ->setParameter('title', 'X')
    ->setParameter('screenId', $defaultLandingScreenId)        // first page in section
    ->setParameter('parentId', null)
    ->setParameter('level', 1)
    ->setParameter('order', 800)                                // after existing sections
    ->setParameter('status', true)
    ->setParameter('params', json_encode(['icon' => ['name' => 'x-icon']]))
    ->executeStatement();
```

The `icon` value must match an OXD-supported icon name. Look at existing side-panel item rows to see what's available.

After adding the side panel section, also add level-2 (top menu) items underneath it — same pattern, with `parent_id` set to the side panel's id.

## Recipe 3 — A MenuConfigurator for an "edit" screen

```php
// src/plugins/orangehrmXPlugin/Menu/EditWidgetMenuConfigurator.php
namespace OrangeHRM\X\Menu;

use OrangeHRM\Core\Menu\MenuConfigurator;
use OrangeHRM\Core\Traits\ModuleScreenHelperTrait;
use OrangeHRM\Entity\MenuItem;
use OrangeHRM\Entity\Screen;

class EditWidgetMenuConfigurator implements MenuConfigurator
{
    use ModuleScreenHelperTrait;

    public function configure(Screen $screen): ?MenuItem
    {
        // On the edit page, highlight "Widgets" (the list page's menu entry)
        $this->getCurrentModuleAndScreen()->overrideScreen('viewWidgetList');
        return null;
    }
}
```

Then **point the screen at this configurator** in the migration:

```php
// Add the editWidget screen with menu_configurator set
$this->createQueryBuilder()
    ->insert('ohrm_screen')
    ->values([
        'name'              => ':name',
        'module_id'         => ':moduleId',
        'action_url'        => ':url',
        'menu_configurator' => ':configurator',
    ])
    ->setParameter('name', 'Edit Widget')
    ->setParameter('moduleId', $adminModuleId)
    ->setParameter('url', 'editWidget')
    ->setParameter('configurator', 'OrangeHRM\X\Menu\EditWidgetMenuConfigurator')
    ->executeStatement();
```

If you're using the `permission/screens.yaml` helper from `authorization` skill, set `menu_configurator` there:

```yaml
editWidget:
  name: 'Edit Widget'
  module: admin
  url: editWidget
  menu_configurator: OrangeHRM\X\Menu\EditWidgetMenuConfigurator
  permissions:
    - { role: Admin, permission: { read: true, create: false, update: true, delete: false } }
```

Now when the user is on `/admin/editWidget`, the "Widgets" menu item highlights instead of nothing.

## Recipe 4 — A polymorphic configurator (different highlight based on context)

```php
class WidgetDetailConfigurator implements MenuConfigurator
{
    use AuthUserTrait;
    use ControllerTrait;
    use MenuServiceTrait;

    public function configure(Screen $screen): ?MenuItem
    {
        $widgetId = $this->getCurrentRequest()->attributes->get('widgetId');
        $isMine = $this->getAuthUser()->ownsWidget($widgetId);

        if ($isMine) {
            return $this->getMenuService()->getMenuDao()
                ->getMenuItemByTitle('My Widgets', 2);
        }

        return $this->getMenuService()->getMenuDao()
            ->getMenuItemByTitle('All Widgets', 2);
    }
}
```

Pattern from `PIMLeftMenuItemConfigurator`. Useful when one screen serves multiple distinct user-flows and the right menu highlight depends on which flow.

## Recipe 5 — Disable a menu item without removing the screen

```php
$this->createQueryBuilder()
    ->update('ohrm_menu_item')
    ->set('status', ':status')
    ->where('menu_title = :title AND level = 2')
    ->setParameter('status', false)
    ->setParameter('title', 'Deprecated Section')
    ->executeStatement();
```

The screen still works via direct URL; the menu just doesn't show it. Reverse by setting `status = true`. **Cache clear required** for the change to take effect on existing prod instances.

---

# Checklists

## Add a menu entry for a new page

- [ ] Backend page controller exists and renders the Vue component (see `frontend-pages` skill)
- [ ] `Screen` row exists in `ohrm_screen` with the action URL (seed via `permission/screens.yaml` in a migration — see `authorization` skill)
- [ ] `ohrm_user_role_screen` rows seeded so target roles can read the screen
- [ ] Find the parent `MenuItem`'s id (the section side-panel item, or the level-2 top menu item if your screen is a level-3 sub-item)
- [ ] Insert a row in `ohrm_menu_item` with: `menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `status=true`, optionally `additional_params`
- [ ] After deploy: `bin/console cache:clear` (or wait for cache TTL) so the menu service picks up the new item
- [ ] Verify in browser: log in as the targeted role and confirm the menu item appears + clicks to the right page

## Write a MenuConfigurator

- [ ] Class in `src/plugins/orangehrm{X}Plugin/Menu/<Name>MenuConfigurator.php` implementing `MenuConfigurator`
- [ ] `configure(Screen $screen): ?MenuItem` returns the menu item to highlight (or `null` and call `overrideScreen()` to make the default active-detection match a different screen)
- [ ] Compose `ModuleScreenHelperTrait` for `getCurrentModuleAndScreen()`
- [ ] Compose `MenuServiceTrait` if returning a `MenuItem` directly
- [ ] Compose `AuthUserTrait` + `ControllerTrait` if behavior depends on user/request context
- [ ] Set `menu_configurator` to the configurator's FQCN on the relevant `Screen` row (via `permission/screens.yaml` or migration)
- [ ] No registration step — `MenuService` looks up the configurator FQCN per request

## Debug "my new menu item doesn't appear"

- [ ] **Is the row in `ohrm_menu_item`?** Check it directly: `SELECT * FROM ohrm_menu_item WHERE menu_title = '…'`. Missing = migration didn't run, or referenced wrong parent.
- [ ] **Is `status=true`?** Toggle in DB to verify.
- [ ] **Has the cache been invalidated?** `bin/console cache:clear` and refresh. Stale cache is the most common cause in prod.
- [ ] **Does the user have screen permission?** `SELECT * FROM ohrm_user_role_screen WHERE screen_id = … AND user_role_id IN (…)`. No row = item filtered out.
- [ ] **Is `level` right?** A level-2 row with no level-1 parent ID won't appear; a level-1 row with a parent ID won't either.
- [ ] **Is the parent's `status` true?** If the parent side-panel section is disabled, all children are hidden too.
- [ ] **Is the module status enabled?** `SELECT status FROM ohrm_module WHERE name = '…'`. A disabled module means the `ModuleStatusChange` event has flipped its menu items' `status` to false.

## Add a MenuConfigurator for an existing screen

- [ ] Configurator class implementing `MenuConfigurator`
- [ ] Migration updates the `ohrm_screen` row: `UPDATE ohrm_screen SET menu_configurator = 'FQCN' WHERE action_url = '...'`
- [ ] Cache clear after deploy
- [ ] Verify: navigate to the screen, confirm the intended menu item is highlighted instead of nothing

## Things that bite

- **Cache invalidation is mandatory after menu changes.** `MenuService` caches side-panel + per-section top-menu lists in the OHRM cache. Adding a row in `ohrm_menu_item` and not running `bin/console cache:clear` means prod instances keep the old menu indefinitely. Migrations that add menu items should make this clear in their commit message.
- **Menu items linked to a non-existent screen** are silently filtered out. A migration that inserts a menu row before the corresponding screen row creates a dead menu entry. Always seed: Screen → ScreenPermission → MenuItem.
- **Permission filter happens after cache fetch.** That's why caching is safe across users — the cache holds the structure, the per-request filter applies permissions. **But this also means an over-broad cache (e.g., one cached per-user) wouldn't reflect permission changes until clear.** Don't try to "improve" the caching to per-user.
- **`menuTitle` is the lang-string key, not the translated text.** The Vue layer translates via `$t($item.title)`. Migrations that seed a literal English string like `'Widgets'` need a matching `general.widgets` (or whatever group) lang-string entry — see `frontend-platform`. Untranslated keys render as the literal key string to the user.
- **`MenuConfigurator` FQCN is stored as a string in `ohrm_screen.menu_configurator`.** Renaming the class breaks the screen until a migration updates the column. Refactor with care.
- **A `MenuConfigurator` returning `null` is fine** as long as it side-effects (e.g. `overrideScreen()`). A `null` return WITHOUT a side effect just means "I had nothing to say" and the default active-detection runs.
- **Level is denormalized** — it duplicates info derivable from the `parent` chain. Inconsistent `level` values (e.g. level=2 but parent is level=2 not level=1) confuse the menu service and may produce hidden items.
- **`additional_params` is a JSON column.** Don't write strings there — Doctrine will reject or double-encode. The migration `INSERT` uses `json_encode([...])`.
- **The Dashboard plugin's module-status-change subscribers** flip menu items' `status` when their owning module toggles. A "missing menu items after enabling/disabling a module" issue is usually one of those subscribers firing — check `BuzzModuleStatusChangeSubscriber`, `TimeModuleStatusChangeSubscriber`, etc.
