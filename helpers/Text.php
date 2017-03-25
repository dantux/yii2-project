<?php


namespace dantux\helpers;
use Yii;

class Text
{
    public static function bytesToHumanSize($size) {
        if($size < 1024) {
            $text = number_format($size, 2) . " B";
        } elseif (($size >= 1024) && ($size < 1048576)) {
            $text =  number_format($size/1024, 2) . " KB";
        } elseif (($size >= 1048576) && ($size < 1073741824)) {
            $text =  number_format($size/1048576, 2) . " MB";
        } elseif ($size >= 1073741824) {
            $text =  number_format($size/1073741824, 2) . " GB";
        }
        return $text;

    }
    public static function UTF8ToEntities ($string) {
        // note: apply htmlspecialchars if desired /before/ applying this function
        //Only do the slow convert if there are 8-bit characters
        //avoid using 0xA0 (\240) in ereg ranges. RH73 does not like that
        if (! preg_match("[\200-\237]", $string) and ! preg_match("[\241-\377]", $string))
            return $string;

        // reject too-short sequences
        $string = preg_replace("/[\302-\375]([\001-\177])/", "&#65533;\\1", $string);
        $string = preg_replace("/[\340-\375].([\001-\177])/", "&#65533;\\1", $string);
        $string = preg_replace("/[\360-\375]..([\001-\177])/", "&#65533;\\1", $string);
        $string = preg_replace("/[\370-\375]...([\001-\177])/", "&#65533;\\1", $string);
        $string = preg_replace("/[\374-\375]....([\001-\177])/", "&#65533;\\1", $string);

        // reject illegal bytes & sequences
            // 2-byte characters in ASCII range
        $string = preg_replace("/[\300-\301]./", "&#65533;", $string);
            // 4-byte illegal codepoints (RFC 3629)
        $string = preg_replace("/\364[\220-\277]../", "&#65533;", $string);
            // 4-byte illegal codepoints (RFC 3629)
        $string = preg_replace("/[\365-\367].../", "&#65533;", $string);
            // 5-byte illegal codepoints (RFC 3629)
        $string = preg_replace("/[\370-\373]..../", "&#65533;", $string);
            // 6-byte illegal codepoints (RFC 3629)
        $string = preg_replace("/[\374-\375]...../", "&#65533;", $string);
            // undefined bytes
        $string = preg_replace("/[\376-\377]/", "&#65533;", $string);

        // reject consecutive start-bytes
        $string = preg_replace("/[\302-\364]{2,}/", "&#65533;", $string);

        // decode four byte unicode characters
        $string = preg_replace(
            "/([\360-\364])([\200-\277])([\200-\277])([\200-\277])/e",
            "'&#'.((ord('\\1')&7)<<18 | (ord('\\2')&63)<<12 |" .
            " (ord('\\3')&63)<<6 | (ord('\\4')&63)).';'",
        $string);

        // decode three byte unicode characters
        $string = preg_replace("/([\340-\357])([\200-\277])([\200-\277])/e",
    "'&#'.((ord('\\1')&15)<<12 | (ord('\\2')&63)<<6 | (ord('\\3')&63)).';'",
        $string);

        // decode two byte unicode characters
        $string = preg_replace("/([\300-\337])([\200-\277])/e",
        "'&#'.((ord('\\1')&31)<<6 | (ord('\\2')&63)).';'",
        $string);

        // reject leftover continuation bytes
        $string = preg_replace("/[\200-\277]/", "&#65533;", $string);

        return $string;
    }

    public static function unix_friendly($file = ""){
        $file = self::normalize_special_characters($file);
        $file = self::UTF8ToEntities($file);
        $chars_array = array(
            '&#65533;'=> 'I',
            '&#259;'=>'a',
            '&#258;'=> 'A',
            '&#539;'=> 't',
            '&#537;'=> 's',
            '&#536;'=> 'S',
            '&#355;'=> 't',
            '&#354;'=> 'T',
            '&#351;'=> 's',
            '&#350;'=> 'S',
            ' '=>'_',
            '`'=>'.',
            '!'=>'.',
            '@'=>'_at_',
            '#'=>'x',
            '$'=>'S',
            '%'=>'percent',
            '^'=>'caret',
            '&'=>'',
            '*'=>'x',
            ','=>'.',
            '('=>'-',
            ')'=>'-',
            '+'=>'plus',
            '='=>'_eq_',
            '|'=>'I',
            '\\'=>'II',
            '}'=>'-',
            '{'=>'-',
            ']'=>'-',
            '['=>'-',
            '\''=>'.',
            '"'=>'_q_',
            ':'=>'_',
            ';'=>'_',
            '?'=>'_Q_',
            '/'=>'slash',
            '<'=>'_lt_',
            '>'=>'_gt_',
            'Â'=>'A',
            'â'=>'a',
            'Î'=>'I',
            'î'=>'i',
            'Ă'=>'A',
            'Ț'=>'T',
            'ț'=>'t',
            'ă'=>'a',
            'ș'=>'s',
            'Ș'=>'S'
        );
        return	str_replace(array_keys($chars_array),$chars_array,$file);
    }

    public static function rom_to_eng($string) {
        // ... not in use
        // ... created just to test stuff
        //
        $romanian_chars = array(
            '&#539;'=>'t',
            '&#259;'=>'a',
            '&#537;'=>'s',
            '&#536;'=>'S',
            '�'=>'S',
            '�'=>'a',
            '�'=>'s',
            '�'=>'i',
            '�'=>'a',
            '�'=>'t',
            '&atilde;'=>'a',
            '&Atilde;'=>'A'
        );
        $string = UTF8ToEntities($string);
        $chars = array(
        '&#65533;'=> 'a',
        '&#259;'=>'a',
        '&#258;'=> 'A',
        '&#539;'=> 't',
        '&#537;'=> 's',
        '&#536;'=> 'S',
        '&#355;'=> 't',
        '&#354;'=> 'T',
        '&#351;'=> 's',
        '&#350;'=> 'S'
        );
        return str_replace(array_keys($chars), $chars, $string);
    }
    public static function convert_chars($string) {
    //	$output = preg_replace_callback("/(&#[0-9]+;)/",
    //		function($m) {
    //			return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
    //		}
    //	, $string
    //	);
    //	return $output;
    }

    public static function normalize_special_characters( $str )
    {
        # Quotes cleanup
        /*
        $str = preg_replace( chr(ord('`')), "'", $str );        # `
        $str = preg_replace( chr(ord('�')), "'", $str );        # ´
        $str = preg_replace( chr(ord('�')), ",", $str );        # �
        $str = preg_replace( chr(ord('`')), "'", $str );        # `
        $str = preg_replace( chr(ord('�')), "'", $str );        # ´
        $str = preg_replace( chr(ord('�')), "\"", $str );        # �
        $str = preg_replace( chr(ord('�')), "\"", $str );        # �
        $str = preg_replace( chr(ord('�')), "'", $str );        # ´
        */

        $unwanted_array = array(    '�'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A', '�'=>'A','�'=>'A', '�'=>'A', '�'=>'C', '�'=>'E', '�'=>'E',
                                    '�'=>'E', '�'=>'E', '�'=>'I', '�'=>'I', '�'=>'I', '�'=>'I', '�'=>'N', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'O', '�'=>'U',
                                    '�'=>'U', '�'=>'U', '�'=>'U', '�'=>'Y', '�'=>'B', '�'=>'Ss', '�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a', '�'=>'a', 'å'=>'a', '�'=>'a', 'æ'=>'a', 'ç'=>'c',
                                    'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                                    'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
        $str = strtr( $str, $unwanted_array );

        # Bullets, dashes, and trademarks
        /*
        $str = preg_replace( chr(149), "&#8226;", $str );    # bullet •
        $str = preg_replace( chr(150), "&ndash;", $str );    # en dash
        $str = preg_replace( chr(151), "&mdash;", $str );    # em dash
        $str = preg_replace( chr(153), "&#8482;", $str );    # trademark
        $str = preg_replace( chr(169), "&copy;", $str );    # copyright mark
        $str = preg_replace( chr(174), "&reg;", $str );        # registration mark
        */

        return $str;
    }

    public static function pad_with_zero($number, $digits = 1, $left = true, $right = false)
    {
        switch($digits)
        {
            case 1:
            default:
              if($number < 10)
                  return "0{$number}";
              else
                  return $number;
              break;
            case 2:
              if($number < 10)
              {
                  return "00{$number}";
              }
              elseif($number < 100)
              {
                  return "0{$number}";
              }
              else
              {
                  return $number;
              }
              break;
        }
    }

    public static function joinr($join, $value, $lvl=0)
    {
        if (!is_array($join)) return Text::joinr(array($join), $value, $lvl);
        $res = array();
        if (is_array($value)&&sizeof($value)&&is_array(current($value))) { // Is value are array of sub-arrays?
            foreach($value as $val)
                $res[] = Text::joinr($join, $val, $lvl+1);
        }
        elseif(is_array($value)) {
            $res = $value;
        }
        else $res[] = $value;
        return join(isset($join[$lvl])?$join[$lvl]:"", $res);
    }

    public static function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }


    public static function titleCase($string, $delimiters = array(" ", "-", ".", "'", "O'", "Mc","D-nl","D-na"), $exceptions = array("and", "to", "of", "das", "dos", "I", "II", "III", "IV", "V", "VI"))
    {
        /*
         * Exceptions in lower case are words you don't want converted
         * Exceptions all in upper case are any words you don't want converted to title case
         *   but should be converted to upper case, e.g.:
         *   king henry viii or king henry Viii should be King Henry VIII
         */
        $string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
        foreach ($delimiters as $dlnr => $delimiter) {
            $words = explode($delimiter, $string);
            $newwords = array();
            foreach ($words as $wordnr => $word) {
                if (in_array(mb_strtoupper($word, "UTF-8"), $exceptions)) {
                    // check exceptions list for any words that should be in upper case
                    $word = mb_strtoupper($word, "UTF-8");
                } elseif (in_array(mb_strtolower($word, "UTF-8"), $exceptions)) {
                    // check exceptions list for any words that should be in upper case
                    $word = mb_strtolower($word, "UTF-8");
                } elseif (!in_array($word, $exceptions)) {
                    // convert to uppercase (non-utf8 only)
                    $word = ucfirst($word);
                }
                array_push($newwords, $word);
            }
            $string = join($delimiter, $newwords);
       }//foreach
       return $string;
    }


    public static function getUniqueCode($length = 8)
    {
        return substr(hexdec(substr(md5(microtime()), 0, 15)), 0, $length);
    }


}
