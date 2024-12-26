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
  let questionNo = questionIndex + 1;
  let courseSession = examCourse.course_session!;
  let question = courseSession.questions![questionIndex];
  let questionImageHandler = new QuestionImageHandler(exam, courseSession);

  return (
    <div className="question-main">
      <div className="tile text-center p-1 mb-3">
        <div className="tile-title question-no mb-0 shadow py-1">
          Question {questionNo} of {courseSession.questions!.length}
        </div>
      </div>

      <div className="instruction">
        {getInstruction(courseSession, questionNo)}
      </div>
      <div className="passage">{getPassage(courseSession, questionNo)}</div>
      <div
        className="question-text"
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
      className="animated-radio-button option pointer"
      onClick={() =>
        examUtil.getAttemptManager().setAttempt(question.id, optionLetter)
      }
    >
      <label className="selection">
        <span className="option-letter">{optionLetter})</span>
        <input
          type="radio"
          name="option"
          checked={
            examUtil.getAttemptManager().getAttempt(question.id) ===
            optionLetter
          }
          data-selection={optionLetter}
          onChange={() => {}}
        />
        <span className="label-text">
          <span
            className="option-text"
            dangerouslySetInnerHTML={{ __html: optionText }}
          ></span>
        </span>
      </label>
    </div>
  );
}

function getInstruction(
  courseSession: CourseSession,
  questionNo: number
): string {
  const instructions = courseSession.instructions || [];
  var instructionsStr = '';
  for (const instruction of instructions) {
    if (hasInstruction(instruction, questionNo)) {
      instructionsStr += `${instruction.instruction}<br>`;
    }
  }
  return instructionsStr;
}

function getPassage(courseSession: CourseSession, questionNo: number): string {
  const passages = courseSession.passages || [];
  var passagesStr = '';
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
