<?php
/**
	usage: 

	- create utilFileDownload object  & call download function (accepts file path as parameter)
	
			$obj = new utilFileDownload;
			$obj->download($sPath);
**/

class utilFileDownload
{

	public function download($sFilePath)
	{
		$aFile = explode('/', $sFilePath);
		$sName = $aFile[sizeof($aFile)-1];
		$sMime = "text/plain";

		if(!is_readable($sFilePath)) die('File not found or inaccessible!');

		$iSize = filesize($sFilePath);
		$sName = rawurldecode($sName);

		$aKnownMime=array(
					"pdf" => "application/pdf",
					"txt" => "text/plain",
					"html" => "text/html",
					"htm" => "text/html",
					"exe" => "application/octet-stream",
					"zip" => "application/zip",
					"doc" => "application/msword",
					"xls" => "application/vnd.ms-excel",
					"ppt" => "application/vnd.ms-powerpoint",
					"gif" => "image/gif",
					"png" => "image/png",
					"jpeg"=> "image/jpg",
					"jpg" =>  "image/jpg",
					"php" => "text/plain"
		);

		if($sMime==''){
			$sFileExt = strtolower(substr(strrchr($sFilePath,"."),1));
			
			if(array_key_exists($sFileExt, $aKnownMime)){
				$sMime=$aKnownMime[$sFileExt];
			} else {
				$sMime="application/force-download";
			};
		};

		@ob_end_clean(); //turn off output buffering to decrease cpu usage

		// required for IE, otherwise Content-Disposition may be ignored
		if(ini_get('zlib.output_compression'))
				ini_set('zlib.output_compression', 'Off');

		header('Content-Type: ' . $sMime);
		header('Content-Disposition: attachment; filename="'.$sName.'"');
		header("Content-Transfer-Encoding: binary");
		header('Accept-Ranges: bytes');

		/* The three lines below basically make the
		download non-cacheable */
		header("Cache-control: private");
		header('Pragma: private');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

		// multipart-download and download resuming support
		if(isset($_SERVER['HTTP_RANGE'])) {
			list($a, $range) = explode("=",$_SERVER['HTTP_RANGE'],2);
			list($range) = explode(",",$range,2);
			list($range, $range_end) = explode("-", $range);
			$range=intval($range);
			
			if(!$range_end) {
				$range_end=$iSize-1;
			} else {
				$range_end=intval($range_end);
			}

			$new_length = $range_end-$range+1;
			header("HTTP/1.1 206 Partial Content");
			header("Content-Length: $new_length");
			header("Content-Range: bytes $range-$range_end/$iSize");
		} else {
			$new_length=$iSize;
			header("Content-Length: ".$iSize);
		}

		/* output the file itself */
		$chunksize = 1*(1024*1024); //you may want to change this
		$bytes_send = 0;
		if ($sFilePath = fopen($sFilePath, 'r')) {
			if(isset($_SERVER['HTTP_RANGE']))
				fseek($sFilePath, $range);

			while(!feof($sFilePath) && (!connection_aborted()) && ($bytes_send<$new_length)) {
				$buffer = fread($sFilePath, $chunksize);
				print($buffer); //echo($buffer); // is also possible
				flush();
				$bytes_send += strlen($buffer);
			}
			
			fclose($sFilePath);
		} else die('Error - can not open file.');

		die();
	}


}