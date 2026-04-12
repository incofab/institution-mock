<?php
use Illuminate\Support\Arr;

if (!isset($paginatedData)) {
  return;
}

$prevPageUrl = null;
$nextPageUrl = null;
$urlParams = [];

$requestUri = request()->server('REQUEST_URI', request()->getRequestUri());
$query = Arr::get(parse_url($requestUri), 'query', '');
parse_str($query, $urlParams);
$currentUrl = url()->current(); //

if (!$paginatedData->onFirstPage()) {
  $urlParams['page'] = $paginatedData->currentPage() - 1;
  $prevPageUrl = $currentUrl . '?' . http_build_query($urlParams);
}

if ($paginatedData->hasMorePages()) {
  $urlParams['page'] = $paginatedData->currentPage() + 1;
  $nextPageUrl = $currentUrl . '?' . http_build_query($urlParams);
}

$total = $paginatedData->total();
$from = $paginatedData->firstItem();
$to = $paginatedData->lastItem();

$showInfo = "Showing {$from} - {$to} of {$total}";
?>

<div class="container">
	<div class="px-3 my-2 clearfix row">
		<div class="col-md-4">
			@if($prevPageUrl)
			<a href="{{$prevPageUrl}}" 
				class="float-start pull-left paginate paginate-previous">&laquo; Previous</a>
			@endif
		</div>
		<div class="col-md-4">
			<span class="float-start pull-left paginate paginate-info">{{$showInfo}}</span>
		</div>
		<div class="col-md-4">
			@if($nextPageUrl)
			<a href="{{$nextPageUrl}}" 
				class="float-end pull-right paginate paginate-next">Next &raquo;</a>
			@endif
		</div>
	</div>
</div>
