=== Tweetable ===
Contributors: redwall_hp
Plugin URI: http://www.webmaster-source.com/tweetable-twitter-plugin-wordpress/
Author URI: http://www.webmaster-source.com
Donate link: http://www.webmaster-source.com/donate/
Tags: twitter, tweet, tweetable, wordpress, plugin
Requires at least: 2.7
Tested up to: 2.7.1
Stable tag: 1.0.4

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

Note: Please ensure that your server is running PHP 5 or higher before installing.



== Installation ==

1. FTP the entire tweetable directory to your Wordpress blog's plugins folder (/wp-content/plugins/).

2. Activate the plugin on the "Plugins" tab of the administration panel.

3. Choose an option from the Twitter menu in the sidebar to run the setup wizard and connect your Twitter account to your blog.


== Upgrading ==
Generally, all you should have to do is click the Update button on the Plugins page when a new update becomes available.



== Frequently Asked Questions ==

= What the @!%$ is OAuth? =
OAuth (http://oauth.net/) is a standard used by sites like Twitter to allow third-party scripts to request access to your account. It's much more secure than simply handing an application your Twitter username and password. Thanks to OAuth, nobody can steal your Twitter password if they managed to gain access to your database.

= Who made the icons used in Tweetable? =
The icons used throughout Tweetable are part of the Silk Icons set by FamFamFam. http://www.famfamfam.com/lab/icons/silk/

= How do I contact the plugin author? =
If you have a bug report, a feature request, or some other issue, please use the contact form found here to contact me: http://www.webmaster-source.com/about/



== Template Tags ==
There are a few template tags available in Tweetable.

* `<?php tweetable_latest_tweets(); ?>` - Outputs your lastest tweets. You can optionally pass a number to it to controll how many it prints. E.g. `<?php tweetable_latest_tweets(5); ?>`

* `<?php tweetable_follower_count(); ?>` - Prints the number of people following you on Twitter in plain text. You can also call it in the form of `<?php $var = tweetable_follower_count(FALSE); ?>` if you need to have the number returned instead of output to the screen.

* `<?php tweetable_tweetmeme_button(); ?>` - Displays a Tweetmeme (Tweetmeme.com) button. Call `<?php tweetable_tweetmeme_button('compact'); ?>` for the compact version.



== Screenshots ==
1. The Twitter screen in the WordPress admin
2. Tweetable settings
3. The Track screen in the WordPress admin
4. The Twitter Dashboard widget



== Version history ==
* Version 1.0
* Version 1.0.1 - Fixed issue with items on the Track screen not deleting, PHP4 detection problems.
* Version 1.0.2 - Fixed bug triggering fatal error on all installations.
* Version 1.0.3 - Fluid-width dashboard widget (with loading throbber), 140-char limit check when posting updates, AJAX calls include token for security.
* Version 1.0.4 - Fixed error when Safe Mode is on, stopped Shorten URL icon from disappearing when it finished blinking.