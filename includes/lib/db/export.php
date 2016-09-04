<?php
namespace lib\db;

class export {
    public static function csv($_arg) {

        $title    = isset($_arg['title'])       ? $_arg['title']    :  "Untitled";
        $filename = isset($_arg['filename'])    ? $_arg['filename'] :  "Untitled";
        $fieltype = isset($_arg['fieltype'])    ? $_arg['fieltype'] :  "csv";
        $data     = isset($_arg['data'])        ? $_arg['data']     :  [];

        $now = gmdate("D, d M Y H:i:s");

        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
        // $file = fopen("a.csv","w");

        // foreach ($data as $line) {

        //     fputcsv($file, $line);
        // }

        // fclose($file);

    }

}

 ?>