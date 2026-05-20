# ELI (Explain Like I'm 5) - Project Structure

A Laravel web application that explains complex concepts in simple, easy-to-understand language using AI.

```
eli-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AIController.php          # Main AI request handler
│   │   │   ├── ConversationController.php # Conversation history management
│   │   │   └── Controller.php            # Base controller
│   │   │
│   │   └── Middleware/
│   │       └── VerifyCsrfToken.php       # CSRF token validation (excludes API routes)
│   │
│   ├── Models/
│   │   ├── User.php                      # User model
│   │   ├── Conversation.php              # Conversation history model
│   │   ├── Project.php                   # Project model
│   │   └── Attachment.php                # File attachment model
│   │
│   ├── Services/
│   │   ├── GeminiService.php             # Google Gemini API integration
│   │   ├── DeepSeekService.php           # DeepSeek API integration (backup)
│   │   └── AIServiceInterface.php        # AI service interface for extensibility
│   │
│   ├── Providers/
│   │   └── AppServiceProvider.php        # Application service provider
│   │
│   └── Exceptions/
│       └── Handler.php                   # Global exception handler
│
├── bootstrap/
│   ├── app.php                           # Application configuration & middleware setup
│   ├── cache/
│   │   ├── packages.php
│   │   └── services.php
│   └── providers.php
│
├── config/
│   ├── app.php                           # General application configuration
│   ├── auth.php                          # Authentication configuration
│   ├── cache.php                         # Cache configuration
│   ├── database.php                      # Database configuration
│   ├── filesystems.php                   # File storage configuration
│   ├── mail.php                          # Email configuration
│   ├── queue.php                         # Queue configuration
│   ├── session.php                       # Session configuration
│   ├── services.php                      # Third-party services config
│   └── logging.php                       # Logging configuration
│
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2026_05_20_041132_create_projects_table.php
│   │   ├── 2026_05_20_041137_create_conversations_table.php
│   │   ├── 2026_05_20_041141_create_attachments_table.php
│   │   └── 2026_05_20_041145_add_tags_to_conversations_table.php
│   │
│   ├── factories/
│   │   └── UserFactory.php               # User model factory for testing
│   │
│   └── seeders/
│       └── DatabaseSeeder.php            # Main database seeder
│
├── public/
│   ├── index.php                         # Application entry point
│   ├── robots.txt                        # Search engine crawling rules
│   └── hot                               # Vite hot module reloading file
│
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php             # Main layout template
│   │   │
│   │   ├── index.blade.php               # Main dashboard view
│   │   └── welcome.blade.php             # Welcome page
│   │
│   ├── css/
│   │   └── app.css                       # Global styles (Tailwind)
│   │
│   └── js/
│       └── app.js                        # Main JavaScript entry point
│
├── routes/
│   ├── web.php                           # Web routes (HTML views + API endpoints)
│   ├── api.php                           # API routes (for future REST API)
│   └── console.php                       # Console/Artisan commands
│
├── storage/
│   ├── app/
│   │   ├── private/                      # Private file storage
│   │   └── public/                       # Public file storage
│   │
│   ├── framework/
│   │   ├── cache/                        # Application cache
│   │   ├── sessions/                     # Session files
│   │   ├── testing/                      # Testing storage
│   │   └── views/                        # Compiled views
│   │
│   └── logs/
│       └── laravel.log                   # Application logs
│
├── tests/
│   ├── Feature/
│   │   ├── AIControllerTest.php          # AI controller tests
│   │   └── ConversationTest.php          # Conversation feature tests
│   │
│   ├── Unit/
│   │   ├── GeminiServiceTest.php         # Gemini service unit tests
│   │   └── ValidationTest.php            # Validation tests
│   │
│   └── TestCase.php                      # Base test case
│
├── .env                                  # Environment variables (local)
├── .env.example                          # Environment variables template
├── .gitignore                            # Git ignore rules
├── artisan                               # Artisan CLI
├── composer.json                         # PHP dependencies
├── composer.lock                         # Locked dependency versions
├── package.json                          # Node.js dependencies
├── package-lock.json                     # Node.js locked versions
├── phpunit.xml                           # PHPUnit configuration
├── postcss.config.js                     # PostCSS configuration
├── tailwind.config.js                    # Tailwind CSS configuration
├── vite.config.js                        # Vite bundler configuration
├── README.md                             # Project documentation
└── PROJECT_STRUCTURE.md                  # This file - project structure
```

## Directory Descriptions

### `/app` - Application Core
- **Http/Controllers/** - Route handlers for processing requests
- **Http/Middleware/** - Request/response middleware
- **Models/** - Database models and relationships
- **Services/** - Business logic for AI integrations
- **Providers/** - Service container bindings
- **Exceptions/** - Custom exception handling

### `/config` - Configuration Files
Environment-specific settings for database, cache, mail, sessions, etc.

### `/database` - Database Management
- **migrations/** - Database schema changes
- **factories/** - Model factories for testing
- **seeders/** - Database seeding scripts

### `/public` - Web Root
Publicly accessible files. Entry point is `index.php`.

### `/resources` - Frontend Assets
- **views/** - Blade template files (HTML + PHP)
- **css/** - Stylesheets (Tailwind CSS)
- **js/** - Frontend JavaScript

### `/routes` - Route Definitions
- **web.php** - Web routes serving HTML/JSON
- **api.php** - REST API routes
- **console.php** - Artisan commands

### `/storage` - Persistent Data
- **app/** - User uploads and private files
- **framework/** - Cache, sessions, compiled views
- **logs/** - Application logs

### `/tests` - Testing
- **Feature/** - High-level feature tests
- **Unit/** - Low-level unit tests

## Key Features

### AI Integration Services
- ✅ Google Gemini API integration
- ✅ DeepSeek API fallback
- ✅ Mock AI mode for offline testing
- ✅ Extensible service interface

### Request Types
1. **Ask** (`/ask`) - General questions
2. **Summarize** (`/summarize`) - Summarize text
3. **ELI5** (`/eli5`) - Explain like I'm 5 years old
4. **Explain Code** (`/explain-code`, `/code`) - Code explanations

### Database Models
- **Users** - Application users
- **Conversations** - Chat history with AI
- **Projects** - User projects
- **Attachments** - File uploads

## Technology Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 13 |
| Database | MySQL |
| Frontend | Blade Templates + Tailwind CSS |
| Frontend Logic | JavaScript (Fetch API) |
| CSS Framework | Tailwind CSS |
| Bundler | Vite |
| PHP Version | 8.3+ |

## Environment Variables

Key `.env` variables:
- `APP_DEBUG` - Debug mode
- `APP_ENV` - Environment (local/production)
- `DB_CONNECTION` - Database type
- `GEMINI_API_KEY` - Google Gemini API key
- `DEEPSEEK_API_KEY` - DeepSeek API key
- `USE_MOCK_AI` - Enable mock AI responses

## Getting Started

```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate
php artisan db:seed

# Build assets
npm run build

# Start server
php artisan serve
```

## API Endpoints

| Method | Route | Description |
|--------|-------|-------------|
| POST | `/ask` | Ask a question |
| POST | `/summarize` | Summarize text |
| POST | `/eli5` | Explain concept simply |
| POST | `/code` | Explain code |
| POST | `/explain-code` | Explain code (alias) |
| GET | `/history` | Get conversation history |
| DELETE | `/history/{id}` | Delete conversation |

## Development Workflow

1. Create migrations for database changes
2. Create models with relationships
3. Add service logic for business rules
4. Create controllers to handle requests
5. Define routes in `routes/web.php`
6. Create/update Blade templates
7. Add styling with Tailwind CSS
8. Test with PHPUnit or manual testing

## Notes

- CSRF protection is disabled for API routes (configured in middleware)
- Mock AI mode available for offline development/testing
- All responses return JSON
- Database uses cascading deletes for data integrity
