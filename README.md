ELI - Explain Like I'm 5
An AI-Powered Learning Management System
📌 About The System
ELI (Explain Like I'm 5) is an innovative, AI-powered Learning Management System (LMS) designed to make complex topics simple and accessible. Built as a mini-capstone project for IT323 - Application Development and Emerging Technologies, ELI demonstrates the practical integration of cutting-edge technologies to solve real-world educational challenges.

The system leverages Google's Gemini AI to provide intelligent tutoring, automated flashcard generation, dynamic quiz creation, and personalized learning experiences. Whether you're a student struggling with difficult concepts, a teacher looking for supplementary materials, or a self-learner exploring new topics, ELI adapts to your needs.

✨ What Makes ELI Innovative?
ELI successfully integrates multiple emerging technologies as required by the IT323 Final Project:

Emerging Technology	Implementation in ELI	Status
Artificial Intelligence (AI)	Google Gemini API for intelligent responses, flashcard generation, and quiz creation	✅ Complete
Machine Learning	AI-powered content analysis and question generation	✅ Complete
Cloud Computing	Laravel Forge-ready deployment, cloud storage for uploads	✅ Complete
Speech Recognition	Voice input for hands-free interaction	✅ Complete
Data Visualization	Progress tracking, mastery statistics, learning analytics	✅ Complete
API Integration	Google Gemini API, RESTful API architecture	✅ Complete
Chatbot / AI Assistant	4 AI modes: ASK, SUMMARIZE, ELI5, CODE	✅ Complete
Automation Tools	Automated flashcard/quiz generation from uploaded documents	✅ Complete
🎯 How ELI Addresses Real-World Problems
Problem	ELI's Solution
Complex topics are hard to understand	ELI5 mode explains anything like you're 5 years old
Students struggle with active recall	AI-generated flashcards with spaced repetition
Manual quiz creation is time-consuming	Instant AI-generated quizzes from any document
No personalized learning	User isolation ensures each learner has their own data
Accessibility barriers	Voice input and dark/light mode support
No progress tracking	Mastery tracking, completion statistics, learning streaks
🏗️ System Architecture
text
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
✅ Accomplished Features
🔐 Authentication & User Management
✅ User registration and login with validation

✅ Guest mode (browse without account)

✅ Password reset functionality

✅ User isolation (each user sees only their own data)

✅ Session management with Laravel

🤖 AI Assistant (4 Modes)
✅ ASK - General Q&A with Gemini AI

✅ SUMMARIZE - Condense long texts into key points

✅ ELI5 - Explain complex topics simply

✅ CODE - Code explanation and debugging

✅ Conversation memory (remembers context)

✅ Voice input support

✅ Export chat history

📚 Flashcards System
✅ Upload PDF/DOCX/TXT files for flashcard generation

✅ AI extracts key concepts and generates Q&A pairs

✅ Deck-based organization (each document = one deck)

✅ Flip card interaction with smooth animation

✅ Easy/Medium/Hard difficulty rating

✅ Mastery tracking (new → learning → mastered)

✅ Session completion modal with statistics:

Total cards studied

Mastered/Learning/Review counts

Accuracy percentage

Time spent

✅ Shuffle cards functionality

✅ Keyboard navigation (← → Space)

✅ Delete individual cards or entire decks

✅ Progress bar per deck

📖 Lessons System
✅ Create, read, update, delete lessons

✅ Add text, video, file, or link content

✅ Track lesson progress

✅ Bookmark important lessons

✅ Add personal notes

✅ Share lesson links

✅ Download as PDF

🎯 Quizzes System
✅ Generate quizzes from uploaded documents

✅ Multiple choice questions

✅ Timer support

✅ Score tracking

✅ Retake option

✅ Results page with detailed feedback

🎨 UI/UX Excellence
✅ Dark/Light theme toggle (persists across sessions)

✅ Consolas monospace font for developer aesthetic

✅ Material Icons (no emojis)

✅ Collapsible sidebar

✅ Responsive design (mobile-ready)

✅ Toast notifications

✅ Loading animations

✅ Keyboard shortcuts

📋 Project Requirements Met (IT323)
Requirement	Status
Functional application prototype	✅ Complete
Research-style documentation	✅ In progress
Integration of emerging technologies (AI)	✅ Complete
Team collaboration and project management	✅ Complete
Professional documentation	✅ In progress
Oral presentation and system demonstration	📅 Scheduled
GitHub repository with source code	✅ Complete
Contribution matrix	✅ Complete
🚀 How to Install and Run ELI
Prerequisites
Requirement	Version
PHP	8.5 or higher
Composer	Latest
MySQL	5.7 or higher / 8.0
Node.js	18.x or higher (for Vite)
NPM	9.x or higher
XAMPP / WAMP / Laragon (for local development)	Any
Step 1: Clone the Repository
bash
git clone https://github.com/yourusername/eli-app.git
cd eli-app
Step 2: Install PHP Dependencies
bash
composer install
Step 3: Install Frontend Dependencies
bash
npm install
Step 4: Environment Configuration
bash
cp .env.example .env
Edit .env and configure:

text
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
Step 5: Generate Application Key
bash
php artisan key:generate
Step 6: Run Migrations
bash
php artisan migrate
Step 7: Install PDF Extraction Tool (pdftotext)
Windows:

Download Xpdf command line tools from: https://www.xpdfreader.com/download.html

Extract and copy pdftotext.exe to your project root or C:\Windows\System32

Linux/Mac:

bash
sudo apt-get install poppler-utils  # Ubuntu/Debian
brew install poppler                 # MacOS
Step 8: Build Frontend Assets
bash
npm run build
Step 9: Start Development Server
bash
php artisan serve
Step 10: Access the Application
Open your browser and navigate to: http://localhost:8000

Default Accounts:

Register a new account, or

Use Guest Mode (browse without account)

📱 Mobile / APK Deployment
To generate an Android APK:

Option 1: Native WebView (Recommended)
Use Laravel PWA package for offline capabilities

Use WebView Gold or Capacitor to wrap the web app

Generate APK using Android Studio

Option 2: Direct Mobile Access
Simply access http://your-server-ip:8000 from any mobile browser

The UI is fully responsive

Voice input works on mobile browsers

🧪 Testing Credentials
Role	Email	Password
Admin User	alther@gmail.com	alther123
Test User	try@gmail.com	(register new)
Guest	N/A	Click "Browse as Guest"
📁 Project Structure Highlights
text
eli-app/
├── app/
│   ├── Http/Controllers/     # AIController, FlashcardController, etc.
│   ├── Models/               # User, Flashcard, Lesson, Quiz, etc.
│   ├── Services/             # GeminiService, TextExtractorService
│   └── Traits/               # GetCurrentUserId
├── database/migrations/      # 20+ tables including flashcard_mastery
├── resources/views/
│   ├── flashcards/           # index, deck, generate
│   ├── lessons/              # index, show, create
│   ├── quizzes/              # index, generate, take
│   └── layouts/              # app.blade.php (master layout)
└── routes/web.php            # All application routes
🔧 Technologies Used
Category	Technology
Backend Framework	Laravel 13.x
Frontend	Blade + Tailwind CSS
AI	Google Gemini API (gemini-2.5-flash)
Database	MySQL + Eloquent ORM
Authentication	Laravel Breeze + Custom Guest Mode
File Processing	pdftotext, ZipArchive
Icons	Google Material Symbols
Fonts	Consolas, System UI
Build Tools	Vite, NPM
Version Control	Git + GitHub
📊 API Endpoints Summary
Method	Endpoint	Description
POST	/ask	AI Q&A (ASK mode)
POST	/summarize	Text summarization
POST	/eli5	Simplify complex topics
POST	/explain-code	Code explanation
GET	/history	Get conversation history
GET	/lms/flashcards	List flashcard decks
POST	/lms/flashcards/generate	Generate from document
GET	/lms/flashcards/deck/{id}	View specific deck
POST	/lms/flashcards/{id}/mastery	Update mastery level
GET	/lms/lessons	List lessons
POST	/lms/lessons	Create lesson
GET	/lms/quizzes	List quizzes
POST	/lms/quizzes/generate	Generate quiz from document
👥 Team Contributions
Role	Member	Contributions
Project Manager / Lead Developer	[Name]	System architecture, Laravel backend, AI integration
UI/UX Designer / Frontend Developer	[Name]	Blade templates, Tailwind CSS, responsive design
Database Designer	[Name]	Schema design, migrations, Eloquent relationships
Documentation Lead	[Name]	Technical documentation, README, user guide
🔜 Future Improvements
Spaced repetition algorithm for flashcards

Social sharing of quiz scores

Email notifications for reminders

Collaborative study groups

Offline mode (PWA)

Mobile native app (Flutter)

Export flashcards to Anki

AI-generated lesson plans

Voice response (text-to-speech)

📄 License
This project is developed for IT323 - Application Development and Emerging Technologies as a final project requirement. All rights reserved.

🙏 Acknowledgments
Google Gemini AI for providing the intelligence behind ELI

Laravel Community for the amazing framework

Instructor for guidance and feedback

Classmates for testing and suggestions

📞 Contact
For questions or contributions:

GitHub: yourusername/eli-app

Email: [your.email@example.com]

Made with ❤️ for IT323 Final Project