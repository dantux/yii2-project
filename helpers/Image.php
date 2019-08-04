<?php

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


    public static function rgb2hex($rgb) {
       $hex = "#";
       $hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
       $hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
       $hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);

       return $hex; // returns the hex value including the number sign (#)
    }

    public static function getBestContrastColor($inputcolor = '#FFFFFF')
    {
        # First, if the input color is a named color, return the opposite
        if(array_key_exists(strtolower($inputcolor), self::namedColors()))
            $inputcolor = self::colorNameToHEX(strtolower($inputcolor));
 
        // strip off chars that defines it as inputcolor:
        $to_strip = array('#', '0x', '0X');
        $inputcolor = str_replace($to_strip, '', $inputcolor);

        $split = str_split($inputcolor, 2);

        $r = hexdec($split[0]);
        $g = hexdec($split[1]);
        $b = hexdec($split[2]);

        $contrast_r = ($r < 128) ? 255 : 0;
        $contrast_g = ($g < 128) ? 255 : 0;
        $contrast_b = ($b < 128) ? 255 : 0;
        
        return self::rgb2hex(array($contrast_r, $contrast_g, $contrast_b));

    }


    public static function getOppositeColor($inputcolor = '#FFFFFF')
    {
        
        # First, if the input color is a named color, return the opposite
        if(array_key_exists(strtolower($inputcolor), self::namedColors()))
            $inputcolor = self::colorNameToHEX(strtolower($inputcolor));
            
        // strip off chars that defines it as inputcolor:
        $to_strip = '';
        if(substr($inputcolor, 0, 1) == '#')
            $to_strip = '#';
        elseif(substr($inputcolor, 0, 2) == '0x')
            $to_strip = '0x';
        elseif(substr($inputcolor, 0, 2) == '0X')
            $to_strip = '0X';

        $inputcolor = str_replace($to_strip, '', $inputcolor);

        // White and black return them right away:
        if(
                strtolower($inputcolor) == 'ffffff' ||
                strtolower($inputcolor) == 'fffee0' ||
                strtolower($inputcolor) == 'ffffe0' ||
                strtolower($inputcolor) == 'fafad2' 
          )
            return '#000000';

        if($inputcolor == '000000')
            return '#ffffff';

        $redhex  = substr($inputcolor,0,2);
        $greenhex = substr($inputcolor,2,2);
        $bluehex = substr($inputcolor,4,2);
       // $var_r, $var_g and $var_b are the three decimal fractions to be input to our RGB-to-HSL conversion routine

        $var_r = (hexdec($redhex)) / 255;
        $var_g = (hexdec($greenhex)) / 255;
        $var_b = (hexdec($bluehex)) / 255;
        // Input is $var_r, $var_g and $var_b from above
        // Output is HSL equivalent as $h, $s and $l — these are again expressed as fractions of 1, like the input values
        // That is "Hue", "Saturation", "Luminosity"

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
            $r = 255 * self::hue_2_rgb($var_1,$var_2,$h2 + (1 / 3));
            $g = 255 * self::hue_2_rgb($var_1,$var_2,$h2);
            $b = 255 * self::hue_2_rgb($var_1,$var_2,$h2 - (1 / 3));
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

    public static function image_MD5($path, $width, $height, $rotate)
    {
        return md5($path . $width . $height . $rotate);
    }

    public static function imageUrl($imagePath, $width = null, $height = null, $rotate = 0, $background = '#FFF', $quality = 95)
    {
        ini_set("gd.jpeg_ignore_warning", 1);
        // Use a default image if none exists
        if(!is_file($imagePath) ||  !preg_match('/image/', mime_content_type($imagePath)))
            $imagePath = \Yii::getAlias('@baseWebPath').'/images/default_image.jpg';

        // Create the destination folder if doesn't exist 
        if(!file_exists(\Yii::getAlias('@assets').'/image_cache'))
            mkdir(\Yii::getAlias('@assets').'/image_cache');

        // Generate the final file name
        $finalImage = \Yii::getAlias('@assets').'/image_cache/'. self::image_MD5($imagePath,$width,$height,$rotate,$quality).'.jpg';

        if(!is_file($finalImage))
        {
            // Using: https://github.com/yurkinx/yii2-image

            $image = \Yii::$app->image->load($imagePath);
            $image->resize($width,$height, \yii\image\drivers\Image::ADAPT)->background($background)->rotate($rotate)->save($finalImage, $quality);
        }
     
        #$image->resize($width = NULL, $height = NULL, $master = NULL);
        #$image->crop($width, $height, $offset_x = NULL, $offset_y = NULL);
        #$image->sharpen($amount);
        #$image->rotate($degrees);
        #$image->save($file = NULL, $quality = 100);
        #$image->render($type = NULL, $quality = 100);
        #$image->reflection($height = NULL, $opacity = 100, $fade_in = FALSE);
        #$image->flip($direction);
        #$image->background($color, $opacity = 100);
        #$image->watermark(Image $watermark, $offset_x = NULL, $offset_y = NULL, $opacity = 100);
        #
        #Using resize with resize constrains
        #
        #$image->resize($width, $height, \yii\image\drivers\Image::HEIGHT);
        #$image->resize($width, $height, \yii\image\drivers\Image::ADAPT)->background('#fff');

       return \Yii::$app->request->baseUrl.'/assets/image_cache/'.basename($finalImage);
    }

    public static function namedColors()
    {
        return [
            "aliceblue" => "#F0F8FF",
            "antiquewhite" => "#FAEBD7",
            "aqua" => "#00FFFF",
            "aquamarine" => "#7FFFD4",
            "azure" => "#F0FFFF",
            "beige" => "#F5F5DC",
            "bisque" => "#FFE4C4",
            "black" => "#000000",
            "blanchedalmond" => "#FFEBCD",
            "blue" => "#0000FF",
            "blueviolet" => "#8A2BE2",
            "brown" => "#A52A2A",
            "burlywood" => "#DEB887",
            "cadetblue" => "#5F9EA0",
            "chartreuse" => "#7FFF00",
            "chocolate" => "#D2691E",
            "coral" => "#FF7F50",
            "cornflowerblue" => "#6495ED",
            "cornsilk" => "#FFF8DC",
            "crimson" => "#DC143C",
            "cyan" => "#00FFFF",
            "darkblue" => "#00008B",
            "darkcyan" => "#008B8B",
            "darkgoldenrod" => "#B8860B",
            "darkgray" => "#A9A9A9",
            "darkgreen" => "#006400",
            "darkkhaki" => "#BDB76B",
            "darkmagenta" => "#8B008B",
            "darkolivegreen" => "#556B2F",
            "darkorange" => "#FF8C00",
            "darkorchid" => "#9932CC",
            "darkred" => "#8B0000",
            "darksalmon" => "#E9967A",
            "darkseagreen" => "#8FBC8B",
            "darkslateblue" => "#483D8B",
            "darkslategray" => "#2F4F4F",
            "darkturquoise" => "#00CED1",
            "darkviolet" => "#9400D3",
            "deeppink" => "#FF1493",
            "deepskyblue" => "#00BFFF",
            "dimgray" => "#696969",
            "dodgerblue" => "#1E90FF",
            "firebrick" => "#B22222",
            "floralwhite" => "#FFFAF0",
            "forestgreen" => "#228B22",
            "fuchsia" => "#FF00FF",
            "gainsboro" => "#DCDCDC",
            "ghostwhite" => "#F8F8FF",
            "gold" => "#FFD700",
            "goldenrod" => "#DAA520",
            "gray" => "#808080",
            "green" => "#008000",
            "greenyellow" => "#ADFF2F",
            "honeydew" => "#F0FFF0",
            "hotpink" => "#FF69B4",
            "indianred" => "#CD5C5C",
            "indigo" => "#4B0082",
            "ivory" => "#FFFFF0",
            "khaki" => "#F0E68C",
            "lavender" => "#E6E6FA",
            "lavenderblush" => "#FFF0F5",
            "lawngreen" => "#7CFC00",
            "lemonchiffon" => "#FFFACD",
            "lightblue" => "#ADD8E6",
            "lightcoral" => "#F08080",
            "lightcyan" => "#E0FFFF",
            "lightgoldenrodyellow" => "#FAFAD2",
            "lightgray" => "#D3D3D3",
            "lightgreen" => "#90EE90",
            "lightpink" => "#FFB6C1",
            "lightsalmon" => "#FFA07A",
            "lightsalmon" => "#FFA07A",
            "lightseagreen" => "#20B2AA",
            "lightskyblue" => "#87CEFA",
            "lightslategray" => "#778899",
            "lightsteelblue" => "#B0C4DE",
            "lightyellow" => "#FFFFE0",
            "lime" => "#00FF00",
            "limegreen" => "#32CD32",
            "linen" => "#FAF0E6",
            "magenta" => "#FF00FF",
            "maroon" => "#800000",
            "mediumaquamarine" => "#66CDAA",
            "mediumblue" => "#0000CD",
            "mediumorchid" => "#BA55D3",
            "mediumpurple" => "#9370DB",
            "mediumseagreen" => "#3CB371",
            "mediumslateblue" => "#7B68EE",
            "mediumslateblue" => "#7B68EE",
            "mediumspringgreen" => "#00FA9A",
            "mediumturquoise" => "#48D1CC",
            "mediumvioletred" => "#C71585",
            "midnightblue" => "#191970",
            "mintcream" => "#F5FFFA",
            "mistyrose" => "#FFE4E1",
            "moccasin" => "#FFE4B5",
            "navajowhite" => "#FFDEAD",
            "navy" => "#000080",
            "oldlace" => "#FDF5E6",
            "olive" => "#808000",
            "olivedrab" => "#6B8E23",
            "orange" => "#FFA500",
            "orangered" => "#FF4500",
            "orchid" => "#DA70D6",
            "palegoldenrod" => "#EEE8AA",
            "palegreen" => "#98FB98",
            "paleturquoise" => "#AFEEEE",
            "palevioletred" => "#DB7093",
            "papayawhip" => "#FFEFD5",
            "peachpuff" => "#FFDAB9",
            "peru" => "#CD853F",
            "pink" => "#FFC0CB",
            "plum" => "#DDA0DD",
            "powderblue" => "#B0E0E6",
            "purple" => "#800080",
            "rebeccapurple" => "#663399",
            "red" => "#FF0000",
            "rosybrown" => "#BC8F8F",
            "royalblue" => "#4169E1",
            "saddlebrown" => "#8B4513",
            "salmon" => "#FA8072",
            "sandybrown" => "#F4A460",
            "seagreen" => "#2E8B57",
            "seashell" => "#FFF5EE",
            "sienna" => "#A0522D",
            "silver" => "#C0C0C0",
            "skyblue" => "#87CEEB",
            "slateblue" => "#6A5ACD",
            "slategray" => "#708090",
            "snow" => "#FFFAFA",
            "springgreen" => "#00FF7F",
            "steelblue" => "#4682B4",
            "tan" => "#D2B48C",
            "teal" => "#008080",
            "thistle" => "#D8BFD8",
            "tomato" => "#FF6347",
            "turquoise" => "#40E0D0",
            "violet" => "#EE82EE",
            "wheat" => "#F5DEB3",
            "white" => "#FFFFFF",
            "whitesmoke" => "#F5F5F5",
            "yellow" => "#FFFF00",
            "yellowgreen" => "#9ACD32",
            ];
    }

    public static function colorNameToHEX($color_name)
    {
        $colors = self::namedColors(); 

        if( array_key_exists($color_name, $colors) )
            return $colors[$color_name];
        else
            return '#000000';
    }

}
