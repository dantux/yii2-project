<?php

use \Yii;

namespace dantux\helpers;


class Image
{
    public static function exif($exif_details, $date_time = false) {
        $exif_info_string = '';
        foreach($exif_details as $key => $section) {
            foreach($section as $name => $val) {
                if($name != "MakerNote") {
                    $exif_info_string .= $key . " | " . $name . ": " . $val . "\n";
                }
            }
        }
        if($date_time) {
            if(is_array($exif_details) && (isset($exif_details['IFD0']['DateTime']))) {
                return $exif_details['IFD0']['DateTime'];
            } else {
                return false;
            }
        } else {
            return $exif_info_string;
        }
    }

    public static function isColorBright($hex) {

        // strip off chars that defines it as hex:
        $to_strip = array('#', '0x', '0X');
        $hex = str_replace($to_strip, '', $hex);

        // If first character is below 7, the color is dark

        $first_char = substr($hex, 0, 1);

        if(is_numeric($first_char) && ($first_char > 5))
            return true;
        else
            return false;
    }

    public static function getOppositeColor($hexcode = '#FFFFFF')
    {
        // strip off chars that defines it as hexcode:
        $to_strip = '';
        if(substr($hexcode, 0, 1) == '#')
            $to_strip = '#';
        elseif(substr($hexcode, 0, 2) == '0x')
            $to_strip = '0x';
        elseif(substr($hexcode, 0, 2) == '0X')
            $to_strip = '0X';

        $hexcode = str_replace($to_strip, '', $hexcode);


        $redhex  = substr($hexcode,0,2);
        $greenhex = substr($hexcode,2,2);
        $bluehex = substr($hexcode,4,2);
       // $var_r, $var_g and $var_b are the three decimal fractions to be input to our RGB-to-HSL conversion routine

        $var_r = (hexdec($redhex)) / 255;
        $var_g = (hexdec($greenhex)) / 255;
        $var_b = (hexdec($bluehex)) / 255;
        // Input is $var_r, $var_g and $var_b from above
        // Output is HSL equivalent as $h, $s and $l — these are again expressed as fractions of 1, like the input values
        // That is "Hue", "Saturation", "Luminosit"

        $var_min = min($var_r,$var_g,$var_b);
        $var_max = max($var_r,$var_g,$var_b);
        $del_max = $var_max - $var_min;

        $l = ($var_max + $var_min) / 2;

        if ($del_max == 0)
        {
            $h = 0;
            $s = 0;
        }
        else
        {
            if ($l < 0.5)
            {
                $s = $del_max / ($var_max + $var_min);
            }
            else
            {
                $s = $del_max / (2 - $var_max - $var_min);
            };

            $del_r = ((($var_max - $var_r) / 6) + ($del_max / 2)) / $del_max;
            $del_g = ((($var_max - $var_g) / 6) + ($del_max / 2)) / $del_max;
            $del_b = ((($var_max - $var_b) / 6) + ($del_max / 2)) / $del_max;

            if ($var_r == $var_max)
                $h = $del_b - $del_g;
            elseif ($var_g == $var_max)
                $h = (1 / 3) + $del_r - $del_b;
            elseif ($var_b == $var_max)
                $h = (2 / 3) + $del_g - $del_r;

            if ($h < 0)
                $h += 1;

            if ($h > 1)
                $h -= 1;
        };
        $h2 = $h + 0.5;
        if ($h2 > 1)
            $h2 -= 1;
       // Input is HSL value of complementary colour, held in $h2, $s, $l as fractions of 1
       // Output is RGB in normal 255 255 255 format, held in $r, $g, $b
       // Hue is converted using public static function hue_2_rgb, shown at the end of this code

        if ($s == 0)
        {
            $r = $l * 255;
            $g = $l * 255;
            $b = $l * 255;
        }
        else
        {
            if ($l < 0.5)
                $var_2 = $l * (1 + $s);
            else
                $var_2 = ($l + $s) - ($s * $l);

            $var_1 = 2 * $l - $var_2;
            $r = 255 * hue_2_rgb($var_1,$var_2,$h2 + (1 / 3));
            $g = 255 * hue_2_rgb($var_1,$var_2,$h2);
            $b = 255 * hue_2_rgb($var_1,$var_2,$h2 - (1 / 3));
        };
        $rhex = sprintf("%02X",round($r));
        $ghex = sprintf("%02X",round($g));
        $bhex = sprintf("%02X",round($b));

        return $to_strip.$rhex.$ghex.$bhex;
    }

    // Function to convert hue to RGB, called from above

    public static function hue_2_rgb($v1,$v2,$vh)
    {
        if ($vh < 0)
            $vh += 1;

        if ($vh > 1)
            $vh -= 1;

        if ((6 * $vh) < 1)
            return ($v1 + ($v2 - $v1) * 6 * $vh);

        if ((2 * $vh) < 1)
            return ($v2);

        if ((3 * $vh) < 2)
            return ($v1 + ($v2 - $v1) * ((2 / 3 - $vh) * 6));

        return ($v1);
    }

    public static function imageResizeHeight($file_path = '', $height = '50')
    {
        $full_file_name = basename($file_path);
        $extension = get_extension($full_file_name);
        $file_name = basename($file_path, ".$extension");
        $output_file = IMG_DIR.DS.'imagecache'.DS.$file_name.'_'.$height.'px.'.$extension;
        $output_url = IMG_URL.DS.'imagecache/'.$file_name.'_'.$height.'px.'.$extension;
        if(!file_exists($output_file))
        {
            require_once('SimpleImage.php');
            $image = new SimpleImage(); 
            $image->load($file_path); 
            $image->resizeToHeight($height); 
            $image->save($output_file);
        }
        return $output_url;
    }


    public static function imageResizeWidth($file_path = '', $width = '50', $height='50')
    {
        $full_file_name = basename($file_path);
        $extension = get_extension($full_file_name);
        $file_name = basename($file_path, ".$extension");
        $output_file = IMG_DIR.DS.'imagecache'.DS.$file_name.'_'. $height.'px.'.$extension;
        $output_url = IMG_URL.DS.'imagecache/'.$file_name.'_'.$height.'px.'.$extension;
        if(!file_exists($output_file))
        {
            require_once('SimpleImage.php');
            $image = new SimpleImage(); 
            $image->load($file_path); 
            $image->resizeToWidth($width); 
            $image->save($output_file);
        }
        return $output_url;
    }

    public static function randomColorHex()
    {
        $hex1 = str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
        $hex2 = str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
        $hex3 = str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);

        $hex_string = $hex1.$hex2.$hex3;
        $hex = 0;
        for ($i=0; $i < strlen($hex_string); $i++)
        {
            $hex += dechex(ord($hex_string[$i]));
        }
        return $hex;
    }

    public static function pseudoRandomHex()
    {
        $hex_values = [
            0x003333, 0x003666, 0x0033cc, 0x0033ff, 0x3300ff, 0x3300cc, 0x330066,
            0x660000, 0x603366, 0x660099, 0x05051c, 0x8b4513, 0x4682b4, 0x008080,
            0x17000e, 0x708090, 0xff6347, 0x740048, 0x000202, 0x050500, 0x0e1000,
            0x252b00, 0x000900, 0x001200, 0x003500, 0x044700, 0x000706, 0x011513,
            0x00241f, 0x020005, 0x060010, 0x0c0020, 0x10002b, 0x110030, 0x080015,
            0x120012, 0x1b001b, 0x240024, 0x2c002c, 0x350035, 0x470047, 0x500050,
            0x0b0003, 0x150005, 0x200008, 0x2b000b, 0x35000d, 0x400010, 0x4b0013,
            0x140000, 0x270000, 0x3b0000, 0x430000, 0x620000, 0x750000, 0xb00000,
            ];
        $max = sizeof($hex_values) - 1;
        return rand(0, $hex_values[$max]);
    }
}
