# Authoring skills & commands for all coding agents

Rules for any AI coding agent (or human) **generating or updating** the skills and slash
commands under `.agents/`. The goal is one source of truth that reads and behaves identically across every coding agent a developer might bring to this repo — Claude Code, Cursor, Codex, Google Antigravity, GitHub Copilot, Gemini CLI / Jules, Windsurf, Aider, Cline, and any other `AGENTS.md`-aware tool.

If you are about to add, edit, rename, or delete a skill or command, read this first. The companion docs are [`SETUP.md`](SETUP.md) (how each tool bridges to `.agents/`) and [`skills/README.md`](skills/README.md) (the catalog of available skills). This file is the single source of truth for *how* to author; the README is the index of *what* exists.

---

## Why this file exists

The substantive content lives in **tool-neutral locations** (`AGENTS.md`,
`.agents/skills/<name>/SKILL.md`, `.agents/commands/<name>.md`). Each tool then *discovers* that
content differently. If a skill is written assuming one tool's features, it silently breaks for
every other tool. These rules keep skills portable.

### How the target agents discover skills

Authors must write for the **lowest common denominator** of these three discovery models:

| Discovery model | Tools (examples) | What it means for you |
|---|---|---|
| **Auto-load by `description`** | Claude Code (`.claude/skills/`), Cursor ("Agent Requested" rules) | The `description` frontmatter is the *only* thing the tool sees until the skill fires. It must be self-contained and trigger-rich. |
| **Read `AGENTS.md` + catalog, load on demand** | Codex, Gemini CLI, Aider, most `AGENTS.md`-aware tools | No auto-trigger. The agent finds skills via `skills/README.md` and reads the `SKILL.md` when a task matches. The catalog must stay accurate. |
| **Generated bridge / pointer rules** | Claude Code (symlink/copy), Cursor (`.cursor/rules/*.mdc`) | The bridge is generated *from* your frontmatter `description`. Body edits flow through; structural changes need a re-sync. |

Google Antigravity, GitHub Copilot, Windsurf, Cline, and similar tools fall into one of the above depending on their `AGENTS.md` / rules support — none of them change the rules below.

---

## Golden rules

1. **Source of truth is `.agents/`.** Edit `.agents/skills/<name>/SKILL.md` and `.agents/commands/<name>.md` only. Never edit the generated bridges under `.claude/skills/`, `.claude/commands/`, or `.cursor/rules/` — they are regenerated and your edit will be lost.

2. **Write tool-neutral prose.** A skill body must not assume *which* agent is reading it. See [Tool-neutral bodies](#tool-neutral-bodies) for the do/don't list. If you catch yourself writing "the Skill tool", "this slash command", or "your `.claude` folder" in a skill body, stop — that's tool-specific.

3. **The `description` is the trigger, not a summary.** For auto-loading tools it is the entire basis for relevance. Pack it with the tasks, symbols, file paths, and synonyms that should pull the skill in. See [Frontmatter contract](#frontmatter-contract).

4. **Bare kebab-case names, no vendor prefix.** `rest-endpoints`, not `ohrm-rest-endpoints` or `orangehrm_rest_endpoints`. Skills are already scoped by living under `.agents/`. The `name` frontmatter must equal the directory name.

5. **Reference files by repo-relative path.** `src/plugins/orangehrmPimPlugin/Api/` works in every tool. Absolute paths, `~`, or tool-specific path variables do not. Cross-link sibling skills by their bare `name` (e.g. "see the `daos` skill"), not by a bridge path.

6. **Keep code-derived facts out.** Teach agents *where to look*, don't hardcode versions, route tables, service IDs, or class lists that the code owns. See [Keep code-derived facts out](#keep-code-derived-facts-out).

7. **Match the existing shape.** New skills mirror an existing one of similar size; edits keep the established structure (see [File structure](#file-structure)).

8. **Update the catalog and re-sync when the set changes.** Adding, removing, or renaming a skill — or changing a `description` — means updating [`skills/README.md`](skills/README.md) and regenerating the bridges (`/agent-sync`). On-demand tools rely on the catalog; bridged tools rely on the regenerated pointers.

---

## Frontmatter contract

Every `SKILL.md` starts with exactly this YAML, then a blank line, then the body:

```yaml
---
name: <bare-kebab-case, equals the directory name>
description: <one line — the trigger blurb (see below)>
---
```

- **`name`** — the skill ID. Must match the directory: `.agents/skills/<name>/SKILL.md`.
- **`description`** — a single line (no line breaks; long is fine — existing skills run several
  sentences). Treat it as the *retrieval query* an auto-loading tool matches against. A good
  description states:
  - **what the skill covers** (the subsystem, the key classes/files/patterns), and
  - **when to load it** — the concrete tasks, questions, and synonyms a developer would phrase,
    plus pointers to companion skills.

  Look at [`services/SKILL.md`](skills/services/SKILL.md) or [`migrations/SKILL.md`](skills/migrations/SKILL.md) for the bar to hit — they name the files, the trigger tasks ("adding a service method", "writing a new migration"), and the neighbours.

Do **not** add other frontmatter keys (`alwaysApply`, `globs`, `model`, etc.). Those are tool-specific and are injected by the bridge generators (`SETUP.md`), not stored in the source.

---

## Tool-neutral bodies

The body is read verbatim by humans and by every agent. Keep it about *the OrangeHRM codebase*, not about *the agent reading it*.

**Don't:**
- Name a specific tool's machinery — "the Skill tool", "use `/agent-sync`", "your `.claude/` skills folder", "Cursor will…", "in Claude Code you can…".
- Assume an invocation mechanism — "when you type this slash command", "after this skill
  auto-loads". Other tools load it differently or on demand.
- Hardcode the skill's own discovery path or count of skills (those drift).
- Address one tool's UI or keybindings.

**Do:**
- Describe the code, the conventions, the recipes, and the gotchas in tool-agnostic terms.
- Cross-reference other skills by bare `name`: "see the `authorization` skill".
- Point to owning files for facts the code controls.
- Write so a developer with *no* agent could follow it by hand.

> Slash commands (`.agents/commands/*.md`) are the one place a more procedural, "do this then
> that" voice is expected — because some tools run the command body as a literal prompt. Even
> there, write the body so it works as a plain instruction to *any* agent, not just the tool with
> a `/command` UI.

---

## File structure

Each `<name>/SKILL.md` follows this shape:

1. **YAML frontmatter** — `name` + `description`.
2. **Substantive sections** — the topic, with code examples.
3. **Recipes** — concrete copy-and-adapt patterns for common tasks.
4. **Checklists** — what to verify when doing those tasks.
5. **Things that bite** — the gotchas worth flagging.

Keep this shape when editing; mirror a similar-sized existing skill when creating.

---

## Keep code-derived facts out

Skills should teach agents **where to look** for facts owned by code or package metadata, not
duplicate those facts as static prose — that's how guidance goes stale when the codebase changes.

Examples:

- PHP support / Composer constraints: read the relevant `composer.json` (`src/composer.json`,
  `devTools/core/composer.json`) instead of hardcoding versions in a skill.
- Composer platform settings: read the relevant `composer.json` `config.platform` section instead
  of copying the value.
- Frontend package manager / Node policy: read the relevant `package.json` (`packageManager`,
  `engines`, scripts) instead of hardcoding versions.
- Routes, service IDs, class names, entity mappings, permission names, and migration registries:
  point agents to the owning files or an established skill recipe, then have them inspect the
  current code.

It is fine for a skill to include examples, but label them as examples — they should not claim to
be the current source of truth unless the skill points to the file that owns that truth.

---

## Authoring slash commands

Commands live in [`commands/`](commands/) as `<name>.md` with `name` + `description` frontmatter and a body that *is* the prompt the agent runs. Compatibility rules:

- Claude Code and Cursor expose them as `/<name>`; **other tools have no slash UI** and run the body as a pasted prompt. Write the body to stand on its own either way.
- Reference repo-relative paths and bare skill names, same as skills.
- After adding/removing/renaming a command, re-sync the bridges (`/agent-sync`).

---

## Before you commit — checklist

- [ ] Edited only under `.agents/` (not the `.claude/` or `.cursor/` bridges).
- [ ] `name` frontmatter matches the directory name; bare kebab-case, no vendor prefix.
- [ ] `description` is a single self-contained line, rich in trigger tasks/synonyms.
- [ ] No tool-specific machinery named in the body (grep for `Skill tool`, `.claude`, `.cursor`,
      `slash command`, `auto-load`).
- [ ] File paths are repo-relative; sibling skills referenced by bare `name`.
- [ ] No code-derived facts hardcoded that should be read from the owning file.
- [ ] Structure follows the 5-part shape.
- [ ] If a skill/command was **added, removed, renamed, or its `description` changed** —
      [`skills/README.md`](skills/README.md) catalog updated **and** bridges re-synced
      (`/agent-sync`, or per `SETUP.md` for the host tool/OS).
- [ ] Ran the project's style/lint expectations for any code in examples (they should compile /
      match real APIs).

---

## Things that bite

- **Editing the bridge instead of the source.** A change in `.claude/skills/<name>/SKILL.md` that is a symlink edits the source (fine), but if it's a Windows *copy* you've edited a throwaway — it's overwritten on the next sync. Always edit under `.agents/`.
- **A great body with a weak `description`.** Auto-loading tools never reach the body if the description doesn't match the task. The description is where triggering is won or lost.
- **Renaming a skill but not the catalog.** On-demand tools (Codex, Gemini CLI, …) discover skills through `skills/README.md`; a rename that skips the catalog makes the skill invisible to them even after a bridge re-sync.
- **Tool-specific phrasing leaking into a body.** "Run `/agent-sync`" reads fine in Claude Code and confuses every tool without that command. Keep procedural, tool-specific steps in `SETUP.md` / commands, not in skill bodies.
- **Adding frontmatter keys.** Extra keys like `globs` or `alwaysApply` belong to a tool's bridge format and are generated by `SETUP.md`, not stored in the source `SKILL.md`.

---

## When the agent landscape changes

If the team adopts a new tool or one of these tools changes how it discovers skills, update the [discovery table](#how-the-target-agents-discover-skills) here and the corresponding section in [`SETUP.md`](SETUP.md). The rules themselves (tool-neutral bodies, trigger-rich descriptions, source-of-truth under `.agents/`) should hold regardless of which tools are in play.
