import React, { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import ExamPage from './components/exam_page';
import { Exam } from './types/models';
import { ExamTrack, ExamUrl } from './types/types';
import useWebForm from './hooks/use-web-form';

// Access the global examData variable
// declare global {
//   interface Window {
//     exam: Exam;
//     exam_track: ExamTrack;
//     timeRemaining: number;
//     baseUrl: string;
//   }
// }

document.addEventListener('DOMContentLoaded', () => {
  const rootElement = document.getElementById('root');
  if (rootElement) {
    const root = createRoot(rootElement);
    root.render(
      <App />
      // <ExamPage
      //   exam={window.exam}
      //   existingAttempts={window.exam_track.attempts}
      //   timeRemaining={window.timeRemaining}
      // />
    );
  }
});

interface ExamProp {
  exam: Exam;
  exam_track: ExamTrack;
  timeRemaining: number;
}

function App() {
  const [examProp, setExamProp] = useState<ExamProp>(null);
  const [error, setError] = useState(null);
  const webForm = useWebForm({});

  useEffect(() => {
    const fetchExamData = async () => {
      // Extract `exam_no` from the URL
      const queryParams = new URLSearchParams(window.location.search);
      const examNo = queryParams.get('exam_no');
      const studentCode = queryParams.get('student_code');

      if (!examNo || !studentCode) {
        setError('Exam number is missing in the URL.');
        return;
      }

      const res = await webForm.submit((data, web) => {
        return web.post(ExamUrl.StartExam, {
          exam_no: examNo,
          student_code: studentCode
        });
      });

      if (!res || !res.ok) {
        setError(res.message ?? 'Error process request');
        return;
      }
      setExamProp(res.data);
    };

    fetchExamData();
  }, []);

  if (webForm.processing) {
    return (
      <div>
        <button disabled>Loading...</button>
      </div>
    );
  }

  if (error || !examProp) {
    return <div>Error: {error}</div>;
  }

  return (
    <ExamPage
      exam={examProp.exam}
      timeRemaining={examProp.timeRemaining}
      existingAttempts={examProp.exam_track.attempts}
    />
  );
}
