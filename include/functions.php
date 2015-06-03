<?php
//----------------------------------------------------------------------
//  CrawlProtect 3.0.0
//----------------------------------------------------------------------
// Security for website
//----------------------------------------------------------------------
// Author: Jean-Denis Brun
//----------------------------------------------------------------------
// Code cleaning: Philippe Villiers
//----------------------------------------------------------------------
// Website: www.crawltrack.net
//----------------------------------------------------------------------
// That script is distributed under GNU GPL license
//----------------------------------------------------------------------
// file: functions.php
//----------------------------------------------------------------------
//  Last update: 10/02/2012
//----------------------------------------------------------------------

/*
 * Transparent SHA-256 Implementation for PHP 4 and PHP 5
 *
 * Author: Perry McGee (pmcgee@nanolink.ca)
 * Website: http://www.nanolink.ca/pub/sha256
 *
 */
if (!class_exists('nanoSha2'))
{
    class nanoSha2
    {
        // php 4 - 5 compatable class properties
        var     $toUpper;
        var     $platform;

        // Php 4 - 6 compatable constructor
        function nanoSha2($toUpper = false) {
            // Determine if the caller wants upper case or not.
            $this->toUpper = is_bool($toUpper)
                           ? $toUpper
                           : ((defined('_NANO_SHA2_UPPER')) ? true : false);

            // Deteremine if the system is 32 or 64 bit.
            $tmpInt = (int)4294967295;
            $this->platform = ($tmpInt > 0) ? 64 : 32;
        }

        // Do the SHA-256 Padding routine (make input a multiple of 512 bits)
        function char_pad($str)
        {
            $tmpStr = $str;

            $l = strlen($tmpStr)*8;     // # of bits from input string

            $tmpStr .= "\x80";          // append the "1" bit followed by 7 0's

            $k = (512 - (($l + 8 + 64) % 512)) / 8;   // # of 0 bytes to append
            $k += 4;    // PHP Strings will never exceed (2^31)-1, 1st 32bits of
                        // the 64-bit value representing $l can be all 0's

            for ($x = 0; $x < $k; $x++) {
                $tmpStr .= "\0";
            }

            // append the 32-bits representing # of bits from input string ($l)
            $tmpStr .= chr((($l>>24) & 0xFF));
            $tmpStr .= chr((($l>>16) & 0xFF));
            $tmpStr .= chr((($l>>8) & 0xFF));
            $tmpStr .= chr(($l & 0xFF));

            return $tmpStr;
        }

        // Here are the bitwise and functions as defined in FIPS180-2 Standard
        function addmod2n($x, $y, $n = 4294967296)      // Z = (X + Y) mod 2^32
        {
            $mask = 0x80000000;

            if ($x < 0) {
                $x &= 0x7FFFFFFF;
                $x = (float)$x + $mask;
            }

            if ($y < 0) {
                $y &= 0x7FFFFFFF;
                $y = (float)$y + $mask;
            }

            $r = $x + $y;

            if ($r >= $n) {
                while ($r >= $n) {
                    $r -= $n;
                }
            }

            return (int)$r;
        }

        // Logical bitwise right shift (PHP default is arithmetic shift)
        function SHR($x, $n)        // x >> n
        {
            if ($n >= 32) {      // impose some limits to keep it 32-bit
                return (int)0;
            }

            if ($n <= 0) {
                return (int)$x;
            }

            $mask = 0x40000000;

            if ($x < 0) {
                $x &= 0x7FFFFFFF;
                $mask = $mask >> ($n-1);
                return ($x >> $n) | $mask;
            }

            return (int)$x >> (int)$n;
        }

        function ROTR($x, $n) { return (int)(($this->SHR($x, $n) | ($x << (32-$n)) & 0xFFFFFFFF)); }
        function Ch($x, $y, $z) { return ($x & $y) ^ ((~$x) & $z); }
        function Maj($x, $y, $z) { return ($x & $y) ^ ($x & $z) ^ ($y & $z); }
        function Sigma0($x) { return (int) ($this->ROTR($x, 2)^$this->ROTR($x, 13)^$this->ROTR($x, 22)); }
        function Sigma1($x) { return (int) ($this->ROTR($x, 6)^$this->ROTR($x, 11)^$this->ROTR($x, 25)); }
        function sigma_0($x) { return (int) ($this->ROTR($x, 7)^$this->ROTR($x, 18)^$this->SHR($x, 3)); }
        function sigma_1($x) { return (int) ($this->ROTR($x, 17)^$this->ROTR($x, 19)^$this->SHR($x, 10)); }

        /*
         * Custom functions to provide PHP support
         */
        // split a byte-string into integer array values
        function int_split($input)
        {
            $l = strlen($input);

            if ($l <= 0) {
                return (int)0;
            }

            if (($l % 4) != 0) { // invalid input
                return false;
            }

            for ($i = 0; $i < $l; $i += 4)
            {
                $int_build  = (ord($input[$i]) << 24);
                $int_build += (ord($input[$i+1]) << 16);
                $int_build += (ord($input[$i+2]) << 8);
                $int_build += (ord($input[$i+3]));

                $result[] = $int_build;
            }

            return $result;
        }

        /**
         * Process and return the hash.
         *
         * @param $str Input string to hash
         * @param $ig_func Option param to ignore checking for php > 5.1.2
         * @return string Hexadecimal representation of the message digest
         */
        function hash($str, $ig_func = false)
        {
            unset($binStr);     // binary representation of input string
            unset($hexStr);     // 256-bit message digest in readable hex format

            // check for php's internal sha256 function, ignore if ig_func==true
            if ($ig_func == false) {
                if (version_compare(PHP_VERSION,'5.1.2','>=')) {
                    return hash("sha256", $str, false);
                } else if (function_exists('mhash') && defined('MHASH_SHA256')) {
                    return base64_encode(bin2hex(mhash(MHASH_SHA256, $str)));
                }
            }

            /*
             * SHA-256 Constants
             *  Sequence of sixty-four constant 32-bit words representing the
             *  first thirty-two bits of the fractional parts of the cube roots
             *  of the first sixtyfour prime numbers.
             */
            $K = array((int)0x428a2f98, (int)0x71374491, (int)0xb5c0fbcf,
                       (int)0xe9b5dba5, (int)0x3956c25b, (int)0x59f111f1,
                       (int)0x923f82a4, (int)0xab1c5ed5, (int)0xd807aa98,
                       (int)0x12835b01, (int)0x243185be, (int)0x550c7dc3,
                       (int)0x72be5d74, (int)0x80deb1fe, (int)0x9bdc06a7,
                       (int)0xc19bf174, (int)0xe49b69c1, (int)0xefbe4786,
                       (int)0x0fc19dc6, (int)0x240ca1cc, (int)0x2de92c6f,
                       (int)0x4a7484aa, (int)0x5cb0a9dc, (int)0x76f988da,
                       (int)0x983e5152, (int)0xa831c66d, (int)0xb00327c8,
                       (int)0xbf597fc7, (int)0xc6e00bf3, (int)0xd5a79147,
                       (int)0x06ca6351, (int)0x14292967, (int)0x27b70a85,
                       (int)0x2e1b2138, (int)0x4d2c6dfc, (int)0x53380d13,
                       (int)0x650a7354, (int)0x766a0abb, (int)0x81c2c92e,
                       (int)0x92722c85, (int)0xa2bfe8a1, (int)0xa81a664b,
                       (int)0xc24b8b70, (int)0xc76c51a3, (int)0xd192e819,
                       (int)0xd6990624, (int)0xf40e3585, (int)0x106aa070,
                       (int)0x19a4c116, (int)0x1e376c08, (int)0x2748774c,
                       (int)0x34b0bcb5, (int)0x391c0cb3, (int)0x4ed8aa4a,
                       (int)0x5b9cca4f, (int)0x682e6ff3, (int)0x748f82ee,
                       (int)0x78a5636f, (int)0x84c87814, (int)0x8cc70208,
                       (int)0x90befffa, (int)0xa4506ceb, (int)0xbef9a3f7,
                       (int)0xc67178f2);

            // Pre-processing: Padding the string
            $binStr = $this->char_pad($str);

            // Parsing the Padded Message (Break into N 512-bit blocks)
            $M = str_split($binStr, 64);

            // Set the initial hash values
            $h[0] = (int)0x6a09e667;
            $h[1] = (int)0xbb67ae85;
            $h[2] = (int)0x3c6ef372;
            $h[3] = (int)0xa54ff53a;
            $h[4] = (int)0x510e527f;
            $h[5] = (int)0x9b05688c;
            $h[6] = (int)0x1f83d9ab;
            $h[7] = (int)0x5be0cd19;

            // loop through message blocks and compute hash. ( For i=1 to N : )
            $N = count($M);
            for ($i = 0; $i < $N; $i++)
            {
                // Break input block into 16 32bit words (message schedule prep)
                $MI = $this->int_split($M[$i]);

                // Initialize working variables
                $_a = (int)$h[0];
                $_b = (int)$h[1];
                $_c = (int)$h[2];
                $_d = (int)$h[3];
                $_e = (int)$h[4];
                $_f = (int)$h[5];
                $_g = (int)$h[6];
                $_h = (int)$h[7];
                unset($_s0);
                unset($_s1);
                unset($_T1);
                unset($_T2);
                $W = array();

                // Compute the hash and update
                for ($t = 0; $t < 16; $t++)
                {
                    // Prepare the first 16 message schedule values as we loop
                    $W[$t] = $MI[$t];

                    // Compute hash
                    $_T1 = $this->addmod2n($this->addmod2n($this->addmod2n($this->addmod2n($_h, $this->Sigma1($_e)), $this->Ch($_e, $_f, $_g)), $K[$t]), $W[$t]);
                    $_T2 = $this->addmod2n($this->Sigma0($_a), $this->Maj($_a, $_b, $_c));

                    // Update working variables
                    $_h = $_g; $_g = $_f; $_f = $_e; $_e = $this->addmod2n($_d, $_T1);
                    $_d = $_c; $_c = $_b; $_b = $_a; $_a = $this->addmod2n($_T1, $_T2);
                }

                for (; $t < 64; $t++)
                {
                    // Continue building the message schedule as we loop
                    $_s0 = $W[($t+1)&0x0F];
                    $_s0 = $this->sigma_0($_s0);
                    $_s1 = $W[($t+14)&0x0F];
                    $_s1 = $this->sigma_1($_s1);

                    $W[$t&0xF] = $this->addmod2n($this->addmod2n($this->addmod2n($W[$t&0xF], $_s0), $_s1), $W[($t+9)&0x0F]);

                    // Compute hash
                    $_T1 = $this->addmod2n($this->addmod2n($this->addmod2n($this->addmod2n($_h, $this->Sigma1($_e)), $this->Ch($_e, $_f, $_g)), $K[$t]), $W[$t&0xF]);
                    $_T2 = $this->addmod2n($this->Sigma0($_a), $this->Maj($_a, $_b, $_c));

                    // Update working variables
                    $_h = $_g; $_g = $_f; $_f = $_e; $_e = $this->addmod2n($_d, $_T1);
                    $_d = $_c; $_c = $_b; $_b = $_a; $_a = $this->addmod2n($_T1, $_T2);
                }

                $h[0] = $this->addmod2n($h[0], $_a);
                $h[1] = $this->addmod2n($h[1], $_b);
                $h[2] = $this->addmod2n($h[2], $_c);
                $h[3] = $this->addmod2n($h[3], $_d);
                $h[4] = $this->addmod2n($h[4], $_e);
                $h[5] = $this->addmod2n($h[5], $_f);
                $h[6] = $this->addmod2n($h[6], $_g);
                $h[7] = $this->addmod2n($h[7], $_h);
            }

            // Convert the 32-bit words into human readable hexadecimal format.
            $hexStr = sprintf("%08x%08x%08x%08x%08x%08x%08x%08x", $h[0], $h[1], $h[2], $h[3], $h[4], $h[5], $h[6], $h[7]);

            return ($this->toUpper) ? strtoupper($hexStr) : $hexStr;
        }

    }
}

if (!function_exists('str_split'))
{
    /**
     * Splits a string into an array of strings with specified length.
     * Compatability with older verions of PHP
     */
    function str_split($string, $split_length = 1)
    {
        $sign = ($split_length < 0) ? -1 : 1;
        $strlen = strlen($string);
        $split_length = abs($split_length);

        if (($split_length == 0) || ($strlen == 0)) {
            $result = false;
        } elseif ($split_length >= $strlen) {
            $result[] = $string;
        } else {
            $length = $split_length;

            for ($i = 0; $i < $strlen; $i++)
            {
                $i = (($sign < 0) ? $i + $length : $i);
                $result[] = substr($string, $sign*$i, $length);
                $i--;
                $i = (($sign < 0) ? $i : $i + $length);

                $length = (($i + $split_length) > $strlen)
                          ? ($strlen - ($i + 1))
                          : $split_length;
            }
        }

        return $result;
    }
}

/**
 * Main routine called from an application using this include.
 *
 * General usage:
 *   require_once('sha256.inc.php');
 *   $hashstr = sha256('abc');
 *
 * Note:
 * PHP Strings are limitd to (2^31)-1, so it is not worth it to
 * check for input strings > 2^64 as the FIPS180-2 defines.
 */
// 2009-07-23: Added check for function as the Suhosin plugin adds this routine.
if (!function_exists('sha256')) {
    function sha256($str, $ig_func = false) {
        $obj = new nanoSha2((defined('_NANO_SHA2_UPPER')) ? true : false);
        return $obj->hash($str, $ig_func);
    }
} else {
    function _nano_sha256($str, $ig_func = false) {
        $obj = new nanoSha2((defined('_NANO_SHA2_UPPER')) ? true : false);
        return $obj->hash($str, $ig_func);
    }
}

// support to give php4 the hash() routine which abstracts this code.
if (!function_exists('hash'))
{
    function hash($algo, $data)
    {
        if (empty($algo) || !is_string($algo) || !is_string($data)) {
            return false;
        }

        if (function_exists($algo)) {
            return $algo($data);
        }
    }
}

//create a unique and random string, thanks to phpsources(http://www.phpsources.org/scripts87-PHP.htm)
function random($car) {
	$string = "";
	$chaine = "abcdefghijklmnpqrstuvwxy";
	srand((double)microtime()*1000000);
	for($i=0; $i<$car; $i++) {
	$string .= $chaine[rand()%strlen($chaine)];
	}
	return $string;
}
//function to format the numbers with specified decimals for display
function numbdisp($value, $decimals = 0) {
	global $crawltlang;
	//test if numeric
	if(is_numeric($value))
		{
		// Use a default value if needed
		if($decimals > 2 || $decimals < 0 || is_null($decimals))
			$decimals = 0;
		if ($crawltlang == 'french') {
			$value = number_format($value,  $decimals, ",", " ");
		} else {
			$value = number_format($value,  $decimals, ".", ",");
		}
		}
	return $value;
}
function numbdisp2($value)
	{
	$value = number_format($value,1,".",",");
	return $value;
	}




//function to escape query string
function sql_quote($value) {
	if (get_magic_quotes_gpc()) {
		$value = stripslashes($value);
	}
	//check if this function exists
	if (function_exists("mysql_real_escape_string")) {
		$value = mysql_real_escape_string($value);
	}
	//for PHP version < 4.3.0 use addslashes
	else {
		$value = addslashes($value);
	}
	return $value;
}

//function to escape query string
function crawlt_sql_quote($value) {
	if (get_magic_quotes_gpc()) {
		$value = stripslashes($value);
	}
	//check if this function exists
	if (function_exists("mysql_real_escape_string")) {
		$value = mysql_real_escape_string($value);
	}
	//for PHP version < 4.3.0 use addslashes
	else {
		$value = addslashes($value);
	}
	return $value;
}

//function to know if the string is encode in utf8
function isutf8($string) {
	return (utf8_encode(utf8_decode($string)) == $string);
}

//function to cut and wrap the url to avoid oversize display
function crawltcuturl($url, $length) {
	global $crawltcharset;
	if ($crawltcharset == 1) {
		if (!isutf8($url)) {
			if (function_exists("mb_convert_encoding")) {
				$url = @mb_convert_encoding($url, "UTF-8", "auto");
			}
		}
	} else {
		if (function_exists("mb_convert_encoding")) {
			$url = mb_convert_encoding($url, "ISO-8859-1", "auto");
		}
	}
	$urldisplaylength = strlen("$url");
	$cutvalue = 0;
	$urldisplay = '';
	while ($cutvalue <= $urldisplaylength) {
		$cutvalue2 = $cutvalue + $length;
		$urldisplay = $urldisplay . htmlspecialchars(substr($url, $cutvalue, $length));
		if ($cutvalue2 <= $urldisplaylength) {
			$urldisplay = $urldisplay . '<br>&nbsp;&nbsp;';
			$urlcut = 1;
		}
		$cutvalue = $cutvalue2;
	}
	return $urldisplay;
}

//function to cut and wrap the keyword to avoid oversize display
function crawltcutkeyword($keyword, $length) {
	global $keywordcut, $keywordtoolong, $crawltcharset;
	if ($crawltcharset == 1) {
		if (!isutf8($keyword)) {
			if (function_exists("mb_convert_encoding")) {
				$keyword = @mb_convert_encoding($keyword, "UTF-8", "auto");
			}
		}
	} else {
		if (function_exists("mb_convert_encoding")) {
			$keyword = @mb_convert_encoding($keyword, "ISO-8859-1", "auto");
		}
	}
	if (preg_match_all("/%/i", $keyword, $out) > 3) {
		$length = 0.6 * $length;
	}
	if (strlen("$keyword") > $length) {
		$keyworddisplay = substr("$keyword", 0, $length) . "...";
		$keywordcut = 1;
	} else {
		$keyworddisplay = $keyword;
		$keywordcut = 0;
	}
	if (strlen("$keyword") > 50) {
		$keywordtoolong = 1;
	} else {
		$keywordtoolong = 0;
	}
	return htmlspecialchars($keyworddisplay);
}



// Function to remove http(s) at beginning of URLs
function strip_protocol($url='')
{
	return preg_replace("/^https?:\/\/(.+)$/i","\\1", $url);
}
//function for looking for file
function lookfile($dirname, $whichfile,$justbad,$nocache,$nostats,$nologs)
    	{
	global $goodfilechmod;
	$list=array();
	$today=strtotime("today");
	$dir = opendir($dirname);
	while($file = readdir($dir))
		{
		if($dirname=='../')
			{
			$file2=$dirname.$file;
			}
		else
			{
			$file2=$dirname."/".$file;
			}

		if($whichfile=='all')
			{
			if($file != '.' && $file != '..' && !is_dir($file2))
				{
				$take=1;
				$timedir=filemtime($file2);			
				if($justbad=='1')
					{											
					$perms=fileperms($file2);
					$chmod=substr(sprintf('%o', $perms), -4);
				
					if(($today-$timedir)> 604800 && in_array($chmod, $goodfilechmod))
						{
						$take++;			
						}				
					
					}
				if($nocache=='1')
					{
					if(preg_match("/cache/i", $file2))
						{
						$take++;
						}
					}
				if($nostats=='1')
					{
					if(preg_match("/stats/i", $file2))
						{
						$take++;
						}
					}
				if($nologs=='1')
					{
					if(preg_match("/logs/i", $file2))
						{
						$take++;
						}
					}
				if($take==1)
					{
					$list[$file2]=$timedir;
					}
				}
			}
		else
			{
			if($file != '.' && $file != '..' && !is_dir($file2) && (preg_match("/htaccess/i", $file) OR preg_match("/index/i", $file) OR preg_match("/header/i", $file) OR preg_match("/footer/i", $file) OR preg_match("/config/i", $file)))
				{
				$take=1;
				$timedir=filemtime($file2);
				if($justbad=='1')
					{		
					
					$perms=fileperms($file2);
					$chmod=substr(sprintf('%o', $perms), -4);
				
					if(($today-$timedir)> 604800 && in_array($chmod, $goodfilechmod))
						{
						$take++;			
						}				
					
					}
				if($nocache=='1')
					{
					if(preg_match("/cache/i", $file2))
						{
						$take++;
						}
					}
				if($nostats=='1')
					{
					if(preg_match("/stats/i", $file2))
						{
						$take++;
						}
					}
				if($nologs=='1')
					{
					if(preg_match("/logs/i", $file2))
						{
						$take++;
						}
					}
				if($take==1)
					{
					$list[$file2]=$timedir;
					}
				}
			}
		}
	closedir($dir);
	return $list;
	} 

//function to have the list of folder
function lookfolder($dirname, $folderlevel,$justbad,$nocache,$nostats,$nologs)
    	{
	global $goodfolderchmod, $listallfolders;
	$list=array();
	$list0=array();
	$today=strtotime("today");
	$dir = opendir($dirname);
	//first level
	while($file = readdir($dir))
		{
		if($file != '.' && $file != '..' && is_dir($dirname.$file))
			{
			$take=1;
			$dir2="../".$file;				
			$timedir=filemtime($dir2);
			if($justbad=='1')
				{
				$perms=fileperms($dir2);
				$chmod=substr(sprintf('%o', $perms), -4);
				
				if(($today-$timedir)> 604800 && in_array($chmod, $goodfolderchmod))
					{
					$take++;			
					}				
					
				}
			if($nocache=='1')
				{
				if(preg_match("/cache/i", $file))
					{
					$take++;
					}
				}
			if($nostats=='1')
				{
				if(preg_match("/stats/i", $file))
					{
					$take++;
					}
				}
			if($nologs=='1')
				{
				if(preg_match("/logs/i", $file))
					{
					$take++;
					}
				}
			if($take==1)
				{
				$list[$file]=$timedir;
				}
			$listallfolders= $list;
			}
		}
	closedir($dir);
$search='go';
$i=1;
while ($search =='go' )
	{
	if($i==1)
		{
		$listtosearch=$listallfolders;
		}
	else
		{
		$listtosearch=$list2;
		$list2=array();
		}	

	foreach($listtosearch as $value=>$time)
		{
		$folder=$dirname.$value."/";
		$dir = opendir($folder);

		while($file = readdir($dir))
			{
			if($file != '.' && $file != '..' && is_dir($folder.$file))
				{
				$take=1;
				$dir2=$folder.$file;
				$timedir=filemtime($dir2);
				if($justbad=='1')
					{
					$perms=fileperms($dir2);
					$chmod=substr(sprintf('%o', $perms), -4);
				
					if(($today-$timedir)> 604800 && in_array($chmod, $goodfolderchmod))
						{
						$take++;			
						}						
					}
				if($nocache=='1')
					{
					if(preg_match( "/cache/i", $file))
						{
						$take++;
						}
					}
				if($nostats=='1')
					{
					if(preg_match("/stats/i", $file))
						{
						$take++;
						}
					}
				if($nologs=='1')
					{
					if(preg_match("/logs/i", $file))
						{
						$take++;
						}
					}
				$key=$value."/".$file;
				if($take==1)
					{
					$list2[$key]=$timedir;
					}
				}
			}
		closedir($dir);	
	
		}
	$list= array_merge($list,$list2);
	$listallfolders=array_merge($listallfolders,$list2);
	if(count($list2)>0 && $folderlevel!='limit')
		{
		$search='go';
		}
	else
		{
		$search='stop';
		}
	$i++;
	}
	return $list;
	}
//function change chmod
function change_chmod($type2, $filedir, $chmod, $validchmod)
	{
	global $filelist, $dirlist, $language, $release, $crawltlang, $min, $minf, $sort, $sortf,$listfiledontchange,$listfolderdontchange,$chmodindex,$chmodadmin;

	if(($validchmod==1 && $chmod ==1) OR $chmod==0)
		{
		if($type2=='file')
			{
			if($chmod==1)
				{				
			       if(function_exists('chmod'))
					{
					if( $filedir !='changeallfiles')
						{
						chmod($filedir, 0444);
						}
					else
						{
						foreach($filelist as $key=>$value)
							{
							if(is_file($key) && !in_array($key, $listfiledontchange))
								{
						    		chmod($key, 0444);
								}
							}
						}
					}
				}
			elseif($chmod==0)
				{
			       if(function_exists('chmod'))
					{
					if( $filedir !='changeallfiles')
						{
						chmod($filedir, 0644);
						}
					else
						{
						foreach($filelist as $key=>$value)
							{
							if(is_file($key) && !in_array($key, $listfiledontchange))
								{
					    			chmod($key, 0644);
								}
							}
						}
					}
				}

			}
		elseif($type2=='folder')
			{
			if($chmod==1)
				{				
			       if(function_exists('chmod'))
					{
					if( $filedir !='changeallfolders')
						{
						chmod($filedir, 0555);
						}
					else
						{
						foreach($dirlist as $key=>$value)
							{
							$dir="../".$key;
							if(!in_array($dir, $listfolderdontchange))
								{								
				    				chmod($dir, 0555);
								}								
							}
						}
					}
				}
			elseif($chmod==0)
				{
			       if(function_exists('chmod'))
					{
					if( $filedir !='changeallfolders')
						{
						chmod($filedir, 0755);
						}
					else
						{
						foreach($dirlist as $key=>$value)
							{
							$dir="../".$key;
							if(!in_array($dir, $listfolderdontchange))
								{
								chmod($dir, 0755);
								}
							}

						}
					}
				}

			}
		}
	else
		{
		if($chmod==1)
			{
			if($type2=='file')
				{
				echo"<div  align=\"center\"><form action=\"index.php#files\" method=\"POST\" >\n";
				}
			else
				{
				echo"<div  align=\"center\"><form action=\"index.php#folders\" method=\"POST\" >\n";
				}
			$text7=$language['yes'];
			echo "<h1>".$language['chmod_safe']."</h1>";
			if($filedir=='changeallfiles')
				{
				echo "<h2>".$language['change_all_files']."</h2>";
				}
			elseif($filedir=='changeallfolders')
				{
				echo "<h2>".$language['change_all_folders']."</h2>";
				}
			else
				{
				echo "<h2>".$filedir."</h2>";
				}
			echo "<p class=\"red\">".$language['change-chmod-to-safe']."</p>";
			echo "<input type=\"hidden\" name ='navig' value='2'>\n";
			echo "<input type=\"hidden\" name ='datedisplay' value='0'>\n";	
			echo "<input type=\"hidden\" name ='changechmod' value='1'>\n";
			echo "<input type=\"hidden\" name ='chmodindex' value='$chmodindex'>\n";
			echo "<input type=\"hidden\" name ='chmodadmin' value='$chmodadmin'>\n";	
			echo "<input type=\"hidden\" name ='filedir' value='$filedir'>\n";
			echo "<input type=\"hidden\" name ='type2' value='$type2'>\n";
			echo "<input type=\"hidden\" name ='min' value='$min'>";
			echo "<input type=\"hidden\" name ='minf' value='$minf'>";
			echo "<input type=\"hidden\" name ='sort' value='$sort'>\n";	
			echo "<input type=\"hidden\" name ='sortf' value='$sortf'>\n";	
			echo "<input type=\"hidden\" name ='chmod' value='1'>\n";
			echo "<input type=\"hidden\" name ='validchmod' value='1'>\n";     
			echo"<br><input name='ok' type='submit'  class='widebutton' value='$text7' size='20' >\n";
			echo"</form><br>\n";	
			$text8=$language['no'];
			if($type2=='file')
				{
				echo"<form action=\"index.php#files\" method=\"POST\" >\n";
				}
			else
				{
				echo"<form action=\"index.php#folders\" method=\"POST\" >\n";
				}
			echo "<input type=\"hidden\" name ='navig' value='2'>\n";
			echo "<input type=\"hidden\" name ='chmodindex' value='$chmodindex'>\n";
			echo "<input type=\"hidden\" name ='chmodadmin' value='$chmodadmin'>\n";	
			echo "<input type=\"hidden\" name ='min' value='$min'>";
			echo "<input type=\"hidden\" name ='minf' value='$minf'>";
			echo "<input type=\"hidden\" name ='sort' value='$sort'>\n";	
			echo "<input type=\"hidden\" name ='sortf' value='$sortf'>\n";
 			echo "<input type=\"hidden\" name ='datedisplay' value='0'>\n";	
			echo "<input type=\"hidden\" name ='validchmod' value='0'>\n";     
			echo"<input name='ok' type='submit'  class='widebutton' value='$text8' size='20' >\n";
			echo"</form></div><br>\n";
			include("sponsors.php");
			echo"<div class=\"footer\">\n";
			echo"<a href=\"http://www.crawlprotect.com\" onclick=\"window.open(this.href);return(false);\">\n";
			echo"CrawlProtect</a>\n";
			echo"</div>\n";
			echo"</div>\n";
			echo"</body>\n";
			echo"</html>\n";
			exit();
			}
		}
	}
	


?>
