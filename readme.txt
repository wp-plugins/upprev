=== upPrev ===

Contributors:      iworks
Plugin Name:       upPrev
Plugin URI:        http://iworks.pl/upPrev/
Tags:              next post, previous post, notification, related, upPrev
Author URI:        http://iworks.pl/
Author:            Marcin Pietrzak, Grzegorz Krzyminski, Jason Pelker
Donate link:       http://iworks.pl/donate/upprev.php
Requires at least: 3.1
Tested up to:      3.2.1
Stable tag:        2.2.1
Version:           2.2.1

When a reader scrolls to the bottom of a single post, show next post in the selected configuration.

== Description ==

When a reader scrolls to the bottom of a single post, a button animates in the page’s bottom right or left corner, allowing the reader to select the next available post in the selected configuration.
Plugin based on "upPrev Previous Post Animated Notification"

== Installation ==

1. Upload upPrev to your plugins directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure upPrev plugin using Apperance -> upPrev

== Screenshots ==

1. upPrev options
2. upPrev on post

== Changelog ==

* IMPROVMENT: added filter **iworks_upprev_box**

= 2.2.1 =

* BUGFIX: fixed display problem with document shorter than browser
* IMPROVMENT: document post type as checkbox list

= 2.2 =

* IMPROVMENT: added upPrev configuration link to admin bar
* IMPROVMENT: added registered custom posts
* BUGFIX: fixed error if the behavior of boxing display for html element
* BUGFIX: fixed wrong method post_type selection

= 2.1.2 =

* BUGFIX: remove margin-top for title element
* IMPROVMENT: added display taxonomies limit

= 2.1.1 =

* BUGFIX: When they scroll down again, the box flies out, which -- on a small screen -- can obscure a big chunk of the content. http://wordpress.org/support/topic/plugin-upprev-return-to-top-of-post-after-clicking-x

= 2.1 =

* IMPROVMENT: added box width option
* IMPROVMENT: added box bottom and side margin option
* IMPROVMENT: added transient cache for scripts and styles
* IMPROVMENT: added actions: **iworks_upprev_box_before** and **iworks_upprev_box_after**, called inside the upPrevBox, before and after post. Now you can add some elements to upPrevBox whithout plugin modyfication.
* IMPROVMENT: added option to display (or not) close button
* IMPROVMENT: added post type choose: post, page or any.
* IMPROVMENT: added random order for displayed posts

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

= 2.1 =

Add support to custom post type.

= 2.1 =

Add some apperance, cache improvments. Scripts and styles optimization. New order available: random.

= 2.0.1 =

Add a polish translation. Fix cache refresh missing after change plugin configuration.

= 2.0 =

More configuration options. Uses transient cache to store results. Optimization activation & deactivation process.

