<?php
    require 'vendor/autoload.php';
    // use League\ColorExtractor\ColorExtractor;
    use League\ColorExtractor\Palette;

    class Image
    {
        public static function checkExt($file)
        {
            // Checkt de extensie van je upload
            $arr = explode('.', $file);
            $extension = end($arr);

            return $extension;
        }

        public static function rename($userId, $extension)
        {
            //Hernoemt je upload
            $fileName = $userId.'_'.time().'.'.$extension;

            return $fileName;
        }

        public static function resize($tempFile, $extension, $newName, $target)
        {
            // maakt een aparte crop van je upload voor elk type
            if ($extension == 'jpg' | 'jpeg') {
                $image_resized = imagescale(imagecreatefromjpeg($tempFile), 500);
                imagejpeg($image_resized, $target.'mini-'.$newName);
            }
            if ($extension == 'png') {
                $image_resized = imagescale(imagecreatefrompng($tempFile), 500);
                // imagescale maakt png background zwart
                // $black = imagecolorallocate($image_resized, 0, 0, 0);
                // imagecolortransparent($image_resized, $black);
                imagepng($image_resized, $target.'mini-'.$newName);
            }
        }

        public static function correctImageOrientation($filename) {
            if (function_exists('exif_read_data')) {
              $exif = exif_read_data($filename);
              if($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
                if($orientation != 1){
                  $img = imagecreatefromjpeg($filename);
                  $deg = 0;
                  switch ($orientation) {
                    case 3:
                      $deg = 180;
                      break;
                    case 6:
                      $deg = 270;
                      break;
                    case 8:
                      $deg = 90;
                      break;
                  }
                  if ($deg) {
                    $img = imagerotate($img, $deg, 0);       
                  }
                  // then rewrite the rotated image back to the disk as $filename
                  imagejpeg($img, $filename, 95);
                } // if there is some rotation necessary
              } // if have the exif orientation info
            } // if function exists     
          }

        public static function extractColors($sourceImage)
        {
            // aanroepen van palette class met parameter de direction van de img
            $palette = \League\ColorExtractor\Palette::fromFilename($sourceImage);
            // extracten op basis van het palette
            $extractor = new \League\ColorExtractor\ColorExtractor($palette);
            // max 4 kleuren
            $colors = $extractor->extract(4);

            return $colors;
        }
    }
