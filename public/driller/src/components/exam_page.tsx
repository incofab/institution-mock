import React, { useEffect, useMemo, useState } from 'react';
import PageHeader from './header/PageHeader';
import FooterNav from './footer_nav';
import Calculator from './calculator/calculator';
import './App.css';
import { Exam, ExamCourse } from '../types/models';
import ExamUtil from '../util/exam/exam-util';
import { ExamAttempt, ExamUrl } from '../types/types';
import useWebForm from '../hooks/use-web-form';
import DisplayQuestion from './display_question';

export default function ExamPage({
  exam,
  existingAttempts,
  timeRemaining
}: {
  exam: Exam;
  existingAttempts: ExamAttempt;
  timeRemaining: number;
}) {
  const webForm = useWebForm({});
  const [key, setKey] = useState<string>('0');
  const [showCalculator, setShowCalculator] = useState<boolean>(false);
  function updateExamUtil() {
    setKey(Math.random() + '');
  }

  useEffect(() => {
    document.addEventListener('keyup', keyListener, false);
    return () => {
      document.removeEventListener('keyup', keyListener, false);
    };
  }, []);

  const examUtil = useMemo(() => {
    const examUtil = new ExamUtil(exam, existingAttempts, updateExamUtil);
    return examUtil;
  }, []);

  function previousClicked() {
    examUtil
      .getTabManager()
      .setCurrentQuestion(examUtil.getExamNavManager().getGoPreviousIndex());
  }

  function nextClicked() {
    examUtil
      .getTabManager()
      .setCurrentQuestion(examUtil.getExamNavManager().getGoNextIndex());
  }

  async function submitExam(showConfirmDialog = true) {
    if (
      showConfirmDialog &&
      !window.confirm('Do you want to submit your exam?')
    ) {
      return;
    }
    await webForm.submit((data, web) => {
      return web.post(ExamUrl.EndExam, {
        exam_no: exam.exam_no,
        event_id: exam.event_id
      });
    });
    window.location.href = `${ExamUrl.ExamLogin}`;
  }

  function keyListener(e: KeyboardEvent) {
    // console.log('Keylistener', e, e.key);
    const keyUpperCase = e.key.toUpperCase();
    switch (keyUpperCase) {
      case 'A':
      case 'B':
      case 'C':
      case 'D':
        examUtil
          .getAttemptManager()
          .setAttempt(
            examUtil.getTabManager().getCurrentQuestion()?.id,
            keyUpperCase
          );
        break;
      case 'P':
        previousClicked();
        break;
      case 'N':
        nextClicked();
        break;
      case 'S':
        submitExam();
        break;
      case 'R':
        // console.log('R clicked');
        break;
      default:
        break;
    }
  }

  return (
    <div key={key}>
      <PageHeader
        exam={exam}
        examUtil={examUtil}
        timeRemaining={timeRemaining}
        submitExam={submitExam}
        setShowCalculator={setShowCalculator}
      />
      <div
        className="container-fluid"
        style={{ marginTop: '85px' }}
        id="exam-base-container"
      >
        <ExamContent
          exam={exam}
          examUtil={examUtil}
          nextClicked={nextClicked}
          previousClicked={previousClicked}
          submitExam={submitExam}
        />
        {showCalculator && <Calculator setShowCalculator={setShowCalculator} />}
      </div>
    </div>
  );
}

function ExamContent({
  exam,
  examUtil,
  nextClicked,
  previousClicked,
  submitExam
}: {
  exam: Exam;
  examUtil: ExamUtil;
  nextClicked: () => void;
  previousClicked: () => void;
  submitExam: (showConfirmDialog: boolean) => void;
}) {
  const currentTabIndex = examUtil.getTabManager().getCurrentTabIndex();
  const activeExamCourse = exam.exam_courses![currentTabIndex];
  return (
    <div id="exam-layout">
      <nav id="exam-tabs" className="clearfix">
        <ul className="nav nav-tabs float-left" id="nav-tab" role="tablist">
          {exam.exam_courses!.map((examCourse, index) => {
            const tab = examUtil.getTabManager().getTab(index);
            examUtil.getTabManager().setTab(index, {
              currentQuestionIndex: tab?.currentQuestionIndex ?? 0,
              exam_course_id: examCourse.id
            });
            return (
              <_ExamTab
                examCourse={examCourse}
                index={index}
                examUtil={examUtil}
                key={examCourse.id}
              />
            );
          })}
        </ul>
      </nav>
      <ExamCourseComponent
        exam={exam}
        examCourse={activeExamCourse}
        examUtil={examUtil}
        key={activeExamCourse.id}
      />
      <FooterNav
        nextClicked={nextClicked}
        previousClicked={previousClicked}
        submitExam={submitExam}
      />
    </div>
  );
}

function _ExamTab({
  examCourse,
  index,
  examUtil
}: {
  examCourse: ExamCourse;
  index: number;
  examUtil: ExamUtil;
}) {
  const currentTabIndex = examUtil.getTabManager().getCurrentTabIndex();
  return (
    <li className="nav-item" key={'exam-tab-' + index}>
      <div
        className={`nav-link text-primary cursor-pointer ${currentTabIndex === index ? 'active' : ''}`}
        data-tab_index={index}
        data-toggle="tab"
        id={'#nav-' + examCourse.id}
        role="tab"
        onClick={() => examUtil.getTabManager().setCurrentTabIndex(index)}
      >
        {examCourse.course_session!.course!.course_code}
      </div>
    </li>
  );
}

function ExamCourseComponent({
  exam,
  examCourse,
  examUtil
}: {
  exam: Exam;
  examCourse: ExamCourse;
  examUtil: ExamUtil;
}) {
  const questionIndex = examUtil.getTabManager().getCurrentQuestionIndex();
  const attemptManager = examUtil.getAttemptManager();

  const tiles = examCourse.course_session!.questions!.map((question, index) => {
    return (
      <div
        data-question_no={question.question_no}
        data-question_id={question.id}
        data-question_index={index}
        className={`${questionIndex === index ? 'current' : ''} ${attemptManager.isAttempted(question.id) ? 'attempted' : ''} question-tile`}
        key={'tile-' + question.id}
        onClick={() => examUtil.getTabManager().setCurrentQuestion(index)}
      >
        {index + 1}
      </div>
    );
  });

  return (
    <div
      // className={`tab-pane fade show ${currentTabIndex === index ? 'active' : ''}`}
      id={'nav-' + examCourse.id}
      role="tabpanel"
      key={`question-${examCourse.id}`}
    >
      <div className="question-main px-1 px-md-3">
        <DisplayQuestion
          questionIndex={questionIndex}
          exam={exam}
          examCourse={examCourse}
          examUtil={examUtil}
        />
      </div>
      <br />
      <div className="text-center">{tiles}</div>
    </div>
  );
}
