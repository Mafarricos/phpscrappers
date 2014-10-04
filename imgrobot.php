<?php
$downloadpath='c:\\temp\\';
$album=[ALBUMHERE];
checklink('http://www.imgrobot.com/album/'.$album.'/?sort=date_desc&page=100',0,'');

function checklink($link,$i,$namealbum){
	$site=$link;
	$curl = curl_init($link);
	curl_setopt($curl, CURLOPT_AUTOREFERER, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt($curl, CURLOPT_TIMEOUT, 2 );
	$html = curl_exec( $curl );
	curl_close( $curl );
	
	$dom = new DOMDocument;
	@$dom->loadHTML($html);

	$links = $dom->getElementsByTagName('img');
	if ($namealbum == ''){
		$metas = $dom->getElementsByTagName('meta');
		$z=0;
		foreach ($metas as $meta){	
			$z=$z+1;
			if ($z==5)
				{
				$namealbum = $meta->getAttribute('content');
				echo '<p>'.$namealbum.'<br>';
				}
		}
	}	
	
	$web_pic_arr;
	$web_src_arr;

	foreach ($links as $link){	
		$img_check_error=0;
		$raw_img_url = $link->getAttribute('src');
		$img_final_link = $raw_img_url;
		if (strstr($img_final_link,'2014') == TRUE){
			$i=$i+1;
			if (strstr($img_final_link,'.jpg') == TRUE){
				$tipo='.jpg';
			}
			if (strstr($img_final_link,'.gif') == TRUE){
				$tipo='.gif';			
			}
			if (strstr($img_final_link,'.png') == TRUE){
				$tipo='.png';			
			}
			$raw_img_url = str_replace('.md.jpg','.jpg',$raw_img_url);
			$raw_img_url = str_replace('.md.png','.png',$raw_img_url);
			$raw_img_url = str_replace('.md.gif','.gif',$raw_img_url);			
			echo $raw_img_url.' '.$i.'<br>';
			$path = parse_url($raw_img_url, PHP_URL_PATH);
			$filename = basename($path);
			$partOne = strtok($filename, '.');
			if (!file_exists($downloadpath.$namealbum.'\\')) {
				mkdir($downloadpath.$namealbum.'\\', 0777, true);
			}
			if (!file_exists($downloadpath.$namealbum.'\\'.$partOne.$tipo)) {
				grab_image($raw_img_url,$downloadpath.$namealbum.'\\'.$partOne.$tipo);
			}
		}
	} // end foreach loop
	if ($i == 0){
		echo '<br><b>Missed Gallery</b><br>';
	}
}

function grab_image($url,$saveto){
    $ch = curl_init ($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    $raw=curl_exec($ch);
    curl_close ($ch);
    if(file_exists($saveto)){
        unlink($saveto);
    }
    $fp = fopen($saveto,'x');
    fwrite($fp, $raw);
    fclose($fp);
}
?>
