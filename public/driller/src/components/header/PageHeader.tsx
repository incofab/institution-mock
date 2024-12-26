import React, { useEffect, useState } from 'react';
import './header.css';
import ExamTimer from '../../util/exam/exam-timer';
import { formatTime } from '../../util/util';
import ExamUtil from '../../util/exam/exam-util';
import useWebForm from '../../hooks/use-web-form';
import { Exam } from '../../types/models';

export default function PageHeader({
  exam,
  timeRemaining,
  submitExam,
  // onIntervalPing,
  examUtil,
  setShowCalculator
}: {
  exam: Exam;
  timeRemaining: number;
  submitExam: (toConfirm: boolean) => void;
  // onIntervalPing: () => void;
  examUtil: ExamUtil;
  setShowCalculator: React.Dispatch<React.SetStateAction<boolean>>;
}) {
  const webForm = useWebForm({});

  function toggleCalculator() {
    setShowCalculator((showCalculator) => !showCalculator);
    // props.dispatch({
    //   type: K.ACTION_TOGGLE_CALCULATOR,
    //   payload: {
    //     show_calculator: !props.show_calculator
    //   }
    // });
  }

  async function onTimeElapsed() {
    await examUtil.getAttemptManager().sendAttempts(webForm);
    submitExam(false);
  }

  function onIntervalPing() {
    examUtil.getAttemptManager().sendAttempts(webForm);
  }

  return (
    <header className="app-header py-2">
      <div className="row w-100">
        <div className="col-7 text-truncate text-left">
          <div className="pl-1" id="name">
            {exam.student?.firstname} {exam.student?.lastname}
          </div>
        </div>
        <div className="col-5">
          <div className="pr-3 clearfix" style={{ textAlign: 'right' }}>
            <div className="float-right text-truncate">
              <i
                className="fa fa-calculator toggleCalculator pointer"
                onClick={toggleCalculator}
              ></i>
              <TimerView
                timeRemaining={timeRemaining}
                onIntervalPing={onIntervalPing}
                onTimeElapsed={onTimeElapsed}
              />
              {/* <i
                className={`fa fa-pause pointer ml-3 d-none`}
                onClick={pauseExam}
                id="pause-exam"
                data-toggle="tooltip"
                data-placement="left"
                title="Pause/Play this exam."
              ></i> */}
            </div>
          </div>
        </div>
      </div>
    </header>
  );
}

function TimerView({
  timeRemaining,
  onTimeElapsed,
  onIntervalPing
}: {
  timeRemaining: number;
  onTimeElapsed: () => void;
  onIntervalPing: () => void;
}) {
  const [timer, setTimer] = useState<string>('');

  useEffect(() => {
    const examTimer = new ExamTimer(onTimerTick, onTimeElapsed, onIntervalPing);
    examTimer.start(timeRemaining);
    return () => examTimer.stop();
  }, []);

  function onTimerTick(timeRemaining: number) {
    setTimer(formatTime(timeRemaining) + '');
  }
  return <div>{timer}</div>;
}
