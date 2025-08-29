# EduSpark - School Quiz Application

A multilingual quiz application for grades 1-8 built with Laravel 12, Livewire 3, and Filament 4.

## Features

### User Types
- **Teachers**: Full admin access via Filament panel at `/teacher`
- **Students**: Dashboard with quiz history and quiz access
- **Guests**: Anonymous quiz participation

### Quiz System
- Grade-based quizzes (1-8)
- Subject categorization
- Multilingual content (Serbian, English, Hungarian)
- Real-time quiz playing
- Score tracking and analytics

### Technology Stack
- Laravel 12
- Livewire 3 (full-page components)
- Filament 4 (teacher admin panel)
- Tailwind CSS
- MySQL

## Installation

### Requirements
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL

### Setup

1. **Clone and install dependencies**
```bash
git clone <repository-url> eduspark
cd eduspark
composer install
npm install
```

2. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Configure database in `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eduspark
DB_USERNAME=your_username
DB_PASSWORD=your_password

APP_LOCALE=sr
APP_FALLBACK_LOCALE=sr
```

4. **Run migrations and seed data**
```bash
php artisan migrate
php artisan db:seed
```

5. **Build assets**
```bash
npm run build
```

6. **Start the application**
```bash
php artisan serve
```

## Database Schema

### Core Tables

**users**
- id, name, email, password, role (teacher/student), timestamps

**subjects** 
- id, key (unique), name (JSON), timestamps

**quizzes**
- id, grade (1-8), subject_id, title (JSON), description (JSON), is_published, timestamps

**questions**
- id, quiz_id, prompt (JSON), explanation (JSON), order, timestamps

**answers**
- id, question_id, text (JSON), is_correct, order, timestamps

**attempts**
- id, user_id (nullable), guest_name (nullable), quiz_id, score, total, started_at, finished_at, locale, timestamps

**attempt_answers**
- id, attempt_id, question_id, answer_id, is_correct, timestamps

## User Accounts

### Default Seeded Users

**Teacher Account**
- Email: `teacher@example.com`
- Password: `password`
- Access: `/teacher` panel

**Student Account**
- Email: `student@example.com`  
- Password: `password`
- Access: `/dashboard`

## Routes & Pages

### Public Routes
- `/` - Welcome page with language switcher and quiz start
- `/start` - Quiz selection (grade → subject → begin)
- `/quiz/{quiz}` - Active quiz playing
- `/quiz/{quiz}/result/{attempt}` - Results page

### Authenticated Routes
- `/dashboard` - Student dashboard (redirects based on role)
- `/teacher` - Teacher Filament panel

## Internationalization

### Available Locales
- `sr` (Serbian) - Default
- `en` (English)
- `hu` (Hungarian)

### Locale Resolution Priority
1. URL query parameter `?lang=xx`
2. Session stored locale
3. User preference
4. Default (`sr`)

### JSON Translation Fields
All content fields store translations as JSON:
```json
{
  "sr": "Српски текст",
  "en": "English text", 
  "hu": "Magyar szöveg"
}
```

## Livewire Pages (Full Page Components)

### WelcomePage
- App introduction
- Language switcher
- Start Quiz / Sign In buttons

### QuizStartPage  
- **Authenticated**: Grade selection → Subject selection → Start
- **Guest**: Name input + Grade + Subject → Start

### QuizPlayPage
- Question display with progress
- Answer selection (click/keyboard)
- Auto-advance on answer
- Prevent double submission

### QuizResultPage
- Final score display
- Question review with correct answers
- Play Again / Dashboard navigation

### StudentDashboardPage
- Quiz history table
- Quick start options
- Personal statistics

## Teacher Panel (Filament)

### Path: `/teacher`
- Restricted to users with `role = 'teacher'`

### Resources
1. **Users** - Manage teachers and students
2. **Subjects** - Multilingual subject management
3. **Quizzes** - Grade/subject quizzes with translation support
4. **Questions** - Question bank with explanations
5. **Answers** - Answer options with correctness flags
6. **Attempts** - View quiz attempts and analytics

### Features
- Per-locale content editing tabs
- Relation managers for nested data
- Analytics widgets
- Bulk operations

## Quiz Flow Logic

### Starting a Quiz
1. User selects grade and subject (or provides guest name)
2. System creates `attempts` record with `started_at`
3. Redirect to first question

### During Quiz
1. Display question with answer options
2. On answer selection:
   - Create `attempt_answers` record
   - Calculate correctness
   - Auto-advance to next question
3. Prevent back navigation
4. Show progress indicator

### Completing Quiz  
1. On final answer submission:
   - Set `finished_at` timestamp
   - Calculate final score
   - Redirect to results page

### Guest vs Authenticated
- **Guest**: `user_id` = null, store `guest_name`
- **Authenticated**: Link to user account, track in dashboard

## Content Management

### Creating Content
1. Access teacher panel at `/teacher`
2. Create subjects with multilingual names
3. Create quizzes for each grade/subject combination
4. Add 3+ questions per quiz
5. Add 4 answers per question (1 correct)
6. Publish quizzes

### Translation Workflow
1. Enter content in Serbian (primary)
2. System auto-copies to English/Hungarian
3. Edit other languages as needed
4. Fallback to Serbian if translation missing

## Seeded Content

After running `php artisan db:seed`, the app includes:

### Subjects
- Mathematics (Математика)
- Serbian Language (Српски језик)  
- Science (Природа и друштво)
- English Language (Енглески језик)
- History (Историја)

### Sample Quizzes
- One quiz per grade (1-8) × subject combination
- 3 educational questions per quiz
- 4 answer options each (1 correct)
- Appropriate to grade level and subject

## Development

### Key Commands
```bash
# Start development
composer run dev

# Run tests  
php artisan test

# Clear caches
php artisan optimize:clear

# Migration commands
php artisan migrate:fresh --seed
```

### File Structure
```
app/
├── Models/           # Eloquent models
├── Livewire/         # Full-page components
├── Http/Middleware/  # Locale & Teacher middleware
└── Filament/Teacher/ # Admin panel resources

database/
├── migrations/       # Schema definitions
└── seeders/         # Sample data

resources/
└── views/           # Blade layouts
```

## Testing

### Feature Tests
- Guest quiz completion flow
- Student authentication and dashboard
- Teacher panel access restrictions
- Language switching functionality

### Unit Tests  
- Score calculation logic
- Translation helper methods
- Model relationships

## Security

### Authentication & Authorization
- Role-based access control
- Teacher panel restrictions
- CSRF protection
- Input validation

### Data Protection
- Password hashing
- SQL injection prevention
- XSS protection via Blade escaping

## Performance

### Optimizations
- Eager loading relationships
- Query caching for subjects/quizzes
- Asset compilation
- Database indexing on foreign keys

## Deployment

### Laravel Forge Deployment
This application is optimized for Laravel Forge deployment:

1. **Create Forge Site**
   - Connect your Git repository
   - Configure environment variables
   - Set up database and Redis

2. **Environment Variables**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

3. **Deploy Script**
Use the included `forge-deploy.sh` for automated deployment:
- Pulls latest code
- Installs dependencies
- Builds assets
- Runs migrations
- Warms up cache
- Restarts queue workers

4. **Queue Configuration**
Set up queue workers in Forge:
- Command: `php artisan queue:work --sleep=3 --tries=3 --timeout=90`
- Processes: 1
- Auto-restart: Yes

### Local Development (Laravel Herd)
For local development using Laravel Herd:

1. **Install Herd**: Download from Laravel Herd website
2. **Clone Repository**: `git clone <repo> && cd eduspark`
3. **Install Dependencies**: `composer install && npm install`
4. **Environment**: Copy `.env.example` to `.env` and configure
5. **Database**: Use Herd's built-in MySQL or external
6. **Redis**: Install Redis locally or use Herd's services
7. **Build Assets**: `npm run dev` for development
8. **Access**: Use Herd's local domain (e.g., `eduspark.test`)

## API Documentation

The application uses web routes exclusively. For API access, consider adding:
- Laravel Sanctum for authentication
- API Resource transformers
- Rate limiting
- API versioning

## Support

### Common Issues
- **Migration errors**: Check database connection and permissions
- **Asset loading**: Run `npm run build` after changes
- **Language not switching**: Clear browser cache and sessions
- **Teacher panel 403**: Ensure user has `role = 'teacher'`

### Logging
- Application logs: `storage/logs/laravel.log`
- Query debugging: Enable `DB_LOG_QUERIES=true`

## Contributing

1. Follow PSR-12 coding standards
2. Write tests for new features
3. Update documentation
4. Use conventional commits
5. Ensure translations are complete

## License

This project is open-sourced under the [MIT license](LICENSE).