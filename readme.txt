=== Tweetable ===
Contributors: redwall_hp
Plugin URI: http://www.webmaster-source.com/tweetable-twitter-plugin-wordpress/
Author URI: http://www.webmaster-source.com
Donate link: http://www.webmaster-source.com/donate/
Tags: twitter, tweet, tweetable, wordpress, plugin
Requires at least: 2.8
Tested up to: 3.3
Stable tag: 1.2.5

Integrate Twitter with your WordPress blog. Automatically tweet new posts, display your latest tweet in your sidebar, etc. Uses OAuth for user authentication, so your Twitter password is not stored in plain text.



== Description ==

Twitter is big. Too big to ignore if you're a blogger.  It's a great way to connect with your readers, and promote your blog a bit.

Tweetable is intended to help integrate Twitter into your blog. It can automatically tweet links to your blog posts as they are published. It can display your lastest tweet in your sidebar and add a tweetmeme widget after your posts. You can even use it to share a Twitter account among a blog's author's if you wish.

* Automatically tweet your blog posts when they are published. Optionally add Google Analytics campaign tags to the shortened URLs. You also have your pick of URL shorteners.
* Tweet from within WordPress. The plugin adds a dedicated Twitter page where you can browse your friends timeline and post updates. An optional quick-tweet Dashboard widget is available as well.
* Display your latest tweets in your blog sidebar with a customizable widget. Includes support to display follower count.
* Set the minimum user level to access the Twitter page in the WordPress backend.
* Automatically add a full-size or compact Tweetmeme widget to your posts.
* Track tweets based on keywords of your choice via the Twitter API.
* Embed tweets in your posts with oEmbed.

Note: Please ensure that your server is running PHP 5 or higher before installing.



== Installation ==

1. FTP the entire tweetable directory to your Wordpress blog's plugins folder (/wp-content/plugins/).

2. Activate the plugin on the "Plugins" tab of the administration panel.

3. Choose an option from the Twitter menu in the sidebar to run the setup wizard and connect your Twitter account to your blog.


== Upgrading ==
Generally, all you should have to do is click the Update button on the Plugins page when a new update becomes available.



== Frequently Asked Questions ==

= What the @!%$ is OAuth? =
OAuth (http://oauth.net/) is a standard used by sites like Twitter to allow third-party scripts to request access to your account. It's much more secure than simply handing an application your Twitter username and password. Thanks to OAuth, nobody can steal your Twitter password if they managed to gain access to your WordPress database.

= oEmbed? What's that? How do I use it? =
[oEmbed](http://codex.wordpress.org/oEmbed) is a protocol that makes it easier to embed things in your blog posts, such as YouTube videos. If you have the "Auto-embeds" option turned on in your Media settings, then Tweetable will add Twitter oEmbed support. This means you can embed tweets in your blog posts, and they will be rendered in a fancy manner that looks consistant with Twitter.com. To embed a tweet, look for the "Embed this Tweet" link below a status update on Twitter.com. (For an example of where to find it, look [here](https://twitter.com/#!/redwall_hp/statuses/146744631483318273).) Click it and copy the plain Link it gives you. Paste the link on it's own line in your post, and it will be replaced by the entire tweet when you view the published post.

= What is the bookmarklet on the "Tweet" page? =
If you drag it into your browser's bookmarks toolbar, you can tweet a link to a web page just by clicking it. (You will have to log into your blog admin, of course.)

= How do I enable YOURLS support? What does it do? =
YOURLS (http://yourls.org), or "Your Own URL Shortener" is a PHP application by Lester "Gamerz" Chan and Ozh Richard. If you have a short domain name handy, you can quickly set up your own URL shortener, complete with an API that Tweetable can hook into.

In order to let Tweetable create new short URLs using your YOURLS install, you need to configure a few settings on the Twitter->Settings page in WordPress. First you must input your YOURLS username and the domain where it is installed into the "Shortener Login" field. An example would be `you@example.org`. The `@` symbol is used as a separator between the domain name and the username. If YOURLS is installed in a subdirectory of a domain, you may have to use something along the lines of `you@example.org/directory`. The second step is to put the YOURLS password in the "Shortener API Key" field.

= Who made the icons used in Tweetable? =
The icons used throughout Tweetable are part of the Silk Icons set by FamFamFam. http://www.famfamfam.com/lab/icons/silk/

= How do I contact the plugin author? =
If you have a bug report, a feature request, or some other issue, please use the WordPress support forum here: http://wordpress.org/extend/plugins/tweetable/



== Template Tags ==
There are a few template tags available in Tweetable.

* `<?php tweetable_latest_tweets(); ?>` - Outputs your lastest tweets. You can optionally pass a number to it to controll how many it prints. E.g. `<?php tweetable_latest_tweets(5); ?>`.

* `<?php tweetable_follower_count(); ?>` - Prints the number of people following you on Twitter in plain text. You can also call it in the form of `<?php $var = tweetable_follower_count(FALSE); ?>` if you need to have the number returned instead of output to the screen.

* `<?php tweetable_tweetmeme_button(); ?>` - Displays a Tweetmeme (Tweetmeme.com) button. Call `<?php tweetable_tweetmeme_button('compact'); ?>` for the compact version.



== Hooks ==
You can extend Tweetable by tying your theme or plugin functions into the included hooks.

= Filter: tweetable_autotweet_permalink =
Runs when a post is tweeted, before the URL is shortened. Parameters: $permalink.

= Filter: tweetable_autotweet_title =
Runs after the post title is retrieved, but before it is merged into the string to be tweeted. Parameters: $title.

= Filter: tweetable_autotweet_tweet =
Runs immediately before the auto-tweet is sent, after the title and link are combined and (if necessary) shortened. Parameters: $tweet.



== Screenshots ==
1. The Twitter screen in the WordPress admin
2. Tweetable settings
3. The Track screen in the WordPress admin
4. The Twitter Dashboard widget
5. Embedding tweets in blog posts with oEmbed



== Changelog ==

= Version 1.2.5 =
* Clickability improvements! Thanks to [Steffen Vogel](http://www.steffenvogel.de/) for the patch.

= Version 1.2.4 =
* The plugin repository wasn't updating to show the new documentation changes, so I'm pushing a new version.
* Added an extra screenshot.

= Version 1.2.3 =
* Added oEmbed support for Twitter! Now if you paste a properly-formed URL into a post, it will be replaced with an awesome widget from Twitter.

= Version 1.2.2 =
* Fixed "Missing Argument 2" warning.

= Version 1.2.1 =
* Made a minor change to the widget-handling code to replace a couple of deprecated functions with their new counterparts.

= Version 1.2.0 =
* Removed some debugging code. If you are running 1.1.9, please update.

= Version 1.1.9 =
* Added support for J.mp version 3 of the Bit.ly/J.mp API. Thanks to WordPress.org forum member xeresjp for the patch!

= Version 1.1.8 =
* Added support for the YOURLS shortener.
* Changed the shortener API key field (on the settings page) from a "text" to a "password" input.

= Version 1.1.7 =
Tweets with "&" symbols are no longer cut off.

= Version 1.1.6 =
Added a conditional statement to check if the TwitterOAuth class is already included, for compatibility with some other plugins.

= Version 1.1.5 =
* Added Bit.ly shortener support. (Requires entering your API key.)
* Added Ow.ly shortener support.

= Version 1.1.4 =
* Fixed issue where Twitter outages sometimes prevented blogs from loading. (The Latest Tweets widget would trigger a fatal error and end the script.)

= Version 1.1.3 =
* Added support for StumbleUpon's Su.pr URL shortener.
* Added a message bubble to the installer when the Twitter account is being changed.

= Version 1.1.2 =
The Change Account link is *really* fixed now...

= Version 1.1.1 =
Okay, *now* the Change Account link is fixed...

= Version 1.1.0 =
* Fixed the Change Account link in the plugin settings.
* Added three filter hooks to the auto-tweet subroutine.

= Version 1.0.9 =
Fix for the "You do not have sufficient permissions to access this page" error when trying to run the installer under WordPress 2.8.1.

= Version 1.0.8 =
The 1.0.7 update didn't go over properly. Missing files, stuff like that. 1.0.8 should fix any problems.

= Version 1.0.7 =
* Added #hashtag support. Any hashtags in tweets are made into clickable triggers that open a dialog with a Twitter Search of that hashtag.
* The "Documentation" link in the Tweetable page footers is now parsed into (much more legible) HTML.
* Added "Settings" link near the Activate/Deactivate and Edit links on the plugins tab.
* Updated README, changed "Version History" to "Changelog."

= Version 1.0.6 =
* Added an option to the widget for a "widget title link," so you can optionally link your widget's title to your Twitter profile.
* Added an upper limit for the "Number of Tweets to Show" option in the widget (20), to prevent unexpected results if set too high.

= Version 1.0.5 =
* Auto-tweets no longer show-up in the sidebar widget, as long as you specify a tweet prefix in the settings.
* Option to exclude Tweetable stylesheet. If you don't display your latest tweets publicly on your blog, why should a stylesheet be included?
* Added Tweetable "Tweet This" bookmarklet. You can find it on the "Tweet" page.
* Added "3.ly" to the supported URL shorteners.
* Added a link to reset the setup wizard if something goes wrong and it doesn't complete.

= Version 1.0.4 =
Fixed error when Safe Mode is on, stopped Shorten URL icon from disappearing when it finished blinking.

= Version 1.0.3 =
Fluid-width dashboard widget (with loading throbber), 140-char limit check when posting updates, AJAX calls include token for security.

= Version 1.0.2 =
Fixed bug triggering fatal error on all installations.

= Version 1.0.1 =
Fixed issue with items on the Track screen not deleting, PHP4 detection problems.

= Version 1.0 =
Initial Release