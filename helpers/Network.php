<?php

use \Yii;

namespace dantux\helpers;


class Network
{

    public static function ip_info($address = 'google.com')
    {
        $url = "http://freegeoip.net/json/${address}";
        $curl = curl_init();
        curl_setopt ($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec ($curl);
        curl_close ($curl);
        if(dantux\helpers\Text::isJson($result))
        {
            return json_decode($result);
            /*
            '{"ip":"89.122.102.185","country_code":"RO","country_name":"Romania","region_code":"BV","region_name":"Judetul Brasov","city":"Fagaras","zip_code":"505200","time_zone":"Europe/Bucharest","latitude":45.85,"longitude":24.9667,"metro_code":0}';
            $ip = $ip_info->ip;
            $country_code = $ip_info->country_code;
            $country_name = $ip_info->country_name;
            $region_code = $ip_info->region_code;
            $region_name = $ip_info->region_name;
            $city = $ip_info->city;
            $zip_code = $ip_info->zip_code;
            $time_zone = $ip_info->time_zone;
            $latitude = $ip_info->latitude;
            $longitude = $ip_info->longitude;
            $metro_code = $ip_info->metro_code;
            foreach($ip_info as $key => $val)
            {
                echo $key . ' => ' . $val . "\n";
            }
            */
        }
        else
        {
            $not_found = '{"ip":"not_found","country_code":"not_found","country_name":"not_found","region_code":"not_found","region_name":"not_found","city":"not_found","zip_code":"not_found","time_zone":"not_found","latitude":0,"longitude":0,"metro_code":0}';
            return json_decode($not_found);
        }
    }

	// Function to check response time
	public static function pingDomain($domain, $port, $timeout, $log_output = 'none'){
		$client_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'none';
		$time_now = date('H:i:s');
		$starttime = microtime(true);
		$file      = fsockopen ($domain, $port, $errno, $errstr, $timeout);
		$stoptime  = microtime(true);
		$status    = 0;

		if (!$file) $status = -1;  // Site is down
		else {
			fclose($file);
			$status = ($stoptime - $starttime) * 1000;
			$status = floor($status);
		}
		$ok_message = "{$time_now} | tcp/$port ping OK for $domain response time={$status} ms\r\n";
		$not_ok_message = "{$time_now} | tcp packet on [$domain]:{$port} didn't make it back in {$timeout} seconds\r\n";

		switch($log_output)
		{
			case 'all':
				chdir(dirname(__FILE__));
				chdir('..' . DIRECTORY_SEPARATOR . 'logs');
				$log_file = fopen(date('Y-m-d') . '_' . $client_ip . '_all.log', 'a+') or die ("can't create log file");
				if($status != -1)
				{
					echo $ok_message;
					fwrite($log_file, $ok_message);
					fclose($log_file);
				}
				else
				{
					echo $not_ok_message;
					fwrite($log_file, $not_ok_message);
					fclose($log_file);
				}
				break;
			case 'errors':
				chdir(dirname(__FILE__));
				chdir('..' . DIRECTORY_SEPARATOR . 'logs');
				$log_file = fopen(date('Y-m-d') . '_' . $client_ip . '_errors.log', 'a+') or die ("can't create log file");
				if($status != -1)
					echo $ok_message;
				else
				{
					echo $not_ok_message;
					fwrite($log_file, $not_ok_message);
					fclose($log_file);
				}
				break;
			case 'none':
			default:
				if($status != -1)
					echo $ok_message;
				else
					echo $not_ok_message;
		}
	}

	public static function remoteResponds($domain, $port, $timeout){
		$client_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'none';
		$time_now = date('H:i:s');
		$starttime = microtime(true);
		$file      = fsockopen ($domain, $port, $errno, $errstr, $timeout);
		$stoptime  = microtime(true);
		$status    = 0;

		if (!$file) $status = -1;  // Site is down
		else {
			fclose($file);
			$status = ($stoptime - $starttime) * 1000;
			$status = floor($status);
		}

        return $status > -1 ? false : true;
	}
}
