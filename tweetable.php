<?php
/*
Plugin Name: Tweetable
Plugin URI: http://www.webmaster-source.com/tweetable-twitter-plugin-wordpress/
Description: Integrate Twitter with your WordPress blog. Automatically tweet new posts, display your latest tweet in your sidebar, etc.
Author: Matt Harzewski (redwall_hp)
Author URI: http://www.webmaster-source.com
Version: 1.2.5
*/



/*** Version Checking. ***/
global $wp_version;
if (version_compare($wp_version, '2.7', '<')) {
	exit("Tweetable requires WordPress 2.7 or greater.");
}
if (version_compare(PHP_VERSION, '5.0.0', '<')) {
	exit("Tweetable requires PHP 5 or greater.");
}
	



/*** Includes ***/
include 'mh_twitter_class.php';
include 'tweet_embeds.php';




/*** Hooks ***/
add_filter('the_content', 'tweetable_add_tweetmeme');
add_action('widgets_init', 'tweetable_create_widget');
add_action('init', 'tweetable_frontend_styles_and_scripts');
add_action('publish_post', 'tweetable_publish_tweet', 100);
add_filter('plugin_action_links', 'tweetable_add_plugin_links', 10, 2);




/*** Add Admin Menus ***/
if (is_admin()) {
	include 'admin_menus.php';
}




/*** Template Tags ***/

//Display the latest tweets by the Tweetable user
function tweetable_latest_tweets($num=3) {

	$twitter_user = get_option('tweetable_twitter_user');
	
	$latest = tweetable_get_recent_tweets();
	echo '<ol class="tweetable_latest_tweets">';
	for ( $counter=0; $counter <= ($num-1); $counter += 1 ) {
		echo '<li class="tweetable_item">';
		$date = strftime('%A, %m.%d.%y %H:%M', strtotime($latest['tweets'][$counter]['created_at']));
		echo '<span class="twitter_status">';
		echo '<span class="status-text">'.make_clickable(tweetable_make_clickable($latest['tweets'][$counter]['text'])).'</span>';
		echo '<span class="twitter_meta">'.$date.'</span>';
		echo '</span>';
		echo '</li>';
	}
	echo '</ol>';


}

function tweetable_make_clickable($str) {
	$str = preg_replace('/\#([a-z0-9]+)/i', '<a href="http://search.twitter.com/search?q=%23$1">#$1</a>', $str);
	$str = preg_replace('/@([a-z0-9_]+)/i', '<a href="http://twitter.com/$1">@$1</a>', $str);

	return $str;
}

//Get the follower count of the Tweetable user. Pass FALSE to return instead of echo.
function tweetable_follower_count($output=TRUE) {

	$twitter_user = get_option('tweetable_twitter_user');
	
	$latest = tweetable_get_recent_tweets();
	
	if ($output != TRUE) {
		return $latest['tweets']['0']['user']['followers_count'];
	} else {
		echo $latest['tweets']['0']['user']['followers_count'];
	}

}


//Display a Tweetmeme button. Pass string 'compact' for smaller size.
function tweetable_tweetmeme_button($type='full') {

	$twitter_user = get_option('tweetable_twitter_user');

	if ($type == 'full') {
		$tweetmeme = '<script type="text/javascript">';
		$tweetmeme .= 'tweetmeme_url = \''.get_permalink().'\';';
		$tweetmeme .= 'tweetmeme_source = \''.$twitter_user.'\';';
		$tweetmeme .= '</script>';
		$tweetmeme .= '<script type="text/javascript" src="http://tweetmeme.com/i/scripts/button.js"></script>';
		return $tweetmeme;
	} else {
		$tweetmeme = '<script type="text/javascript">';
		$tweetmeme .= 'tweetmeme_url = \''.get_permalink().'\';';
		$tweetmeme .= 'tweetmeme_source = \''.$twitter_user.'\';';
		$tweetmeme .= 'tweetmeme_style = \'compact\';';
		$tweetmeme .= '</script>';
		$tweetmeme .= '<script type="text/javascript" src="http://tweetmeme.com/i/scripts/button.js"></script>';
		return $tweetmeme;
	}
		
}




/*** Sidebar Widget ***/
function tweetable_create_widget() {

	$installed = get_option('tweetable_account_activated');
	if (!$installed) {
		return;
	}

	if (!get_option('tweetable_widget_options')) {
		$options['title'] = 'Twitter';
		$options['num_tweets'] = '1';
		$options['follow_count'] = '1';
		add_option('tweetable_widget_options', $options);
	}

	wp_register_sidebar_widget('tweetable', 'Tweetable', 'tweetable_write_widget');
	wp_register_widget_control('tweetable', 'Tweetable', 'tweetable_widget_options');
	
}


function tweetable_write_widget($args) {

	extract($args);
	$options = get_option('tweetable_widget_options');
	$twitter_user = get_option('tweetable_twitter_user');
	$tweet_prefix = get_option('tweetable_auto_tweet_prefix');
	
	echo $before_widget;
	
	if ($options['title'] != '') {
		if ($options['title_link'] == '') {
			$widget_title = $options['title'];
		} else {
			$widget_title = '<a href="'.$options['title_link'].'">' . $options['title'] . '</a>';
		}
		echo "\n".$before_title; echo $widget_title; echo $after_title;
	}
	
	$latest = tweetable_get_recent_tweets();
	$followers = $latest['tweets']['0']['user']['followers_count'];
	$counter = 0;
	if ($tweet_prefix != '') {
		foreach ($latest['tweets'] as $tweet) {
			if ( strpos($tweet['text'], $tweet_prefix) !== FALSE ) {
				unset($latest['tweets'][$counter]);
			}
			$counter++;
		}
	}
	echo '<ol class="tweetable_latest_tweets">';
	$counter = 0;
	foreach ( $latest['tweets'] as $tweet ) {
		echo '<li class="tweetable_item">';
		$date = strftime('%A, %m.%d.%y %H:%M', strtotime($tweet['created_at']));
		echo '<span class="twitter_status">';
		echo '<span class="status-text">'.make_clickable(tweetable_make_clickable($tweet['text'])).'</span>';
		echo '<span class="twitter_meta">'.$date.'</span>';
		echo '</span>';
		echo '</li>';
		if ($counter == $options['num_tweets']-1) {
			break;
		}
		$counter++;
	}
	echo '</ol>';
	if ($options['follow_count'] == '1') {
		echo '<span class="tweetable_follow">Follow <a href="http://twitter.com/'.$twitter_user.'">@'.$twitter_user.'</a> ('.$followers.' followers)</span>';
	}
	echo $after_widget;
	
}


function tweetable_widget_options() {

	if ($_POST['tweetable_save_widget'] != '') {
		$options['title'] = $_POST['tweetable_title'];
		//$options['num_tweets'] = $_POST['tweetable_num_tweets'];
		($_POST['tweetable_num_tweets'] < 1) ? $options['num_tweets'] = '1' : $options['num_tweets'] = $_POST['tweetable_num_tweets'];
		($_POST['tweetable_num_tweets'] > 20) ? $options['num_tweets'] = '20' : $options['num_tweets'] = $_POST['tweetable_num_tweets'];
		($_POST['tweetable_follow_count']) ? $options['follow_count'] = '1' : $options['follow_count'] = '0';
		($_POST['tweetable_title_link'] == 'http://') ? $options['title_link'] = '' : $options['title_link'] = $_POST['tweetable_title_link'];
		update_option('tweetable_widget_options', $options);
	}
	
	$options = get_option("tweetable_widget_options");
	$followcount = ($options['follow_count']=="1") ? 'checked="checked"' : '';
	$twitter_user = get_option('tweetable_twitter_user');
	
	($options['title_link'] == '') ? $options['title_link'] = 'http://' : $options['title_link'] = $options['title_link'];
	
	?>
	<p>Twitter Acount: <em><?php echo $twitter_user; ?></em></p>
	<input type="hidden" name="tweetable_save_widget" value="yes" />
	<p><label for="tweetable_title">Widget Title: <input class="widefat" id="tweetable_title" name="tweetable_title" type="text" value="<?php echo $options['title']; ?>" /></label></p>
	<p><label for="tweetable_title_link">Widget Title Link: <input class="widefat" id="tweetable_title_link" name="tweetable_title_link" type="text" value="<?php echo $options['title_link']; ?>" /></label></p>
	<p><label for="tweetable_num_tweets">Number of Tweets to Show: <input class="widefat" id="tweetable_num_tweets" name="tweetable_num_tweets" type="text" value="<?php echo $options['num_tweets']; ?>" /></label></p>
	<p><label for="tweetable_follow_count">Show follower count: <input id="tweetable_follow_count" name="tweetable_follow_count" type="checkbox" <?php echo $followcount; ?> /></label></p>
	<?php
	
}




/*** Tweetmeme Button ***/
function tweetable_add_tweetmeme($content) {

	$installed = get_option('tweetable_account_activated');

	if (is_single() && $installed) {
		$mode = get_option('tweetable_tweetmeme_button_mode');
		if ($mode == '1') {
			$tweetmeme = '<div class="tweetmeme" style="float: right; margin: 3px 0 0 10px;">';
			$tweetmeme .= tweetable_tweetmeme_button('full');
			$tweetmeme .= '</div>';
			$content = $tweetmeme . $content;
		}
		elseif ($mode == '2') {
			$tweetmeme = '<div class="tweetmeme">';
			$tweetmeme .= tweetable_tweetmeme_button('compact');
			$tweetmeme .= '</div>';
			$content = $content . $tweetmeme;
		}
	}
	
	return $content;
	
}




/*** Send tweet on post publish ***/
function tweetable_publish_tweet($post_id) {

	$installed = get_option('tweetable_account_activated');

	if (get_option('tweetable_auto_tweet_posts') == '1' && $installed) {

		$post = get_post($post_id);
		
		if ($post->post_status == 'private' || $post->post_type == 'page') {
			return;
		}
		
		if ($post->post_date < $post->post_modified) {
			return;
		}
		
		$user_key = get_option('tweetable_access_token');
		$user_key_secret = get_option('tweetable_access_token_secret');
		$twitter = new Twitter_API(get_option('tweetable_app_key'), get_option('tweetable_app_key_secret'));
		$shortener = get_option('tweetable_url_shortener');
		$shortener_login = get_option('tweetable_shortener_login');
		$shortener_apikey = get_option('tweetable_shortener_apikey');
		$tweet_prefix = get_option('tweetable_auto_tweet_prefix');
		$googletags = get_option('tweetable_google_campaign_tags');
		
		$permalink = get_permalink($post_id);
		if ($googletags == '1') {
			$tags = '?utm_source=twitter&utm_medium=social&utm_campaign='.urlencode($post->post_title);
			$permalink = urlencode($permalink . $tags);
		}
		
		$permalink = apply_filters('tweetable_autotweet_permalink', $permalink);
		$permalink = $twitter->shorten_url($permalink, $shortener, $shortener_apikey, $shortener_login);
		$title = $tweet_prefix.' '.$post->post_title;
		$title = apply_filters('tweetable_autotweet_title', $title);
		
		$tweet = $twitter->fit_tweet($title, $permalink);
		$tweet = apply_filters('tweetable_autotweet_tweet', $tweet);
		
		$update = $twitter->update_status($tweet, $user_key, $user_key_secret);
	
	}

}




/*** Add scripts and stylesheets ***/
function tweetable_frontend_styles_and_scripts() {

	$setting_remove_stylesheet = get_option('tweetable_remove_stylesheet');

	if (!is_admin() && !$setting_remove_stylesheet) {
		wp_register_style('tweetable-frontend', WP_PLUGIN_URL.'/tweetable/main_css.css');
		wp_enqueue_style('tweetable-frontend');
	}

}




/*** Helper Functions ***/

//Return plugin path or URL
function tweetable_get_plugin_dir($type='url') {

	if ( !defined('WP_CONTENT_URL') )
		define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
	if ( !defined('WP_CONTENT_DIR') )
		define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
	if ($type=='path') { return WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(__FILE__)); }
	else { return WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)); }
	
}



//Check Twitter API Limit
function tweetable_api_rate_status() {

	$user_key = get_option('tweetable_access_token');
	$user_key_secret = get_option('tweetable_access_token_secret');
	$twitter = new Twitter_API(get_option('tweetable_app_key'), get_option('tweetable_app_key_secret'));
	
	$rate_limit = (array)$twitter->rate_limit_status($user_key, $user_key_secret);
	
	return array( 'remaining' => $rate_limit['remaining-hits'], 'limit' => $rate_limit['hourly-limit'], 'reset' => $rate_limit['reset-time-in-seconds'] );

}



//Returns the latest tweet by the Tweetable user
function tweetable_fetch_latest_tweet($rate_limit='check') {

	$user_key = get_option('tweetable_access_token');
	$user_key_secret = get_option('tweetable_access_token_secret');
	$twitter_user = get_option('tweetable_twitter_user');
	$twitter = new Twitter_API(get_option('tweetable_app_key'), get_option('tweetable_app_key_secret'));

	$latest_tweet = get_option('tweetable_latest_tweet');
	
	if ( $latest_tweet['cache_time'] < (mktime() - 120) ) {
		if ($rate_limit == 'check') {
			$rate_limit = tweetable_api_rate_status();
		}
		if ($rate_limit['remaining'] > 4) {
			$latest = $twitter->latest_tweet($twitter_user, $user_key, $user_key_secret);
			$latest_tweet_new['created_at'] = (string)$latest->created_at;
			$latest_tweet_new['id'] = (string)$latest->id;
			$latest_tweet_new['text'] = (string)$latest->text;
			$latest_tweet_new['source'] = (string)$latest->source;
			$latest_tweet_new['in_reply_to_status_id'] = (string)$latest->in_reply_to_status_id;
			$latest_tweet_new['in_reply_to_user_id'] = (string)$latest->in_reply_to_user_id;
			$latest_tweet_new['favorited'] = (string)$latest->favorited;
			$latest_tweet_new['in_reply_to_screen_name'] = (string)$latest->in_reply_to_screen_name;
			$latest_tweet_new['user']['id'] = (string)$latest->user->id;
			$latest_tweet_new['user']['name'] = (string)$latest->user->name;
			$latest_tweet_new['user']['screen_name'] = (string)$latest->user->screen_name;
			$latest_tweet_new['user']['profile_image_url'] = (string)$latest->user->profile_image_url;
			$latest_tweet_new['user']['url'] = (string)$latest->user->url;
			$latest_tweet_new['user']['followers_count'] = (string)$latest->user->followers_count;
			$latest_tweet_new['user']['friends_count'] = (string)$latest->user->friends_count;
			$latest_tweet = array( 'tweet' => $latest_tweet_new, 'cache_time' => mktime() );
			update_option('tweetable_latest_tweet', $latest_tweet);
		}
	}
	
	return $latest_tweet;

}



//Returns the latest tweets by the user
function tweetable_get_recent_tweets($rate_limit='check') {

	$twitter_user = get_option('tweetable_twitter_user');
	$user_key = get_option('tweetable_access_token');
	$user_key_secret = get_option('tweetable_access_token_secret');
	$twitter = new Twitter_API(get_option('tweetable_app_key'), get_option('tweetable_app_key_secret'));
	
	$latest_tweets = get_option('tweetable_recent_tweets_cache');
	
	if ( $latest_tweets['cache_time'] < (mktime() - 120) ) {
		if ($rate_limit == 'check') {
			$rate_limit = tweetable_api_rate_status();
		}
		if ($rate_limit['remaining'] > 4) {
			$latest_tweets_get = $twitter->user_timeline($twitter_user, $user_key, $user_key_secret);
			//print_r($latest_tweets_get);
			$count = 0;
			if (!isset($latest_tweets_get->tw_error)) {
				foreach ($latest_tweets_get->status as $tweet) {
					$latest_tweets_new[$count]['created_at'] = (string)$tweet->created_at;
					$latest_tweets_new[$count]['id'] = (string)$tweet->id;
					$latest_tweets_new[$count]['text'] = (string)$tweet->text;
					$latest_tweets_new[$count]['source'] = (string)$tweet->source;
					$latest_tweets_new[$count]['in_reply_to_status_id'] = (string)$tweet->in_reply_to_status_id;
					$latest_tweets_new[$count]['in_reply_to_user_id'] = (string)$tweet->in_reply_to_user_id;
					$latest_tweets_new[$count]['favorited'] = (string)$tweet->favorited;
					$latest_tweets_new[$count]['in_reply_to_screen_name'] = (string)$tweet->in_reply_to_screen_name;
					$latest_tweets_new[$count]['user']['id'] = (string)$tweet->user->id;
					$latest_tweets_new[$count]['user']['name'] = (string)$tweet->user->name;
					$latest_tweets_new[$count]['user']['screen_name'] = (string)$tweet->user->screen_name;
					$latest_tweets_new[$count]['user']['profile_image_url'] = (string)$tweet->user->profile_image_url;
					$latest_tweets_new[$count]['user']['url'] = (string)$tweet->user->url;
					$latest_tweets_new[$count]['user']['followers_count'] = (string)$tweet->user->followers_count;
					$latest_tweets_new[$count]['user']['friends_count'] = (string)$tweet->user->friends_count;
					$latest_tweets_new[$count]['user']['created_at'] = (string)$tweet->user->created_at;
					$count++;
				}
			} else {
				$latest_tweets_new[0]['text'] = $latest_tweets_get->tw_error;
			}
			$latest_tweets = array( 'tweets' => $latest_tweets_new, 'cache_time' => mktime() );
			update_option('tweetable_recent_tweets_cache', $latest_tweets);
		}
	}
	
	return $latest_tweets;
	
}




/*** Plugins Menu Additions ***/
function tweetable_add_plugin_links($links, $file) {
	static $this_plugin;
	(!$this_plugin) ? $this_plugin = plugin_basename(__FILE__) : $this_plugin = $this_plugin;
	if ($file == $this_plugin) {
		$settings_link = '<a href="admin.php?page=tweetable_settings">Settings</a>';
		array_push($links, $settings_link);
	}
	return $links;
}



/*
Copyright 2008-2011 Matt Harzewski

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

?>
