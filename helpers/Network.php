<?php

namespace dantux\helpers;



class Network
{

    /**
      *
        // optional parameters: 

            & fields = ip,location,security
            & hostname = 1
            & security = 1
            & language = en
            & callback = MY_CALLBACK
            & output = json
        */
    public static function getIpInfo($address = 'google.com')
    {
        $url = "http://api.ipstack.com/${address}?access_key=cde10baa6aa2e37446610c7c9a9168cc&format=1&hostname=1";
        //$url = "https://json.geoiplookup.io/${address}";
        $curl = curl_init();
        curl_setopt ($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec ($curl);
        if(\dantux\helpers\Text::isJson($result))
        {
            $ip_info = json_decode($result);
            echo "IP: " . $ip_info->ip . "\n";
            echo "Hostname: " . $ip_info->hostname . "\n";
            echo "Continent Code: " . $ip_info->continent_code . "\n";
            echo "Continent Name: " . $ip_info->continent_name . "\n";
            echo "Country Code: " . $ip_info->country_code . "\n";
            echo "Country Code: " . $ip_info->country_name . "\n";
            echo "Region Code: " . $ip_info->region_code . "\n";
            echo "Region Name: " . $ip_info->region_name . "\n";
            echo "City: " . $ip_info->city . "\n";
            echo "Zip: " . $ip_info->zip . "\n";
            echo "Latitude: " . $ip_info->latitude . "\n";
            echo "Longitude: " . $ip_info->longitude . "\n";
            echo "Calling Code: " . $ip_info->location->calling_code . "\n";
            echo "Geoname ID: " . $ip_info->location->geoname_id . "\n";
        }
        else
            echo var_dump($result);

        curl_close ($curl);
    }

    /** 
      * Returns country code
      *
    */
    public static function detectedCountry()
    {
        $address = Network::getUserIP();
        $url = "http://api.ipstack.com/${address}?access_key=cde10baa6aa2e37446610c7c9a9168cc&format=1&hostname=1";
        $curl = curl_init();
        curl_setopt ($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec ($curl);

        if(\dantux\helpers\Text::isJson($result))
        {
            $ip_info = json_decode($result);
            //$code = $ip_info->country_code;
            $code = var_dump($ip_info);
        }
        else
            $code = 'xx-XX';

        curl_close ($curl);

        return $code;
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

	public static function remoteResponds($domain, $port = 80, $protocol = 'tcp', $timeout = 10000)
    {
        try 
        {
            $fp = stream_socket_client("${protocol}://${domain}:${port}", $errno, $errstr, $timeout);
            fclose($fp);
            return true;
        } 
        catch (\Exception $e) {
            return false;
        }
	}

    public static function isUrlResponding($url)
    {
        $goodCodes = ['200','201','202','204','302','301','303'];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 

        curl_exec($ch);
        if(!curl_error($ch))
            $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        else
            $return_code = curl_error($ch);

        if(in_array($return_code, $goodCodes))
            return true;
        else
            return false;
    }

    public static function urlResponseCode($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 

        curl_exec($ch);
        if(!curl_error($ch))
            $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        else
            $return_code = curl_error($ch);

        curl_close($ch);
        return $return_code;
    }

    public static function browser()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
        {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        }
        elseif(preg_match('/Trident/i',$u_agent))
        { // this condition is for IE11
            $bname = 'Internet Explorer';
            $ub = "rv";
        }
        elseif(preg_match('/Firefox/i',$u_agent))
        {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        }
        elseif(preg_match('/Chrome/i',$u_agent))
        {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        }
        elseif(preg_match('/Safari/i',$u_agent))
        {
            $bname = 'Apple Safari';
            $ub = "Safari";
        }
        elseif(preg_match('/Opera/i',$u_agent))
        {
            $bname = 'Opera';
            $ub = "Opera";
        }
        elseif(preg_match('/Netscape/i',$u_agent))
        {
            $bname = 'Netscape';
            $ub = "Netscape";
        }
       
        // finally get the correct version number
        // Added "|:"
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
         ')[/|: ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }
            else {
                $version= $matches['version'][1];
            }
        }
        else {
            $version= $matches['version'][0];
        }

        // check if we have a number
        if ($version==null || $version=="") {$version="?";}

        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern
        );
    } 

    // Function to get the user IP address
    public static function getUserIP() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = '0.0.0.0';
        return $ipaddress;
    }

}

