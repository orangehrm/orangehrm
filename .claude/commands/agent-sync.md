---
description: Re-sync .claude/skills/ from .agents/skills/. Run after editing skills on Windows (where .claude/skills is a copy, not a symlink). On Linux/macOS this is a no-op since .claude/skills is symlinked.
---

You are running this because the user just edited one or more skill files under `.agents/skills/<name>/SKILL.md` and needs Claude Code's `.claude/skills/` to reflect the changes. This command exists because **on Windows, `.claude/skills/` is a copy** of `.agents/skills/` (set up by `.agents/SETUP.md` at first clone), and the copy doesn't update automatically.

## Step 1 — Check what kind of `.claude/skills/` exists

Run a check to find out whether `.claude/skills/` is a symlink, a directory of regular files, or missing entirely:

```bash
ls -la .claude/skills 2>/dev/null
readlink .claude/skills 2>/dev/null
```

Three possible states:

| State | Meaning | Action |
|---|---|---|
| `readlink` prints `../.agents/skills` | It's a symlink (Linux / macOS / WSL2 setup) | **No-op — tell the user "already synced via symlink, nothing to do."** Exit. |
| `ls` lists subdirectories but `readlink` is silent | It's a real directory with copied files (Windows setup) | Proceed to Step 2. |
| Both commands fail / directory missing | Setup never ran | Tell the user to run `.agents/SETUP.md` first. Exit. |

## Step 2 — Identify what needs syncing (Windows path)

Compare `.agents/skills/` (source of truth) with `.claude/skills/` (the copy). Look for:

- **New skill directories** in `.agents/skills/` that don't exist in `.claude/skills/` yet (added by editing or via `git pull`)
- **Modified `SKILL.md` files** where `.agents/skills/<name>/SKILL.md` differs from `.claude/skills/<name>/SKILL.md`
- **Stale directories** in `.claude/skills/` that no longer exist in `.agents/skills/` (a skill was removed)

You can do this with a directory walk + content comparison, or just by re-copying everything and pruning what's gone.

## Step 3 — Sync

The simplest reliable approach: **wipe `.claude/skills/` and re-copy from `.agents/skills/`.** Skills are small files, the copy is fast, and there's no risk of merge artifacts.

Use your file tools (not necessarily shell — pick what's reliable on Windows):

1. Delete `.claude/skills/` entirely (but **only** the `skills` subdir — leave `.claude/commands/`, `.claude/settings.local.json`, etc. alone).
2. Re-create `.claude/skills/` as an empty directory.
3. For each subdirectory in `.agents/skills/`, copy the entire directory (including `SKILL.md`) into `.claude/skills/`.

Don't use the bash command line `cp -r .agents/skills/* .claude/skills/` blindly — that doesn't handle deletions. The wipe-and-recopy approach is more correct.

## Step 4 — Verify

After the sync:

1. List the resulting directories: `ls .claude/skills/`. Count should match `ls .agents/skills/` excluding the `README.md` file (which is at `.agents/skills/README.md`, not `.agents/skills/<name>/`).
2. Spot-check one of the recently-edited skills — open `.claude/skills/<name>/SKILL.md` and confirm the content matches `.agents/skills/<name>/SKILL.md`.
3. Report to the user:
   - How many skills are now in `.claude/skills/`
   - Which skill files looked different from the previous state (if you can detect)
   - Confirm Claude Code will pick up the changes — but **note** that the skill descriptions in Claude Code's auto-load index may be cached until the next session restart. If the user just edited frontmatter (`description`), they may need to restart Claude Code to see the new trigger behavior.

## When NOT to use this command

- **On Linux / macOS / WSL2**: `.claude/skills/` is a symlink — edits to `.agents/skills/` are already visible. The command will detect this and no-op.
- **If `.claude/skills/` doesn't exist at all**: the user hasn't run `.agents/SETUP.md` yet — tell them to run that first instead of this.
- **For changes to `.agents/SETUP.md` itself, or `AGENTS.md`, or any other non-skill file**: this command only handles `.claude/skills/`. Those files don't need syncing — Claude Code reads them in place.

---

**Reminder:** the source of truth is always `.agents/skills/<name>/SKILL.md`. Don't edit `.claude/skills/<name>/SKILL.md` directly — those edits will be wiped on the next `/agent-sync`. If you find yourself doing that, you've drifted from the convention.
