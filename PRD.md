1. Overview
The Evaluation Management System is a Laravel-based web application designed to manage structured evaluations of outlets.
It allows evaluators (users with the evaluator role) to assess outlets against predefined criteria, record scores and comments, and maintain an auditable history of changes.

2. Goals
Provide a structured way for evaluators to submit evaluations.

Maintain a flexible criteria-based scoring system.

Keep a full audit history of all evaluation actions.

Integrate with Laravel Spatie Roles/Permissions for role-based access control.

3. Stakeholders
Admin: Manages criteria, outlets, and user roles.

Evaluator: Conducts evaluations, enters scores and comments.

Manager: Reviews evaluations and approves/rejects results.

4. Key Features
4.1 User Management
Based on Laravel User model.

Integrated with Spatie Laravel Permission.

Roles:

Admin: Full system access.

Evaluator: Can create and edit evaluations.

Manager: Can review, approve, or reject evaluations.

4.2 Outlets
Each evaluation is linked to an outlet.

Outlets have name, location, and optional metadata.

4.3 Evaluation Criteria
Admin can create and manage criteria.

Each criterion has:

Name

Description

Max Score

4.4 Evaluations
Created by evaluators.

Linked to:

Evaluator (User)

Outlet

Optional Report

Status:

pending (default)

completed

approved

rejected

Total score calculated automatically from criteria scores.

4.5 Criteria Scores (Pivot Table)
Table: evaluation_criteria_scores

Fields:

evaluation_id

criteria_id

score

notes (internal reference)

evaluator_comments (visible feedback)

Supports future extensions (e.g., evidence file, is_critical flag).

4.6 History (Audit Log)
Table: histories

Tracks all evaluation actions.

Fields:

evaluation_id

user_id

action (created, updated, approved, rejected, deleted)

details (JSON snapshot of change)

Used for compliance and debugging.

5. System Architecture
Backend Framework: Laravel

Database: MySQL or PostgreSQL

Auth & RBAC: Filament PHP + Filament Shield

Service Layer: EvaluationService to handle all business logic

## Tech Stack Requirements
- Use Filament PHP for admin panel (not custom Laravel controllers/views)
- Use Filament Shield for role-based permissions
- Follow Filament resource patterns for CRUD operations

Eloquent Relationships:

User → Evaluation (1:M)

Outlet → Evaluation (1:M)

Evaluation ↔ EvaluationCriteria (M:M with pivot fields)

Evaluation → History (1:M)

6. Core Flow: Create Evaluation
Actors: Evaluator
Steps:

Evaluator logs in (role = evaluator).

Navigates to “New Evaluation” form.

Selects:

Outlet

Criteria scores & comments

Submits form.

EvaluationService:

Creates evaluation record.

Attaches criteria scores in pivot table.

Calculates total score.

Creates history record.

Returns success message to evaluator.

7. Database Schema
users
id, name, email, password, timestamps

outlets
id, name, location, timestamps

evaluation_criteria
id, name, description, max_score, timestamps

evaluations
id, evaluator_id (FK users), outlet_id, report_id (nullable), total_score, status, timestamps

evaluation_criteria_scores
id, evaluation_id, criteria_id, score, notes, evaluator_comments, timestamps

reports
id, title, content, file_path, timestamps

histories
id, evaluation_id, user_id, action, details (JSON), timestamps

8. Roles & Permissions
Admin

Manage users, outlets, criteria.

View all evaluations.

Approve/reject evaluations.

Evaluator

Create/edit own evaluations.

View assigned outlets and evaluations.

Manager

Review, approve, reject evaluations.

9. Future Enhancements
Photo/video upload as evaluation evidence.

Offline evaluation mode (PWA).

Data analytics dashboard for trends over time.

Criteria weight system.

10. Non-Functional Requirements
Security: Role-based access with Spatie.

Performance: Use eager loading for evaluation listings.

Auditability: All actions logged in histories.

Scalability: Service layer design allows future expansion.