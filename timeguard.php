#!/usr/bin/php -q
<?php

error_reporting(E_ALL|E_STRICT);

class Hostfile
{
	protected $lines;
	protected $entries;
	
	public function __construct()
	{
		$lines = file('/etc/hosts');
		
		$this->lines = $lines;
		$this->entries = $this->parseLines($lines);
	}
	
	public function parseLines(array $lines)
	{
		$entries = array();
		
		foreach($lines as $k=>$line) {
			$line = trim($line);
			
			// Skip comments and empty lines.
			if (empty($line) || $line[0] == '#') {
				continue;
			}
			
			$parts = preg_split("/\s+/", $line);
			$ip    = array_shift($parts);
			$hosts = $parts;
			
			$entries[] = array(
							'line'=>$k,
							'ip'=>$ip,
							'hosts'=>$hosts
						);
		}
		return $entries;
	}
	
	public function &findHost($name)
	{
		foreach($this->entries as &$e) {
			if (in_array($name, $e['hosts'])) {
				return $e;
			}
		}
		return false;
	}
	
	public function &findAddress($ip)
	{
		$found = array();		
		foreach($this->entries as &$e) {
			if ($e['ip'] == $ip) {
				$found[] = &$e;
			}
		}
		
		if (count($found)) {
			return $found;
		}
	}
	
	public function removeHost($host)
	{
		if ($e = &$this->findHost($host)) {
			$arr_pos = array_search($host, $e['hosts']);
			unset($e['hosts'][$arr_pos]);
			return true;
		}
		return false;
	}
	
	public function addHost($host, $ip, $new_line=false)
	{
		// Check if host already exists.
		if ($e = &$this->findHost($host)) {
			if ($e['ip'] == $ip) {
				return false;
			} else {
				$this->removeHost($host);
			}
		}
		
		if (!$new_line && $address = &$this->findAddress($ip)) {
			// Found an existing address.
			$address[0]['hosts'][] = $host;			
		} else {
			// Need to add a new line.
			$this->entries[] = array(
								'line'=>false,
								'ip'=>$ip,
								'hosts'=>array($host)
								);
		}
	}
	
	public function save()
	{
		foreach($this->entries as $e) {
			// First create the string.
			$entry_str = sprintf("%s\t\t%s", $e['ip'], join(' ', $e['hosts']));
			$entry_str .= "\n";
			
			if ($e['line'] !== false) {
				$this->lines[$e['line']] = $entry_str;
			} else {
				$this->lines[] = $entry_str;
			}
		}
		$new_data = join("", $this->lines);
		
		if (!is_writeable('/etc/hosts')) {
			die("Error: You do not have permission to edit host file.\n");
		}
		
		return file_put_contents('/etc/hosts', $new_data);
	}
}

// ---------------------------------------------------------------------

define('FUN_MARKER', 'fun.mark');
define('DEFAULT_SITES', 'reddit.com www.reddit.com www.digg.com digg.com '.
						'news.ycombinator.com slashdot.org www.slashdot.org '.
						'daringfireball.net cnn.com www.cnn.com kottke.org '.
						'www.boingboing.net boingboing.net amazon.com '.
						'www.amazon.com');

// Run program.
timeguard_main($_SERVER['argv'], $_SERVER['argc']);
exit;

function timeguard_main($argv, $argc)
{
	if (!isset($argv[1])) {
		die("Usage: timeguard [enable|disable|addsite] <optional param>\n");
	}
	
	$host = new Hostfile;
	$e = &$host->findHost(FUN_MARKER);
	
	// Create the fun.mark entry if it doesn't yet exist.
	if (!$e) {
		$host->addHost(FUN_MARKER, '127.0.0.1', $new=true);
		$e = &$host->findHost(FUN_MARKER);
		$e['hosts'] += explode(' ', DEFAULT_SITES);
	}
	
	// Find disable marker
	$pos = array_search('#', $e['hosts']);
	
	switch($argv[1]) 
	{
		// Turn guard off.
		case 'off':
			if (!$pos) {
				$start = (array)$e['hosts'][0];
				$end = array_slice($e['hosts'], 1);
				$start[] = '#';
				$e['hosts'] = array_merge($start, $end);
			}
			echo("FunGuard deactivated. Release the joyous squeals.\n");
			break;
			
		// Turn guard off.
		case 'on':
			if ($pos) { 
				unset($e['hosts'][$pos]);
			}
			echo("FunGuard activated! No fun will be had on my watch.\n");
			break;
		
		// Add new site to ban list.
		case 'addsite':
			if (!isset($argv[2]) || empty($argv[2])) {
				fatal_error("Please specify a site to add.");
			}
			$e = &$host->findHost(FUN_MARKER);
			
			if (!in_array($argv[2], $e['hosts'])) {
				$e['hosts'][] = $argv[2];
				echo("Site added to blacklist.\n");
			} else {
				fatal_error("Site already exists in my blacklist.");
			}
			break;
		
		default:
			fatal_error("Unrecognized command: '{$argv[1]}'. ".
						"Valid commands: on, off, addsite [site].");
	}
	$host->save();
}

function fatal_error($message)
{
	printf("Error: %s\n", $message);
	exit;
}