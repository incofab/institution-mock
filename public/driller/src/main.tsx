import React from 'react';
import { createRoot } from 'react-dom/client';
import ExamPage from './components/exam_page';
import { Exam } from './types/models';
import { ExamTrack } from './types/types';

// Access the global examData variable
declare global {
  interface Window {
    exam: Exam;
    exam_track: ExamTrack;
    timeRemaining: number;
    baseUrl: string;
  }
}

// document.addEventListener('DOMContentLoaded', () => {
//   const root = document.getElementById('exam-root');
//   if (root) {
//     ReactDOM.render(<ExamPage {...window.examData} />, root);
//   }
// });

document.addEventListener('DOMContentLoaded', () => {
  const rootElement = document.getElementById('exam-root');
  if (rootElement) {
    const root = createRoot(rootElement);
    root.render(
      <ExamPage
        exam={window.exam}
        existingAttempts={window.exam_track.attempts}
        timeRemaining={window.timeRemaining}
      />
    );
  }
});
