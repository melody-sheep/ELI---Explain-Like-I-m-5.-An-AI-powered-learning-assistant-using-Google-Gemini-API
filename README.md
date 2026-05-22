## Updated README.md

```markdown
# ELI - Explain Like I'm 5

## An AI-Powered Learning Management System

---

### 📌 About The System

**ELI (Explain Like I'm 5)** is an innovative, AI-powered Learning Management System (LMS) designed to make complex topics simple and accessible. Built as a mini-capstone project for **IT323 - Application Development and Emerging Technologies**, ELI demonstrates the practical integration of cutting-edge technologies to solve real-world educational challenges.

The system leverages **Google's Gemini AI** to provide intelligent tutoring, automated flashcard generation, dynamic quiz creation, and personalized learning experiences. Whether you're a student struggling with difficult concepts, a teacher looking for supplementary materials, or a self-learner exploring new topics, ELI adapts to your needs.

---

### ✨ What Makes ELI Innovative?

ELI successfully integrates **multiple emerging technologies** as required by the IT323 Final Project:

| Emerging Technology | Implementation in ELI | Status |
|---------------------|------------------------|--------|
| **Artificial Intelligence (AI)** | Google Gemini API for intelligent responses, flashcard generation, and quiz creation | ✅ Complete |
| **Machine Learning** | AI-powered content analysis and question generation | ✅ Complete |
| **Cloud Computing** | Laravel Forge-ready deployment, cloud storage for uploads | ✅ Complete |
| **Speech Recognition** | Voice input for hands-free interaction | ✅ Complete |
| **Data Visualization** | Progress tracking, mastery statistics, learning analytics | ✅ Complete |
| **API Integration** | Google Gemini API, RESTful API architecture | ✅ Complete |
| **Chatbot / AI Assistant** | 4 AI modes: ASK, SUMMARIZE, ELI5, CODE | ✅ Complete |
| **Automation Tools** | Automated flashcard/quiz generation from uploaded documents | ✅ Complete |

---

### 🎯 How ELI Addresses Real-World Problems

| Problem | ELI's Solution |
|---------|----------------|
| **Complex topics are hard to understand** | ELI5 mode explains anything like you're 5 years old |
| **Students struggle with active recall** | AI-generated flashcards with spaced repetition |
| **Manual quiz creation is time-consuming** | Instant AI-generated quizzes from any document |
| **No personalized learning** | User isolation ensures each learner has their own data |
| **Accessibility barriers** | Voice input and dark/light mode support |
| **No progress tracking** | Mastery tracking, completion statistics, learning streaks |

---

### 🏗️ System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                      ELI-APP System                          │
├─────────────────────────────────────────────────────────────┤
│  Frontend: Laravel Blade + Tailwind CSS + Material Icons   │
│  Backend: Laravel 13.x + PHP 8.5                            │
│  Database: MySQL + Eloquent ORM                             │
│  AI Engine: Google Gemini API (gemini-2.5-flash)           │
│  File Processing: PDF/DOCX/TXT extraction (pdftotext)       │
│  Authentication: Laravel Breeze + Guest Mode                │
│  Deployment: GitHub + Optional APK (WebView)               │
└─────────────────────────────────────────────────────────────┘
```

---

### 🎨 UI Theme Guide

| Module | Theme Color | Button Style |
|--------|-------------|--------------|
| **Lessons** | Green (#22c55e) | Stroke + light green background (0.1 opacity) |
| **Flashcards** | Yellow/Orange (#eab308 → #f97316) | Stroke + light yellow background (0.1 opacity) |
| **Quizzes** | Purple (#a855f7) | Stroke + light purple background (0.1 opacity) |

**Button Interaction Effects:**
- **Hover:** Background opacity increases to 0.2, scale 1.02
- **Pressed:** Scale 0.98
- **Active:** Solid color with white/black text

---

### ✅ Accomplished Features

#### 🔐 Authentication & User Management
- ✅ User registration and login with validation
- ✅ Guest mode (browse without account)
- ✅ Password reset functionality
- ✅ User isolation (each user sees only their own data)
- ✅ Session management with Laravel
- ✅ Dark/Light mode toggle on auth pages

#### 🤖 AI Assistant (4 Modes)
- ✅ **ASK** - General Q&A with Gemini AI
- ✅ **SUMMARIZE** - Condense long texts into key points
- ✅ **ELI5** - Explain complex topics simply
- ✅ **CODE** - Code explanation and debugging
- ✅ Conversation memory (remembers context)
- ✅ Voice input support
- ✅ Export chat history
- ✅ API fallback when rate limit exceeded

#### 📚 Flashcards System (Yellow/Orange Theme)
- ✅ Upload PDF/DOCX/TXT files for flashcard generation
- ✅ AI extracts key concepts and generates Q&A pairs
- ✅ Deck-based organization (each document = one deck)
- ✅ Flip card interaction with smooth animation
- ✅ Easy/Medium/Hard difficulty rating
- ✅ Mastery tracking (new → learning → mastered)
- ✅ Session completion modal with statistics:
  - Total cards studied
  - Mastered/Learning/Review counts
  - Accuracy percentage
  - Time spent
- ✅ Shuffle cards functionality
- ✅ Keyboard navigation (← → Space)
- ✅ Delete individual cards or entire decks
- ✅ Progress bar per deck
- ✅ Yellow/Orange stroke button design with hover/pressed effects

#### 📖 Lessons System (Green Theme)
- ✅ Create, read, update, delete lessons
- ✅ Add text, video, file, or link content
- ✅ Database-backed progress tracking (persists after logout)
- ✅ Database-backed bookmarks (persist forever)
- ✅ Database-backed notes (saved permanently)
- ✅ Mark sections as complete with real-time progress update
- ✅ Completion modal with statistics
- ✅ Edit, rename, and delete lesson options (three-dot menu)
- ✅ Filter lessons by status (All, In Progress, Completed, Bookmarked)
- ✅ Sort lessons by date, title, or progress
- ✅ File attachments with VIEW (PDF) and DOWNLOAD options
- ✅ File notes for each attachment
- ✅ Green stroke button design with hover/pressed effects
- ✅ AJAX delete with proper JSON response handling

#### 🎯 Quizzes System (Purple Theme - In Progress)
- ⚠️ Generate quizzes from uploaded documents (NEEDS FIX)
- ⚠️ Multiple choice questions with AI generation
- ⚠️ Timer support with hide/show toggle
- ⚠️ Score tracking and results page
- ⚠️ Retake option
- ⚠️ Results page with detailed feedback

#### 🎨 UI/UX Excellence
- ✅ Dark/Light theme toggle (persists across sessions)
- ✅ Consolas monospace font for developer aesthetic
- ✅ Material Icons (no emojis)
- ✅ Collapsible sidebar
- ✅ Responsive design (mobile-ready)
- ✅ Toast notifications with animations
- ✅ Loading animations
- ✅ Keyboard shortcuts
- ✅ Consistent button styling across modules
- ✅ Hover and pressed effects on all interactive elements

---

### 📋 Project Requirements Met (IT323)

| Requirement | Status |
|-------------|--------|
| Functional application prototype | ✅ Complete |
| Research-style documentation | ✅ In progress |
| Integration of emerging technologies (AI) | ✅ Complete |
| Team collaboration and project management | ✅ Complete |
| Professional documentation | ✅ In progress |
| Oral presentation and system demonstration | 📅 Scheduled |
| GitHub repository with source code | ✅ Complete |
| Contribution matrix | ✅ Complete |

---

### 🚀 How to Install and Run ELI

#### Prerequisites

| Requirement | Version |
|-------------|---------|
| PHP | 8.5 or higher |
| Composer | Latest |
| MySQL | 5.7 or higher / 8.0 |
| Node.js | 18.x or higher (for Vite) |
| NPM | 9.x or higher |
| XAMPP / WAMP / Laragon (for local development) | Any |

#### Step 1: Clone the Repository

```bash
git clone https://github.com/yourusername/eli-app.git
cd eli-app
```

#### Step 2: Install PHP Dependencies

```bash
composer install
```

#### Step 3: Install Frontend Dependencies

```bash
npm install
```

#### Step 4: Environment Configuration

```bash
cp .env.example .env
```

Edit `.env` and configure:

```env
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eli_db
DB_USERNAME=root
DB_PASSWORD=

# Gemini API Key (Get from https://aistudio.google.com/)
GEMINI_API_KEY=your_api_key_here

# Application URL
APP_URL=http://localhost:8000

# Mock AI Mode (for testing without API)
USE_MOCK_AI=false
```

#### Step 5: Generate Application Key

```bash
php artisan key:generate
```

#### Step 6: Run Migrations

```bash
php artisan migrate
```

#### Step 7: Install PDF Extraction Tool (pdftotext)

**Windows:**
1. Download Xpdf command line tools from: https://www.xpdfreader.com/download.html
2. Extract and copy `pdftotext.exe` to your project root or `C:\Windows\System32`

**Linux/Mac:**
```bash
sudo apt-get install poppler-utils  # Ubuntu/Debian
brew install poppler                 # MacOS
```

#### Step 8: Build Frontend Assets

```bash
npm run build
```

#### Step 9: Start Development Server

```bash
php artisan serve
```

#### Step 10: Access the Application

Open your browser and navigate to: `http://localhost:8000`

**Default Accounts:**
- Register a new account, or
- Use Guest Mode (browse without account)

---

### 📱 Mobile / APK Deployment

To generate an Android APK:

#### Option 1: Native WebView (Recommended)
1. Use **Laravel PWA** package for offline capabilities
2. Use **WebView Gold** or **Capacitor** to wrap the web app
3. Generate APK using Android Studio

#### Option 2: Direct Mobile Access
Simply access `http://your-server-ip:8000` from any mobile browser
- The UI is fully responsive
- Voice input works on mobile browsers

---

### 🧪 Testing Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin User | alther@gmail.com | alther123 |
| Test User | try@gmail.com | (register new) |
| Guest | N/A | Click "Browse as Guest" |

---

### 📁 Project Structure Highlights

```
eli-app/
├── app/
│   ├── Http/Controllers/
│   │   ├── AIController.php          # AI chat endpoints
│   │   ├── LessonController.php      # Lesson CRUD + progress
│   │   ├── FlashcardController.php   # Flashcard generation + mastery
│   │   └── QuizController.php        # Quiz generation + taking
│   ├── Models/
│   │   ├── User.php                  # Authentication
│   │   ├── Lesson.php                # Lessons with relationships
│   │   ├── LessonContent.php         # Lesson sections
│   │   ├── LessonUserProgress.php    # Progress tracking
│   │   ├── LessonBookmark.php        # Bookmark storage
│   │   ├── LessonNote.php            # Note storage
│   │   ├── Flashcard.php             # Flashcards
│   │   ├── FlashcardMastery.php      # Mastery levels
│   │   ├── Quiz.php                  # Quizzes
│   │   └── QuizQuestion.php          # Quiz questions
│   └── Services/
│       ├── GeminiService.php         # AI API wrapper with fallback
│       ├── GeminiLMSService.php      # Flashcard/Quiz generation
│       └── TextExtractorService.php  # PDF/DOCX extraction
├── database/migrations/              # 20+ tables including:
│   ├── 2026_05_20_124638_create_lessons_table.php
│   ├── 2026_05_20_124639_create_lesson_contents_table.php
│   ├── 2026_05_20_124640_create_flashcards_table.php
│   ├── 2026_05_22_063655_create_flashcard_mastery_table.php
│   ├── 2026_05_23_000001_create_lesson_user_progress_table.php
│   ├── 2026_05_23_000002_create_lesson_notes_table.php
│   └── 2026_05_23_000003_create_lesson_bookmarks_table.php
└── resources/views/
    ├── flashcards/
    │   ├── index.blade.php          # Deck list (Yellow/Orange theme)
    │   ├── deck.blade.php           # Study interface
    │   └── generate.blade.php       # Upload + generate
    ├── lessons/
    │   ├── index.blade.php          # Lesson list (Green theme)
    │   ├── show.blade.php           # Lesson content + progress
    │   └── create.blade.php         # Create new lesson
    └── quizzes/
        ├── index.blade.php          # Quiz list (Purple theme - NEEDS FIX)
        ├── generate.blade.php       # Generate quiz (NEEDS FIX)
        └── take.blade.php           # Take quiz (NEEDS FIX)
```

---

### 🔧 Technologies Used

| Category | Technology |
|----------|------------|
| Backend Framework | Laravel 13.x |
| Frontend | Blade + Tailwind CSS |
| AI | Google Gemini API (gemini-2.5-flash) |
| Database | MySQL + Eloquent ORM |
| Authentication | Laravel Breeze + Custom Guest Mode |
| File Processing | pdftotext, ZipArchive |
| Icons | Google Material Symbols |
| Fonts | Consolas, System UI |
| Build Tools | Vite, NPM |
| Version Control | Git + GitHub |

---

### 📊 API Endpoints Summary

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/ask` | AI Q&A (ASK mode) |
| POST | `/summarize` | Text summarization |
| POST | `/eli5` | Simplify complex topics |
| POST | `/explain-code` | Code explanation |
| GET | `/history` | Get conversation history |
| GET | `/lms/flashcards` | List flashcard decks |
| POST | `/lms/flashcards/generate` | Generate from document |
| GET | `/lms/flashcards/deck/{id}` | View specific deck |
| POST | `/lms/flashcards/{id}/mastery` | Update mastery level |
| GET | `/lms/lessons` | List lessons |
| POST | `/lms/lessons` | Create lesson |
| GET | `/lms/lessons/{id}` | View lesson |
| PUT | `/lms/lessons/{id}` | Update lesson |
| DELETE | `/lms/lessons/{id}` | Delete lesson |
| PATCH | `/lms/lessons/{id}/bookmark` | Toggle bookmark |
| POST | `/lms/lessons/{id}/notes` | Save notes |
| DELETE | `/lms/lessons/{lessonId}/content/{contentId}` | Delete section |
| GET | `/lms/quizzes` | List quizzes |
| POST | `/lms/quizzes/generate` | Generate quiz from document |
| GET | `/lms/quizzes/{id}/take` | Take quiz |
| POST | `/lms/quizzes/{id}/submit` | Submit answers |
| GET | `/lms/quizzes/{id}/results` | View results (NEEDS FIX) |

---

### 🐛 Known Issues & Next Steps

#### 🔴 HIGH PRIORITY - Quiz Module

| Issue | Status | Expected Fix |
|-------|--------|--------------|
| Quiz generation uses pre-defined questions | ❌ Broken | Generate from uploaded document content |
| Text extraction not working properly | ❌ Broken | Fix pdftotext integration |
| Route [quizzes.results] not defined | ❌ Broken | Add missing route |
| Timer has no hide/show option | ❌ Missing | Add toggle button |
| Quiz UI doesn't match other modules | ❌ Missing | Redesign with Purple theme |

#### 🟡 MEDIUM PRIORITY

| Issue | Status |
|-------|--------|
| Flashcard decks appear in Lessons tab | ❌ Needs fix |
| Auth session persistence | ⚠️ Minor issues |
| AI conversation memory | ⚠️ Needs testing |

---

### 👥 Team Contributions

| Role | Member | Contributions |
|------|--------|---------------|
| Project Manager / Lead Developer | Alther | System architecture, Laravel backend, AI integration, Lessons module |
| UI/UX Designer / Frontend Developer | [Name] | Blade templates, Tailwind CSS, responsive design, Theme system |
| Database Designer | [Name] | Schema design, migrations, Eloquent relationships |
| Documentation Lead | [Name] | Technical documentation, README, user guide |

---

### 🔜 Future Improvements

- [ ] Complete Quiz Module with Purple theme
- [ ] Fix text extraction for all document types
- [ ] Add spaced repetition algorithm for flashcards
- [ ] Social sharing of quiz scores
- [ ] Email notifications for reminders
- [ ] Collaborative study groups
- [ ] Offline mode (PWA)
- [ ] Mobile native app (Flutter)
- [ ] Export flashcards to Anki
- [ ] AI-generated lesson plans
- [ ] Voice response (text-to-speech)
- [ ] RESTful PUT/PATCH endpoints for all resources

---

### 📄 License

This project is developed for **IT323 - Application Development and Emerging Technologies** as a final project requirement. All rights reserved.

---

### 🙏 Acknowledgments

- **Google Gemini AI** for providing the intelligence behind ELI
- **Laravel Community** for the amazing framework
- **Instructor** for guidance and feedback
- **Classmates** for testing and suggestions

---

### 📞 Contact

For questions or contributions:
- GitHub: [yourusername/eli-app](https://github.com/yourusername/eli-app)
- Email: [your.email@example.com]

---

### 📊 Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | May 20, 2026 | Initial release - Core AI features |
| 1.1.0 | May 21, 2026 | Authentication + Guest mode |
| 1.2.0 | May 22, 2026 | Flashcards module complete |
| 1.3.0 | May 23, 2026 | Lessons module complete (Green theme) |
| 1.4.0 | TBD | Quiz module overhaul (Purple theme) |

---

**Made with ❤️ for IT323 Final Project**

**Current Version:** 1.3.0
**Last Updated:** May 22, 2026
**Status:** Lessons ✅ | Flashcards ✅ | Quiz ⚠️ In Progress
```