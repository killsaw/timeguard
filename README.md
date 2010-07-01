Timeguard - Protects Against Fun
=================================

"Check Hackernews real quick", cries your brain. "It's educational, I swear."
Five hours later, you are still reading blog posts about multivariate testing with Lisp
or similar. Timeguard sits atop your system's host file, blocking addresses for 
sites that kill your productivity.

Usage
----------

Enable Timeguard (disables access to sites):

	$ sudo timeguard on

Disable Timeguard (removes all ban entries):

	$ sudo timeguard off

Add a site to ban:
	
	$sudo timeguard addsite packetstormsecurity.nl

Timeguard keeps a default list of sites to ban, including:
	
	news.ycombinator.com
	reddit.com
	digg.com
	cnn.com
	daringfireball.net
	
