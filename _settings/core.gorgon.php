<?php

////	Puff Core Settings
//
// The root URL of your site (with trailing slash)
$Sitewide['Settings']['Site Root']                      = 'http://local.localtest.me/gorgon/';
// A title for your site.
$Sitewide['Settings']['Site Title']                     = 'Gorgon';
// Something much longer or much shorter.
$Sitewide['Settings']['Alternative Site Title']         = 'GitHub Organisation Management';
// Stripping the .php from URLs requires server-side configuration.
// Check it works before enabling it.
$Sitewide['Settings']['Strip PHP from URLs']            = true;
// Stop the loading of asset from external domains.
$Sitewide['Settings']['Content Security Policy Header'] = false;
// Honor Do Not Track Headers
$Sitewide['Settings']['Honor DNT Headers']              = true;
// Change to your tracking id like 'UA-1234567-89' for tracking.
$Sitewide['Settings']['Google Analytics']               = false;
// Use a secure connection in future if it's available.
$Sitewide['Settings']['Try to Secure']                  = false;
// Load all the functions to be ready.
$Sitewide['Settings']['AutoLoad']['Functions']          = true;
// When to paginate the sitemap, Google recommends 10 MB or 50k entries.
$Sitewide['Sitemap']['Pagination']                      = 10000;


// Some social settings for your site.
$Sitewide['Social']['Facebook'] = 'https://www.facebook.com/you';
$Sitewide['Social']['Google+']  = 'https://plus.google.com/you';
$Sitewide['Social']['Twitter']  = 'https://twitter.com/you';

// Default Page Settings
$Sitewide['Page']['Title']          = 'Gorgon';
$Sitewide['Page']['Author']         = 'Gorgon';
$Sitewide['Page']['Description']    = 'A Gorgon instance.';
$Sitewide['Page']['Tagline']        = 'A Gorgon instance.';
$Sitewide['Page']['Image']          = '';
$Sitewide['Page']['Published']      = '';
$Sitewide['Page']['Theme Color']    = '#3892E0';
$Sitewide['Page']['Author Name']    = '';
$Sitewide['Page']['Google+ Author'] = $Sitewide['Social']['Google+'];
$Sitewide['Page']['Twitter Author'] = $Sitewide['Social']['Twitter'];
$Sitewide['Page']['Twitter Site']   = $Sitewide['Social']['Twitter'];

$Sitewide['Page']['UsejQl']         = false;
$Sitewide['Page']['JQ']             = 'https://cdn.jsdelivr.net/gh/jquery/jquery@latest/dist/jquery.min.js';
$Sitewide['Page']['JS'][]           = 'https://cdn.jsdelivr.net/gh/Mottie/tablesorter@2/dist/js/jquery.tablesorter.combined.min.js';

// Version
$Sitewide['Version']['Core'] = '0.5.0';
