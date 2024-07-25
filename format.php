<?
define("UNITS", ["B", "KiB", "MiB", "GiB", "TiB"]);

$query = $_GET["query"];
if (isset($_GET["search"])) {
	$query = "ytsearch:" . $query;
}

$output = json_decode(`yt-dlp --no-warnings -J "{$query}"`);
$video = $output->entries[0];

$title = $video->title;
$link = $video->original_url;
?>

<span class="block" id="title" hx-swap-oob="true"> <? echo $title; ?> </span>
<a class="block underline" id="link" hx-swap-oob="true" href="<? echo $link; ?>"> <? echo $link; ?> </a>

<template>
<tbody id="format-tbody" hx-swap-oob="true">
<? foreach ($video->formats as $format) {
	if (isset($format->filesize)) {
		$filesize = $format->filesize;
		$unit = 0;
		while ($filesize > 1024) {
			$filesize /= 1024;
			$unit++;
		}
		$filesize = number_format($filesize, 2);
	}

	if (isset($format->filesize_approx)) {
		$filesize_approx = $format->filesize_approx;
		$unit_approx = 0;
		while ($filesize_approx > 1024) {
			$filesize_approx /= 1024;
			$unit_approx++;
		}
		$filesize_approx = number_format($filesize_approx, 2);
	}
?>

	<tr>
		<td> 
		    <button onclick="add_merge(event, '<? echo $format->format_id; ?>')"> + </button>
		</td>
		<td> <? echo $format->format_id; ?> </td>
		<td> <? echo isset($format->filesize) ? $filesize . UNITS[$unit] : "N/A"; ?> </td>
		<td> <? echo isset($format->filesize_approx) ? $filesize_approx . UNITS[$unit_approx]: "N/A"; ?> </td>
		<td> <? echo isset($format->resolution) ? $format->resolution : "N/A"; ?> </td>
	</tr>
<?}?>
</tbody>
</template>
