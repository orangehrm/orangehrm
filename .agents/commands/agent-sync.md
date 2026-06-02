---
description: Re-sync .claude/skills/ and .claude/commands/ from .agents/. Run after editing skills or commands on Windows (where the .claude/ subdirs are copies, not symlinks). On Linux/macOS this is a no-op since both are symlinked.
---

You are running this because the user just edited one or more files under `.agents/skills/<name>/SKILL.md` or `.agents/commands/<name>.md`, and Claude Code's `.claude/skills/` and `.claude/commands/` need to reflect the changes. This command exists because **on Windows, those `.claude/` directories are copies** of the `.agents/` source (set up by `.agents/SETUP.md` at first clone), and the copies don't update automatically.

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

If **both** paths are symlinks, tell the user "already synced via symlinks, nothing to do" and exit. If **either** is a copy, proceed.

## Step 2 — Sync the copies (Windows path)

For each `.claude/` path that's a copy (not a symlink), use the **wipe-and-recopy** approach to avoid stale files / merge artifacts:

### Sync `.claude/skills/`

1. Delete the contents of `.claude/skills/` (but **not** the directory itself, and **not** any other `.claude/` paths like `commands/` or `settings.local.json`).
2. For each subdirectory in `.agents/skills/`, copy the entire directory (including `SKILL.md`) into `.claude/skills/`.

### Sync `.claude/commands/`

1. Delete the `.md` files in `.claude/commands/` (or wipe the directory contents).
2. Copy every `.md` file from `.agents/commands/` into `.claude/commands/`.

Don't use a blind `cp -r` — it doesn't handle deletions. Wipe-and-recopy is the more correct approach for both paths.

## Step 3 — Verify

After the sync:

1. **Skills**: `ls .claude/skills/` count should match `ls .agents/skills/` excluding the `README.md` at `.agents/skills/README.md`.
2. **Commands**: `ls .claude/commands/` count should match `ls .agents/commands/`.
3. Spot-check one recently-edited file — open it under `.claude/` and confirm the content matches under `.agents/`.

## Step 4 — Report

Tell the user:
- How many skills are now in `.claude/skills/`
- How many commands are now in `.claude/commands/`
- Whether any files changed visibly (if you can detect)
- **Restart caveat**: if the edit was to a skill or command's frontmatter `description`, Claude Code's auto-load index may be cached until the next session restart. Plain body edits typically pick up immediately, but description changes may need a restart.

## When NOT to use this command

- **On Linux / macOS / WSL2**: both `.claude/skills/` and `.claude/commands/` are symlinks — edits to `.agents/` are already visible. The command detects this and no-ops.
- **If `.claude/skills/` or `.claude/commands/` doesn't exist at all**: the user hasn't run `.agents/SETUP.md` yet — tell them to run that first instead of this.
- **For changes to `.agents/SETUP.md` itself, `AGENTS.md`, or `.agents/skills/README.md`**: this command only handles the `skills/` and `commands/` subdirectories. Those other files don't need syncing — Claude Code (or any other tool) reads them in place.

---

**Reminder:** the source of truth is always under `.agents/`. Don't edit files in `.claude/skills/` or `.claude/commands/` directly — those edits will be wiped on the next `/agent-sync`. If you find yourself doing that, you've drifted from the convention.
