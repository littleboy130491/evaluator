# Evaluation Management System

A comprehensive Laravel-based web application for managing structured evaluations of outlets, enabling evaluators to assess outlets against predefined criteria, record scores and comments, and maintain an auditable history of changes.

## 🎯 Project Overview

This system provides a complete solution for conducting and managing evaluations across multiple outlets with role-based access control, comprehensive audit trails, and an intuitive admin interface built with Filament.

## ✨ Key Features

### Core Functionality
- **Outlet Management**: Create, update, and manage outlet locations
- **Evaluation Criteria**: Define and manage evaluation standards and scoring
- **Evaluation Process**: Conduct evaluations with scores and comments
- **Audit Trail**: Complete history tracking for all changes
- **Role-Based Access**: Multi-level permissions (Admin, Manager, Evaluator)

### Admin Panel Features
- **Filament Admin Interface**: Modern, responsive admin panel
- **Real-time Updates**: Livewire-powered dynamic interfaces
- **Export/Import**: Data export capabilities
- **Advanced Filtering**: Search and filter evaluations by multiple criteria
- **User Management**: Complete user administration with roles and permissions

## 🛠️ Technology Stack

| Technology | Version | Purpose |
|------------|---------|---------|
| Laravel | ^12.0 | PHP Framework |
| Filament | ^3.0 | Admin Panel |
| Livewire | ^3.0 | Dynamic UI Components |
| Spatie Laravel Permission | ^6.0 | Role-based Access Control |
| MySQL/PostgreSQL | - | Database |

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL 8.0+ or PostgreSQL 12+
- Web server (Apache/Nginx)

## 🚀 Installation Guide

### 1. Clone the Repository
```bash
git clone [repository-url]
cd evaluator
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Configuration
Update your `.env` file with database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=evaluator
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Run Migrations and Seeders
```bash
# Run migrations
php artisan migrate

# Run seeders for initial data
php artisan db:seed
```

### 6. Build Assets
```bash
# Build for development
npm run dev

# Build for production
npm run build
```

### 7. Start the Application
```bash
# Using Laravel's built-in server
php artisan serve

# Or configure your web server
```

## 🔐 Default Credentials

After running the seeders, you can log in with:
- **Admin**: admin@example.com / password
- **Manager**: manager@example.com / password
- **Evaluator**: evaluator@example.com / password

## 📊 Database Schema

### Core Tables
- **users**: User accounts and authentication
- **outlets**: Outlet locations and details
- **evaluations**: Main evaluation records
- **evaluation_criteria**: Scoring criteria definitions
- **evaluation_criteria_scores**: Individual criterion scores
- **histories**: Complete audit trail
- **reports**: Generated evaluation reports

### Relationships
- User → Evaluations (One-to-Many)
- Outlet → Evaluations (One-to-Many)
- Evaluation → EvaluationCriteriaScores (One-to-Many)
- EvaluationCriteria → EvaluationCriteriaScores (One-to-Many)
- All tables → Histories (Polymorphic)

## 🎯 Usage Guide

### Creating an Evaluation
1. Navigate to the admin panel (`/admin`)
2. Go to "Evaluations" section
3. Click "New Evaluation"
4. Select outlet and evaluator
5. Add scores for each criterion
6. Submit for review

### Managing Criteria
1. Go to "Evaluation Criteria" section
2. Add new criteria with scoring rules
3. Set minimum and maximum scores
4. Define criteria descriptions

### Viewing History
1. Any record has a "View History" button
2. See complete change log with timestamps
3. Track who made changes and when

## 🧪 Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --filter=EvaluationTest

# Run with coverage
php artisan test --coverage
```

### Test Categories
- **Unit Tests**: Model relationships and business logic
- **Feature Tests**: API endpoints and user workflows
- **Browser Tests**: Filament admin panel functionality

## 🔧 Development Commands

### Database Commands
```bash
# Reset and re-seed database
php artisan migrate:fresh --seed

# Run specific seeder
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### Filament Commands
```bash
# Create new Filament resource
php artisan make:filament-resource Outlet

# Create new Filament page
php artisan make:filament-page Dashboard
```

## 📁 Project Structure

```
evaluator/
├── app/
│   ├── Filament/Resources/     # Filament admin resources
│   ├── Models/                 # Eloquent models
│   ├── Policies/               # Authorization policies
│   ├── Services/               # Business logic services
│   └── Traits/                 # Reusable traits
├── database/
│   ├── migrations/             # Database migrations
│   ├── seeders/               # Database seeders
│   └── factories/              # Model factories
├── resources/
│   └── views/                  # Blade templates
├── routes/                     # Web routes
├── tests/                      # Test files
└── storage/                    # Logs, cache, uploads
```

## 🛡️ Security Features

- **Role-based Access Control**: Granular permissions
- **Audit Logging**: Complete change tracking
- **Input Validation**: Comprehensive validation rules
- **CSRF Protection**: Laravel's built-in security
- **SQL Injection Prevention**: Eloquent ORM protection

## 📈 Performance Optimizations

- **Eager Loading**: Optimized database queries
- **Caching**: Redis support for improved performance
- **Database Indexes**: Optimized for common queries
- **Asset Optimization**: Vite for asset compilation

## 🔍 Troubleshooting

### Common Issues

#### Database Connection Issues
```bash
# Check database connection
php artisan tinker
>>> DB::connection()->getPdo()
```

#### Permission Issues
```bash
# Fix storage permissions
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

#### Cache Issues
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 📞 Support

For support and questions:
- Create an issue in the repository
- Check existing documentation
- Review test cases for usage examples

---

**Built with ❤️ using Laravel & Filament**
