<?php

$reactFolder = 'build';

// $htmlFilename = "../public/examiner/$reactFolder/index.html";
$htmlFilename = public_path("driller/$reactFolder/index.html");

$dom = new \DOMDocument();
$dom->loadHTMLFile($htmlFilename);

$scriptNode = $dom->createElement('script');
$scriptNode->appendChild(
  new \DOMText(
    'window.dev = ' .
      json_encode(config('app.debug')) .
      '; ' .
      'window.exam = ' .
      json_encode($exam) .
      '; ' .
      'window.exam_track = ' .
      json_encode($exam_track) .
      '; ',
    'window.timeRemaining = ' . json_encode($timeRemaining) . '; ',
    'window.baseUrl = ' . json_encode($baseUrl) . '; ',
  ),
);

//     $body = $dom->getElementsByTagName('body')->item(0);
$body = $dom->getElementById('startup-record');
$body->appendChild($scriptNode);
$finalHTML = $dom->saveHTML();

//     dlog($final);
//     dDie($final);
//     $finalHTML = file_get_contents($htmlFilename);

echo $finalHTML;
