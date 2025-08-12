# Coding Agent Handbook

---

## 📋 Project Reference Documents
**ALWAYS consult these documents before any implementation:**
- **ERD.md** - Database structure, relationships, and Filament resource guidance
- **PRD.md** - Business requirements, features, and tech stack requirements

## 🚀 Quick Reference Workflow

1. **DESIGN**
   - Review ERD.md and PRD.md to understand requirements and constraints
   - Create `phase_{n}.md` with Objectives, Success Criteria, and Test Plan.
   - Follow TDD.

2. **EXECUTE**
   - **Always reference ERD.md for database relationships and constraints**
   - **Always reference PRD.md for business logic and requirements**

2.1 Execute: Backend / Eloquent Models
Context: Use Laravel's Eloquent ORM and conventions. Follow ERD.md structure.

Steps:

Create migration using php artisan make:migration.
Create model with php artisan make:model ModelName.
Use factories for test data (php artisan make:factory).
Ensure models use fillable/guarded properly.
Implement relationships as defined in ERD.md.
Update phase doc Execution Log with model and migration filenames.

2.2 Execute: Filament Resource
Context: Use MCP context7 for Filament. Use Filament PHP + Filament Shield as specified in PRD.md Tech Stack Requirements.

Steps:

Generate resource:
php artisan make:filament-resource ModelName
Configure forms, tables, and filters per ERD.md specifications.
Implement authorization with Filament Shield + Spatie Roles per PRD.md.
Follow Filament UI/UX guidelines (section ordering, field types, search, filters).
Add integration tests to confirm the resource works.

2.3 Execute: Testing (TDD)
Context: Use Laravel’s built-in PHPUnit/Pest.

Steps:

Write failing test first (tests/Feature or tests/Unit).
Implement minimal code to pass test.
Refactor with green tests.
Use factories & seeders to simulate realistic data.

2.4 Execute: Controllers
Steps:

Generate controller (php artisan make:controller).
Implement routes in routes/web.php.
Validate requests using Form Requests.
Return JSON or view depending on endpoint type.

2.5 Execute: Blade / Frontend
Steps:

Use Laravel Blade templates.
Follow DRY principle — extract layouts and components.
Keep Tailwind styling consistent with project design.

2.6 Execute: MCP Context Usage
MCP Context7 Rule:

When generating Laravel code, explicitly set MCP context to “Laravel vX + Filament v3 + Spatie Permissions” before writing code.
Include relevant imports, service providers, and config changes in the code output.
Never generate raw PHP without Laravel conventions.

3. **UPDATE**
   - Document results, affected files, and changes to tests or criteria.

4. **CHECK**
   - If criteria ✅ → Mark phase Completed + update `project_master_plan.md`.
   - If ❌ → Add Failure Report and keep phase In Progress/Blocked.

5. **SYNC**
   - Always update `project_master_plan.md` after any phase change.
   - Keep status table, progress bar, and notes in sync.

---

This handbook defines the rules, processes, templates, and automation steps for managing a project using a phase-based workflow with Test-Driven Development (TDD).

---

## 1. Roles & Functions

### **Architect**
**Purpose:** Create a high-level plan and break the project into phases.

**Steps:**
1. Split project into sequential phases, each stored in `phase_{number}.md`.
2. For each phase, include:
   - **Objectives**
   - **Success Criteria**
   - **Test Plan** (TDD)
3. Follow the Phase Template (Section 3.1).

---

### **Execute**
**Purpose:** Implement tasks defined in a given phase.

**Steps:**
1. Follow TDD:
   - Write tests first.
   - Implement code to pass tests.
   - Refactor while keeping tests green.
2. Append work results to the `Execution Log` in the phase doc.
3. Include:
   - Task description
   - Related filenames
   - Result summary

---

### **Update**
**Purpose:** Keep documentation current.

**Steps:**
1. After execution, update:
   - Execution results
   - Affected files
   - Changes to tests or criteria
2. Ensure traceability for all changes.

---

### **Check**
**Purpose:** Validate phase completion.

**Steps:**
1. Review all deliverables and tests.
2. If **criteria are met**:
   - Mark ✅ in phase doc
   - Mark phase as Completed in master plan
3. If **criteria are not met**:
   - Append Failure Report
   - Keep phase In Progress or Blocked

---

## 2. Master Plan Structure

### **2.1 `project_master_plan.md` Template**
```markdown
# Project Master Plan – {Project Name}

## 1. Overview
**Project Goal:**  
{Description}  

**Approach:**  
- Phased delivery  
- Test-Driven Development (TDD)  
- Documentation for each phase

---

## 2. Phase Summary Table
| Phase # | Title / Description | Status | Completion Date | Phase Doc Link |
|---------|---------------------|--------|-----------------|----------------|
| 1       | Example Title        | ✅ Completed | 2025-08-12      | [phase_1.md](phase_1.md) |

---

## 3. Progress Overview
- **Total Phases:** X  
- **Completed:** X  
- **In Progress:** X  
- **Blocked:** X  
- **Remaining:** X  

**Visual Progress:**  
```text
[#####-----] 50%
4. Dependencies & Notes
List dependencies here

5. Change Log
Date	Change Description	Author
YYYY-MM-DD	Example change	Agent

yaml
Copy
Edit

---

## 3. Phase Documentation

### **3.1 `phase_{number}.md` Template**
```markdown
# Phase {number} – {Phase Title}

## 1. Objectives
- [ ] List goals

## 2. Success Criteria
- [ ] Define measurable outcomes

## 3. Test Plan (TDD)
### Pre-Implementation Tests
- **Test Name:**  
- **Description:**  
- **Expected Result:**  

---

## 4. Execution Log
| Date       | Task Description | Related Files | Notes/Result |
|------------|------------------|---------------|--------------|
| YYYY-MM-DD | Example Task     | file.js       | Passed tests |

---

## 5. Check & Validation
- **Validation Date:** YYYY-MM-DD  
- **Criteria Met?** ✅ / ❌  
- **Details:**  

---

## 6. Failure Report (If Criteria Not Met)
- **Reason(s):**  
- **Related Files:**  
- **Next Steps / Fixes Needed:**

---

## 7. Phase Completion Status
- Status: `In Progress` / `Completed` / `Blocked`
- Completion Date: YYYY-MM-DD
4. Automation Rules
4.1 Sync Between Phase Docs and Master Plan
On phase creation → Add row to master plan.

On phase execution completion → No change unless criteria met.

On phase check:

✅ → Mark Completed in master plan + update progress bar.

❌ → Keep In Progress / Blocked + add note.

On status change → Update master plan table + progress bar.

On title change → Update title in master plan.

On global notes/dependencies → Append to master plan notes.

Always preserve markdown table formatting.

5. Sync Prompt for Agent
When updating or creating a phase_{n}.md, follow these steps:

Step 1 – Identify Phase

Extract {n} from filename.

Get title from first heading.

Step 2 – Determine Action

New file → Add row in master plan.

Execution update → Only update if criteria changed.

Check update → Update status in master plan.

Title change → Update title in master plan.

Step 3 – Update Master Plan

Keep table sorted by phase number.

Format link as [phase_{n}.md](phase_{n}.md).

Step 4 – Update Progress

Calculate:

mathematica
Copy
Edit
Progress % = (Completed / Total) × 100
Bar: [#####-----] {percent}%

Step 5 – Dependencies & Notes

Add any new issues from Failure Report.

Step 6 – Log Change

Copy
Edit
| YYYY-MM-DD | {Description} | Agent |
Step 7 – Save

Save both files after update.