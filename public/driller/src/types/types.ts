import { Exam } from './models';

export interface ExamAttempt {
  [questionId: string | number]: string;
}

export interface ExamTrack extends Exam {
  attempts: ExamAttempt;
}

export const ExamUrl = {
  EndExam: `${window.baseUrl}exam-route/end-exam.php`,
  AttemptQuestion: `${window.baseUrl}exam-route/attempt-question.php`,
  ExamLogin: `${window.baseUrl}exam/login`
};
