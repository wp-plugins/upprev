=== upPrev ===
Contributors: iworks
Donate link: http://iworks.pl/donate/upprev.php
Tags: animated, animation, box, fade, featured, featured, flyout, fly-out, links, new york times, notification, NYTimes, post, posts, previous post, previous posts, related, related content, rtl, seo, slider, thumbnail
Requires at least: 3.3
Tested up to: 4.3
Stable tag: 3.3.29
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display cool, animated flyout or fade box with related content. Just like New York Times.

== Description ==

Display cool, animated flyout or fade box with related content. Just like New York Times.

When a reader scrolls to the bottom of a single post, page or custom post
type, a button animates in the page’s bottom right or left corner,
allowing the reader to select the previous or random available post or posts
in the selected configuration:

1. Just previous
2. Previous in category
3. Previous in tag
4. Random
5. Related using YARPP (only post/pages)

= Translators =

* Brazilian Portuguese (pt_BR) - [Leonardo Antonioli](http://www.tobeguarany.com/)
* Bulgarian (bg_BG) - [Martin Halachev](http://wordpress.org/support/profile/mhalachev)
* Czech (cs_CZ) - [Michal Bláha](http://michalblaha.cz/)
* Dutch (nl_NL) - [Ruud Kok](http://www.ruudkok.nl/)
* French (fr_FR) - Eva, [Agence web - My Client is Rich](http://myclientisrich-leblog.com/)
* German (de_DE) - [Mario Wolf](http://wolfmedien.de/)
* Hebrew (he_IL) - [עמיעד](http://hatul.info), [של אודי בורג](http://blog.udiburg.com)
* Italian (it_IT) - [Francesco Giossi](http://www.giossi.com)
* Polish (pl_PL) - [Marcin Pietrzak](http://iworks.pl/)
* Romanian (ro_RO) - [Florin Arjocu](http://drumliber.ro/)
* Russian (ru_RU) - [Вадим Сохин](http://webbizreshenie.ru/)
* Simplified Chinese - [Leo](http://smallseotips.com/)
* Slovak (sk_SK) - Daniel Schmidt
* Spanish (er_ES) - [Apasionados del Marketing](http://www.apasionadosdelmarketing.es)
* Tagalog (tl_TL) - [Kel DC](https://profiles.wordpress.org/kel-dc)
* Turkish (tr_TR) - [wpdestek](http://wordpress.org/support/profile/wpdestek/)
* Vietnamese (vi_VI) - [Xman](http://thegioimanguon.com/)

If you have created your own language pack, or have an update of an existing one, you can send [gettext PO and MO files](http://codex.wordpress.org/Translating_WordPress) to me so that I can bundle it into upPrev. You can [download the latest POT file from here](http://plugins.svn.wordpress.org/upprev/trunk/languages/upprev.pot).

== Installation ==

1. Upload upPrev to your plugins directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Configure upPrev plugin using Appearance -> upPrev

== Frequently Asked Questions ==

= upPrev is turn on, but ther is no box, what now? =

First of all, check your template. Rof proper work plugin requires function `wp_head` and `wp_footer`. If your template dont use one of theme, upPrev will not work. If you cant check this action in your templates manualy use this code to check it: https://gist.github.com/378450

= My website is not in English, will upPrev work? =

upPrev plugin will work on websites in the following languages:

* Brazilian Portuguese
* Bulgarian
* Czech
* Dutch
* French
* German
* Hebrew
* Italian
* Polish
* Romanian
* Russian
* Simplified Chinese
* Slovak
* Spanish
* Turkish
* Vietnamese

= How to add default image to post without thumbnail? =

Use the `iworks_upprev_image` action:
`
<?php
add_action( 'iworks_upprev_image' , 'default_image' );
function default_image()
{
    return '<img src="image.png" alt="" />';
}
`
= How to change post thubnail to other image? =

Use the `iworks_upprev_get_the_post_thumbnail` filter:
`
<?php
add_filter( 'iworks_upprev_get_the_post_thumbnail' , 'change_thumbnail' );
function change_thumbnail( $image )
{
    return '<img src="image.png" alt="" />';
}
`

= How to add upPrev for pages or custom post types? =

Yes. Just select post types on `Appearance -> upPrev -> Content` page in `Select post types` section.

= How I can customize with my own styles? =

See here: [How I can customize with my own styles?](http://upprev.com/faq/how-i-can-customize-with-my-own-styles.html)

= Need more snippets? =

Visit: [upPrev: snippet archive](http://upprev.com/tag/snippet)

== Screenshots ==

1. upPrev on post
2. upPrev options: apperance
3. upPrev options: content
4. upPrev options: links
5. upPrev options: cache

== Changelog ==

= 3.3.29 =

* Release date: 2015-09-01
* IMPROVMENT: added Tagalog translation by [Kel DC](https://profiles.wordpress.org/kel-dc)

= 3.3.28 =

* IMPROVMENT: added Slovak translation by Daniel Schmidt

= 3.3.27 =

* IMPROVMENT: added Dutch translation by [Ruud Kok](http://www.ruudkok.nl/)

= 3.3.26 =

* BUGFIX: fixed empty post_type value thx to [Zeus](http://wordpress.org/support/profile/prabhakaraan) [UpPrev error - array_key_exists()!](http://wordpress.org/support/topic/upprev-error-array_key_exists)
* IMPROVMENT: added Italian translation by [Francesco Giossi](http://www.giossi.com/)

= 3.3.25 =

* IMPROVMENT: updated Simplified Chinese translation by [Leo](http://smallseotips.com/)
* IMPROVMENT: added filter '[iworks_upprev_box_title](http://upprev.com/documentation/filter-reference/iworks_upprev_box_title)' for box title, return false to remove title

= 3.3.24 =

* BUGFIX: prevent to display upPrev box on attachment page thx to [Swaps4](http://wordpress.org/support/profile/swaps4) [Upprev displaying on attachment pages with no styling](http://wordpress.org/support/topic/upprev-displaying-on-attachment-pages-with-no-styling)
* BUGFIX: remove add_contextual_help function (deprecated from 3.3).
* IMPROVMENT: updated IworksOptionClass to 2.0.0

= 3.3.23 =

* BUGFIX: default value only when is need thx to [Jeff](http://wordpress.org/support/profile/lambje) [Offset Not Working](http://wordpress.org/support/topic/offset-not-working)
* IMPROVMENT: updated IworksOptionClass to 1.7.7

= 3.3.22 =

* IMPROVMENT: add [iworks_upprev_check filter](http://upprev.com/documentation/filter-reference/iworks_upprev_check), see documentation: [Filter Reference – iworks_upprev_check](http://upprev.com/fiter_reference_iworks_upprev_check.html)

= 3.3.21 =

* BUGFIX: replace WP_PLUGIN_URL with plugins_url() thx to [tigr](http://wordpress.org/support/profile/tigr) [SSL compatibility](http://wordpress.org/support/topic/ssl-compatibility)

= 3.3.20 =

* IMPROVMENT: updated IworksOptionClass to 1.7.4
* IMPROVMENT: check upPrev compatybility with WordPress 3.7
* BUGFIX: fixed "last selected tab"

= 3.3.19 =

* IMPROVMENT: updated Hebrew translation by [של אודי בורג](http://blog.udiburg.com)

= 3.3.18 =

* IMPROVMENT: updated Bulgarian translation by [Martin Halachev](http://wordpress.org/support/profile/mhalachev)

= 3.3.17 =

* BUGFIX: Move custom css after wp_enqueue_style. thx to [007me](http://wordpress.org/support/profile/007me) [Can't change font size and style and costumize close button](http://wordpress.org/support/topic/cant-change-font-size-and-style-and-costumize-close-button)

= 3.3.16 =

* BUGFIX: Excerpt number of words to show option not working for a concrete excerpt. thx to [gyalokai](http://wordpress.org/support/profile/gyalokai) [Excerpt number of words to show option not working](http://wordpress.org/support/topic/excerpt-number-of-words-to-show-option-not-working)
* IMPROVMENT: updated IworksOptionClass to 1.7.2
* IMPROVMENT: added box to front page thx to [SARed](http://wordpress.org/support/profile/sared) [Using Upprev on a front page with latest posts?](http://wordpress.org/support/topic/using-upprev-on-a-front-page-with-latest-posts)

= 3.3.15 =

* IMPROVMENT: added Hebrew translation by [עמיעד](http://hatul.info)

= 3.3.14 =

* BUGFIX: fixed limit for taxonomies thx to [darkjedipete](http://wordpress.org/support/profile/darkjedipete)

= 3.3.13 =

* IMPROVMENT: added Czech translation by [Michal Bláha](http://michalblaha.cz/)

= 3.3.12 =

* BUGFIX: fixed compatybility errors with YARPP 4.x version thx to [adamdport](http://wordpress.org/support/profile/adamdport)
* IMPROVMENT: add css to changed tabs class in WordPress 3.5
* IMPROVMENT: check upPrev compatybility with WordPress 3.5

= 3.3.11 =

* IMPROVMENT: added Bulgarian translation by [Martin Halachev](http://wordpress.org/support/profile/mhalachev)

= 3.3.10 =

* IMPROVMENT: added Spanish translation by [Ramón Rautenstrauch](http://www.apasionadosdelmarketing.es/about/)

= 3.3.9 =

* IMPROVMENT: added Romanian translation by [Florin Arjocu](http://drumliber.ro/)

= 3.3.8 =

* BUGFIX: critical update, plugin crash site if choose no post types

= 3.3.7 =

* IMPROVMENT: added Russian translation by [Вадим Сохин](http://webbizreshenie.ru/)

= 3.3.6 =

* IMPROVMENT: added German translation by [Mario Wolf](http://wolfmedien.de/)

= 3.3.5 =

* BUGFIX: fixed double output when using YARPP thx to [gyutae](http://wordpress.org/support/profile/gyutae)
* BUGFIX: hide developer admin option

= 3.3.4 =

* IMPROVMENT: added Brazilian Portuguese translation by [Leonardo Antonioli](http://www.tobeguarany.com/)
* BUGFIX: fixed minor description bug (thx Eva)

= 3.3.3 =

* IMPROVMENT: added Vietnamese translation by [Xman](http://thegioimanguon.com/)
* BUGFIX: use crc32 to build ids for tabbed config, wich collapsed in other than utf8 charset

= 3.3.2 =

* IMPROVMENT: added GA option: non-interaction to prevent events in bounce-rate calculation.

= 3.3.1 =

* IMPROVMENT: added French translation by [Eva](http://myclientisrich-leblog.com/)

= 3.3 =

* IMPROVMENT: added option to hide upPrevBox on mobile devices, matching imlemented from [WP Mobile Detector](http://wordpress.org/extend/plugins/wp-mobile-detector/) ticket from [forum](http://wordpress.org/support/topic/plugin-upprev-mobile-themes)

= 3.2 =

* IMPROVMENT: added action *[iworks_upprev_image](http://upprev.com/documentation/action-reference/iworks_upprev_image)* - you can add own code to produce icon, when them don't support post-thumbnails
* IMPROVMENT: added thumbnail filter *iworks_upprev_get_the_post_thumbnail* - now you can easy change thumbnail
* IMPROVMENT: added purging transient cache entries from $wpdb->options table when turn off this cache [forum](http://wordpress.org/support/topic/plugin-upprev-crazy-number-of-wp-options-database-entries)
* IMPROVMENT: add check _gaq object exist
* CHECK: checked compatybility to WordPress 3.3
* IMPROVMENT: updated IworksOptionClass to version 1.0.1

= 3.1.1 =

* IMPROVMENT: added ability to turn off "remove_all_filters" function

= 3.1 =

* IMPROVMENT: change GA trackEvent syntax
* IMPROVMENT: added Turkish translation by [wpdestek](http://wordpress.org/support/profile/wpdestek)

= 3.0.1 =

* BUGFIX: fixed printing GA code when "I don't have GA tracking on site." is unticked. [forum](http://wordpress.org/support/topic/plugin-upprev-google-analytics-tracking-code-error-ga-tracking-installed) thx [win101](http://wordpress.org/support/profile/win101)

= 3.0 =

* BUGFIX: fixed end date filter for imported posts
* BUGFIX: fixed javascript conflict on edit post screen
* BUGFIX: fixed problem with unchecking 'Excerpts'. [forum](http://wordpress.org/support/topic/plugin-upprev-bugs-no-box-in-firefox-6-offset-doesnt-work-disable-excerpts-doesnt-work) thx [benjamin](http://wordpress.org/support/profile/kbenjamin)
* BUGFIX: fixed sticky posts display loop
* BUGFIX: fixed thumbnail display problem
* IMPROVMENT: added filter '[iworks_upprev_box_item](http://upprev.com/documentation/filter-reference/iworks-upprev-box-item)' for any item excerpt YARPPs
* IMPROVMENT: added GA track: view box and click link
* IMPROVMENT: added option *ignore sticky posts*
* IMPROVMENT: added sanitize function for offset
* IMPROVMENT: added thumbnail preview on posts/pages list
* IMPROVMENT: cleaning empty styles from custom css field
* REFACTORING: option managment

= 2.3.7 =

* BUGFIX: fixed problem for defaults post_type if no one choosed [forum](http://wordpress.org/support/topic/plugin-upprev-error)

= 2.3.6 =

* BUGFIX: fixed problem with using thumbnails in themes with thumbnail support [forum](http://wordpress.org/support/topic/plugin-upprev-version-235-update-breaks-thumbnail-support)
* IMPROVMENT: added custom css rules (forum](http://wordpress.org/support/topic/plugin-upprev-version-235-update-breaks-thumbnail-support)

= 2.3.5 =

* BUGFIX: fixed problem with using thumbnails in themes without thumbnail support

= 2.3.4 =

* BUGFIX: fixed problem with default values and values saving (again)
* IMPROVMENT: added correct way to enquene style and js files

= 2.3.3 =

* BUGFIX: hide configuration link on plugins list page for WordPress MU
* BUGFIX: fixed problem with post excerpt
* BUGFIX: fixed problem with default values and values saving

= 2.3.2 =

* BUGFIX: fixed translation bug
* BUGFIX: removed date limit for random posts
* BUGFIX: fixed open in new window bug
* IMPROVMENT: added limit to display only on selected post types [forum](http://wordpress.org/support/topic/plugin-upprev-previous-post-animated-notification-custom-post-types)

= 2.3.1 =

* BUGFIX: fixed small bug with display option

= 2.3 =

* IMPROVMENT: added filter **iworks_upprev_box**
* IMPROVMENT: added tabed options (based on [Breadcrumb NavXT](http://wordpress.org/extend/plugins/breadcrumb-navxt/) plugin
* IMPROVMENT: added prefix and suffix to urls
* IMPROVMENT: added option to allow open links in new window
* IMPROVMENT: added integration with [YARPP](http://wordpress.org/extend/plugins/yet-another-related-posts-plugin/)
* BUGFIX: fixed [Transients Cache Lifetime is set to wrong seconds](http://wordpress.org/support/topic/plugin-upprev-transients-cache-lifetime-is-set-to-wrong-seconds)
* BUGFIX: fixed deactivation hook option names

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

* BUGFIX: When they scroll down again, the box flies out, which -- on a small screen -- can obscure a big chunk of the content. [forum](http://wordpress.org/support/topic/plugin-upprev-return-to-top-of-post-after-clicking-x)

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
* IMPROVMENT: added Polish translation by [Marcin Pietrzak](http://iworks.pl/)

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

* INIT: copy and massive refactoring of plugin [upPrev Previous Post Animated Notification](http://wordpress.org/extend/plugins/upprev-nytimes-style-next-post-jquery-animated-fly-in-button/)

== Upgrade Notice ==

= 3.3.13 =

Add Czech translation.

= 3.3.12 =

Fixed using YARPP 4.x. Check upPrev compatybility with WordPress 3.5.

= 3.3.11 =

Add Bulgarian translation.

= 3.3.10 =

Add Spanish translation.

= 3.3.9 =

Add Russian translation.

= 3.3.8 =

Critical update to prevent site crash!

= 3.3.3.1 =

Add Brazilian Portuguese translation.

= 3.0 =

Add GA tracking for display and click. Add filter and action to modify result.

= 2.3 =

Add YARPP integration.

= 2.1 =

Add support to custom post type.

= 2.1 =

Add some apperance, cache improvments. Scripts and styles optimization. New order available: random.

= 2.0.1 =

Add a polish translation. Fix cache refresh missing after change plugin configuration.

= 2.0 =

More configuration options. Uses transient cache to store results. Optimization activation & deactivation process.

