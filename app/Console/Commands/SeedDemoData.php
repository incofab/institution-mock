<?php

namespace App\Console\Commands;

use App\Enums\ContentSource;
use App\Enums\ExamStatus;
use App\Models\Course;
use App\Models\CourseSession;
use App\Models\Event;
use App\Models\EventCourse;
use App\Models\Exam;
use App\Models\ExamContent;
use App\Models\ExamCourse;
use App\Models\ExternalContent;
use App\Models\Grade;
use App\Models\Institution;
use App\Models\InstitutionUser;
use App\Models\Instruction;
use App\Models\Passage;
use App\Models\Question;
use App\Models\Student;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SeedDemoData extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:seed-demo-data
    {--fresh : Run migrate:fresh before seeding demo data}
    {--institutions=2 : Number of demo institutions to create}
    {--students=18 : Students to create for each institution}
    {--questions=12 : Questions to create for each course session}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Seed a realistic exam-management dataset for local testing';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    if ($this->option('fresh')) {
      $this->call('migrate:fresh', ['--force' => true]);
    }

    $admin = $this->seedAdmin();
    $institutions = max(1, intval($this->option('institutions')));
    $studentsPerInstitution = max(6, intval($this->option('students')));
    $questionsPerSession = max(5, intval($this->option('questions')));

    $this->line('Seeding demo users, institutions, content, students, events and exams...');

    for ($index = 1; $index <= $institutions; $index++) {
      $institution = $this->seedInstitution($admin, $index);
      $institutionUser = $this->seedInstitutionUser($institution, $index);
      $grades = $this->seedGrades($institution);
      $students = $this->seedStudents($institution, $grades, $studentsPerInstitution);
      $examContents = $this->seedExamContents($institution);
      $courseSessions = $this->seedCoursesAndQuestions(
        $institution,
        $examContents,
        $questionsPerSession,
      );
      $externalContent = $this->seedExternalContent($index, $courseSessions);
      $this->seedEventsAndExams($institution, $students, $courseSessions, $externalContent);

      $this->info(
        "Seeded {$institution->name} ({$institution->code}) for {$institutionUser->email}",
      );
    }

    $this->newLine();
    $this->info('Demo data seeded successfully.');
    $this->line('Admin login: ' . config('app.admin.email') . ' / password');
    $this->line('Institution user password: password');

    return self::SUCCESS;
  }

  protected function seedAdmin(): User
  {
    return User::query()->updateOrCreate(
      ['email' => config('app.admin.email')],
      [
        'name' => 'Admin Admin',
        'phone' => '09033229933',
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
      ],
    );
  }

  protected function seedInstitution(User $admin, int $index): Institution
  {
    $schools = [
      [
        'name' => 'Cedar Ridge International School',
        'address' => '18 Adebayo Doherty Road, Lekki Phase 1, Lagos',
        'phone' => '08061234567',
        'email' => 'registry@cedarridge.test',
      ],
      [
        'name' => 'Greenfield Science College',
        'address' => '42 Sani Abacha Way, GRA, Port Harcourt',
        'phone' => '08062345678',
        'email' => 'office@greenfieldscience.test',
      ],
      [
        'name' => 'Hillcrest Model Academy',
        'address' => '7 Ahmadu Bello Crescent, Wuse 2, Abuja',
        'phone' => '08063456789',
        'email' => 'admin@hillcrestmodel.test',
      ],
    ];
    $school = $schools[($index - 1) % count($schools)];

    return Institution::query()->updateOrCreate(
      ['code' => (string) (910000 + $index)],
      [
        'created_by_user_id' => $admin->id,
        'name' => $index > count($schools) ? $school['name'] . " {$index}" : $school['name'],
        'address' => $school['address'],
        'phone' => $school['phone'],
        'email' => $school['email'],
        'status' => 'active',
      ],
    );
  }

  protected function seedInstitutionUser(Institution $institution, int $index): User
  {
    $user = User::query()->updateOrCreate(
      ['email' => "institution{$index}@demo.test"],
      [
        'name' => "Institution Manager {$index}",
        'phone' => '081' . str_pad((string) $index, 8, '0', STR_PAD_LEFT),
        'email_verified_at' => now(),
        'password' => Hash::make('password'),
      ],
    );

    InstitutionUser::query()->updateOrCreate(
      [
        'institution_id' => $institution->id,
        'user_id' => $user->id,
      ],
      ['status' => 'active'],
    );

    return $user;
  }

  protected function seedGrades(Institution $institution): array
  {
    return collect([
      ['title' => 'JSS 1', 'description' => 'Junior secondary one candidates'],
      ['title' => 'JSS 2', 'description' => 'Junior secondary two candidates'],
      ['title' => 'SS 1', 'description' => 'Senior secondary one candidates'],
      ['title' => 'SS 2', 'description' => 'Senior secondary two candidates'],
      ['title' => 'SS 3', 'description' => 'Final year senior secondary candidates'],
    ])
      ->map(
        fn($grade) => Grade::query()->updateOrCreate(
          [
            'institution_id' => $institution->id,
            'title' => $grade['title'],
          ],
          ['description' => $grade['description']],
        ),
      )
      ->all();
  }

  protected function seedStudents(
    Institution $institution,
    array $grades,
    int $studentsPerInstitution,
  ): array {
    $firstNames = [
      'Aisha',
      'Chinedu',
      'Tomiwa',
      'Mariam',
      'Ifeanyi',
      'Sade',
      'David',
      'Zainab',
      'Kelechi',
      'Boluwatife',
      'Fatima',
      'Daniel',
      'Nkechi',
      'Tunde',
      'Rukayat',
      'Emeka',
      'Grace',
      'Abdul',
    ];
    $lastNames = [
      'Okafor',
      'Balogun',
      'Ibrahim',
      'Adeyemi',
      'Eze',
      'Mohammed',
      'Ogunleye',
      'Nwachukwu',
      'Usman',
      'Afolabi',
      'Okoro',
      'Yakubu',
      'Adebayo',
      'Nwosu',
      'Lawal',
      'Ekanem',
      'Onyeka',
      'Salami',
    ];

    return collect(range(1, $studentsPerInstitution))
      ->map(function ($studentIndex) use ($institution, $grades, $firstNames, $lastNames) {
        $grade = $grades[($studentIndex - 1) % count($grades)];
        $studentCode = 'S-' . $institution->code . str_pad(
          (string) $studentIndex,
          3,
          '0',
          STR_PAD_LEFT,
        );

        return Student::query()->updateOrCreate(
          ['code' => $studentCode],
          [
            'institution_id' => $institution->id,
            'grade_id' => $grade->id,
            'firstname' => $firstNames[($studentIndex - 1) % count($firstNames)],
            'lastname' => $lastNames[($studentIndex - 1) % count($lastNames)],
          ],
        );
      })
      ->all();
  }

  protected function seedExamContents(Institution $institution): array
  {
    $contents = [
      [
        'exam_name' => "Demo WAEC {$institution->code}",
        'fullname' => 'West African Senior School Certificate Examination',
        'description' => 'Mock WASSCE content for senior school examination practice.',
      ],
      [
        'exam_name' => "Demo BECE {$institution->code}",
        'fullname' => 'Basic Education Certificate Examination',
        'description' => 'Mock BECE content for junior secondary assessment.',
      ],
    ];

    return collect($contents)
      ->map(
        fn($content) => ExamContent::query()->updateOrCreate(
          [
            'institution_id' => $institution->id,
            'exam_name' => $content['exam_name'],
          ],
          [
            'fullname' => $content['fullname'],
            'description' => $content['description'],
            'is_file_content_uploaded' => false,
          ],
        ),
      )
      ->all();
  }

  protected function seedCoursesAndQuestions(
    Institution $institution,
    array $examContents,
    int $questionsPerSession,
  ): array {
    $subjects = [
      ['code' => 'ENG', 'title' => 'English Language', 'category' => 'Core'],
      ['code' => 'MTH', 'title' => 'Mathematics', 'category' => 'Core'],
      ['code' => 'BIO', 'title' => 'Biology', 'category' => 'Science'],
      ['code' => 'ECO', 'title' => 'Economics', 'category' => 'Commercial'],
      ['code' => 'GOV', 'title' => 'Government', 'category' => 'Arts'],
    ];
    $sessions = ['2023', '2024', '2025 Mock'];
    $courseSessions = [];

    foreach ($examContents as $contentIndex => $examContent) {
      foreach ($subjects as $subjectIndex => $subject) {
        $course = Course::query()->updateOrCreate(
          [
            'institution_id' => $institution->id,
            'exam_content_id' => $examContent->id,
            'course_code' => $subject['code'],
          ],
          [
            'category' => $subject['category'],
            'course_title' => $subject['title'],
            'description' => "Practice questions and marking guide for {$subject['title']}.",
            'order' => ($contentIndex * 100) + $subjectIndex + 1,
            'is_file_content_uploaded' => false,
          ],
        );

        foreach ($sessions as $session) {
          $courseSession = CourseSession::query()->updateOrCreate(
            [
              'course_id' => $course->id,
              'session' => $session,
            ],
            [
              'category' => $subject['category'],
              'general_instructions' =>
                'Answer every question. Choose the option that best completes each item.',
              'file_version' => 1,
            ],
          );

          $this->seedSessionContent($courseSession, $subject['title'], $questionsPerSession);
          $courseSessions[] = $courseSession->fresh(['course', 'questions', 'instructions', 'passages']);
        }
      }
    }

    return $courseSessions;
  }

  protected function seedSessionContent(
    CourseSession $courseSession,
    string $courseTitle,
    int $questionsPerSession,
  ): void {
    Instruction::query()->updateOrCreate(
      [
        'course_session_id' => $courseSession->id,
        'from' => 1,
        'to' => min(5, $questionsPerSession),
      ],
      [
        'instruction' =>
          'Use the information provided and answer questions 1 to ' .
          min(5, $questionsPerSession) .
          '.',
      ],
    );

    Passage::query()->updateOrCreate(
      [
        'course_session_id' => $courseSession->id,
        'from' => 1,
        'to' => min(5, $questionsPerSession),
      ],
      [
        'passage' =>
          "A school assessment committee reviewed the {$courseTitle} mock results and asked candidates to explain the key lesson from the report.",
      ],
    );

    foreach (range(1, $questionsPerSession) as $questionNo) {
      $answer = ['A', 'B', 'C', 'D'][($questionNo - 1) % 4];
      Question::query()->updateOrCreate(
        [
          'course_session_id' => $courseSession->id,
          'question_no' => $questionNo,
        ],
        [
          'question' => "{$courseTitle} question {$questionNo}: which option best matches the stated objective?",
          'option_a' => 'It identifies the main idea clearly.',
          'option_b' => 'It ignores the available evidence.',
          'option_c' => 'It changes the subject of the question.',
          'option_d' => 'It repeats an unrelated example.',
          'option_e' => null,
          'answer' => $answer,
          'answer_meta' => "Correct option: {$answer}.",
        ],
      );
    }
  }

  protected function seedExternalContent(int $index, array $courseSessions): ExternalContent
  {
    $sampleSessions = collect($courseSessions)
      ->take(4)
      ->map(function (CourseSession $session) {
        return [
          'id' => $session->id,
          'course_id' => $session->course_id,
          'category' => $session->category,
          'session' => $session->session,
          'general_instructions' => $session->general_instructions,
          'course' => [
            'id' => $session->course->id,
            'course_code' => $session->course->course_code,
            'course_title' => $session->course->course_title,
          ],
          'questions' => $session->questions->take(5)->values()->toArray(),
          'instructions' => $session->instructions->values()->toArray(),
          'passages' => $session->passages->values()->toArray(),
        ];
      })
      ->values()
      ->all();

    return ExternalContent::query()->updateOrCreate(
      [
        'source' => ContentSource::Examscholars->value,
        'content_id' => 7000 + $index,
      ],
      [
        'name' => "External Demo Content {$index}",
        'exam_content' => [
          'exam_name' => "External Demo Mock {$index}",
          'fullname' => 'External Examination Content Snapshot',
          'description' => 'Sample external content imported for local integration testing.',
          'courses' => [
            [
              'course_code' => 'EXT',
              'course_title' => 'External Aptitude',
              'course_sessions' => $sampleSessions,
            ],
          ],
        ],
      ],
    );
  }

  protected function seedEventsAndExams(
    Institution $institution,
    array $students,
    array $courseSessions,
    ExternalContent $externalContent,
  ): void {
    $internalEvent = Event::query()->updateOrCreate(
      [
        'institution_id' => $institution->id,
        'title' => 'First Term CBT Mock',
      ],
      [
        'description' => 'Internal mock examination for active course sessions.',
        'duration' => 90,
        'status' => 'active',
        'external_content_id' => null,
        'external_event_courses' => null,
      ],
    );

    $selectedSessions = collect($courseSessions)->take(4)->values();
    foreach ($selectedSessions as $session) {
      EventCourse::query()->updateOrCreate(
        [
          'event_id' => $internalEvent->id,
          'course_session_id' => $session->id,
        ],
        [
          'status' => 'active',
          'num_of_questions' => $session->questions->count(),
        ],
      );
    }

    $this->seedExams($institution, $internalEvent, $students, $selectedSessions->all());

    Event::query()->updateOrCreate(
      [
        'institution_id' => $institution->id,
        'title' => 'External Content Practice',
      ],
      [
        'description' => 'Practice event backed by a seeded external content snapshot.',
        'duration' => 60,
        'status' => 'active',
        'external_content_id' => $externalContent->id,
        'external_event_courses' => collect($courseSessions)
          ->take(2)
          ->map(
            fn(CourseSession $session) => [
              'course_session_id' => $session->id,
              'status' => 'active',
              'num_of_questions' => $session->questions->count(),
              'course_session' => [
                'id' => $session->id,
                'course_id' => $session->course_id,
                'category' => $session->category,
                'session' => $session->session,
                'general_instructions' => $session->general_instructions,
                'course' => [
                  'id' => $session->course->id,
                  'course_code' => $session->course->course_code,
                  'course_title' => $session->course->course_title,
                ],
                'questions' => $session->questions->take(5)->values()->toArray(),
                'instructions' => $session->instructions->values()->toArray(),
                'passages' => $session->passages->values()->toArray(),
              ],
            ],
          )
          ->values()
          ->all(),
      ],
    );
  }

  protected function seedExams(
    Institution $institution,
    Event $event,
    array $students,
    array $courseSessions,
  ): void {
    foreach (array_slice($students, 0, min(10, count($students))) as $studentIndex => $student) {
      $status = [ExamStatus::Pending, ExamStatus::Active, ExamStatus::Ended][$studentIndex % 3];
      $startedAt = $status === ExamStatus::Pending ? null : now()->subMinutes(35 + $studentIndex);
      $endedAt = $status === ExamStatus::Ended ? now()->subMinutes($studentIndex + 3) : null;
      $totalQuestions = collect($courseSessions)->sum(fn($session) => $session->questions->count());
      $score = $status === ExamStatus::Ended ? max(1, $totalQuestions - ($studentIndex % 5)) : null;

      $exam = Exam::query()->updateOrCreate(
        [
          'institution_id' => $institution->id,
          'event_id' => $event->id,
          'student_id' => $student->id,
        ],
        [
          'exam_no' => date('Y') . $institution->code . str_pad(
            (string) ($studentIndex + 1),
            3,
            '0',
            STR_PAD_LEFT,
          ),
          'time_remaining' => $status === ExamStatus::Active ? 2700 : 0,
          'start_time' => $startedAt,
          'pause_time' => null,
          'end_time' => $endedAt,
          'score' => $score,
          'num_of_questions' => $status === ExamStatus::Ended ? $totalQuestions : null,
          'status' => $status->value,
          'attempts' =>
            $status === ExamStatus::Ended
              ? [['question_no' => 1, 'answer' => 'A', 'score' => 1]]
              : null,
        ],
      );

      foreach ($courseSessions as $session) {
        ExamCourse::query()->updateOrCreate(
          [
            'exam_id' => $exam->id,
            'course_session_id' => $session->id,
          ],
          [
            'score' => $status === ExamStatus::Ended ? max(0, $session->questions->count() - 1) : null,
            'num_of_questions' => $session->questions->count(),
            'status' => $status === ExamStatus::Ended ? 'ended' : 'active',
            'course_code' => $session->course->course_code,
            'session' => $session->session,
          ],
        );
      }
    }
  }
}