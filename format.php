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

<p> <b> Video Title: </b> <span id="title"> <? echo $title; ?> </span> </p>
<p> <b> Video Link: </b> <a id="link" href="<? echo $link; ?>"> <? echo $link; ?> </a> </p>

<table>
<thead>
	<tr>
		<th> Format ID </th>
		<th> File Size </th>
		<th> File Size Approx </th>
		<th> Resolution </th>
	</tr>
<thead>

<tbody>
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
		<td> <? echo $format->format_id; ?> </td>
		<td> <? echo isset($format->filesize) ? $filesize . UNITS[$unit] : "N/A"; ?> </td>
		<td> <? echo isset($format->filesize_approx) ? $filesize_approx . UNITS[$unit_approx]: "N/A"; ?> </td>
		<td> <? echo isset($format->resolution) ? $format->resolution : "N/A"; ?> </td>
		<td> 
		    <button onclick="add_merge('<? echo $format->format_id; ?>')"> + </button>
		</td>
	</tr>
	<?}?>
</tbody>
</table>
