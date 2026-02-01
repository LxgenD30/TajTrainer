# Database & Model Alignment Documentation

## ✅ All Models Match Database Tables

### Core Models (9 Total)

| Model | Database Table | Primary Key | Status | Notes |
|-------|---------------|-------------|---------|-------|
| Role.php | `role` | id | ✅ Active | User types (admin, teacher, student) |
| User.php | `users` | id | ✅ Active | Base authentication with role_id FK |
| Teacher.php | `teachers` | id | ✅ Active | Uses users.id as FK, has name/biodata/title |
| Student.php | `students` | id | ✅ Active | Uses users.id as FK, has name/biodata/current_level |
| Material.php | `materials` | material_id | ✅ Active | **Updated with is_public field** |
| Classroom.php | `classrooms` | id | ✅ Active | Has teacher_id FK, access_code for enrollment |
| Assignment.php | `assignments` | assignment_id | ✅ Active | Links material_id & class_id, includes Quran fields |
| AssignmentSubmission.php | `assignment_submissions` | id | ✅ Active | Uses audio_file_path, tajweed_analysis JSON |
| Score.php | `scores` | score_id | ✅ Active | Uses score field (not points_earned) |

### Tables Without Models (Using Direct DB Queries)

| Database Table | Access Method | Status | Notes |
|---------------|---------------|---------|-------|
| `enrollment` | Pivot relationship | ✅ Correct | Student::classrooms() & Classroom::students() |
| `practice_sessions` | DB::table() | ✅ Correct | StudentController uses direct DB insert |
| `password_reset_tokens` | Laravel Auth | ✅ Correct | Built-in password reset |
| `sessions` | Laravel Session | ✅ Correct | Built-in session management |
| `cache` / `cache_locks` | Laravel Cache | ✅ Correct | Built-in caching |
| `jobs` / `job_batches` / `failed_jobs` | Laravel Queue | ✅ Correct | Built-in job queue |

## 📋 Database Field Alignment

### Materials Table (FIXED ✅)
```php
material_id       // Primary key
title             // string, required
description       // text, nullable (was 'content')
file_path         // string, nullable
video_link        // string, nullable - YouTube or any video URL
thumbnail         // string, nullable (was 'thumbnail_path')
type              // enum: pdf/video/audio/document
is_public         // boolean, default true (NEWLY ADDED)
timestamps
```

### Assignment Submissions Table (FIXED ✅)
```php
id
assignment_id
student_id
audio_file_path   // string, nullable (was 'audio_path')
text_submission   // text, nullable (was 'submission_text')
transcription     // text, nullable
submitted_at
status            // enum: pending/graded/late
tajweed_analysis  // JSON, nullable
timestamps
```

### Scores Table (FIXED ✅)
```php
score_id
assignment_id
user_id
score             // integer (was 'points_earned')
feedback          // text, nullable
timestamps
```

### Enrollment Table (FIXED ✅)
```php
id
user_id           // FK to students.id
class_id          // FK to classrooms.id
date_joined       // date, required (was missing in some code)
timestamps
```

## ⚠️ Important Field Name Changes

### Old → New Field Names
- ❌ `content` → ✅ `description` (materials table)
- ❌ `thumbnail_path` → ✅ `thumbnail` (materials table)
- ❌ `audio_path` → ✅ `audio_file_path` (assignment_submissions)
- ❌ `submission_text` → ✅ `text_submission` (assignment_submissions)
- ❌ `points_earned` → ✅ `score` (scores table)
- ❌ `category` → ✅ Removed (not in materials table)

### Kept Fields
- ✅ `video_link` - Added back to materials table for YouTube/video URLs

### Removed Non-Existent Fields
- ❌ `tajweed_score` (assignment_submissions)
- ❌ `tajweed_grade` (assignment_submissions)
- ❌ `marks_obtained` (assignment_submissions)
- ❌ `teacher_feedback` (assignment_submissions)

## 🔧 Updated Controllers

### MaterialController.php
- ✅ store() - Uses description, thumbnail, is_public
- ✅ update() - Uses description, thumbnail, is_public
- ✅ Removed old video_link and category handling

### StudentController.php
- ✅ enrollClass() - Includes date_joined parameter
- ✅ storeSubmission() - Uses audio_file_path field
- ✅ submitPractice() - Inserts to practice_sessions table

### TeacherController.php
- ✅ updateGrade() - Uses score field instead of points_earned

## 📁 Consolidated Migrations (5 Files)

1. **0001_01_01_000000_create_base_tables.php**
   - role, users, password_reset_tokens, sessions
   - cache, cache_locks, jobs, job_batches, failed_jobs

2. **0001_01_01_000001_create_teachers_and_students_tables.php**
   - teachers, students

3. **0001_01_01_000002_create_classrooms_and_materials_tables.php**
   - materials (with is_public), classrooms, enrollment

4. **0001_01_01_000003_create_assignments_and_submissions_tables.php**
   - assignments, assignment_submissions, scores

5. **0001_01_01_000004_create_practice_sessions_table.php**
   - practice_sessions

## ✨ Materials Create/Edit Forms

### Create Form Fields
- Title (required)
- **Video link** (optional) - YouTube or any video URL ✅
- File upload (optional)
- Thumbnail upload (optional) - Auto-extracts from YouTube links
- **is_public checkbox** ✅ (newly added)

### Edit Form Fields
- All create fields +
- Shows current file/thumbnail/video link
- All create fields +
- Shows current file/thumbnail
- Replace option for file/thumbnail
- **is_public checkbox** ✅

## 🎯 Migration Status

Last migration: `php artisan migrate:fresh --seed`
- ✅ All 5 migrations executed successfully
- ✅ is_public field added to materials table
- ✅ RoleSeeder executed (3 roles created)
- ✅ MaterialSeeder executed

## 🚀 Next Steps

1. ✅ Database structure aligned with models
2. ✅ All field names updated across codebase
3. ✅ Materials CRUD fully functional with is_public
4. ✅ No unused models remaining
5. ⏳ Future: Add Python integration for real Tajweed analysis
6. ⏳ Future: Create PracticeSession model if needed for relationships

---
*Last Updated: December 2024*
*All models verified and aligned with consolidated database structure*
