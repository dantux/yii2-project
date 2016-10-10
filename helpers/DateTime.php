<?php

use \Yii;

namespace dantux\helpers;

class DateTime
{

    public static function data_lizibila($datetime = null) {
        if($datetime != null) {
            $unixtime = strtotime($datetime);
        } else {
            $unixtime = time();
        }
        $an = strftime("%Y", $unixtime);
        $luna = strftime("%m", $unixtime);
        $zi = strftime("%d", $unixtime);

        return $zi . " " . nume_luna($luna) . " " . $an;
    }

    public static function formatted_time($datetime = null, $language_id = 1) {
        $string = "";
        if($datetime != null) {
            $unixtime = strtotime($datetime);
        } else {
            $unixtime = time();
        }
        switch($language_id) {
        case 2: //romanian
            setlocale(LC_ALL, 'ro_RO.ISO8859-2');
            $pattern = '%d %B %Y la ora %T';
            $string = strftime($pattern, $unixtime);
            setlocale(LC_ALL, 'en_US.UTF-8');
            break;
        default: // english
            setlocale(LC_ALL, 'en_US.UTF-8');
            $pattern = '%B %d, %Y ' . 'at' . ' %I:%M %p';
            $string = strftime($pattern, $unixtime);
        }
        return $string;
    }
    public static function nume_luna($luna, $lang = 'rom') {
        switch($lang) {
            case 'rom':
                    $toate_lunile = array(
                        '01'=> 'Ianuarie',
                        '02'=> 'Februarie',
                        '03'=> 'Martie',
                        '04'=> 'Aprilie',
                        '05'=> 'Mai',
                        '06'=> 'Iunie',
                        '07'=> 'Iulie',
                        '08'=> 'August',
                        '09'=> 'Septembrie',
                        '10'=> 'Octombrie',
                        '11'=> 'Noiembrie',
                        '12'=> 'Decembrie');
                    break;
        }
        return $toate_lunile[$luna];
    }

    public static function displayDate($date = NULL)
    {
        if($date == NULL)
            $date = strftime('%Y-%m-%d', time());
        $unixTime = strtotime($date);

        return strftime('%a, %b %d %Y', $unixTime);

    }
    public static function sql_time($datetime = null) {
        if($datetime != null) {
            $unixtime = strtotime($datetime);
        } else {
            $unixtime = time();
        }
        $pattern = '%Y-%m-%d %H:%M:%S';
        return strftime($pattern, $unixtime);
    }

    public static function zodia($luna, $zi, $return_info = "text", $size = '') {
        $zodia = 'Necunoscuta';
        // Make sure the values are numbers
        $luna = $luna * 1;
        $zi = $zi * 1;

        if((($luna == 1) && ($zi >= 21)) || (($luna == 2) && $zi <= 19)) {
            $zodia = 'Varsator';
        }
        if((($luna == 2) && ($zi >= 20)) || (($luna == 3) && $zi <= 20)) {
            $zodia = 'Pesti';
        }
        if((($luna == 3) && ($zi >= 21)) || (($luna == 4) && $zi <= 20)) {
            $zodia = 'Berbec';
        }
        if((($luna == 4) && ($zi >= 21)) || (($luna == 5) && $zi <= 21)) {
            $zodia = 'Taur';
        }
        if((($luna == 5) && ($zi >= 22)) || (($luna == 6) && $zi <= 21)) {
            $zodia = 'Gemeni';
        }
        if((($luna == 6) && ($zi >= 22)) || (($luna == 7) && $zi <= 22)) {
            $zodia = 'Rac';
        }
        if((($luna == 7) && ($zi >= 23)) || (($luna == 8) && $zi <= 23)) {
            $zodia = 'Leu';
        }
        if((($luna == 8) && ($zi >= 24)) || (($luna == 9) && $zi <= 22)) {
            $zodia = 'Fecioara';
        }
        if((($luna == 9) && ($zi >= 23)) || (($luna == 10) && $zi <= 22)) {
            $zodia = 'Balanta';
        }
        if((($luna == 10) && ($zi >= 23)) || (($luna == 11) && $zi <= 21)) {
            $zodia = 'Scorpion';
        }
        if((($luna == 11) && ($zi >= 22)) || (($luna == 12) && $zi <= 21)) {
            $zodia = 'Sagetator';
        }
        if((($luna == 12) && ($zi >= 22)) || (($luna == 1) && $zi <= 20)) {
            $zodia = 'Capricorn';
        }
        if($return_info == "text") {
            return $zodia;
        } else {
            if(empty($size)) {
                return "<img src=\"" . IMG_URL.DS.'zodii'.DS . strtolower($zodia) . "." . $return_info . "\" width=\"50px\" alt=\"{$zodia}\" />";
            } else {
                return "<img src=\"" . IMG_URL.DS.'zodii'.DS . strtolower($zodia) . "." . $return_info . "\" width=\"" . $size . "\" alt=\"{$zodia}\" />";
            }
        }
    }

    public static function age($dob_unix_time, $return_month = false, $return_day = false, $age_at_unix_time = null)
    {
        if(!isset($dob_unix_time))
            return "Furnizeaza data in format Unix";

        if($age_at_unix_time == null)
            $age_at_unix_time = time();

        $DOB_year = (int)strftime("%Y", $dob_unix_time);
        $DOB_month = (int)strftime("%m", $dob_unix_time);
        $DOB_day = (int)strftime("%d", $dob_unix_time);

        $END_year = (int)strftime("%Y", $age_at_unix_time);
        $END_month = (int)strftime("%m", $age_at_unix_time);
        $END_day = (int)strftime("%d", $age_at_unix_time);

        $year_diff = $END_year - $DOB_year;
        $month_diff = $END_month - $DOB_month;
        $day_diff = $END_day - $DOB_day;


        if($month_diff < 0 || ($month_diff == 0 && $day_diff < 0))
        {
            $year_diff--;
        }
        $an = ($year_diff == (int)1) ? "an" : "ani";
        $zi = ($day_diff == (int)1) ? "zi" : "zile";

        $varsta_ani = "{$year_diff} {$an},";
        if($varsta_ani == '0 ani,')
            $varsta_ani = '';

        $varsta_diff_zile = "{$day_diff} {$zi}";
        if($varsta_diff_zile == '0 zile')
            $varsta_diff_zile = '';


        if($return_month && $return_day)
        {
            // return as "Year, Month and Day
            $days = ((days_for_month($END_month) - $DOB_day) + $END_day);

            $varsta_zile = "{$days} {$zi}";
            if($varsta_zile == '0 zile')
                $varsta_zile = '';
            
            switch($month_diff)
            {
                case -11:
                    if($day_diff < 0)
                        return "$varsta_ani $varsta_zile";
                    else
                        return "$varsta_ani 1 lună $varsta_diff_zile";
                    break;
                case -10:
                    if($day_diff < 0)
                        return "$varsta_ani 1 lună $varsta_zile";
                    else
                        return "$varsta_ani 2 luni $varsta_diff_zile";
                    break;
                case -9:
                    if($day_diff < 0)
                        return "$varsta_ani 2 luni $varsta_zile";
                    else
                        return "$varsta_ani 3 luni $varsta_diff_zile";
                    break;
                case -8:
                    if($day_diff < 0)
                        return "$varsta_ani 3 luni $varsta_zile";
                    else
                        return "$varsta_ani 4 luni $varsta_diff_zile";
                    break;
                case -7:
                    if($day_diff < 0)
                        return "$varsta_ani 4 luni $varsta_zile";
                    else
                        return "$varsta_ani 5 luni $varsta_diff_zile";
                    break;
                case -6:
                    if($day_diff < 0)
                        return "$varsta_ani 5 luni $varsta_zile";
                    else
                        return "$varsta_ani 6 luni $varsta_diff_zile";
                    break;
                case -5:
                    if($day_diff < 0)
                        return "$varsta_ani 6 luni $varsta_zile";
                    else
                        return "$varsta_ani 7 luni $varsta_diff_zile";
                    break;
                case -4:
                    if($day_diff < 0)
                        return "$varsta_ani 7 luni $varsta_zile";
                    else
                        return "$varsta_ani 8 luni $varsta_diff_zile";
                    break;
                case -3:
                    if($day_diff < 0)
                        return "$varsta_ani 8 luni $varsta_zile";
                    else
                        return "$varsta_ani 9 luni $varsta_diff_zile";
                    break;
                case -2:
                    if($day_diff < 0)
                        return "$varsta_ani 9 luni $varsta_zile";
                    else
                        return "$varsta_ani 10 luni $varsta_diff_zile";
                    break;
                case -1:
                    if($day_diff < 0)
                        return "$varsta_ani 10 luni $varsta_zile";
                    else
                        return "$varsta_ani 11 luni $varsta_diff_zile";
                    break;
                case 0:
                    if($day_diff < 0)
                        return "$varsta_ani 11 luni $varsta_zile";
                    else
                        return "$varsta_ani $varsta_diff_zile";
                    break;
                case 1:
                    if($day_diff < 0)
                        return "$varsta_ani $varsta_zile";
                    else
                        return "$varsta_ani 1 lună $varsta_diff_zile";
                    break;
                case 2:
                    if($day_diff < 0)
                        return "$varsta_ani 1 lună $varsta_zile";
                    else
                        return "$varsta_ani 2 luni $varsta_diff_zile";
                    break;
                case 3:
                    if($day_diff < 0)
                        return "$varsta_ani 2 luni $varsta_zile";
                    else
                        return "$varsta_ani 3 luni $varsta_diff_zile";
                    break;
                case 4:
                    if($day_diff < 0)
                        return "$varsta_ani 3 luni $varsta_zile";
                    else
                        return "$varsta_ani 4 luni $varsta_diff_zile";
                    break;
                case 5:
                    if($day_diff < 0)
                        return "$varsta_ani 4 luni $varsta_zile";
                    else
                        return "$varsta_ani 5 luni $varsta_diff_zile";
                    break;
                case 6:
                    if($day_diff < 0)
                        return "$varsta_ani 5 luni $varsta_zile";
                    else
                        return "$varsta_ani 6 luni $varsta_diff_zile";
                    break;
                case 7:
                    if($day_diff < 0)
                        return "$varsta_ani 6 luni $varsta_zile";
                    else
                        return "$varsta_ani 7 luni $varsta_diff_zile";
                    break;
                case 8:
                    if($day_diff < 0)
                        return "$varsta_ani 7 luni $varsta_zile";
                    else
                        return "$varsta_ani 8 luni $varsta_diff_zile";
                    break;
                case 9:
                    if($day_diff < 0)
                        return "$varsta_ani 8 luni $varsta_zile";
                    else
                        return "$varsta_ani 9 luni $varsta_diff_zile";
                    break;
                case 10:
                    if($day_diff < 0)
                        return "$varsta_ani 9 luni $varsta_zile";
                    else
                        return "$varsta_ani 10 luni $varsta_diff_zile";
                    break;
                case 11:
                    if($day_diff < 0)
                        return "$varsta_ani 10 luni $varsta_zile";
                    else
                        return "$varsta_ani 11 luni $varsta_diff_zile";
                    break;
            }
        }
        elseif($return_month && !$return_day)
        {
            // return as "Year and Months"
            switch($month_diff)
            {
                case -11:
                    if($day_diff < 0)
                        return "$varsta_ani 0 luni";
                    else
                        return "$varsta_ani 1 lună";
                    break;
                case -10:
                    if($day_diff < 0)
                        return "$varsta_ani 1 lună";
                    else
                        return "$varsta_ani 2 luni";
                    break;
                case -9:
                    if($day_diff < 0)
                        return "$varsta_ani 2 luni";
                    else
                        return "$varsta_ani 3 luni";
                    break;
                case -8:
                    if($day_diff < 0)
                        return "$varsta_ani 3 luni";
                    else
                        return "$varsta_ani 4 luni";
                    break;
                case -7:
                    if($day_diff < 0)
                        return "$varsta_ani 4 luni";
                    else
                        return "$varsta_ani 5 luni";
                    break;
                case -6:
                    if($day_diff < 0)
                        return "$varsta_ani 5 luni";
                    else
                        return "$varsta_ani 6 luni";
                    break;
                case -5:
                    if($day_diff < 0)
                        return "$varsta_ani 6 luni";
                    else
                        return "$varsta_ani 7 luni";
                    break;
                case -4:
                    if($day_diff < 0)
                        return "$varsta_ani 7 luni";
                    else
                        return "$varsta_ani 8 luni";
                    break;
                case -3:
                    if($day_diff < 0)
                        return "$varsta_ani 8 luni";
                    else
                        return "$varsta_ani 9 luni";
                    break;
                case -2:
                    if($day_diff < 0)
                        return "$varsta_ani 9 luni";
                    else
                        return "$varsta_ani 10 luni";
                    break;
                case -1:
                    if($day_diff < 0)
                        return "$varsta_ani 10 luni";
                    else
                        return "$varsta_ani 11 luni";
                    break;
                case 0:
                    if($day_diff < 0)
                        return "$varsta_ani 11 luni";
                    else
                        return "$varsta_ani 0 luni";
                    break;
                case 1:
                    if($day_diff < 0)
                        return "$varsta_ani 0 luni";
                    else
                        return "$varsta_ani 1 lună";
                    break;
                case 2:
                    if($day_diff < 0)
                        return "$varsta_ani 1 lună";
                    else
                        return "$varsta_ani 2 luni";
                    break;
                case 3:
                    if($day_diff < 0)
                        return "$varsta_ani 2 luni";
                    else
                        return "$varsta_ani 3 luni";
                    break;
                case 4:
                    if($day_diff < 0)
                        return "$varsta_ani 3 luni";
                    else
                        return "$varsta_ani 4 luni";
                    break;
                case 5:
                    if($day_diff < 0)
                        return "$varsta_ani 4 luni";
                    else
                        return "$varsta_ani 5 luni";
                    break;
                case 6:
                    if($day_diff < 0)
                        return "$varsta_ani 5 luni";
                    else
                        return "$varsta_ani 6 luni";
                    break;
                case 7:
                    if($day_diff < 0)
                        return "$varsta_ani 6 luni";
                    else
                        return "$varsta_ani 7 luni";
                    break;
                case 8:
                    if($day_diff < 0)
                        return "$varsta_ani 7 luni";
                    else
                        return "$varsta_ani 8 luni";
                    break;
                case 9:
                    if($day_diff < 0)
                        return "$varsta_ani 8 luni";
                    else
                        return "$varsta_ani 9 luni";
                    break;
                case 10:
                    if($day_diff < 0)
                        return "$varsta_ani 9 luni";
                    else
                        return "$varsta_ani 10 luni";
                    break;
                case 11:
                    if($day_diff < 0)
                        return "$varsta_ani 10 luni";
                    else
                        return "$varsta_ani 11 luni";
                    break;
            }
        }
        else
        {
            // return as Year
            return "{$year_diff} ani";
        }
    }

    public static function age_years($dob_unix_time, $age_at_unix_time = null)
    {
        if(!isset($dob_unix_time))
            return "Furnizeaza data in format Unix";

        if($age_at_unix_time == null)
            $age_at_unix_time = time();

        $DOB_year = (int)strftime("%Y", $dob_unix_time);
        $DOB_month = (int)strftime("%m", $dob_unix_time);
        $DOB_day = (int)strftime("%d", $dob_unix_time);

        $END_year = (int)strftime("%Y", $age_at_unix_time);
        $END_month = (int)strftime("%m", $age_at_unix_time);
        $END_day = (int)strftime("%d", $age_at_unix_time);

        $year_diff = $END_year - $DOB_year;
        $month_diff = $END_month - $DOB_month;
        $day_diff = $END_day - $DOB_day;


        if($month_diff < 0 || ($month_diff == 0 && $day_diff < 0))
        {
            $year_diff--;
        }
        return $year_diff;

    }

    public static function month_name($month, $lang = 'eng')
    {
        $rom_months = array('','Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie', 'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie');
        $eng_months = array('','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        switch($lang)
        {
            case 'rom':
                return $rom_months[ltrim($month, '0')];
                break;
            default:
                return $eng_months[ltrim($month, '0')];
                break;
        }
    }

    public static function week_day($year, $month, $day, $lang = 'eng') {
        $unix_time = mktime(0,0,0,$month,$day,$year);
        $week_day_nr = strftime("%u", $unix_time);
        $rom_week_days = array('Luni','Marti','Miercuri','Joi','Vineri','Sambata','Duminica');
        $eng_week_days = array('Mon','Tue','Wed','Thu','Fri','Sat','Sun');
        switch($lang) {
        case "rom":
            $week_day = $rom_week_days[$week_day_nr - 1];
            break;
        default:
            $week_day = $eng_week_days[$week_day_nr - 1];
            break;
        }
        return $week_day;
    }
    public static function is_weekend($y=null, $m=null, $d=null) {
        $is_weekend = false;
        $year = isset($y)? $y : strftime("%Y", time());
        $month = isset($m)? $m : strftime("%m", time());
        $day = isset($d)? $d : strftime("%d", time());

        $unix_time = mktime(0,0,0,$month,$day,$year);
        $week_day_nr = strftime("%u", $unix_time);

        switch($week_day_nr){
        case 6:
            $is_weekend = true;
            break;
        case 7:
            $is_weekend = true;
            break;
        default:
            $is_weekend = false;
            break;
        }
        return $is_weekend;
    }
    public static function days_for_month($month, $year = NULL) {
        if($year == NULL) {
            $year = strftime('%Y', time());
        }
        $leap_year = date('L', mktime(0,0,0,1,1,$year));
        $feb = ($leap_year == 1) ? '29' : '28';

        $months = array(
            '1'=>'31',
            '2'=>$feb,
            '3'=>'31',
            '4'=>'30',
            '5'=>'31',
            '6'=>'30',
            '7'=>'31',
            '8'=>'31',
            '9'=>'30',
            '10'=>'31',
            '11'=>'30',
            '12'=>'31');
       return $months[ltrim($month, '0')];
    }
    public static function nr_zile_lucratoare($an, $luna) {
        $days = days_for_month($luna, $an);
        $nr_zile = 0;
        for($i = 1; $i <= $days; $i++) {
            if(!is_weekend($an, $luna, $i)) {
                $nr_zile++;
            }
        }
        return $nr_zile;
    }
    public static function nr_zile_lucratoare_perioada($start_date, $end_date) {
        $nr_zile = 0;
        list($s_year, $s_month, $s_day) = explode('-', $start_date);
        list($e_year, $e_month, $e_day) = explode('-', $end_date);
        $unix_start_time = mktime(0,0,0,$s_month, $s_day, $s_year);
        $unix_end_time = mktime(0,0,0,$e_month, $e_day, $e_year);

        $seconds_passed = $unix_start_time;
        while($seconds_passed <= $unix_end_time) {
            list($year, $month, $day) = explode('-', strftime("%Y-%m-%d", $seconds_passed));
            if(!is_weekend($year, $month, $day)) {
                $nr_zile++;
            }
            $seconds_passed += (3600 * 24);
        }
        return $nr_zile;
    }
    public static function a_trecut_luna($an, $luna) {
        $ultima_zi = days_for_month($luna, $an);
        $unix_luna_cautata = mktime(23,59,59,$luna,$ultima_zi,$an);
        $unix_luna_curenta = time();
        if($unix_luna_curenta > $unix_luna_cautata) {
            return true;
        } else {
            return false;
        }
    }
    public static function previous_year_month($year = null, $month = null) {
        // this will return an array
        // with "year" and "month" as values
        // based on the provided year and month
        $year = is_null($year)? strftime("%Y", time()) : $year;
        $month = is_null($month)? strftime("%Y", time()) : $month;
        $unix_time_this_month = mktime(0,0,0,$month,1,$year);
        $unix_time_prev_month = $unix_time_this_month - 3600; // one hour before the 1st day of the current month
        return array(strftime("%Y", $unix_time_prev_month), strftime("%m", $unix_time_prev_month));
    }
    public static function next_year_month($year = null, $month = null) {
        // this will return an array
        // with "year" and "month" as values
        // based on the provided year and month
        $year = is_null($year)? strftime("%Y", time()) : $year;
        $month = is_null($month)? strftime("%Y", time()) : $month;
        $last_day_of_month = days_for_month($month, $year);

        $unix_time_this_month = mktime(23,59,59,$month,$last_day_of_month,$year);
        $unix_time_next_month = $unix_time_this_month + 3600; // one hour after the midnight in the last day of the current month
        return array(strftime("%Y", $unix_time_next_month), strftime("%m", $unix_time_next_month));
    }
    public static function drop_box_year($to_return = 'year', $selected = NULL, $link_left = '', $link_right = '') {
        $output = '';
        $this_year = strftime("%Y", time());
        $year = is_null($selected)? $this_year : $selected;
        $output .= $link_left;
        $output .= "<select name=\"" . $to_return . "\" onChange=\"this.form.submit()\">\n";
        for($i = $this_year; $i >= 2011; $i--) {
            if( (!empty($selected) && ($selected == $i)) ||
                (isset($_SESSION[$to_return]) && ($_SESSION[$to_return] == $i)))
            {
                $output .= "\t<option value=\"{$i}\" selected>{$i}</option>\n";
            } else {
                $output .= "\t<option value=\"{$i}\">{$i}</option>\n";
            }
        }
        $output .= "</select>";
        $output .= $link_right;
        return $output;
    }

    public static function how_many_days($start_date, $end_date) {
        list($start_y, $start_m, $start_d) = explode('-',$start_date);
        list($end_y, $end_m, $end_d) = explode('-',$end_date);
        $unix_start = mktime(0,0,0,$start_m, $start_d, $start_y);
        $unix_end = mktime(0,0,0,$end_m, $end_d, $end_y);
        if($unix_end >= $unix_start) {
            $total_seconds = $unix_end - $unix_start;
        } else {
            $total_seconds = $unix_start - $unix_end;
        }
        return $total_seconds / 3600 / 24;
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

    public static function year($date)
    {
        // Date should be in this format: YYYY-MM-DD;
        if(preg_match('/(\d{4})-(\d{2})-(\d{2})/', $date, $matches))
        {
            return $matches[1];
        }
        else
            return '0000';
    }

    public static function month($date)
    {
        // Date should be in this format: YYYY-MM-DD;
        if(preg_match('/(\d{4})-(\d{2})-(\d{2})/', $date, $matches))
        {
            return $matches[2];
        }
        else
            return '00';
    }
    public static function day($date)
    {
        // Date should be in this format: YYYY-MM-DD;
        if(preg_match('/(\d{4})-(\d{2})-(\d{2})/', $date, $matches))
        {
            return $matches[3];
        }
        else
            return '00';
    }

    public static function dataRo($data = null)
    {
        if($data == null)
            $data = strftime("%Y-%m-%d", time());

        $an = year($data);
        $luna = nume_luna(month($data));
        $zi = day($data);

        return "{$zi} {$luna}, {$an}";
    }

    public static function USDateTime($datetime)
    {
        $unixTime = strtotime($datetime);
        $format = '%b %e, %Y %l:%S %P';
        return strftime($format, $unixTime);
    }
    public static function USDate($datetime)
    {
        $unixTime = strtotime($datetime);
        $format = '%b %e, %Y';
        return strftime($format, $unixTime);
    }
    public static function isToday($datetime)
    {
        $unixtime = strtotime($datetime);
        return strftime('%Y%m%d', $unixtime) === strftime('%Y%m%d', time());
    }
    public static function isYesterday($datetime)
    {
        $unixtime = strtotime($datetime);
        $todayDate = strftime("%Y-%m-%d", time()) . ' 00:00:00';
        $unixToday = strtotime($todayDate);
        $unixYesterday = $unixToday - (3600 * 24);

        return strftime('%Y%m%d', $unixtime) === strftime('%Y%m%d', $unixYesterday);
    }

    public static function minutesToHours($minutes)
    {
        $unix_time = strtotime('1970-01-01 00:00:00');
        return strftime("%H:%M", $unix_time + ($minutes * 60));
    }

    public static function timeToUnixTime($time)
    {
        list($h, $m, $s) = explode(":", $time);
        return ($h * 60 * 60) + ($m * 60) + $s;
    }

    public static function secondsToTime($seconds) 
    {
        if($seconds < 60) 
        {
            if($seconds < 10) { $seconds = "0".$seconds; }
            return "00:00:".$seconds;
        } else if($seconds < 3600) {
            $minutes = $seconds / 60;
            $min = floor($minutes);
            $sec = $seconds - ($min * 60);
            if($min < 10) { $min = "0".$min; }
            if($sec < 10) { $sec = "0".$sec; }
            return "00:".$min+":".$sec;
        } else {
            $hours = $seconds / 3600;
            $hour = floor($hours);
            $seconds = $seconds - ($hour * 3600);
            $minutes = $seconds / 60;
            $min = floor($minutes);
            $sec = $seconds - ($min * 60);
            if($hour < 10) { $hour = "0".$hour; }
            if($min < 10) { $min = "0".$min; }
            if($sec < 10) { $sec = "0".$sec; }
            return $hour.":".$min.":".$sec;
        }
    }

}
