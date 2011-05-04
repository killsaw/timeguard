Timeguard - Protects Against Fun
=================================

"Check Hackernews real quick", cries your brain. "It's educational, I swear."
Five hours later, you are still reading blog posts about multivariate testing with Lisp
or similar. 

Timeguard sits atop your system's host file, blocking addresses for 
sites that kill your productivity.

Usage
----------

Enable Timeguard (disables access to sites):

	$ sudo timeguard.php on

Disable Timeguard (removes all ban entries):

	$ sudo timeguard.php off

Add a site to ban:
	
	$sudo timeguard addsite packetstormsecurity.nl

Default Sites
-------------
Timeguard keeps a default list of sites to ban, including:
	
	news.ycombinator.com
	reddit.com
	digg.com
	cnn.com
	daringfireball.net
	
Recommended
-----------
It's recommended you put timeguard into a directory included in your PATH list.

Author
------
Timeguard is written by [Steven Bredenberg](http://killsaw.com/). Questions/comments
email him: steven@killsaw.com
