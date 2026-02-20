# 🏢 COT HRM – Development, Branding & Deployment Guide

This repository is a customized fork of **OrangeHRM Open Source**, adapted for internal company use while preserving compatibility with future OrangeHRM updates, bug fixes, and security patches.

This document is **mandatory** for all contributors. Every developer must follow this workflow strictly.

---

## 🎯 Objectives

- Customize OrangeHRM safely for company usage  
- Preserve ability to sync future OrangeHRM updates  
- Avoid merge conflicts and technical debt  
- Maintain a clean CI/CD deployment pipeline  

---

## 🔁 Git Branching Model (Strict)

### Branch Responsibilities

- **main**
  - Mirrors the official OrangeHRM upstream
  - Receives updates only from upstream
  - Must never contain company-specific runtime customizations

- **develop**
  - Primary working branch for company HRM
  - All features and branding changes merge here
  - CI/CD deployments run from this branch

- **feature/***
  - One task or change per branch
  - Temporary branches
  - Always merged into develop

---

## 🚫 Non-Negotiable Rules

- Never commit directly to main  
- Never merge feature branches into main  
- Never bypass Pull Requests  
- Never hardcode credentials or secrets  
- Never modify OrangeHRM core logic without approval  
- Never pull from upstream unless authorized (Tech Lead only)  

Violations will result in PR rejection.

---

## 🟢 Daily Developer Workflow

### Step 1: Sync with develop

```
git checkout develop
git pull origin develop
```

---

### Step 2: Create a Feature Branch

Branch naming format:
```
feature/<task-name>
```

Examples:
```
feature/branding
feature/logo-update
feature/theme-colors
```

Create branch:
```
git checkout -b feature/branding
```

---

### Step 3: Scope of Branding Changes

Allowed:
- Logo replacement
- Product name text updates
- Theme and color adjustments
- Footer content updates (must retain “Powered by OrangeHRM”)

Not Allowed:
- Core business logic changes
- Database schema modifications
- Authentication or authorization logic changes
- Renaming OrangeHRM core classes or files

---

### Step 4: Commit Guidelines

- Keep commits small and meaningful
- Use clear and descriptive commit messages

Example:
```
git add .
git commit -m "Branding: replace logos and update product name"
```

Avoid:
- wip
- temp
- fix later

---

### Step 5: Push Feature Branch

```
git push origin feature/branding
```

---

### Step 6: Create Pull Request (PR)

- Source branch: feature/branding  
- Target branch: develop  

PR description must include:
- What was changed
- Screens or modules affected
- Any potential risks

Pull Requests targeting main will be closed immediately.

---

### Step 7: Merge and Cleanup (After Approval)

```
git checkout develop
git merge feature/branding
git push origin develop
```

Delete feature branch:
```
git branch -d feature/branding
git push origin --delete feature/branding
```

---

## 🔄 Upstream Sync Process (Tech Lead Only)

Only designated Tech Leads may perform upstream sync.

```
git checkout main
git fetch upstream
git merge upstream/main
git push origin main
```

Merge upstream updates into develop:
```
git checkout develop
git merge main
git push origin develop
```

Developers must never pull directly from upstream.

---

## 🚀 Deployment Policy

- CI/CD pipelines deploy only from develop  
- main is never deployed  
- Production always reflects the latest stable state of develop  

---

## 🧠 Mental Model (Remember This)

Feature work flow:
```
feature/* → develop → Deploy
```

Upstream update flow:
```
OrangeHRM upstream → main → develop
```

---

## 🧩 Best Practices

- One task per feature branch
- One clear purpose per Pull Request
- Keep main clean forever
- Ask before making architectural or structural changes

---

## 📞 Questions & Clarifications

If unsure at any point:
- Stop working
- Ask the Tech Lead
- Do not assume or guess

---

## ✅ Developer Acknowledgement

All contributors must read, understand, and follow this guide before starting any work on this repository.
