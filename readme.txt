=== Another Twitter Plugin ===
Contributors: kunicmarko20
Donate Link:
Tags: Twitter, Twitter dev, Best Twitter Plugin, Twitter Social, Twitter Custom, Best Twitter, Twitter customizable plugin, fully customizable Twitter plugin, Twitter plugin, Another Twitter Plugin, ATP, Tweets,
Requires at least: 4.0
Tested up to: 4.7
Stable tag: 4.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Twitter plugin for developers, plugin that you want and need, fully customizable style, works with multiple hashtags or usernames and you are not limited to only your account for tweets.

== Description ==

Did you ever want Twitter plugin that allows you to setup multiple hashtags or usernames, collect new tweets on selected time and on top of that, you can define style and format as you wish for displaying ?

Look no more, Another Twitter Plugin is here, yes it is "another" one of that plugins, but this one you want to have and it will save you time.

A few options:

*   Enable/Disable auto collecting new data
*   Reset old Tweets
*   Define up to 5 usernames or hashtags you want to collect tweets from
*   Choose what information to display and how
*   Choose how often to collect new data
*   Choose how much tweets to save/display on yor website

== Installation ==


1. Upload the plugin files to the `/wp-content/plugins/another-twitter-extension` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Find Another Twitter-> Twitter Settings in your Admin Panel and add your twitter application.
1. Find Another Twitter-> Plugin Settings in your Admin Panel and setup your hashtags/usernames.
1. Find Another Twitter-> Dashboard in your Admin Panel and enable plugin.
1. Copy the Shortcode **[dt_atp_twitter]** :

*   If you want to add it to sidebar follow this :
http://www.wpbeginner.com/wp-tutorials/how-to-use-shortcodes-in-your-wordpress-sidebar-widgets/data
*   If you want to add it directly to your source code, just add this to your code: **`<?php echo do_shortcode( '[dt_atp_twitter]' ); ?>`**
*   If you want to add it to some kind of post, just add this to your post text: **[dt_atp_twitter]**

== Frequently Asked Questions ==

= Will WP cron work if I have low traffic? =

No, you will have to add your website to cronjob if you have low traffic.


== Screenshots ==

1. Dashboard
2. Display Style
3. Plugin Settings
4. Twitter Settings

== Changelog ==
= 1.0.6 =
* Fix problem with twitter api returning float for id

= 1.0.5 =
* Update wp cron function

= 1.0.4 =
* Fix problem with regex for #/@ in status output

= 1.0.3.1 =
* Ajax problem fix

= 1.0.3 =
* Custom Date Format setup

= 1.0.2 =
* Few UX Updates

= 1.0.1 =
* Minor Updates

= 1.0 =
* Beta release to public.

== Upgrade Notice ==
= 1.0 =
* Beta release to public.

== How It's Made? ==

This plugin was made using [Abraham Williams](https://abrah.am/) script for [Twitter oAuth](https://github.com/abraham/twitteroauth) , you can find Another Twitter Plugin on Github [here](https://github.com/kunicmarko20/Another-Twitter-Plugin) or you can contact [me](http://kunicmarko.ml) if you need any help.
