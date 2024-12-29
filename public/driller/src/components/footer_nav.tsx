import React from 'react';

export default function FooterNav({
  nextClicked,
  previousClicked,
  submitExam
}: {
  nextClicked: () => void;
  previousClicked: () => void;
  submitExam: (showConfirmDialog: boolean) => void;
}) {
  return (
    <div className="question-nav text-center d-flex justify-content-between align-items-center py-2 px-2">
      <button
        className="btn btn-primary"
        id="previous-question"
        onClick={previousClicked}
        style={{ width: '100px' }}
      >
        &laquo;&nbsp;Previous
      </button>
      <button
        className="btn btn-primary mx-auto px-3"
        id="stop-exam"
        onClick={() => submitExam(true)}
        data-toggle="tooltip"
        data-placement="top"
        title="Submit and end this exam. Cannot be resumed"
      >
        <i className="fa fa-paper-plane"></i> Submit
      </button>
      <button
        className="btn btn-primary"
        id="next-question"
        onClick={nextClicked}
        style={{ width: '100px' }}
      >
        Next&nbsp;&raquo;
      </button>
    </div>
  );
}
