# Coding-agent setup for this repository

This repository ships project-level documentation tuned for AI coding agents (architecture skills, conventions, onboarding workflows). The substantive content lives in tool-neutral locations:

- **`AGENTS.md`** at the repo root — the project's primary instruction document. Tools that recognize the `AGENTS.md` convention (Cursor, Codex, others) discover it automatically; Claude Code reads it via the `CLAUDE.md → @AGENTS.md` import shim.
- **`.agents/skills/<name>/SKILL.md`** — the skill files. Each is a markdown document with YAML frontmatter describing when it applies.
- **`.agents/commands/<name>.md`** — slash commands. Markdown files with YAML frontmatter; the body is the prompt the agent runs when the command is invoked.
- **`.agents/AUTHORING.md`** — the rules for *writing or updating* skills and commands (tool-neutral bodies, frontmatter, naming, structure). This file is about *discovering/bridging* them; `AUTHORING.md` is about authoring their content.

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

Claude Code only auto-discovers skills under `.claude/skills/` and slash commands under `.claude/commands/`. Since this project's source of truth is `.agents/skills/` and `.agents/commands/`, you need to create local bridges. **The bridges are gitignored, so each developer runs this setup once per clone.**

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

One-time setup. The symlinks live outside git (gitignored), so each clone needs them.

```bash
cd <repo root>
mkdir -p .claude
ln -s ../.agents/skills   .claude/skills
ln -s ../.agents/commands .claude/commands
```

Verify:

```bash
ls .claude/skills/services/SKILL.md       # should print the path, not "no such file"
ls .claude/commands/agent-sync.md         # should print the path
readlink .claude/skills                    # should print "../.agents/skills"
readlink .claude/commands                  # should print "../.agents/commands"
```

Tell the user setup is complete and that **skills and commands stay in sync automatically** — edits to `.agents/skills/<name>/SKILL.md` or `.agents/commands/<name>.md` are seen by Claude Code via the symlinks with no further action.

### § 2b Windows native — copy

Symlinks on Windows require admin rights / developer mode and break across drives. The fallback is to **copy** both `.agents/skills/` and `.agents/commands/` into the matching `.claude/` paths once at setup, then re-copy after any edit.

Do the copies now (you, the agent, with your file tools — don't ask the user to run shell commands unless that's the only path):

1. Ensure `.claude/skills/` and `.claude/commands/` both exist (create if missing).
2. For each subdirectory in `.agents/skills/`, copy the entire directory (containing `SKILL.md`) into `.claude/skills/`. After this, `.claude/skills/services/SKILL.md` etc. should exist and be byte-identical to the source under `.agents/skills/`.
3. For each `.md` file in `.agents/commands/`, copy it into `.claude/commands/`. After this, `.claude/commands/agent-sync.md` and `.claude/commands/onboard.md` should both exist.

Verify a few of them:

```
.claude/skills/services/SKILL.md
.claude/skills/rest-endpoints/SKILL.md
.claude/skills/migrations/SKILL.md
.claude/commands/agent-sync.md
.claude/commands/onboard.md
```

Tell the user setup is complete and **flag the sync caveat**: any edit to `.agents/skills/<name>/SKILL.md` or `.agents/commands/<name>.md` won't be reflected in Claude Code until they re-run sync. The easiest way to re-sync is the project's `/agent-sync` slash command — type it in Claude Code after editing.

---

## § 3 Cursor setup

Cursor discovers project rules under `.cursor/rules/*.mdc` and custom slash commands under `.cursor/commands/*.md`. Both are **generated bridges** (gitignored, like the `.claude/` bridges) — run this setup once per clone, then re-run `/agent-sync` after edits under `.agents/` (see "Keeping in sync" below for when that's actually needed).

The rules are **thin pointers**, not content copies: each `.mdc` carries the skill's `description` from the SKILL.md frontmatter (with `alwaysApply: false`, so Cursor's "Agent Requested" mechanism decides relevance from the description — mirroring Claude Code's auto-load semantics) plus a body instructing the agent to read the real `.agents/skills/<name>/SKILL.md` before proceeding. The SKILL.md stays the single source of truth.

### Generate the bridges

If you have a shell available, run from the repo root:

```bash
mkdir -p .cursor/rules .cursor/commands
rm -f .cursor/rules/*.mdc
for d in .agents/skills/*/; do
  name=$(basename "$d")
  [ -f "${d}SKILL.md" ] || continue
  desc=$(awk '/^description:/{sub(/^description: */,""); print; exit}' "${d}SKILL.md")
  {
    printf -- '---\ndescription: %s\nalwaysApply: false\n---\n\n' "$desc"
    printf '# %s (OrangeHRM skill pointer)\n\n' "$name"
    printf 'This rule is a pointer, not the content. Before doing the task, read the full skill document at:\n\n'
    printf '`.agents/skills/%s/SKILL.md`\n\n' "$name"
    printf 'That file is the source of truth for this topic. Apply its conventions to your changes.\n'
  } > ".cursor/rules/${name}.mdc"
done
rm -f .cursor/commands/*.md
cp .agents/commands/*.md .cursor/commands/
```

No shell (or it fails)? Do the equivalent with your file tools: for each `.agents/skills/<name>/SKILL.md`, read the single-line `description:` from the frontmatter and write `.cursor/rules/<name>.mdc` with that description, `alwaysApply: false`, and the pointer body shown above; then copy every `.agents/commands/*.md` into `.cursor/commands/`.

### Verify

1. `ls .cursor/rules/*.mdc` — count must match the skill directories under `.agents/skills/` (`README.md` is not a skill).
2. Open one rule (e.g. `.cursor/rules/services.mdc`) — the description should match the source SKILL.md frontmatter and the body should point at the right path.
3. `.cursor/commands/` should contain the same `.md` files as `.agents/commands/`.

### Keeping in sync

Because the rules are pointers, **SKILL.md body edits flow through automatically** — Cursor reads the pointed-to file at use time. Regeneration is only needed when a skill is added, removed, or renamed, or when a frontmatter `description` changes, or when a command file changes. The `/agent-sync` command handles `.cursor/` too (it detects the directory), so running it after any `.agents/` edit is always safe.

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

1. **List skills**: `ls .claude/skills/` (for Claude Code) or `ls .agents/skills/` (always). The count should match the skill directories under `.agents/skills/`.
2. **List commands**: `ls .claude/commands/` should show `agent-sync.md` and `onboard.md` (and whatever else the project added since).
3. **Read a known skill**: open one of them (e.g. `services/SKILL.md`) and confirm the YAML frontmatter is intact.
4. **Confirm the doc shim**: open the root `CLAUDE.md`. It should contain `@AGENTS.md` (and nothing else).
5. **Report to the user and point to the next step**: "Setup complete — I see N skills and M commands under `.claude/`. The project's main instructions are in `AGENTS.md`. **Start a fresh session so the skills and commands register**, then run `/onboard` to stand up your Docker dev environment. And if you need links to the project's external resources — source repos, docs, downloads, the demo site, mobile apps — just ask; I'll pull them up."

If any check fails, **stop and tell the user** rather than continuing silently.

---

## Editing skills or commands after setup

> For *how* to write skill/command content (tool-neutral bodies, frontmatter, naming, structure),
> see [`AUTHORING.md`](AUTHORING.md). This section only covers **re-syncing the bridges** once
> you've edited the source.

Edit the source **always under `.agents/`** — `.agents/skills/<name>/SKILL.md` or `.agents/commands/<name>.md`, not the matching files under `.claude/` or `.cursor/` (where they might be a symlink or a copy). Then re-sync per your tool:

- **Claude Code on Linux / macOS / WSL2**: symlinks keep everything in sync automatically. No action needed after edit.
- **Claude Code on Windows native**: run `/agent-sync` (or manually copy `.agents/skills/*` → `.claude/skills/*` and `.agents/commands/*` → `.claude/commands/*`) so Claude Code sees the updated content.
- **Cursor (any OS)**: SKILL.md body edits need nothing (rules are pointers). Run `/agent-sync` after adding/removing/renaming a skill, changing a frontmatter `description`, or editing a command.

---

## When this file changes

If the team adopts a new tool (e.g. Windsurf, Zed) or the layout shifts, update this file. Agents read it once per clone, so changes here change how new clones bootstrap. Existing clones may need re-setup.
