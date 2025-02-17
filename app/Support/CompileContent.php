<?php
namespace App\Support;

use App\Models\CourseSession;
use App\Models\Event;
use App\Models\EventCourse;
use File;
use Str;

class CompileContent
{
  private $mCrypt;

  private string $contentOutputFolder;
  private string $questionsOutputFolder;
  private string $imagesOutputFolder;

  function __construct(private Event $event)
  {
    $event->load('eventCourses.courseSession.course');
    $this->contentOutputFolder = self::getBaseFolder("event_{$event->id}");
    $this->questionsOutputFolder = $this->contentOutputFolder . 'questions/';
    $this->imagesOutputFolder = $this->contentOutputFolder . 'images/';
  }

  static function make(Event $event)
  {
    return new self($event);
  }

  static function isContentAvailable(Event $event)
  {
    return file_exists(self::getZipFilename("event_{$event->id}"));
  }

  static function getDownloadUrl(Event $event)
  {
    return self::getZipFilename("event_{$event->id}");
  }

  function delete()
  {
    $zipFilename = $this->getZipFilename("event_{$this->event->id}");

    if (file_exists($zipFilename)) {
      File::delete($zipFilename);
    }

    return successRes('Folder and its content deleted');
  }

  function compine($toEncrypt = false)
  {
    if (file_exists($this->contentOutputFolder)) {
      return failRes('Content already processed');
    }
    //         die('Dont run yet');
    ini_set('max_execution_time', 960);

    if (!file_exists($this->questionsOutputFolder)) {
      mkdir($this->questionsOutputFolder, 0777, true);
    }
    if (!file_exists($this->imagesOutputFolder)) {
      mkdir($this->imagesOutputFolder, 0777, true);
    }

    // $this->mCrypt = new MCrypt2();
    // $this->toEncrpt = $toEncrypt;
    // $questionHtml = '';
    $eventCourses = $this->event
      ->eventCourses()
      ->with(
        'courseSession.course',
        'courseSession.passages',
        'courseSession.instructions',
        'courseSession.questions',
      )
      ->get();

    $eventCourses->each(function (EventCourse $eventCourse) {
      $this->generateJSON($eventCourse->courseSession);
    });

    $eventCourses->each(function (EventCourse $eventCourse) {
      $questionHtml = $this->getQuestionHtml($eventCourse->courseSession);
      $srcAttributes = $this->getImageSrcAttributes($questionHtml, false, true);
      $this->compileImages($srcAttributes, $eventCourse->courseSession);
    });

    $zipFilename = $this->zipContent($this->contentOutputFolder);

    $ret['zip_filename'] = $zipFilename;
    return $ret;
  }

  private function getQuestionHtml(CourseSession $courseSession)
  {
    $html = '';
    foreach ($courseSession->questions as $key => $question) {
      $html .= "<div>{$question->question} {$question->option_a} {$question->option_b} {$question->option_c} {$question->option_d} {$question->option_e} {$question->answer_meta}</div>";
    }
    return $html;
  }

  private function generateJSON(CourseSession $courseSession)
  {
    $filename = "{$this->questionsOutputFolder}course_session_{$courseSession->id}.json";

    $this->saveContentInFile($filename, json_encode($courseSession), false);

    return successRes('Operation Complete');
  }

  private static function getOutputFolder(bool $forUrl = false)
  {
    $folder = 'output';
    return $forUrl ? asset($folder) . '/' : public_path("$folder/");
  }

  private static function getBaseFolder($examContentName)
  {
    $folder = self::filterFolderName($examContentName);

    return self::getOutputFolder() . "$folder/";
  }

  private static function filterFolderName($folderName)
  {
    return str_replace(['/', '.', ':', ','], ['_-_'], $folderName);
  }

  private static function getZipFilename($examContentName, bool $forUrl = false)
  {
    return self::getOutputFolder($forUrl) .
      Str::slug($examContentName) .
      '.zip';
  }

  private function saveContentInFile($filename, string $strContent, $toEncrypt)
  {
    if ($toEncrypt) {
      $strContent = $this->mCrypt->encrypt($strContent);
    }
    file_put_contents($filename, $strContent);
  }

  private function compileImages(
    array $imageSrcAttributes,
    CourseSession $courseSession,
  ) {
    foreach ($imageSrcAttributes as $imageSrc) {
      $filename = "session_{$courseSession->id}_" . basename($imageSrc);
      $this->downloadImage($imageSrc, $this->imagesOutputFolder . $filename);
    }
  }
  private function downloadImage($imageUrl, $savePath)
  {
    $imageData = @file_get_contents($imageUrl);
    if ($imageData === false) {
      return false;
    }
    return file_put_contents($savePath, $imageData) !== false;
  }

  private function getImageSrcAttributes(
    $html,
    $basenameOnly = true,
    $skipEmbeddedImages = false,
  ): array {
    if (empty($html)) {
      return [];
    }

    libxml_use_internal_errors(true);
    $dom = new \DOMDocument();
    $dom->loadHTML($html);

    $images = $dom->getElementsByTagName('img');

    $imageSrcAttributes = [];
    /** @var \DOMElement $image */
    foreach ($images as $image) {
      $src = $image->getAttribute('src');
      if (empty($src)) {
        continue;
      }
      if ($skipEmbeddedImages && str_contains($src, 'data:')) {
        continue;
      }
      $imageSrcAttributes[] = $basenameOnly ? basename($src) : $src;
    }

    return $imageSrcAttributes;
  }

  function zipContent(string $contentFolder)
  {
    $zipFilename = self::getZipFilename("event_{$this->event->id}");

    if (file_exists($zipFilename)) {
      unlink($zipFilename);
    }

    \App\Core\Helper::zipContent($contentFolder, $zipFilename);

    File::deleteDirectory($contentFolder);

    return $zipFilename;
  }
}
