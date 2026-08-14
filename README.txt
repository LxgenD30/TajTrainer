============================================================
TAJTRAINER - README
============================================================

TajTrainer is a Quran Tajweed learning platform. Students record
their recitation and the system uses AI (Whisper + Python analysis)
to transcribe it, score Tajweed rules (Madd, Idgham), and generate
feedback. Teachers create classes/assignments and review grading.

Stack:
  - Backend  : Laravel 11 (PHP >= 8.2), MySQL
  - Frontend : Blade + Tailwind/Bootstrap, Vite
  - AI/Audio : Python (python/tajweed_analyzer.py),
               Tarteel Whisper (local) + OpenAI Whisper fallback,
               OpenAI GPT for feedback
  - Auth     : Email/password + Google OAuth

============================================================
1) HOW TO USE
============================================================

Two roles (users table, role_id):
  - Student  : role_id = 2
  - Teacher  : role_id = 3

Login / register: click Login / Register in the top-right (modals
on the landing page). Choose your role when registering.

TEACHER
  - Dashboard (/home): stats + Grading Queue (pending review submissions).
  - My Classes -> create a classroom (shows an Access Code for students).
  - New Assignment: pick Surah + verse range; the expected recitation
    and reference audio are fetched automatically from QuranCDN.
  - My Students: list enrolled students, view their submissions, grade.
  - Grade a submission: teacher/submission/{id}/grade (AI analysis,
    per-rule feedback, manual score override).

STUDENT
  - Dashboard (/home): classes, pending/completed assignments, avg score.
  - My Classes: join with the teacher's Access Code.
  - Assignments: record/upload your recitation -> auto analyzed.
  - Practice (/student/practice): free recitation practice with scoring.
  - Memorization (/student/memorization): pick a Surah and recite
    ayah-by-ayah; the system checks each word in real time.
  - Progress (/student/progress): overall accuracy, trend, focus areas,
    recurring errors.

============================================================
2) SETUP (LOCAL)
============================================================

Requirements: PHP 8.2+, Composer, Node.js, MySQL, Python 3, FFmpeg.

1. Clone the repo and install dependencies:
     composer install
     npm install

2. Copy the environment file and generate an app key:
     copy .env.example .env
     php artisan key:generate

3. Set your database in .env:
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=tajtrainer
     DB_USERNAME=root
     DB_PASSWORD=

4. Run migrations and create the storage link:
     php artisan migrate
     php artisan storage:link

5. Install Python dependencies (required for audio analysis):
     cd python
     pip install -r requirements.txt

6. Build frontend assets:
     npm run build        (or: npm run dev)

7. Start the local server:
     php artisan serve

8. Open http://127.0.0.1:8000

Note: The first audio analysis downloads the Tarteel Whisper model
(~290 MB). If it fails, the system automatically falls back to the
OpenAI Whisper API (OPENAI_API_KEY must be set).

============================================================
3) LINK THE SYSTEM (ENV CONFIGURATION)
============================================================

Edit .env with the following:

  - APP_URL
    Public URL, e.g. https://yourdomain.com

  - Database
    DB_DATABASE / DB_USERNAME / DB_PASSWORD (see Setup)

  - PYTHON_PATH
    Path to the Python interpreter that has the requirements.txt
    packages installed.
    Linux:  /usr/bin/python3
    Windows: C:\Python310\python.exe
    Leave empty to use the system python3.

  - FFMPEG_PATH
    Directory containing the ffmpeg binary (audio conversion).
    Leave empty to use system ffmpeg.

  - OPENAI_API_KEY
    OpenAI key. Used for:
      * Whisper transcription fallback (if local model fails)
      * AI-generated Tajweed feedback
    (Optional but recommended.)

  - GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET / GOOGLE_REDIRECT_URI
    Google OAuth for "Login with Google". Set up an OAuth app in
    Google Cloud Console. Redirect URI:
      {APP_URL}/auth/google/callback

  - TELEGRAM_BOT_TOKEN / TELEGRAM_BOT_USERNAME
    Optional Telegram bot integration.

Queue / scheduler: set QUEUE_CONNECTION=database. Assignments are
processed synchronously, so a queue worker is not strictly required.

============================================================
4) PROTOTYPE (FEATURES)
============================================================

  - Landing page with Login/Register modals
  - Teacher: classrooms, access codes, assignments, materials,
    student management, submission grading with AI feedback
  - Student: enroll by access code, submit voice recordings,
    practice page, memorization tracker (per-word checking),
    progress analytics (accuracy, trends, focus areas)
  - Tajweed analysis: Madd, Idgham Bila Ghunnah, Idgham Bi Ghunnah
    scored per rule with per-occurrence feedback
  - AI Assistant panel: summary, strengths, improvements,
    next steps (OpenAI)
  - Google login with automatic profile picture
  - Pause markers (۝) and diacritic-aware transcription display

============================================================
5) DATABASE
============================================================

MySQL database "tajtrainer" (see DB_* in .env).

Main tables:
  users                - login accounts (role_id: 2 student, 3 teacher)
  students             - student profile (id = users.id)
  teachers             - teacher profile (id = users.id)
  classrooms           - classes (teacher_id, access_code)
  enrollment           - student <-> classroom pivot
  assignments          - surah, start/end verse, due date, marks,
                         expected_recitation, reference_audio_url
  assignment_submissions - student submission, audio file,
                         transcription, tajweed_analysis (JSON), status
                         (submitted / pending_review / graded)
  scores               - final score + feedback per submission
  practice_sessions    - practice attempts (accuracy_score)
  tajweed_error_logs   - per-rule errors/correct entries
                         (practice_session_id / assignment_submission_id)
  materials, material_items - reference materials
  memorization_status  - surah/ayah memorization progress

Run migrations:  php artisan migrate

============================================================
6) RAW DATA
============================================================

Quran verses are fetched live from the QuranCDN API:
  https://api.qurancdn.com/api/qdc/...
  - text_uthmani (clean) and text_uthmani_tajweed (colored rules)
  - reference audio: https://verses.quran.com/Alafasy/mp3/{surah}{ayah}.mp3

Expected recitation + reference audio are stored on the assignment at
creation time (expected_recitation column, references/ storage dir).

User uploads / recordings are stored under:
  storage/app/public/submissions/...
Profile pictures: storage/app/public/profile_pictures/...

Audio processing pipeline:
  recording -> FFmpeg webm->wav -> librosa load ->
  Tarteel Whisper (local) or OpenAI Whisper -> normalization ->
  rule scoring vs expected text -> JSON stored in tajweed_analysis

============================================================
DONE - TajTrainer
============================================================
