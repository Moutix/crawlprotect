<?php
//----------------------------------------------------------------------
//  CrawlProtect 3.2.3
//----------------------------------------------------------------------
// Protect your website from hackers
//----------------------------------------------------------------------
// Author: Jean-Denis Brun
//----------------------------------------------------------------------
// Website: www.crawltrack.net
//----------------------------------------------------------------------
// That script is distributed under GNU GPL license
//----------------------------------------------------------------------
// file: footer.php
//----------------------------------------------------------------------
//  Last update: 10/08/2013
//----------------------------------------------------------------------

?>
</div>
<?php include ("include/sponsors.php"); ?>
	<div class="footer">
		<table width="100%">
			<tr><td width="33%">&nbsp;</td><td valign="top">
			<?php
			if ($crawltlang == 'french' ) {
			?>			
			<a href="http://www.crawlprotect.fr" onclick="window.open(this.href);return(false);">CrawlProtect</a>
			<?php
			}else{			
			?>
			<a href="http://www.crawlprotect.com" onclick="window.open(this.href);return(false);">CrawlProtect</a>
			<?php
			}
			?>
			</td><td align="right" valign="top" width="33%">
			<?php
			if (!isset($crawlencode)) {
				$crawlencode = '';
			}
			?>
			<a href="index.php?navig=<?php echo $navig ?>&amp;site=<?php echo $site ?>"><img src="./images/super.png" width="16" height="16" border="0" title="<?php echo $language['bookmark'] ?>" alt="<?php echo $language['bookmark'] ?>" /></a>
			</td></tr>
		</table>
	</div>
</div>
