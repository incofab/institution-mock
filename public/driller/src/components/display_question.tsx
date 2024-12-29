import React from 'react';
import {
  CourseSession,
  Exam,
  ExamCourse,
  Instruction,
  Passage,
  Question
} from '../types/models';
import ExamUtil from '../util/exam/exam-util';
import QuestionImageHandler from '../util/exam/question-image-handler';

export default function DisplayQuestion({
  exam,
  examUtil,
  examCourse,
  questionIndex
}: {
  exam: Exam;
  examUtil: ExamUtil;
  examCourse: ExamCourse;
  questionIndex: number;
}) {
  const questionNo = questionIndex + 1;
  const courseSession = examCourse.course_session!;
  const question = courseSession.questions![questionIndex];
  const questionImageHandler = new QuestionImageHandler(exam, courseSession);

  return (
    <div className="question-main">
      <div className="tile text-center p-1 mb-3">
        <div className="tile-title question-no mb-0 py-1">
          Question {questionNo} of {courseSession.questions!.length}
        </div>
      </div>

      <div className="instruction">
        {getInstruction(courseSession, questionNo)}
      </div>
      <div className="passage">{getPassage(courseSession, questionNo)}</div>
      <div
        className="question-text mb-2"
        dangerouslySetInnerHTML={{
          __html: questionImageHandler.handleImages(question.question)
        }}
      />

      <div className="options">
        {[
          {
            optionText: questionImageHandler.handleImages(question.option_a),
            optionLetter: 'A'
          },
          {
            optionText: questionImageHandler.handleImages(question.option_b),
            optionLetter: 'B'
          },
          {
            optionText: questionImageHandler.handleImages(question.option_c),
            optionLetter: 'C'
          },
          {
            optionText: questionImageHandler.handleImages(question.option_d),
            optionLetter: 'D'
          },
          {
            optionText: questionImageHandler.handleImages(question.option_e),
            optionLetter: 'E'
          }
        ].map((item) => (
          <DisplayOption
            key={item.optionLetter}
            optionText={item.optionText}
            optionLetter={item.optionLetter}
            examUtil={examUtil}
            question={question}
          />
        ))}
      </div>
    </div>
  );
}

function DisplayOption({
  optionLetter,
  optionText,
  examUtil,
  question
}: {
  optionLetter: string;
  optionText: string;
  examUtil: ExamUtil;
  question: Question;
}) {
  if (!optionText) return <></>;

  return (
    <div
      className="animated-radio-button option pointer mt-3"
      onClick={() =>
        examUtil.getAttemptManager().setAttempt(question.id, optionLetter)
      }
    >
      <label className="selection d-flex">
        <span className="option-letter" style={{ width: 'auto' }}>
          {optionLetter})
        </span>
        {/* <input
          type="radio"
          name="option"
          checked={
            examUtil.getAttemptManager().getAttempt(question.id) ===
            optionLetter
          }
          data-selection={optionLetter}
          onChange={() => {}}
          className="mx-2"
        /> */}
        <div
          className={`mx-2 check-icon ${
            examUtil.getAttemptManager().getAttempt(question.id) ===
            optionLetter
              ? 'checked'
              : 'unchecked'
          }`}
        ></div>
        <div className="label-text" style={{ flex: 1 }}>
          <span
            className="option-text"
            dangerouslySetInnerHTML={{ __html: optionText }}
          ></span>
        </div>
      </label>
    </div>
  );
}

function getInstruction(
  courseSession: CourseSession,
  questionNo: number
): string {
  const instructions = courseSession.instructions || [];
  let instructionsStr = '';
  for (const instruction of instructions) {
    if (hasInstruction(instruction, questionNo)) {
      instructionsStr += `${instruction.instruction}<br>`;
    }
  }
  return instructionsStr;
}

function getPassage(courseSession: CourseSession, questionNo: number): string {
  const passages = courseSession.passages || [];
  let passagesStr = '';
  for (const passage of passages) {
    if (hasPassage(passage, questionNo)) {
      passagesStr += `${passage.passage}<br>`;
    }
  }
  return passagesStr;
}

function hasInstruction(instruction: Instruction, questionNo: number) {
  return (
    questionNo >= (instruction.from ?? 0) && questionNo <= (instruction.to ?? 0)
  );
}

function hasPassage(passage: Passage, questionNo: number) {
  return questionNo >= (passage.from ?? 0) && questionNo <= (passage.to ?? 0);
}
