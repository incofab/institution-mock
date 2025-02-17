<?php
namespace App\Support;

class ContentFilePath
{
  private string $baseFolder;
  private string $imagesFolder;
  private string $examsFolder;

  function __construct($eventId)
  {
    $this->baseFolder = public_path("content/event_{$eventId}");
    $this->imagesFolder = $this->baseFolder . '/images/';
    $this->examsFolder = $this->baseFolder . '/exams/';
  }

  public static function make($eventId)
  {
    return new static($eventId);
  }

  function getBaseFolder()
  {
    return $this->baseFolder;
  }

  function getImagesFolder()
  {
    return $this->imagesFolder;
  }

  function getExamsFolder()
  {
    return $this->examsFolder;
  }

  function createFolders()
  {
    mkdir($this->examsFolder, 0777, true);
    mkdir($this->imagesFolder, 0777, true);
  }

  public function examFilename($examNo)
  {
    return "{$this->examsFolder}exam_$examNo.json";
  }

  function courseSessionFilename($courseSessionId)
  {
    return "{$this->baseFolder}/course_session_$courseSessionId.json";
  }
}
