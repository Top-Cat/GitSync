<?php

/**************************************************************
**                                                           **
**                          GitSync                          **
**                                                           **
**************************************************************/

/*
 * Configuration
 */

$user = "Top-Cat";
$repo = "SpoutSync";

/*
 * Don't edit me bro
 */

$lastcommit = file_get_contents("lastcommit");
$url = "https://api.github.com/repos/" . $user . "/" . $repo . "/compare/" . $lastcommit . "...master";

$ch = curl_init($url); 
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$content = curl_exec($ch);
if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) {
	$data = json_decode($content);

	if (sizeOf($data->commits) > 0) {
		foreach ($data->files as $file) {
			file_put_contents("patch", $file->patch);
			exec("/usr/bin/patch '" . $file->filename . "' './patch'");
		}

		file_put_contents("lastcommit", current($data->commits)->sha);
	}
}
curl_close($ch);

?>