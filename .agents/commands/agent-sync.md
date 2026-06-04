---
description: Re-sync the generated agent-discovery bridges from .agents/ — the .claude/skills/ and .claude/commands/ copies (Windows; symlinked and auto-synced on Linux/macOS) and the .cursor/rules/ + .cursor/commands/ bridges (any OS, if Cursor is set up on this clone). Run after editing skills or commands.
---

You are running this because the user just edited one or more files under `.agents/skills/<name>/SKILL.md` or `.agents/commands/<name>.md`, and the generated discovery bridges need to reflect the changes. There are two kinds of bridge, both set up by `.agents/SETUP.md` at first clone and both gitignored:

- **`.claude/skills/` and `.claude/commands/`** — symlinks on Linux/macOS/WSL2 (auto-synced, nothing to do); **copies on Windows** (need re-copy after edits).
- **`.cursor/rules/` and `.cursor/commands/`** — generated pointer rules + command copies for Cursor, on any OS. Only present if Cursor setup was run on this clone.

## Step 1 — Check what kind of `.claude/skills/` and `.claude/commands/` exist

Run a check on each to find out whether it's a symlink, a directory of regular files, or missing:

```bash
ls -la .claude/skills 2>/dev/null
readlink .claude/skills 2>/dev/null
ls -la .claude/commands 2>/dev/null
readlink .claude/commands 2>/dev/null
```

For each of the two paths, the state is one of:

| State | Meaning | Action |
|---|---|---|
| `readlink` prints `../.agents/skills` (or `../.agents/commands`) | It's a symlink (Linux / macOS / WSL2 setup) | No-op for this path. |
| `ls` lists files but `readlink` is silent | It's a real directory of copies (Windows setup) | Sync this path (Step 2). |
| Both commands fail / directory missing | Setup never ran | Tell the user to run `.agents/SETUP.md` first. Exit. |

If **both** paths are symlinks, the `.claude/` side needs nothing — note that and skip ahead to Step 3 (Cursor). If **either** is a copy, do Step 2 first.

## Step 2 — Sync the copies (Windows path)

For each `.claude/` path that's a copy (not a symlink), use the **wipe-and-recopy** approach to avoid stale files / merge artifacts:

### Sync `.claude/skills/`

1. Delete the contents of `.claude/skills/` (but **not** the directory itself, and **not** any other `.claude/` paths like `commands/` or `settings.local.json`).
2. For each subdirectory in `.agents/skills/`, copy the entire directory (including `SKILL.md`) into `.claude/skills/`.

### Sync `.claude/commands/`

1. Delete the `.md` files in `.claude/commands/` (or wipe the directory contents).
2. Copy every `.md` file from `.agents/commands/` into `.claude/commands/`.

Don't use a blind `cp -r` — it doesn't handle deletions. Wipe-and-recopy is the more correct approach for both paths.

## Step 3 — Regenerate Cursor bridges (if present)

Check whether `.cursor/rules/` exists. If it doesn't, Cursor setup hasn't been run on this clone — skip this step entirely (don't create the directory; that's `.agents/SETUP.md` §3's job, opted into per clone).

If it exists, regenerate from `.agents/` with wipe-and-regenerate:

1. Delete `.cursor/rules/*.mdc`, then for each `.agents/skills/<name>/SKILL.md`, write `.cursor/rules/<name>.mdc` containing the skill's frontmatter `description:` line, `alwaysApply: false`, and the standard pointer body. The exact generation script lives in `.agents/SETUP.md` §3 — use it verbatim rather than improvising the format.
2. Delete `.cursor/commands/*.md`, then copy every `.agents/commands/*.md` into `.cursor/commands/`.

Note this regeneration matters only when a skill was added/removed/renamed, a frontmatter `description` changed, or a command changed — SKILL.md **body** edits flow through automatically because the rules are pointers Cursor follows at use time. Running it unconditionally is still safe and cheap.

## Step 4 — Verify

After the sync:

1. **Skills**: `ls .claude/skills/` count should match `ls .agents/skills/` excluding the `README.md` at `.agents/skills/README.md`.
2. **Commands**: `ls .claude/commands/` count should match `ls .agents/commands/`.
3. **Cursor (if Step 3 ran)**: `ls .cursor/rules/*.mdc` count should match the skill-directory count; `.cursor/commands/` should match `.agents/commands/`.
4. Spot-check one recently-edited file — open it under `.claude/` and confirm the content matches under `.agents/`.

## Step 5 — Report

Tell the user:
- How many skills are now in `.claude/skills/`
- How many commands are now in `.claude/commands/`
- Whether Cursor bridges were regenerated (and rule count), or skipped because `.cursor/rules/` doesn't exist
- Whether any files changed visibly (if you can detect)
- **Restart caveat**: if the edit was to a skill or command's frontmatter `description`, Claude Code's auto-load index may be cached until the next session restart. Plain body edits typically pick up immediately, but description changes may need a restart.

## When NOT to use this command

- **On Linux / macOS / WSL2 without Cursor**: both `.claude/` paths are symlinks and there's no `.cursor/rules/` — nothing to do. The command detects this and no-ops.
- **If `.claude/skills/` or `.claude/commands/` doesn't exist at all**: the user hasn't run `.agents/SETUP.md` yet — tell them to run that first instead of this.
- **For changes to `.agents/SETUP.md` itself, `AGENTS.md`, or `.agents/skills/README.md`**: this command only handles the `skills/` and `commands/` subdirectories. Those other files don't need syncing — Claude Code (or any other tool) reads them in place.

---

**Reminder:** the source of truth is always under `.agents/`. Don't edit files in `.claude/skills/`, `.claude/commands/`, or `.cursor/rules/` directly — those edits will be wiped on the next `/agent-sync`. If you find yourself doing that, you've drifted from the convention.
