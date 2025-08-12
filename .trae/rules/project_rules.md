# CODING_AGENT_HANDBOOK

---

## 📌 Compact Rules (Quick Reference)

### Core References
- `PRD.md` — Product Requirements
- `ERD.md` — Entity Relationships
- `CODING_AGENT_HANDBOOK.md` — Full rules

### Roles
- **ARCHITECT:** Make `project_master_plan.md` + `phase_{n}.md` from templates. Objectives, success criteria, and test plan from PRD/ERD.
- **EXECUTE:** Follow phase doc, use TDD, Filament MCP Context7, log filenames, update Execution Log & master plan.
- **UPDATE:** Adjust phase doc with results & changes, sync master plan progress.
- **CHECK:** Verify success criteria met, run tests. Pass → mark completed; Fail → log Failure Analysis, mark failed.
- **DEBUG:** Trace bugs to phase, find root cause (test gap, PRD/ERD issue, deviation), log in phase Debug Log + master plan tracker.
- **REPAIR:** Fix DEBUG issues using TDD without skipping documentation.

### Templates
- **project_master_plan.md:** Table of phases (status), Debug Tracker.
- **phase_{n}.md:** Objectives, success criteria, tasks, tests, execution log, failure analysis, debug log.

### Commands
- **ARCHITECT INIT:** Create master plan.
- **ARCHITECT PHASE:** Create new phase doc.
- **EXECUTE:** Verify PRD/ERD alignment → Write/Update tests → Implement in small steps → Log results.
- **UPDATE:** Sync docs with latest state.
- **CHECK:** Validate, run tests, update status.
- **DEBUG:** Identify phase → Analyze → Log → Suggest fix/schedule.
- **REPAIR:** Apply fix following EXECUTE rules.

### Principles
1. Always TDD.
2. Keep PRD/ERD current.
3. Log filenames for traceability.
4. Never skip CHECK.
5. All DEBUG findings must be documented.
6. For Filament resources, **always use Filament generators**, never create controllers/routes manually unless explicitly stated.

---

## 📖 Full Handbook

### 1. ARCHITECT
**Goal:** Plan the project in structured phases.

**Actions:**
1. Create `project_master_plan.md` using the **Master Plan Template**.
2. Create `phase_{number}.md` files using the **Phase Template**.
3. Ensure **Objectives** and **Success Criteria** are based on `PRD.md` and `ERD.md`.
4. Define **test cases** for each success criterion.

---

### 2. EXECUTE
**Goal:** Implement tasks defined in `phase_{number}.md` using **Test Driven Development**.

**Pre-Execution Verification (Mandatory)**
- [ ] All related `ERD.md` tables, fields, and relationships identified
- [ ] All related `PRD.md` requirements mapped to technical tasks
- [ ] No conflicting constraints between PRD and ERD
- [ ] Tests written based on PRD success criteria
- [ ] For any Filament feature, review and follow the official Filament testing guide: https://filamentphp.com/docs/3.x/panels/testing
- [ ] For any Livewire feature, review and follow the official Livewire testing guide: https://livewire.laravel.com/docs/testing

**Execution Rules**
1. Follow TDD strictly.
2. Use **Laravel + Filament MCP Context7** for Filament resources.
3. **Never create controllers/routes manually for Filament-managed resources** — always use `php artisan make:filament-resource`.
4. Write/update relevant tests first, then implement code.
5. Record all created/modified filenames in the Execution Log.
6. Update `project_master_plan.md` with progress.

---

### 3. UPDATE
**Goal:** Keep documentation in sync with implementation.

**Actions:**
1. Append results to `phase_{number}.md` Execution Log.
2. Record changes in `project_master_plan.md`.
3. Update PRD/ERD if scope changes.

---

### 4. CHECK
**Goal:** Confirm success criteria are met.

**Actions:**
1. Run all tests defined in the phase doc.
2. If all pass → Mark phase as `Completed` in `project_master_plan.md`.
3. If any fail → Log Failure Analysis in `phase_{number}.md`.

---

### 5. DEBUG
**Goal:** Investigate and resolve unexpected issues.

**Actions:**
1. Trace the bug to the responsible phase.
2. Identify root cause:
   - Missing or incomplete test
   - Incorrect implementation
   - PRD/ERD mismatch
   - Skipped Filament generation rules
3. Log findings in `Debug Log` section of `phase_{number}.md`.
4. Suggest counter-measures but do **not** edit the code directly unless instructed.

---

### 6. REPAIR
**Goal:** Fix issues identified during DEBUG while maintaining TDD discipline.

**Actions:**
1. Perform DEBUG first to identify and document the root cause in Debug Log.
2. Upon instruction to repair, follow EXECUTE rules:
   - Verify PRD/ERD alignment.
   - Write/update failing tests first.
   - For Filament and Livewire testing, follow the official Filament and Livewire testing guide
   - Implement fix in small steps.
   - Record all created/modified filenames in the Execution Log.
3. Update:
   - `phase_{number}.md` Execution Log with repair steps.
   - `project_master_plan.md` with progress.
4. Run all tests from the relevant phase to confirm fix before marking as completed.

---

## 📂 Templates

### PRD.md Template
```markdown
# Product Requirements Document

## 1. Overview
Describe the purpose, scope, and key outcomes of the project.

## 2. Features
List features grouped by category.

## 3. Packages / Setup
**Required Packages:**
| Package | Version | Purpose |
|---------|---------|---------|
| filament/filament | ^3.0 | Admin panel |
| bezhansalleh/filament-shield | ^3.0 | Role & permission management |

**Installation Commands:**
```bash
composer require filament/filament
php artisan filament:install
composer require bezhansalleh/filament-shield
php artisan vendor:publish --tag="filament-shield-config"
