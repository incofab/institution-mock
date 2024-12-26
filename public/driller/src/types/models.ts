export interface Row {
  id: number;
  created_at?: string;
  upated_at?: string;
}

export interface Event extends Row {
  title: string;
  duration: number;
  status: string;
  event_courses?: EventCourse[];
}

export interface EventCourse extends Row {
  event_id: number;
  course_session_id: number;
  num_of_questions: number;
  status: string;
  course_session?: CourseSession;
}

export interface Student extends Row {
  firstname: string;
  lastname: string;
  name: string;
  code: string;
  grade_id: number;
}

export interface Exam extends Row {
  exam_no: string;
  event_id: number;
  student_id: number;
  status: string;
  exam_courses?: ExamCourse[];
  event?: Event;
  student?: Student;
}

export interface ExamCourse extends Row {
  exam_id: number;
  course_session_id: number;
  num_of_questions: number;
  score: number;
  status: string;
  course_session?: CourseSession;
}

export interface Course extends Row {
  course_code: string;
  course_title: string;
  course_session?: CourseSession;
}

export interface CourseSession extends Row {
  course_id: number;
  session: string;
  course?: Course;
  passages?: Passage[];
  instructions?: Instruction[];
  questions?: Question[];
}

export interface Passage extends Row {
  course_session_id: number;
  passage: string;
  from?: number;
  to?: number;
}

export interface Instruction extends Row {
  course_session_id: number;
  instruction: string;
  from?: number;
  to?: number;
}

export interface Question extends Row {
  course_session_id: number;
  question_no: number;
  question: string;
  option_a: string;
  option_b: string;
  option_c: string;
  option_d: string;
  option_e: string;
  answer: string;
}
