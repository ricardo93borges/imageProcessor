<?php

/**
 * Created by PhpStorm.
 * User: ricardo
 * Date: 25/07/16
 * Time: 10:51
 */
class ImageProcessor
{

    private $source;
    private $destination;

    /**
     * ImageProcessor constructor.
     */
    public function __construct($source, $destination=null)
    {
        $this->source = $source;
        if(is_null($destination)) {
            $this->destination = $source;
        }else{
            $this->destination = $destination;
        }
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param mixed $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return null
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param null $destination
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
    }

    function compress(){
        if (!file_exists($this->source)) {
            throw new Exception("File does not exist: $this->source");
        }
        $img = new Imagick($this->source);
        $type = $img->getImageFormat();
        switch ($type){
            case "PNG":
                $this->compressPng();
                break;
            case "JPEG":
                $this->compressJpg();
        }
    }
    /**
     * @param int $maxQuality
     */
    function compressPng($maxQuality = 90)
    {
        try {
            $minQuality = 60;

            // '-' makes it use stdout, required to save to $compressed_png_content variable
            // '<' makes it read from the given file path
            // escapeshellarg() makes this safe to use with any path
            $compressed = shell_exec("pngquant --quality=$minQuality-$maxQuality - < " . escapeshellarg($this->source));

            if (!$compressed) {
                throw new Exception("Conversion to compressed PNG failed.");
            }

            file_put_contents($this->destination, $compressed);
        }catch (Exception $e){
            print $e->getMessage();
        }
    }

    function compressJpg($quality = 90)
    {
        try {
            $img = new Imagick($this->source);
            $img->setImageCompression(Imagick::COMPRESSION_JPEG);
            $img->setImageCompressionQuality($quality);
            $img->writeImage($this->destination);
        }catch (Exception $e){
            print $e->getMessage();
        }
    }

    /**
     * @param $source
     * @param $destination
     * @return array images's exif
     */
    function stripExif(){
        $img = new Imagick($this->source);
        $exif = $img->getImageProperties('exif:*');

        $img->stripImage();
        $img->writeImage($this->destination);
        $img->destroy();
        return $exif;
    }

}