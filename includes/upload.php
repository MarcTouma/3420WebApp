<?php 


function createFilename($file, $path, $prefix,$uniqueID){
    $filename = $_FILES[$file]['name'];
    $exts = explode(".", $filename);
    $ext = $exts[count($exts)-1];
    $filename = $prefix.$uniqueID.".".$ext;
    $newname=$path.$filename;
    return $newname;
   }
   
   
   function checkErrors($file, $limit){
    //modified from http://www.php.net/manual/en/features.file-upload.php
    try{
        // Undefined | Multiple Files | $_FILES Corruption Attack
        // If this request falls under any of them, treat it invalid.
        if(!isset($_FILES[$file]['error']) || is_array($_FILES[$file]['error'])) {
            throw new RuntimeException('Invalid parameters.');
        }
   
        // Check Error value.
        switch ($_FILES[$file]['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Exceeded filesize limit.');
            default:
                throw new RuntimeException('Unknown errors.');
        }
   
        // You should also check filesize here.
        if ($_FILES[$file]['size'] > $limit) {
            throw new RuntimeException('Exceeded filesize limit.');
        }
   
        // Check the File type
        if (exif_imagetype( $_FILES[$file]['tmp_name']) != IMAGETYPE_GIF
         and exif_imagetype( $_FILES[$file]['tmp_name']) != IMAGETYPE_JPEG
         and exif_imagetype( $_FILES[$file]['tmp_name']) != IMAGETYPE_PNG){
   
             throw new RuntimeException('Invalid file format.');
        }
   
       return "";
   
    } catch (RuntimeException $e) {
   
       return $e->getMessage();
   
    }
   
   }
?>