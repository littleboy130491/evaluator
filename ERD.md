1. Goal

Provide an ERD and a step-by-step Filament admin panel plan (resources, forms, tables, relations) so you can quickly scaffold an admin UI for the Evaluation Management System.

2. ERD (Entity Relationship Diagram)

erDiagram
    USERS ||--o{ EVALUATIONS : creates
    OUTLETS ||--o{ EVALUATIONS : receives
    EVALUATIONS ||--o{ EVALUATION_CRITERIA_SCORES : has
    EVALUATION_CRITERIA ||--o{ EVALUATION_CRITERIA_SCORES : used_in
    EVALUATIONS ||--o{ HISTORIES : logs
    EVALUATIONS ||--o{ REPORTS : may_have

    USERS {
      bigint id PK
      string name
      string email
    }
    OUTLETS {
      bigint id PK
      string name
      string location
    }
    EVALUATION_CRITERIA {
      bigint id PK
      string name
      text description
      int max_score
    }
    EVALUATIONS {
      bigint id PK
      bigint evaluator_id FK->users.id
      bigint outlet_id FK->outlets.id
      bigint report_id FK->reports.id nullable
      int total_score
      string status
      timestamp created_at
      timestamp updated_at
    }
    EVALUATION_CRITERIA_SCORES {
      bigint id PK
      bigint evaluation_id FK->evaluations.id
      bigint criteria_id FK->evaluation_criteria.id
      int score
      text notes
      text evaluator_comments
      string evidence_url nullable
    }
    HISTORIES {
      bigint id PK
      bigint evaluation_id FK->evaluations.id
      bigint user_id FK->users.id
      string action
      json details
      timestamp created_at
    }
    REPORTS {
      bigint id PK
      string title
      text content
      string file_path nullable
    }

The mermaid ERD above describes the major tables and foreign key relationships. It also shows the pivot evaluation_criteria_scores as the many-to-many link with extra fields.

3. Filament Admin Panel Plan

We will use Filament to manage: Users (roles via Spatie), Outlets, Evaluation Criteria, Evaluations, Evaluation Criteria Scores (via relation manager), Reports, and Histories (read-only audit view).

3.1 Packages / Setup

Install Filament:

composer require filament/filament
php artisan filament:install

Install and configure Filament Shield for RBAC integration with Spatie Permissions:

composer require bezhansalleh/filament-shield
php artisan shield:install

This will automatically integrate Spatie's laravel-permission package and generate permission management via the Filament admin panel. Assign roles such as evaluator, admin, and manager through Shield's UI or seeders.

Ensure your User model uses:

use Spatie\Permission\Traits\HasRoles;

In FilamentServiceProvider::boot() (if needed), you can further restrict access based on role or permission:

Filament::serving(function () {
    if (!auth()->user()->hasAnyRole(['admin', 'manager', 'evaluator'])) {
        abort(403);
    }
});

3.2 Filament Resources (recommended)

UserResource (Filament) — manage users and assign roles

Fields: name, email, roles (BelongsToMany/CheckboxList)

OutletResource — CRUD outlets

Fields: name, location, metadata

EvaluationCriteriaResource — CRUD criteria

Fields: name, description, max_score

ReportResource — create/manage report records or upload files

EvaluationResource — main resource

Table columns: id, evaluator->name, outlet->name, total_score, status, created_at

Filters: status, outlet, evaluator, date range

Actions: view, edit, approve, export PDF

Form:

evaluator_id (Select -> Users with role evaluator)

outlet_id (Select)

status (Select)

Criteria relation manager (see below)

Report relation (BelongsTo)

Relation Manager: CriteriaScoresRelationManager

Manages evaluation_criteria_scores rows for that evaluation

Fields per row: criterion (Select/BelongsTo), score (Numeric), notes (Textarea), evaluator_comments (Textarea), evidence_url (Text)

Validation: score <= criterion.max_score

HistoryResource — read-only

Table columns: id, evaluation_id (link to evaluation), user->name, action, created_at

Action: view details (JSON pretty print)

3.3 Relation Managers & Inline Editing

Use Filament Relation Manager for criteria pivot to allow creating/editing pivot rows within Evaluation form.

Use Table::make() with TextColumn showing pivot.score and TextColumn::make('pivot.evaluator_comments').

If you prefer strict workflow, hide edit action after status == 'completed'.

4. Filament Code Sketches

Below are example skeletons you can drop in and adapt.

4.1 EvaluationResource (skeleton)

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Resources\Forms;
use Filament\Tables; // etc

class EvaluationResource extends Resource
{
    protected static ?string $model = \App\Models\Evaluation::class;

    public static function form(Forms\Components\Form $form): Forms\Components\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('evaluator_id')
                    ->relationship('evaluator', 'name')
                    ->options(fn() => \Spatie\Permission\Models\Role::findByName('evaluator') ? \App\Models\User::role('evaluator')->pluck('name','id') : [])
                    ->required(),

                Forms\Components\Select::make('outlet_id')->relationship('outlet','name')->required(),
                Forms\Components\Select::make('status')->options(['pending'=>'pending','completed'=>'completed','approved'=>'approved','rejected'=>'rejected'])->required(),

                // Relation manager will handle criteria
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('evaluator.name')->label('Evaluator'),
                Tables\Columns\TextColumn::make('outlet.name')->label('Outlet'),
                Tables\Columns\TextColumn::make('total_score'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->filters([
                // add filters
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }
}

4.2 CriteriaScoresRelationManager (skeleton)

namespace App\Filament\Resources\EvaluationResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Forms;
use Filament\Tables;

class CriteriaScoresRelationManager extends RelationManager
{
    protected static string $relationship = 'criteria'; // belongsToMany relation

    public static function form(Forms\Components\Form $form): Forms\Components\Form
    {
        return $form->schema([
            Forms\Components\Select::make('criteria_id')
                ->relationship('evaluationCriteria', 'name')
                ->label('Criterion')
                ->required(),

            Forms\Components\TextInput::make('pivot.score')->numeric()->required(),
            Forms\Components\Textarea::make('pivot.notes'),
            Forms\Components\Textarea::make('pivot.evaluator_comments'),
            Forms\Components\TextInput::make('pivot.evidence_url'),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('pivot.score'),
                Tables\Columns\TextColumn::make('pivot.evaluator_comments')->limit(50),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}

5. Work items / Implementation Checklist

Scaffold Filament & basic resources (User, Outlet, Criteria, Report).

Implement Spatie permission and seed roles (admin, evaluator, manager).

Create migrations & models for evaluations, evaluation_criteria_scores, histories.

Build EvaluationResource with CriteriaScoresRelationManager.

Add validation to ensure score <= criterion.max_score (form level + server-side in EvaluationService).

Implement EvaluationService and call it from resource actions or controllers.

Add History writes on create/update/approve flows.

Add tests for major flows (create evaluation, edit, approve).

6. UX notes / Policies

Only users with evaluator role can create evaluations — ensure Filament menu visibility respects this.

Histories are read-only in Filament, but include a View action that pretty-prints the details JSON.

Consider creating a Reports -> Export PDF action on EvaluationResource.

