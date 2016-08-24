<?php

use \Yii;

namespace dantux\helpers;


class File
{
    public static function get_extension($filename) {
        $pos_last_dot = strrpos($filename,".");
        if (!$pos_last_dot) { return ""; }
        $length_of_name = strlen($filename) - $pos_last_dot;
        $ext = substr($filename,$pos_last_dot+1,$length_of_name);
        return $ext;
    }

    public static function delete_folder($directory, $is_empty=false) {
        if($is_empty) {
            touch($directory.DS.'dummy_file');
        }
        // if the path has a slash at the end we remove it here
        if(substr($directory,-1) == DS) {
            $directory = substr($directory,0,-1);
        }

        // if the path is not valid or is not a directory ...
        if(!file_exists($directory) || !is_dir($directory)) {
            // ... we return false and exit the function
            return false;

        // ... if the path is not readable
        }elseif(!is_readable($directory)) {
            // ... we return false and exit the function
            return false;

        // ... else if the path is readable
        }else{

            // we open the directory
            $handle = opendir($directory);

            // and scan through the items inside
            while (false !== ($item = readdir($handle))) {
                // if the filepointer is not the current directory
                // or the parent directory
                if($item != '.' && $item != '..') {
                    // we build the new path to delete
                    $path = $directory.DS.$item;

                    // if the new path is a directory
                    if(is_dir($path)) {
                        // we call this public static function with the new path
                        delete_folder($path);

                    // if the new path is a file
                    }else{
                        // we remove the file
                        unlink($path);
                    }
                }
            }
            closedir($handle);

            // if the option to empty is not set to true
            if($is_empty == false) {
                // try to delete the now empty directory
                if(!rmdir($directory)) {
                    // return false if not possible
                    return false;
                }
            }
            // return success
            return true;
        }
    }

}
