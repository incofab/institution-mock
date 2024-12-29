import { Exam } from './models';

export interface ExamAttempt {
  [questionId: string | number]: string;
}

export interface ExamTrack extends Exam {
  attempts: ExamAttempt;
}

export const baseUrl = process.env.REACT_APP_BASE_URL;

export const ExamUrl = {
  EndExam: `${baseUrl}exam-route/end-exam.php`,
  AttemptQuestion: `${baseUrl}exam-route/attempt-question.php`,
  ExamLogin: `${baseUrl}exam/login`,
  StartExam: `${baseUrl}api/exam/start`
};
