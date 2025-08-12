# CLAUDE.md - Development Instructions

## Project Structure
This is a Laravel application with Filament admin panel for an evaluation system.

## Development Workflow
Follow the structured approach defined in `coding_agent_handbook.md`:

### Core References
- `PRD.md` — Product Requirements Document
- `ERD.md` — Entity Relationship Diagram  
- `coding_agent_handbook.md` — Complete development rules
- `project_master_plan.md` — Phase tracking and status
- `phase_{n}.md` — Individual phase documentation

### Development Roles
1. **ARCHITECT:** Plan projects in phases, create master plan and phase documents
2. **EXECUTE:** Implement using TDD, log all changes, update documentation
3. **UPDATE:** Keep docs synchronized with implementation
4. **CHECK:** Verify success criteria, run tests, update status
5. **DEBUG:** Trace issues to phases, analyze root causes, document findings

### Key Principles
- Always use Test Driven Development (TDD)
- Keep PRD/ERD documentation current
- Log all filenames for traceability
- Never skip CHECK phase
- Document all DEBUG findings

### Testing
- Run tests with: `php artisan test`
- Use Laravel's built-in testing framework
- Write tests before implementing features

### Linting/Type Checking
- PHP: Use Laravel Pint for code formatting
- Run: `./vendor/bin/pint`

### Technology Stack
- Laravel framework
- Filament admin panel
- MySQL database
- PHP testing framework

### File Organization
- Models: `app/Models/`
- Filament Resources: `app/Filament/Resources/`
- Services: `app/Services/`
- Policies: `app/Policies/`
- Tests: `tests/Feature/` and `tests/Unit/`

## Important Notes
- Always verify PRD/ERD alignment before implementation
- Use Filament MCP Context7 for admin resources
- Record all created/modified files in execution logs
- Update project master plan with progress