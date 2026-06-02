# Coding-agent setup for this repository

This repository ships project-level documentation tuned for AI coding agents (architecture skills, conventions, onboarding workflows). The substantive content lives in tool-neutral locations:

- **`AGENTS.md`** at the repo root — the project's primary instruction document. Tools that recognize the `AGENTS.md` convention (Cursor, Codex, others) discover it automatically; Claude Code reads it via the `CLAUDE.md → @AGENTS.md` import shim.
- **`.agents/skills/<name>/SKILL.md`** — the skill files. Each is a markdown document with YAML frontmatter describing when it applies.

Different agents discover skills in different places, so this file is the **one-time setup script**: a coding agent reads it on first contact with the repo, identifies itself + the host OS, and configures its own discovery path accordingly.

---

## How to use this file (instructions for the agent)

If you are a coding agent reading this file, follow the decision tree below. **Don't ask the user to do the steps — do them yourself with your file tools.** The user should only need to type one prompt:

> "Please follow `.agents/SETUP.md` to set yourself up for this project."

After setup completes, report back what you did and verify the skills are discoverable.

---

## Step 1 — Identify yourself

| You are | Go to |
|---|---|
| **Claude Code** (this file was loaded by `claude` CLI / Claude Code IDE extension / claude.ai/code) | [§2 Claude Code](#-2-claude-code-setup) |
| **Cursor** | [§3 Cursor](#-3-cursor-setup) |
| **Codex / OpenAI agent** | [§4 Generic AGENTS.md tools](#-4-generic-agentsmd-tools) |
| **Other / not sure** | Ask the user: *"I'm reading `.agents/SETUP.md` to set myself up. Which coding agent am I? Claude Code, Cursor, Codex, or something else?"* Then re-read this file under the right section. |

---

## § 2 Claude Code setup

Claude Code only auto-discovers skills under `.claude/skills/`. Since this project's source of truth is `.agents/skills/`, you need to create a local bridge. **The bridge is gitignored, so each developer runs this setup once per clone.**

### Detect the OS

Run one of:

```bash
uname -s    # → Linux / Darwin (macOS) / MINGW…* or MSYS…* (Git Bash on Windows)
```

```powershell
$PSVersionTable.OS    # → "Microsoft Windows…" on PowerShell
```

If `uname` succeeds and reports `Linux` or `Darwin` (or you're in WSL2), proceed to **[§2a Symlink](#-2a-linux--macos--wsl2--symlink)**.
If you're on Windows natively (no WSL, native PowerShell / cmd), proceed to **[§2b Copy](#-2b-windows-native--copy)**.

### § 2a Linux / macOS / WSL2 — symlink

One-time setup. The symlink lives outside git (gitignored), so each clone needs it.

```bash
cd <repo root>
mkdir -p .claude
ln -s ../.agents/skills .claude/skills
```

Verify:

```bash
ls .claude/skills/services/SKILL.md     # should print the path, not "no such file"
readlink .claude/skills                  # should print "../.agents/skills"
```

Tell the user setup is complete and that **skills will stay in sync automatically** — edits to `.agents/skills/<name>/SKILL.md` are seen by Claude Code via the symlink with no further action.

### § 2b Windows native — copy

Symlinks on Windows require admin rights / developer mode and break across drives. The fallback is to **copy** `.agents/skills/` into `.claude/skills/` once at setup, then re-copy after any edit.

Do the copy now (you, the agent, with your file tools — don't ask the user to run shell commands unless that's the only path):

1. Ensure `.claude/skills/` exists (create if missing).
2. For each subdirectory in `.agents/skills/`, copy the entire directory (containing `SKILL.md`) into `.claude/skills/`. After this, `.claude/skills/services/SKILL.md` etc. should exist and be byte-identical to the source under `.agents/skills/`.

Verify a few of them:

```
.claude/skills/services/SKILL.md
.claude/skills/rest-endpoints/SKILL.md
.claude/skills/migrations/SKILL.md
```

Tell the user setup is complete and **flag the sync caveat**: any edit to `.agents/skills/<name>/SKILL.md` won't be reflected in Claude Code until they re-run sync. The easiest way to re-sync is the project's `/agent-sync` slash command — type it in Claude Code after editing skills.

---

## § 3 Cursor setup

> **To be added when the team adopts Cursor.** Likely path: write `.cursor/rules/<name>.mdc` files that reference the substantive content in `.agents/skills/<name>/SKILL.md`, or copy/transform the content into Cursor's rule format. Cursor's discovery model differs from Claude Code's "auto-load by description" — Cursor rules are always-on or globbed by file path. The trigger metadata from each SKILL.md's frontmatter doesn't translate directly; the rules need to be authored separately.

If the team is adopting Cursor now and this section hasn't been written, ask the user how they'd like to proceed.

---

## § 4 Generic AGENTS.md tools

You've likely already read `AGENTS.md` from the repo root — that's the project's primary instruction document. The "skills" referenced there are markdown files under `.agents/skills/<name>/SKILL.md`. Each file has YAML frontmatter (`name`, `description`) describing when it's relevant.

There's nothing for you to *install* — the content is already accessible. Recommended pattern for use:

1. **Read `.agents/skills/README.md`** for the catalog of available skills with one-line descriptions.
2. **When working on a task** that matches a skill's `description`, read that skill's full `SKILL.md` content before proceeding.

If your tool supports loading these as "rules" or "context" persistently, consult its documentation for how to point it at `.agents/skills/`.

---

## After setup

Run these checks (you, the agent):

1. **List skills**: `ls .claude/skills/` (for Claude Code) or `ls .agents/skills/` (always). Should show ~25 directories.
2. **Read a known skill**: open one of them (e.g. `services/SKILL.md`) and confirm the YAML frontmatter is intact.
3. **Confirm the doc shim**: open the root `CLAUDE.md`. It should contain `@AGENTS.md` (and nothing else).
4. **Report to the user**: "Setup complete — I see N skills under `<path>`. The project's main instructions are in `AGENTS.md`."

If any check fails, **stop and tell the user** rather than continuing silently.

---

## Editing skills after setup

The source of truth is **always `.agents/skills/<name>/SKILL.md`** — edit there, not in `.claude/skills/` (where it might be a symlink or a copy).

- **Linux / macOS / WSL2**: symlink keeps everything in sync automatically. No action needed after edit.
- **Windows native**: run `/agent-sync` in Claude Code (or manually copy `.agents/skills/*` → `.claude/skills/*`) so Claude Code sees the updated content.

---

## When this file changes

If the team adopts a new tool (e.g. Cursor) or the layout shifts, update this file. Agents read it once per clone, so changes here change how new clones bootstrap. Existing clones may need re-setup.
