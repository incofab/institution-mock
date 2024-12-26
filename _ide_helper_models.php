<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel query()
 */
	class BaseModel extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $institution_id
 * @property int|null $exam_content_id
 * @property string $course_code
 * @property string|null $category
 * @property string|null $course_title
 * @property string|null $description
 * @property int $order
 * @property int $is_file_content_uploaded
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CourseSession> $courseSessions
 * @property-read int|null $course_sessions_count
 * @property-read \App\Models\Institution|null $institution
 * @method static \Database\Factories\CourseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Course forInstitution($institution)
 * @method static \Illuminate\Database\Eloquent\Builder|Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course query()
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCourseCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCourseTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereExamContentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereInstitutionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereIsFileContentUploaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereUpdatedAt($value)
 */
	class Course extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $course_id
 * @property string $session
 * @property string|null $category
 * @property string|null $general_instructions
 * @property int $file_version
 * @property string|null $file_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course $course
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Instruction> $instructions
 * @property-read int|null $instructions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Passage> $passages
 * @property-read int|null $passages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Question> $questions
 * @property-read int|null $questions_count
 * @method static \Database\Factories\CourseSessionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSession query()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSession whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSession whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSession whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSession whereFileVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSession whereGeneralInstructions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSession whereSession($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseSession whereUpdatedAt($value)
 */
	class CourseSession extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $institution_id
 * @property string $title
 * @property string|null $description
 * @property int $duration
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EventCourse> $eventCourses
 * @property-read int|null $event_courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Exam> $exams
 * @property-read int|null $exams_count
 * @property-read \App\Models\Institution $institution
 * @method static \Illuminate\Database\Eloquent\Builder|Event active()
 * @method static \Database\Factories\EventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Event forInstitution($institution)
 * @method static \Illuminate\Database\Eloquent\Builder|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereInstitutionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUpdatedAt($value)
 */
	class Event extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $event_id
 * @property int $course_session_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CourseSession $courseSession
 * @property-read \App\Models\Event $event
 * @method static \Database\Factories\EventCourseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|EventCourse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCourse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCourse query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventCourse whereCourseSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCourse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCourse whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCourse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCourse whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventCourse whereUpdatedAt($value)
 */
	class EventCourse extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $institution_id
 * @property int $event_id
 * @property string $exam_no
 * @property int $student_id
 * @property float $time_remaining
 * @property string|null $start_time
 * @property string|null $pause_time
 * @property string|null $end_time
 * @property int|null $score
 * @property int|null $num_of_questions
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Event $event
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExamCourse> $examCourses
 * @property-read int|null $exam_courses_count
 * @property-read \App\Models\Student $student
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\ExamFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Exam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam query()
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereExamNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereInstitutionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereNumOfQuestions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam wherePauseTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereTimeRemaining($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exam whereUpdatedAt($value)
 */
	class Exam extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @author Incofab
 * @property int $id
 * @property int|null $institution_id
 * @property string $exam_name
 * @property string|null $fullname
 * @property string|null $description
 * @property int $is_file_content_uploaded
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses
 * @property-read int|null $courses_count
 * @method static \Database\Factories\ExamContentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ExamContent forInstitution($institution)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamContent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamContent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamContent query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamContent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamContent whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamContent whereExamName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamContent whereFullname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamContent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamContent whereInstitutionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamContent whereIsFileContentUploaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamContent whereUpdatedAt($value)
 */
	class ExamContent extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $exam_id
 * @property int $course_session_id
 * @property int|null $score
 * @property int|null $num_of_questions
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CourseSession $courseSession
 * @property-read \App\Models\Exam $exam
 * @method static \Database\Factories\ExamCourseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ExamCourse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamCourse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamCourse query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExamCourse whereCourseSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamCourse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamCourse whereExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamCourse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamCourse whereNumOfQuestions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamCourse whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamCourse whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExamCourse whereUpdatedAt($value)
 */
	class ExamCourse extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $institution_id
 * @property string $title
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Institution $institution
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Student> $students
 * @property-read int|null $students_count
 * @method static \Database\Factories\GradeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Grade forInstitution($institution)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade query()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereInstitutionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereUpdatedAt($value)
 */
	class Grade extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $created_by_user_id
 * @property string $code
 * @property string $name
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $email
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses
 * @property-read int|null $courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $createdByUser
 * @property-read int|null $created_by_user_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Grade> $grades
 * @property-read int|null $grades_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InstitutionUser> $institutionUsers
 * @property-read int|null $institution_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Student> $students
 * @property-read int|null $students_count
 * @method static \Database\Factories\InstitutionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Institution newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Institution newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Institution query()
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereCreatedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereUpdatedAt($value)
 */
	class Institution extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $institution_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Institution $institution
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|InstitutionUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InstitutionUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InstitutionUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|InstitutionUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstitutionUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstitutionUser whereInstitutionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstitutionUser whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstitutionUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InstitutionUser whereUserId($value)
 */
	class InstitutionUser extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $course_session_id
 * @property string $instruction
 * @property int $from
 * @property int $to
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CourseSession|null $session
 * @method static \Illuminate\Database\Eloquent\Builder|Instruction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Instruction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Instruction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Instruction whereCourseSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instruction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instruction whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instruction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instruction whereInstruction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instruction whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Instruction whereUpdatedAt($value)
 */
	class Instruction extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $course_session_id
 * @property string $passage
 * @property int $from
 * @property int $to
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CourseSession|null $session
 * @method static \Illuminate\Database\Eloquent\Builder|Passage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Passage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Passage query()
 * @method static \Illuminate\Database\Eloquent\Builder|Passage whereCourseSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Passage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Passage whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Passage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Passage wherePassage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Passage whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Passage whereUpdatedAt($value)
 */
	class Passage extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $course_session_id
 * @property int|null $topic_id
 * @property int $question_no
 * @property string $question
 * @property string $option_a
 * @property string $option_b
 * @property string|null $option_c
 * @property string|null $option_d
 * @property string|null $option_e
 * @property string $answer
 * @property string|null $answer_meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CourseSession|null $session
 * @method static \Database\Factories\QuestionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Question newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Question newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Question query()
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereAnswerMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereCourseSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereOptionA($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereOptionB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereOptionC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereOptionD($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereOptionE($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereQuestionNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereTopicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Question whereUpdatedAt($value)
 */
	class Question extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $institution_id
 * @property int|null $grade_id
 * @property string $firstname
 * @property string $lastname
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Grade|null $grade
 * @property-read \App\Models\Institution $institution
 * @property-read mixed $name
 * @method static \Database\Factories\StudentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Student forInstitution($institution)
 * @method static \Illuminate\Database\Eloquent\Builder|Student newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Student newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Student query()
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereFirstname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereGradeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereInstitutionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereLastname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereUpdatedAt($value)
 */
	class Student extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @author Incofab
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $phone
 * @property string $username
 * @property float $balance
 * @property int $pin_balance
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

