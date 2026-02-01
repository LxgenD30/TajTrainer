<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TelegramUser;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Request as TelegramRequest;

class TelegramBotController extends Controller
{
    private $telegram;
    
    public function __construct()
    {
        try {
            $this->telegram = new Telegram(
                config('telegram.bot_token'),
                config('telegram.bot_username')
            );
        } catch (\Exception $e) {
            Log::error('Telegram Bot initialization failed: ' . $e->getMessage());
        }
    }

    public function webhook(Request $request)
    {
        try {
            $update = $request->all();
            Log::info('Telegram Webhook received', ['update' => $update]);

            if (!isset($update['message'])) {
                return response()->json(['status' => 'ok']);
            }

            $message = $update['message'];
            $chatId = $message['chat']['id'];
            $text = $message['text'] ?? '';
            $telegramId = (string)$message['from']['id'];
            $username = $message['from']['username'] ?? null;
            $firstName = $message['from']['first_name'] ?? null;
            $lastName = $message['from']['last_name'] ?? null;

            // Handle commands
            if (strpos($text, '/') === 0) {
                $command = explode(' ', $text)[0];
                $args = trim(str_replace($command, '', $text));

                switch ($command) {
                    case '/start':
                        $this->handleStart($chatId, $telegramId, $username, $firstName, $lastName);
                        break;
                    
                    case '/link':
                        $this->handleLink($chatId, $telegramId, $args, $username, $firstName, $lastName);
                        break;
                    
                    case '/unlink':
                        $this->handleUnlink($chatId, $telegramId);
                        break;
                    
                    case '/classSummary':
                    case '/classsummary':
                        $this->handleClassSummary($chatId, $telegramId);
                        break;
                    
                    case '/stats':
                        $this->handleStats($chatId, $telegramId, $args);
                        break;
                    
                    case '/materialsAdd':
                    case '/materialsadd':
                        $this->handleMaterialsAdd($chatId, $telegramId);
                        break;
                    
                    case '/help':
                        $this->handleHelp($chatId);
                        break;
                    
                    default:
                        $this->sendMessage($chatId, "Unknown command\. Type /help to see available commands\.");
                }
            }

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Telegram webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function handleStart($chatId, $telegramId, $username, $firstName, $lastName)
    {
        $telegramUser = TelegramUser::where('telegram_id', $telegramId)->first();

        if ($telegramUser) {
            $user = $telegramUser->user;
            $name = $this->escapeMarkdown($this->getUserName($user));
            $isTeacher = $user->role_id == 3;
            
            $message = "🌟 *Welcome back, {$name}\!* 🌟\n\n";
            $message .= "Your TajTrainer account is already linked\!\n\n";
            
            if ($isTeacher) {
                $message .= "👨‍🏫 *Teacher Commands:*\n";
                $message .= "/classSummary \- View all your classes with student averages\n";
                $message .= "/stats student\\_email \- View specific student statistics\n";
                $message .= "/materialsAdd \- Add new learning materials\n";
                $message .= "/help \- Show all commands";
            } else {
                $message .= "Available commands:\n";
                $message .= "/help \- Show all commands";
            }
        } else {
            $message = "🌙 *As\ssalamualaikum\!* 🌙\n\n";
            $message .= "Welcome to TajTrainer Teacher Bot\! 👨‍🏫📖\n\n";
            $message .= "To link your TajTrainer account, use:\n";
            $message .= "`/link your_email@example\\.com`\n\n";
            $message .= "Example:\n";
            $message .= "`/link teacher@gmail\\.com`\n\n";
            $message .= "Need help? Type /help";
        }

        $this->sendMessage($chatId, $message);
    }

    private function handleLink($chatId, $telegramId, $email, $username, $firstName, $lastName)
    {
        if (empty($email)) {
            $this->sendMessage($chatId, "❌ Please provide your email\.\n\nExample: `/link student@tajtrainer\\.com`");
            return;
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->sendMessage($chatId, "❌ No TajTrainer account found with email: " . $this->escapeMarkdown($email) . "\n\nPlease check your email or register on TajTrainer first\.");
            return;
        }

        // Check if already linked
        $existing = TelegramUser::where('telegram_id', $telegramId)->first();
        if ($existing) {
            $this->sendMessage($chatId, "⚠️ Your Telegram account is already linked to another TajTrainer account\.\n\nContact support if you need to change this\.");
            return;
        }

        // Link accounts
        TelegramUser::create([
            'user_id' => $user->id,
            'telegram_id' => $telegramId,
            'telegram_username' => $username,
            'telegram_first_name' => $firstName,
            'telegram_last_name' => $lastName,
            'notifications_enabled' => true,
            'last_interaction' => now(),
        ]);

        $name = $this->escapeMarkdown($this->getUserName($user));
        $message = "✅ *Account Linked Successfully\!*\n\n";
        $message .= "Welcome, *{$name}*\! 🎉\n\n";
        
        if ($user->role_id == 3) {
            $message .= "👨‍🏫 *Teacher Account Linked*\n\n";
            $message .= "You can now:\n";
            $message .= "/classSummary \- View class summaries\n";
            $message .= "/stats <email> \- Check student stats\n";
            $message .= "/materialsAdd \- Add materials\n";
            $message .= "/help \- Get help";
        } else {
            $message .= "You can now use:\n";
            $message .= "/help \- Get help";
        }

        $this->sendMessage($chatId, $message);
    }

    private function handleUnlink($chatId, $telegramId)
    {
        $telegramUser = TelegramUser::where('telegram_id', $telegramId)->first();

        if (!$telegramUser) {
            $this->sendMessage($chatId, "❌ You don't have a linked TajTrainer account\.\n\nUse `/link your_email@example\\.com` to link your account\.");
            return;
        }

        $user = $telegramUser->user;
        $name = $this->escapeMarkdown($this->getUserName($user));

        // Delete the link
        $telegramUser->delete();

        $message = "✅ *Account Unlinked Successfully\!*\n\n";
        $message .= "Your TajTrainer account \(" . $name . "\) has been unlinked from Telegram\.\n\n";
        $message .= "You will no longer receive notifications from TajTrainer\.\n\n";
        $message .= "To link again, use:\n";
        $message .= "`/link your_email@example\\.com`";

        $this->sendMessage($chatId, $message);
    }

    private function handleClassSummary($chatId, $telegramId)
    {
        $telegramUser = TelegramUser::where('telegram_id', $telegramId)->first();

        if (!$telegramUser) {
            $this->sendMessage($chatId, "❌ Please link your account first using `/link your_email@example\\.com`");
            return;
        }

        $user = $telegramUser->user;
        
        // Check if user is a teacher
        if ($user->role_id != 3) {
            $this->sendMessage($chatId, "⚠️ This command is only available for teachers\.");
            return;
        }

        // Get all classrooms for this teacher
        $classrooms = Classroom::where('teacher_id', $user->id)->get();

        if ($classrooms->isEmpty()) {
            $this->sendMessage($chatId, "📚 *Your Classes*\n\nYou don't have any classes yet\.\n\nCreate a class on TajTrainer to see them here\!");
            return;
        }

        $message = "📚 *Your Classes Summary*\n\n";
        
        foreach ($classrooms as $classroom) {
            // Get enrolled students count
            $studentsCount = DB::table('enrollment')
                ->where('class_id', $classroom->id)
                ->count();
            
            // Get average assignment score for this classroom
            $avgScore = DB::table('scores')
                ->join('assignments', 'scores.assignment_id', '=', 'assignments.assignment_id')
                ->where('assignments.class_id', $classroom->id)
                ->avg('scores.score');
            
            // Get practice average for enrolled students
            $studentIds = DB::table('enrollment')
                ->where('class_id', $classroom->id)
                ->pluck('user_id');
            
            $practiceAvg = 0;
            if ($studentIds->isNotEmpty()) {
                $practiceAvg = DB::table('practice_sessions')
                    ->whereIn('student_id', $studentIds)
                    ->avg('accuracy_score');
            }
            
            // Calculate overall average
            if ($avgScore && $practiceAvg) {
                $overallAvg = round(($avgScore + $practiceAvg) / 2, 1);
            } elseif ($avgScore) {
                $overallAvg = round($avgScore, 1);
            } elseif ($practiceAvg) {
                $overallAvg = round($practiceAvg, 1);
            } else {
                $overallAvg = 0;
            }
            
            $emoji = $overallAvg >= 80 ? '🟢' : ($overallAvg >= 60 ? '🟡' : '🔴');
            
            $message .= "{$emoji} *" . $this->escapeMarkdown($classroom->class_name) . "*\n";
            $message .= "   👥 Students: {$studentsCount}\n";
            $message .= "   📊 Average Score: " . $this->escapeMarkdown($overallAvg) . "%\n";
            $message .= "   🔑 Code: `" . $this->escapeMarkdown($classroom->access_code) . "`\n\n";
        }
        
        $message .= "💡 Use `/stats` to view  student list\\.\n";
        $message .= "💡 Use `/stats student@email\\.com` to view individual student details\\.";

        $this->sendMessage($chatId, $message);

        // Update last interaction
        $telegramUser->update(['last_interaction' => now()]);
    }

    private function handleStats($chatId, $telegramId, $studentEmail)
    {
        $telegramUser = TelegramUser::where('telegram_id', $telegramId)->first();

        if (!$telegramUser) {
            $this->sendMessage($chatId, "❌ Please link your account first using `/link your_email@example\\.com`");
            return;
        }

        $user = $telegramUser->user;
        
        // Check if user is a teacher
        if ($user->role_id != 3) {
            $this->sendMessage($chatId, "⚠️ This command is only available for teachers\\.");
            return;
        }

        if (empty($studentEmail)) {
            // List all students in teacher's classes
            $enrollments = DB::table('users')
                ->join('students', 'users.id', '=', 'students.id')
                ->join('enrollment', 'users.id', '=', 'enrollment.user_id')
                ->join('classrooms', 'enrollment.class_id', '=', 'classrooms.id')
                ->where('classrooms.teacher_id', $user->id)
                ->where('users.role_id', 2)
                ->select('users.id', 'users.email', 'students.name')
                ->distinct()
                ->get();

            if ($enrollments->isEmpty()) {
                $this->sendMessage($chatId, "👥 *Your Students*\n\nNo students enrolled in your classes yet\.\n\nShare your class codes with students\!");
                return;
            }

            $message = "👥 *Your Students*\n\n";
            $message .= "Use `/stats <email>` to view a student's details\\.\n\n";
            
            foreach ($enrollments as $index => $student) {
                // Get class names for this student
                $classNames = DB::table('classrooms')
                    ->join('enrollment', 'classrooms.id', '=', 'enrollment.class_id')
                    ->where('enrollment.user_id', $student->id)
                    ->where('classrooms.teacher_id', $user->id)
                    ->pluck('classrooms.class_name')
                    ->toArray();
                
                $classes = $this->escapeMarkdown(implode(', ', $classNames));
                
                $message .= "*" . ($index + 1) . "\\.* " . $this->escapeMarkdown($student->name) . "\n";
                $message .= "   📧 `" . $this->escapeMarkdown($student->email) . "`\n";
                $message .= "   📚 Classes: {$classes}\n\n";
            }

            $this->sendMessage($chatId, $message);
            return;
        }

        // Get specific student statistics
        $student = User::where('email', $studentEmail)->where('role_id', 2)->first();

        if (!$student) {
            $this->sendMessage($chatId, "❌ Student not found with email: " . $this->escapeMarkdown($studentEmail));
            return;
        }

        // Check if student is in teacher's class
        $isInClass = DB::table('enrollment')
            ->join('classrooms', 'enrollment.class_id', '=', 'classrooms.id')
            ->where('enrollment.user_id', $student->id)
            ->where('classrooms.teacher_id', $user->id)
            ->exists();

        if (!$isInClass) {
            $this->sendMessage($chatId, "⚠️ This student is not enrolled in any of your classes\.");
            return;
        }

        $studentName = $this->getUserName($student);

        // Get practice statistics
        $practiceCount = DB::table('practice_sessions')
            ->where('student_id', $student->id)
            ->count();

        $practiceAvg = DB::table('practice_sessions')
            ->where('student_id', $student->id)
            ->avg('accuracy_score');

        // Get assignment statistics
        $assignmentCount = DB::table('scores')
            ->where('user_id', $student->id)
            ->count();

        $assignmentAvg = DB::table('scores')
            ->where('user_id', $student->id)
            ->avg('score');

        // Get recent practice sessions
        $recentPractices = DB::table('practice_sessions')
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get common errors
        $commonErrors = DB::table('tajweed_error_logs')
            ->whereNotNull('practice_session_id')
            ->whereExists(function($query) use ($student) {
                $query->select(DB::raw(1))
                    ->from('practice_sessions')
                    ->whereColumn('practice_sessions.id', 'tajweed_error_logs.practice_session_id')
                    ->where('practice_sessions.student_id', $student->id);
            })
            ->select('error_type', DB::raw('count(*) as count'))
            ->groupBy('error_type')
            ->orderBy('count', 'desc')
            ->limit(3)
            ->get();

        // Escape function for MarkdownV2
        $escape = function($text) {
            return str_replace(
                ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'],
                ['\\_', '\\*', '\\[', '\\]', '\\(', '\\)', '\\~', '\\`', '\\>', '\\#', '\\+', '\\-', '\\=', '\\|', '\\{', '\\}', '\\.', '\\!'],
                (string)$text
            );
        };

        $message = "📊 *Student Statistics*\n\n";
        $message .= "👤 *Name:* " . $escape($studentName) . "\n";
        $message .= "📧 *Email:* " . $escape($studentEmail) . "\n\n";
        
        $message .= "🎯 *Practice Sessions*\n";
        $message .= "   Total: " . $escape($practiceCount) . "\n";
        $message .= "   Average: " . $escape(round($practiceAvg ?? 0, 1)) . "%\n\n";
        
        $message .= "📝 *Assignments*\n";
        $message .= "   Submitted: " . $escape($assignmentCount) . "\n";
        $message .= "   Average: " . $escape(round($assignmentAvg ?? 0, 1)) . "%\n\n";

        if (!$recentPractices->isEmpty()) {
            $message .= "📈 *Recent Practices*\n";
            foreach ($recentPractices as $practice) {
                $score = round($practice->accuracy_score, 1);
                $date = \Carbon\Carbon::parse($practice->created_at)->format('M d');
                $emoji = $score >= 80 ? '✅' : ($score >= 60 ? '⚠️' : '❌');
                $message .= "   " . $emoji . " " . $escape($score) . "% \\- " . $escape($date) . "\n";
            }
            $message .= "\n";
        }

        if (!$commonErrors->isEmpty()) {
            $message .= "⚠️ *Common Errors*\n";
            foreach ($commonErrors as $error) {
                $message .= "   • " . $escape($error->error_type) . ": " . $escape($error->count) . "x\n";
            }
        }

        $this->sendMessage($chatId, $message);

        // Update last interaction
        $telegramUser->update(['last_interaction' => now()]);
    }

    private function handleMaterialsAdd($chatId, $telegramId)
    {
        $telegramUser = TelegramUser::where('telegram_id', $telegramId)->first();

        if (!$telegramUser) {
            $this->sendMessage($chatId, "❌ Please link your account first using `/link your_email@example\\.com`");
            return;
        }

        $user = $telegramUser->user;
        
        // Check if user is a teacher
        if ($user->role_id != 3) {
            $this->sendMessage($chatId, "⚠️ This command is only available for teachers\\.");
            return;
        }

        $message = "📚 *Add Learning Materials*\n\n";
        $message .= "To add new learning materials, please visit:\n";
        $message .= "🔗 " . $this->escapeMarkdown(env('APP_URL')) . "/Materials/create\n\n";
        $message .= "You can upload:\n";
        $message .= "• 📄 PDF documents\n";
        $message .= "• 🎵 Audio files \(MP3\)\n";
        $message .= "• 🎥 Video files \(MP4\)\n";
        $message .= "• 🖼️ Images \(PNG, JPG\)\n\n";
        $message .= "Materials will be available to students in your classes\!";

        $this->sendMessage($chatId, $message);

        // Update last interaction
        $telegramUser->update(['last_interaction' => now()]);
    }

    private function handleHelp($chatId)
    {
        $message = "📚 *TajTrainer Teacher Bot Commands*\n\n";
        $message .= "*Account Management:*\n";
        $message .= "/start \- Start the bot\n";
        $message .= "/link \<email\> \- Link your TajTrainer account\n";
        $message .= "/unlink \- Unlink your TajTrainer account\n\n";
        
        $message .= "*Teacher Functions:*\n";
        $message .= "/classSummary \- View all your classes with student averages\n";
        $message .= "/stats \- List all your students\n";
        $message .= "/stats \<email\> \- View specific student statistics\n";
        $message .= "/materialsAdd \- Instructions to add learning materials\n\n";
        
        $message .= "*Help:*\n";
        $message .= "/help \- Show this help message\n\n";
        
        $message .= "👨‍🏫 Manage your classes and track student progress with TajTrainer\!";

        $this->sendMessage($chatId, $message);
    }

    private function sendMessage($chatId, $message)
    {
        $url = "https://api.telegram.org/bot" . config('telegram.bot_token') . "/sendMessage";
        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'MarkdownV2',
        ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        curl_close($ch);
        Log::info('Telegram message sent', ['chat_id' => $chatId, 'response' => $response]);
    }

    private function getUserName($user)
    {
        if ($user->role_id == 2) { // Student
            $student = Student::find($user->id);
            return $student->name ?? $user->email ?? 'Student';
        } elseif ($user->role_id == 3) { // Teacher
            $teacher = Teacher::find($user->id);
            return $teacher->name ?? $user->email ?? 'Teacher';
        }
        return $user->email ?? 'User';
    }

    private function escapeMarkdown($text)
    {
        return str_replace(
            ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'],
            ['\_', '\*', '\[', '\]', '\(', '\)', '\~', '\`', '\>', '\#', '\+', '\-', '\=', '\|', '\{', '\}', '\.', '\!'],
            (string)$text
        );
    }
}