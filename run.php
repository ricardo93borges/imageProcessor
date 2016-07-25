<?php
/**
 * Created by PhpStorm.
 * User: ricardo
 * Date: 25/07/16
 * Time: 10:55
 */

require "ImageProcessor.php";


if(count($argv) < 2){
    print "Use: php run.php source destination";
}else {
    $dest = isset($argv[2]) ? $argv[2] : null;
    $ip = new ImageProcessor($argv[1], $dest);

    //Stats
    $img = new Imagick($argv[1]);
    $size = $img->getImageSize();
    print "\n Source: ".$argv[1];
    print "\n Destination: ".$argv[2];
    print "\n Type: ".$img->getImageFormat();
    print "\n Size: ".$size." Bytes";

    //Strip exif
    $ip->stripExif();

    //Compress
    $ip = new ImageProcessor($dest, $dest);
    $ip->compress();

    //Stats
    $img = new Imagick($dest);
    print "\n Compressed: ".ceil(($img->getImageSize()*100/$size))."%\n";
}