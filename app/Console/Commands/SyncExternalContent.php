<?php

namespace App\Console\Commands;

use App\Enums\ContentSource;
use App\Models\ExternalContent;
use Http;
use Illuminate\Console\Command;

class SyncExternalContent extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:sync-content';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Pull contents from our central content repository';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $this->syncExamscholarsContents();
  }

  function syncExamscholarsContents()
  {
    $nextPageUrl = 'http://content.examscholars.com/api/exam-contents';
    do {
      $this->comment("Calling... $nextPageUrl");
      $res = Http::get($nextPageUrl);
      if (!$res->ok()) {
        $this->comment('Error syncing courses: ' . $res->body());
        return;
      }
      $data = $res->json('exam_contents');
      $nextPageUrl = $data['next_page_url'] ?? null;
      $examContents = $data['data'];
      foreach ($examContents as $key => $examContent) {
        $this->comment("Recording... ({$examContent['exam_name']})");
        ExternalContent::query()->updateOrCreate(
          [
            'source' => ContentSource::Examscholars->value,
            'content_id' => $examContent['id'],
          ],
          [
            'name' => $examContent['exam_name'],
            'exam_content' => $examContent,
          ],
        );
      }
    } while ($nextPageUrl);
  }
}
