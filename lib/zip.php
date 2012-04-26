<?php
	/**
     * Unzip function
     * by Jed Woo, 2011
	 * zip : directory 
	 * hdef : unzip directory
	 * usuage : ezip('/home/files.zip', 'unzip_files/');
     */

	function ezip($zip, $hedef = '')
    {
        $zip = zip_open($zip);

        while($zip_icerik = zip_read($zip)):
            $zip_dosya = zip_entry_name($zip_icerik);
            if(strpos($zip_dosya, '.')):
                $hedef_yol = $root . $hedef . 'x/'.$zip_dosya;
                touch($hedef_yol);
                $yeni_dosya = fopen($hedef_yol, 'w+');
                fwrite($yeni_dosya, zip_entry_read($zip_icerik));
                fclose($yeni_dosya); 
            else:
                @mkdir($root . $hedef . 'x/'.$zip_dosya);
            endif;
        endwhile;
    }
?>