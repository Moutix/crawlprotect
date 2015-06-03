<?php
//----------------------------------------------------------------------
//  CrawlProtect 3.0.0
//----------------------------------------------------------------------
// Protect your website from hackers
//----------------------------------------------------------------------
// Author: Jean-Denis Brun
//----------------------------------------------------------------------
// Website: www.crawlprotect.com
//----------------------------------------------------------------------
// That script is distributed under GNU GPL license
//----------------------------------------------------------------------
// file: origine-graph.php
//----------------------------------------------------------------------
// this graph is made with artichow    website: www.artichow.org
//----------------------------------------------------------------------
//  Last update: 19/02/2012
//----------------------------------------------------------------------
//get graph infos
	if(isset($_GET['lang']))
		{
		$lang=$_GET['lang'];	
		}
	else
		{
		$lang='english';
		}
	if(isset($_GET['site']))
		{
		$site=$_GET['site'];	
		}
	else
		{
		$site='1';
		}		
	include("../language/".$lang.".php");
	include ("../include/functions.php");		
	include ("../include/connection.php");	
	$connexion = @mysql_connect($crawlthost,$crawltuser,$crawltpassword);
	$selection = @mysql_select_db($crawltdb);


		$country2=array();
		$listip=array();
		$sql = "SELECT * FROM crawlp_stats WHERE id_site='" . sql_quote($site) . "'";
		$requete = mysql_query($sql, $connexion);
		$nbrresult=mysql_num_rows($requete);
		if($nbrresult>=1)
			{
			if(file_exists("./geoipdatabase/geoip.inc"))
				{
				$case=1;
				}
			else
				{
				$case=2;
				}
			// Test to see if the server is running a standalone version of GeoIP
			if(!function_exists('geoip_country_name_by_addr'))
				{
				if($case==1)
					{
					include("./geoipdatabase/geoip.inc");
					}
				else
					{
					include("../geoipdatabase/geoip.inc");
					}
				}
			while($ligne = mysql_fetch_assoc($requete))
				{
				$ip=$ligne['ip'];
				if($case==1)
					{
					$gi = geoip_open("./geoipdatabase/GeoIP.dat",GEOIP_STANDARD);
					}
				else
					{
					$gi = geoip_open("../geoipdatabase/GeoIP.dat",GEOIP_STANDARD);
					}
				$code = str_replace("'"," ",strtolower(geoip_country_code_by_addr($gi, $ip)));
				geoip_close($gi);

				if(isset($country2[$code]))
					{
					$country2[$code]++;
					}
				else
					{
					$country2[$code]=1;
					}
				if(isset($listip[$ligne['ip']]))
					{
					$listip[$ligne['ip']]++;
					}
				else
					{
					$listip[$ligne['ip']]=1;
					}

				}
			$countryserialize=serialize($country2);
			$listipserialize=serialize($listip);
			}
		else
			{
			$listipserialize='a:0:{}';
			$countryserialize='a:0:{}';
			}



$datatransfert= array();
//get graph values
$datatransfert= unserialize($countryserialize);
arsort($datatransfert);
$totvalues=array_sum( $datatransfert);  
$i=0;
$total=0;
foreach ($datatransfert as $key => $value)
	{
	if($i<7)
		{
		if($key==''||$key=='a1')
			{
			$key='xx';
			}
		$legend[] = $country[$key];
		$values[]=$value;
		$total=$total+$value;
		}
	$i++;
	}
if($total<$totvalues)
	{
	$legend[]=$language['others'];
	$values[]= $totvalues-$total;
	}

  
//build the graph
//test to see if ttf font is available
if(function_exists('gd_info'))
	{
	$fontttf= gd_info();
	if( @$fontttf['FreeType Linkage']=='with freetype')
		{
		$ttf='ok';
		}
	else
		{
		$ttf='no-ok';
		}
	}
else
		{
		$ttf='no-ok';
		}
require_once("artichow/Pie.class.php");



$graph = new Graph(450, 200);
if(function_exists('imageantialias'))
    {
    $graph->setAntiAliasing(TRUE);
    }
else
    {
     $graph->setAntiAliasing(FALSE);   
    }
$graph->border->hide(TRUE);

$graph->shadow->setSize(5);
$graph->shadow->smooth(TRUE);

$graph->shadow->setPosition('SHADOW_LEFT_BOTTOM');
$graph->shadow->setColor(new DarkBlue);



$plot = new Pie($values);
$plot->setCenter(0.33, 0.5);
$plot->setSize(0.6, 0.8);
$plot->set3D(15);
$plot->setLabelPosition(10);
$plot->label->setColor(new DarkBlue);
if ($ttf=='ok')
    {
    $plot->label->setFont(new Tuffy(10));
    }
else
    {
    $plot->label->setFont(new Font(2));
    }
$plot->setBorder(new DarkBlue);

$plot->setLegend($legend);


$plot->legend->setPosition(1.6);

$plot->legend->shadow->setSize(0);
$plot->legend->setBackgroundColor(new White);
$plot->legend->border->hide(TRUE);
$plot->legend->setTextColor(new DarkBlue);
if ($ttf=='ok')
	{
	$plot->legend->setTextFont(new Tuffy(10));
	}
else
    {
    $plot->legend->setTextFont(new Font(2));
    }
$graph->add($plot);
$graph->draw();

?>
