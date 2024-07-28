<?
define("UNITS", ["B", "KiB", "MiB", "GiB", "TiB"]);

$query = $_GET["query"];
if (isset($_GET["search"])) {
	$query = "ytsearch:" . $query;
}

$json = `yt-dlp --no-warnings -J --compat-options manifest-filesize-approx "{$query}"`;
$output = json_decode($json);

$video = $output;
if (isset($output->entries[0]->formats)) {
	$video = $output->entries[0];
} else if (!isset($output->formats)) {
	echo "No results found, Json output: \n";
	echo $json;
        exit;
}

$title = $video->title;
$link = $video->original_url;
?>

<span class="block" id="title" hx-swap-oob="true"> <? echo $title; ?> </span>
<a class="block underline" id="link" hx-swap-oob="true" href="<? echo $link; ?>"> <? echo $link; ?> </a>

<template>
<tbody id="format-tbody" hx-swap-oob="true">
<? foreach ($video->formats as $format) {
	$approx = false;
	$filesize = $format->filesize;

	if (!isset($format->filesize) && isset($format->filesize_approx)) {
		$approx = true;
		$filesize = $format->filesize_approx;
	}

	if (isset($filesize)) {
		$unit = 0;
		while ($filesize > 1024) {
			$filesize /= 1024;
			$unit++;
		}
		$filesize = ($approx ? '~' : '') . number_format($filesize, 2);
	}
?>

	<tr>
		<td> 
		    <button onclick="add_merge(event, '<? echo $format->format_id; ?>')"> + </button>
		</td>
		<td> <? echo $format->format_id; ?> </td>
		<td class="<? echo $approx ? 'text-slate-700' : ''?>">
			<? echo isset($filesize) ? $filesize . UNITS[$unit] : "N/A"; ?>
		</td>
		<td> <? echo isset($format->resolution) ? $format->resolution : "N/A"; ?> </td>
		<td> <? echo isset($format->vcodec) ? $format->vcodec : "N/A"; ?> </td>
		<td> <? echo isset($format->acodec) ? $format->acodec : "N/A"; ?> </td>
		<td> <? echo isset($format->format) ? $format->format : "N/A"; ?> </td>
	</tr>
<?}?>
</tbody>
</template>
