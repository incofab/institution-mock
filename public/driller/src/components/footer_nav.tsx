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
    <div className="question-nav text-center clearfix">
      <button
        className="btn btn-primary float-left"
        id="previous-question"
        onClick={previousClicked}
        style={{ width: '100px' }}
      >
        &laquo; Previous
      </button>
      <button
        className="btn btn-primary float-right"
        id="next-question"
        onClick={nextClicked}
        style={{ width: '100px' }}
      >
        Next &raquo;
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
    </div>
  );
}
