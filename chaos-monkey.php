<?php
/**
 * chaos-monkey.php
 * A childishly snarky, yet effective tool for significantly skewing any attempt to gather information on your personal browing habits.
 * This browsing Chaos Monkey requires only a text file containing a list of Internet domains to visit, create or download such a list
 * (something like the Alex Top 1M domains ought to do it)
 * 
 * P.S. For larger browsing lists, you should probably tweak the memory limit to something north of 128M
 * ini_set('memory_limit','256M'); 
 *
 * PHP version 5.6+
 *
 * @author		acochenour@hopliteindustries.com
 * @license		Free as in beer, not puppies
 * @version		0.1
 * @since		v0.1
 */

/*
 * Set the memory limit for this process
 */
ini_set('memory_limit','512M');
CONST SLEEPMIN = 2;
CONST SLEEPMAX = 20;

/*
 * Set curl up to accept then bitbucket cookies
 */
$cFile = '/dev/null';

/*
 * Create some User Agent fodder
 */
$uaArray = array();
$uaArray[] = 'Mozilla/5.0 (Android 4.4; Mobile; rv:41.0) Gecko/41.0 Firefox/41.0';
$uaArray[] = 'Mozilla/5.0 (Android 4.4; Tablet; rv:41.0) Gecko/41.0 Firefox/41.0';
$uaArray[] = 'Mozilla/5.0 (Windows NT x.y; rv:10.0) Gecko/20100101 Firefox/10.0';
$uaArray[] = 'Mozilla/5.0 (Windows NT x.y; Win64; x64; rv:10.0) Gecko/20100101 Firefox/10.0';
$uaArray[] = 'Mozilla/5.0 (Windows NT x.y; WOW64; rv:10.0) Gecko/20100101 Firefox/10.0';
$uaArray[] = 'Mozilla/5.0 (Macintosh; Intel Mac OS X x.y; rv:10.0) Gecko/20100101 Firefox/10.0';
$uaArray[] = 'Mozilla/5.0 (Macintosh; PPC Mac OS X x.y; rv:10.0) Gecko/20100101 Firefox/10.0';
$uaArray[] = 'Mozilla/5.0 (X11; Linux i686; rv:10.0) Gecko/20100101 Firefox/10.0';
$uaArray[] = 'Mozilla/5.0 (X11; Linux x86_64; rv:10.0) Gecko/20100101 Firefox/10.0';
$uaArray[] = 'Mozilla/5.0 (X11; Linux i686 on x86_64; rv:10.0) Gecko/20100101 Firefox/10.0';
$uaArray[] = 'Mozilla/5.0 (Maemo; Linux armv7l; rv:10.0) Gecko/20100101 Firefox/10.0 Fennec/10.0';
$uaArray[] = 'Mozilla/5.0 (Mobile; rv:26.0) Gecko/26.0 Firefox/26.0';
$uaArray[] = 'Mozilla/5.0 (Tablet; rv:26.0) Gecko/26.0 Firefox/26.0';
$uaArray[] = 'Mozilla/5.0 (TV; rv:44.0) Gecko/44.0 Firefox/44.0';

/*
 * Collect the domain file name from user input, otherwise exit
 */
if (isset($argv[1])) 	{
	if ($argv[1] == '-h' || $argv[1] == '--help') 	{
		echo "Usage: $argv[0] /path/to/domains.txt\n";
		exit;
	}
	else 	{
		/*
		 * Open the domains file and create a basic array to feed the monkey
		 */
		if (($fHandle = fopen($argv[1], "r")) !== FALSE) {
			$cDomains = array();
			while (($line = fgets($fHandle, 4096)) !== FALSE) {
				if (!empty($line)) 	{
					$line = trim($line);
					$cDomains[] = $line;
					unset($line);
				}
			}
		}

		/*
		 * Let the chaos begin
		 */
		while (true) 	{
			try 	{
				/*
				 * Select a pseudo-random domain from the list
				 */
				$rKey = array_rand($cDomains, 1);
				$domain = $cDomains[$rKey];
				if (!empty($domain)) 	{
					$curlSession = curl_init();
					$uaKey = array_rand($uaArray, 1);
					curl_setopt($curlSession,CURLOPT_USERAGENT, $uaArray[$uaKey]);
					curl_setopt($curlSession, CURLOPT_URL, "http://" . $domain);
					curl_setopt($curlSession, CURLOPT_BINARYTRANSFER, true);
					curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($curlSession, CURLOPT_SSL_VERIFYHOST, 0);
					curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, 0);
					curl_setopt($curlSession, CURLOPT_FOLLOWLOCATION, true);
					curl_setopt($curlSession, CURLOPT_COOKIESESSION, true);
					curl_setopt($curlSession, CURLOPT_COOKIEFILE, $cFile);
					curl_setopt($curlSession, CURLOPT_COOKIEJAR, $cFile);
					/*
					 * Grab then bitbucket the content
					 */
					$cOutput = curl_exec($curlSession);
					echo "Chaos Monkey loves the browser bananas at " . $domain . "\n";
					unset($cOutput);
					unset($domain);
				}
			}
			catch (Exception $e) 	{
						echo "Chaos Monkey Ate a Bad Banana\n";
			}
			
			/*
			 * Sleep for a ranomd time period before hitting the next domain
			 */
			sleep(rand(SLEEPMIN,SLEEPMAX));
		}
	}		
}

?>
