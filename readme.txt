=== upPrev ===

Contributors:      iworks
Plugin Name:       upPrev
Plugin URI:        http://iworks.pl/upPrev/
Tags:              next post, previous post, notification, related, upPrev
Author URI:        http://iworks.pl/
Author:            Marcin Pietrzak, Grzegorz Krzyminski, Jason Pelker
Donate link:       http://iworks.pl/donate/upprev.php
Requires at least: 3.0
Tested up to:      3.2
Stable tag:        2.0.1
Version:           2.0.1

== Description ==

When a reader scrolls to the bottom of a single post, a button animates in the page’s bottom right corner, allowing the reader to select the next available post in the selected configuration.
Plugin based on "upPrev Previous Post Animated Notification"

== Installation ==

1. Upload upPrev to your plugins directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure upPrev plugin using Apperance -> upPrev

== Screenshots ==

1. upPrev options
2. upPrev on post

== Changelog ==

= 2.1.0 =

* IMPROVMENT: added box width option
* IMPROVMENT: added box bottom margin option

= 2.0.1 =

* BUGFIX: fixed translation load
* IMPROVMENT: added show box header option
* IMPROVMENT: added stamp for cache key

= 2.0 =

* BUGFIX: fixed display upPrev box in case of an equal height of the window and the document
* IMPROVMENT: added to use transient cache
* IMPROVMENT: added thumbnail width (height depent of theme thumbnail)
* IMPROVMENT: added prevent some options if active theme dosen't support it
* IMPROVMENT: added activation & deactivation hooks (to setup defaults and remove config )
* BUGFIX: remove all filters the_content for post in upPrev box

= 1.0.1 =

* BUGFIX: added post_date as parametr, to get real previous post
* BUGFIX: javascript error
* IMPROVMENT: added header for simple method

= 1.0 =

* INIT: copy and massive refactoring of plugin "upPrev Previous Post Animated Notification

== Upgrade Notice ==

= 2.0.1 =

Add a polish translation. Fix cache refresh missing after change plugin configuration.

= 2.0 =

More configuration options. Uses transient cache to store results. Optimization activation & deactivation process.

