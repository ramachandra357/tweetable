<?php


/*** Admin Menu Hooks ***/
add_action('init', 'tweetable_install_check');
add_action('admin_init', 'tweetable_styles_and_scripts');
add_action('admin_menu', 'tweetable_add_admin_menus');
add_filter('favorite_actions', 'tweetable_add_menu_favorite');
add_action('wp_dashboard_setup', 'tweetable_add_dashboard_widget');




/*** Add the Menus to the WordPress Admin ***/
function tweetable_add_admin_menus() {

	$main_menu_permission = get_option('tweetable_main_menu_permission');
	
	if ($main_menu_permission == '') {
		add_option('tweetable_main_menu_permission', 'edit_themes');
		$main_menu_permission = 'edit_themes';
	}
	
	add_menu_page("Tweetable Twitter Plugin", "Twitter", $main_menu_permission, __FILE__, "tweetable_write_twittermenu");
	
	add_submenu_page(__FILE__, "Tweetable Twitter Plugin &rsaquo; Tweet", "Tweet", $main_menu_permission, __FILE__, "tweetable_write_twittermenu");
	
	add_submenu_page(__FILE__, "Tweetable Twitter Plugin &rsaquo; Track", "Track", $main_menu_permission, 'tweetable_track', "tweetable_write_trackmenu");
	
	add_submenu_page(__FILE__, "Tweetable Twitter Plugin &rsaquo; Settings", "Settings", 'edit_themes', 'tweetable_settings', "tweetable_write_settingsmenu");
	
	if (!get_option('tweetable_account_activated') || isset($_GET['reset_account'])) {
		add_submenu_page(__FILE__, "Tweetable Twitter Plugin &rsaquo; Install", "Install", 'edit_themes', 'tweetable_install', "tweetable_write_installer");
	}

}




/*** Has a Twitter account been bound to Tweetable? ***/
function tweetable_install_check() {

	if (!get_option('tweetable_account_activated') && !$_GET['installing']) {
		if (strpos($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], 'tweetable')) {
			$admin_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			$admin_url_split = explode('/wp-admin/', $admin_url);
			$admin_url = htmlentities($admin_url_split[0]);
			wp_redirect($admin_url.'/wp-admin/admin.php?page=tweetable_install&installing=1');
		}
	}
	
}




/*** Add scripts and stylesheets to the Admin ***/
function tweetable_styles_and_scripts() {

	wp_register_style('tweetable-admin', WP_PLUGIN_URL.'/tweetable/admin_css.css');
	wp_enqueue_style('tweetable-admin');
	wp_enqueue_style('thickbox');
	wp_enqueue_script('jquery');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('tweetable-twitter', WP_PLUGIN_URL.'/tweetable/admin_scripts.js');

}




/*** Header for Admin Pages ***/
function tweetable_admin_page_header() {
	
	if (!get_option('tweetable_account_activated')) {
		//include 'bind_twitter_account.php';
	}
	
	echo '<div class="wrap">';
	//screen_icon();
	
}




/*** Footer for Admin Pages ***/
function tweetable_admin_page_footer() {

	echo '<div style="margin-top:45px; font-size:0.87em;">';
	echo '<div style="float:right;"><a href="http://www.webmaster-source.com/static/donate_plugin.php?plugin=tweetable&amp;KeepThis=true&amp;TB_iframe=true&amp;height=250&amp;width=550" class="thickbox" title="Donate"><img src="'.tweetable_get_plugin_dir('url').'/images/donate.gif" alt="Donate" /></a></div>';
	echo '<div><a href="'.tweetable_get_plugin_dir('url').'/dialog.php?show=documentation&KeepThis=true&amp;TB_iframe=true&amp;height=450&amp;width=680" class="thickbox" title="Documentation">Documentation</a> | <a href="http://www.webmaster-source.com/tweetable-twitter-plugin-wordpress/">Tweetable Homepage</a></div>';
	echo '</div>';
	
	echo '</div>';
	
}




/*** Add Item to Favorite Actions Menu ***/
function tweetable_add_menu_favorite($actions) {

	$actions['admin.php?page=tweetable/admin_menus.php'] = array('Twitter', 'edit_themes');
	return $actions;
	
}




/*** Twitter Menu ***/
function tweetable_write_installer() {
	require_once('bind_twitter_account.php');
}




/*** Twitter Menu ***/
function tweetable_write_twittermenu() {

	tweetable_admin_page_header();	
	$user_name = get_option('tweetable_twitter_user');
	$user_key = get_option('tweetable_access_token');
	$rate_limit = tweetable_api_rate_status();
	if ($_GET['ntweet']) {
		$tweet_val = $_GET['ntweet'];
	}
	echo '<h2>Twitter (@'.$user_name.')</h2>';
	?>
	
	<div id="twitter-submit">
	<form action="" name="post-twitter">
	<p id="tweet-tools">
	<span id="twitter-tools"><a href="#" id="shorten-url" title="Shorten Link"><img src="<?php echo tweetable_get_plugin_dir('url'); ?>/images/page_link.png" alt="Shorten Link" /></a></span> &nbsp;
	<span id="chars-left"><strong>140</strong> characters left</span>
	</p>
	<textarea name="tweet" id="tweet" rows="2" cols="75"><?php echo $tweet_val; ?></textarea>
	<input type="hidden" name="in_reply_to_user" id="in_reply_to_user" value="" />
	<input type="hidden" name="in_reply_to_status" id="in_reply_to_status" value="" />
	<input type="hidden" name="do" id="do_action" value="update-status" />
	<input type="hidden" name="token" id="js_token" value="<?php echo md5($user_key); ?>" />
	<input type="hidden" name="post_to" id="post_to" value="<?php echo tweetable_get_plugin_dir('url'); ?>/form_post.php" />
	<div id="my-latest-status">
	<?php
	$latest = tweetable_fetch_latest_tweet($rate_limit);
	$date = date('F j, Y g:i', strtotime($latest['tweet']['created_at']));
	echo '<strong>Latest: </strong>'.$latest['tweet']['text'].' <em>'.$date.'</em>';
	?>
	</div>
	<p style="text-align:right;"><span id="loading-send-tweet" style="display:none;"><img src="<?php echo tweetable_get_plugin_dir('url'); ?>/images/loading.gif" alt="Loading..." style="vertical-align:middle" /></span> <input type="submit" class="button-primary" id="update-status" value="Update Status" name="submit" /></p>
	</form>
	<br style="clear:both" />
	</div>
	
	<div class="twitter_timeline">
	<?php tweetable_menu_twitter_timeline($rate_limit); ?>
	</div>
	
	<div style="margin-top:35px; margin-bottom:35px;">
	<strong>Bookmarklet:</strong> <a href="javascript:(function(){loc='http://<?php echo $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]; ?>&ntweet='+document.title+'%20'+window.location;window.open(loc,'tweetable','height=525,width=865,title=no,location=no,menubars=no,navigation=no,statusbar=no,toolbar=no,scrollbars=yes');})();" style="background-color:#21759B; color:#FFFFFF; padding:3px 6px; text-decoration:none;">Tweet This!</a>
	</div>
	
	<?php
	echo '<p class="api-rate">Hourly API requests left: '.$rate_limit['remaining'].'/'.$rate_limit['limit'].'.</p>';
	tweetable_admin_page_footer();
	
}




/*** Dashboard Widget ***/
function tweetable_dashboard_widget() {

	$user_key = get_option('tweetable_access_token');

	?>
	<div id="twitter-submit-widget">
	<form action="" name="post-twitter">
	<p id="tweet-tools" style="width:100%">
	<span id="twitter-tools"><a href="#" id="shorten-url" title="Shorten Link"><img src="<?php echo tweetable_get_plugin_dir('url'); ?>/images/page_link.png" alt="Shorten Link" /></a></span> &nbsp;
	<span id="chars-left"><strong>140</strong> characters left</span>
	</p>
	<textarea name="tweet" id="tweet" rows="2" cols="50" style="width:100%"></textarea>
	<input type="hidden" name="do" id="do_action" value="update-status" />
	<input type="hidden" name="token" id="js_token" value="<?php echo md5($user_key); ?>" />
	<input type="hidden" name="post_to" id="post_to" value="<?php echo tweetable_get_plugin_dir('url'); ?>/form_post.php" />
	<p class="submit" style="width:100%; text-align:right;"><span id="loading-send-tweet" style="display:none;"><img src="<?php echo tweetable_get_plugin_dir('url'); ?>/images/loading.gif" alt="Loading..." style="vertical-align:middle" /></span> <input type="submit" class="button-primary" id="update-status" value="Update Status" name="submit" /></p>
	</form>
	</div>
	<?php
	
}

function tweetable_add_dashboard_widget() {
	$permission = get_option('tweetable_main_menu_permission');
	$installed = get_option('tweetable_account_activated');
	$twitter_user = get_option('tweetable_twitter_user');
	if (current_user_can($permission) && $installed) {
		if (function_exists('wp_add_dashboard_widget')) {
			wp_add_dashboard_widget('tweetable_widget', 'Twitter (@'.$twitter_user.')', 'tweetable_dashboard_widget');
		}
	}
}




/*** Track Menu ***/
function tweetable_write_trackmenu() {

	tweetable_admin_page_header();	
	$user_name = get_option('tweetable_twitter_user');
	$user_key = get_option('tweetable_access_token');
	$user_key_secret = get_option('tweetable_access_token_secret');
	$twitter = new Twitter_API(get_option('tweetable_app_key'), get_option('tweetable_app_key_secret'));
	echo '<h2>Twitter: Track</h2>';

	?>
	
	<div id="twitter-submit">
	<form action="" name="post-twitter">
	<p id="tweet-tools">
	<span id="twitter-tools"><a href="#" id="shorten-url" title="Shorten Link"><img src="<?php echo tweetable_get_plugin_dir('url'); ?>/images/page_link.png" alt="Shorten Link" /></a></span> &nbsp;
	<span id="chars-left"><strong>140</strong> characters left</span>
	</p>
	<textarea name="tweet" id="tweet" rows="2" cols="75"></textarea>
	<input type="hidden" name="in_reply_to_user" id="in_reply_to_user" value="" />
	<input type="hidden" name="in_reply_to_status" id="in_reply_to_status" value="" />
	<input type="hidden" name="do" id="do_action" value="update-status" />
	<input type="hidden" name="token" id="js_token" value="<?php echo md5($user_key); ?>" />
	<input type="hidden" name="post_to" id="post_to" value="<?php echo tweetable_get_plugin_dir('url'); ?>/form_post.php" />
	<div id="my-latest-status">
	<?php
	$latest = tweetable_fetch_latest_tweet($rate_limit);
	$date = date('F j, Y g:i', strtotime($latest['tweet']['created_at']));
	echo '<strong>Latest: </strong>'.$latest['tweet']['text'].' <em>'.$date.'</em>';
	?>
	</div>
	<p style="text-align:right;"><span id="loading-send-tweet" style="display:none;"><img src="<?php echo tweetable_get_plugin_dir('url'); ?>/images/loading.gif" alt="Loading..." style="vertical-align:middle" /></span> <input type="submit" class="button-primary" id="update-status" value="Update Status" name="submit" /></p>
	</form>
	<br style="clear:both" />
	</div>
	
	<p class="search-box" style="float:none; margin: 0 0 25px 5px;">
		<label class="hidden" for="twitter-search-input">New Twitter Search:</label>
		<input type="text" class="search-input" id="twitter-search-input" name="s" value="" />
		<input type="submit" value="New Search" id="add-search" class="button" />
		<span id="loading-add-search" style="display:none;"><img src="<?php echo tweetable_get_plugin_dir('url'); ?>/images/loading.gif" alt="Loading..." style="vertical-align:middle" /></span>
	</p>	

	<?php

	$searches = get_option('tweetable_saved_searches');
	
	if ($searches) {
		foreach ($searches as $search) {
			echo '<div class="search-div" id="search-'.array_search($search, $searches).'">';
			echo '<h3>'.$search.' <a href="#" class="delete-search" title="Remove"><img src="'.tweetable_get_plugin_dir('url').'/images/delete.png" alt="Delete" style="vertical-align:middle" /></a></h3>';
			echo '<div class="twitter_timeline">';
			echo '<ol id="tweetable-timeline">';
			$results = $twitter->search($search, 'en', '10');
			foreach ($results->entry as $tweet) {
				$tweet->content = preg_replace('/<a\shref=\"([^\"]*)\"><b>\#(.*)<\/b><\/a>/siU', '<a href="'.tweetable_get_plugin_dir('url').'/dialog.php?show=hashtag&hashtag=\\2&KeepThis=true&amp;TB_iframe=true&amp;height=450&amp;width=680" class="thickbox hashtag" title="Hashtag Search">#\\2</a>', $tweet->content);
				$status_id = explode(':', $tweet->id);
				$status_id = $status_id[2];
				$status_user = explode(' (', $tweet->author->name);
				$status_user = $status_user[0];
				echo '<li class="status" id="'.$status_id.'">';
				echo '<span class="twitter_thumb"><img src="'.$tweet->link[1]['href'].'" width="48" height="48" alt="" /></span>';
				echo '<span class="twitter_status">';
				echo '<strong><a class="user" href="'.$tweet->link[0]['href'].'">'.$status_user.'</a></strong> ';
				echo '<span class="status-text">'.make_clickable($tweet->content).'</span>';
		    	$date = date('F j, Y g:i', strtotime($tweet->published));
				echo '<span class="twitter_meta">'.$date.'</span>';
				echo '</span>';
		    	echo '<span class="twitter_functions">';
		    	echo '<a class="reply" href="#"><img src="'.tweetable_get_plugin_dir('url').'/images/reply.png" alt="Reply" title="Reply" /></a>&nbsp;';
		    	echo '<a class="retweet" href="#"><img src="'.tweetable_get_plugin_dir('url').'/images/retweet.png" alt="Retweet" title="Retweet" /></a>';
		    	echo '</span>';
				echo '<br style="clear:both" />';
				echo '</li>';
			}
			echo '</ol>';
			echo '</div>';
			echo '</div>';
		}
	} else {
		echo 'No searches are currently running. Maybe you should add a few?';
	}

	tweetable_admin_page_footer();

}




/*** Settings Menu ***/
function tweetable_write_settingsmenu() {
	tweetable_admin_page_header();
	$twitter_user = get_option('tweetable_twitter_user');
	echo '<h2>Tweetable Settings</h2>';
		
	global $wpdb;
	//Update settings
	if ($_POST['issubmitted'] == 'yes') {
		$post_twitter_user_level = $wpdb->escape($_POST['twitter_user_level']);
		$post_tweetmeme = $wpdb->escape($_POST['tweetmeme']);
		$post_url_shortener = $wpdb->escape($_POST['url_shortener']);
		$post_shortener_login = $wpdb->escape($_POST['shortener_login']);
		$post_shortener_apikey = $wpdb->escape($_POST['shortener_apikey']);
		$post_auto_tweet_prefix = $wpdb->escape($_POST['auto_tweet_prefix']);
		($_POST['auto_tweet_posts']) ? $post_auto_tweet_posts = '1' : $post_auto_tweet_posts = '0';
		($_POST['google_campaign_tags']) ? $post_google_campaign_tags = '1' : $post_google_campaign_tags = '0';
		($_POST['remove_stylesheet']) ? $post_remove_stylesheet = '1' : $post_remove_stylesheet = '0';
		update_option("tweetable_main_menu_permission", $post_twitter_user_level);
		update_option("tweetable_tweetmeme_button_mode", $post_tweetmeme);
		update_option("tweetable_url_shortener", $post_url_shortener);
		update_option("tweetable_shortener_login", $post_shortener_login);
		update_option("tweetable_shortener_apikey", $post_shortener_apikey);
		update_option("tweetable_auto_tweet_posts", $post_auto_tweet_posts);
		update_option("tweetable_auto_tweet_prefix", $post_auto_tweet_prefix);
		update_option("tweetable_google_campaign_tags", $post_google_campaign_tags);
		update_option("tweetable_remove_stylesheet", $post_remove_stylesheet);
	}
	//Retrieve current settings
	$setting_twitter_user_level = get_option("tweetable_main_menu_permission");
	$setting_tweetmeme = get_option("tweetable_tweetmeme_button_mode");
	$setting_url_shortener = get_option('tweetable_url_shortener');
	$setting_shortener_login = get_option('tweetable_shortener_login');
	$setting_shortener_apikey = get_option('tweetable_shortener_apikey');
	$setting_auto_tweet = get_option('tweetable_auto_tweet_posts');
	($setting_auto_tweet == '1') ? $setting_auto_tweet = 'checked="checked"' : $setting_auto_tweet = '';
	$setting_auto_tweet_prefix = get_option('tweetable_auto_tweet_prefix');
	$setting_google_campaign_tags = get_option('tweetable_google_campaign_tags');
	($setting_google_campaign_tags == '1') ? $setting_google_campaign_tags = 'checked="checked"' : $setting_google_campaign_tags = '';
	$setting_remove_stylesheet = get_option('tweetable_remove_stylesheet');
	($setting_remove_stylesheet == '1') ? $setting_remove_stylesheet = 'checked="checked"' : $setting_remove_stylesheet = '';
	
	?>
	<div class="alignright">
	<ul class="subsubsub">
	<li><strong>Twitter Account:</strong> <?php echo $twitter_user; ?> |</li>
	<li><a href="admin.php?page=tweetable_install&installing=1&reset_account=1&step=1">Change</a></li>
	</ul>
	</div>

	
	<form method="post" action="admin.php?page=tweetable_settings">
	<table class="form-table">
	
	<tr valign="top">
	<th scope="row">Twitter User Level</th>
	<td><label for="twitter_user_level">
	<select name="twitter_user_level" id="twitter_user_level">
	<option <?php if ($setting_twitter_user_level=='edit_themes') { echo 'selected="selected"'; } ?> value="edit_themes">Administrator</option>
	<option <?php if ($setting_twitter_user_level=='edit_others_posts') { echo 'selected="selected"'; } ?> value="edit_others_posts">Editor</option>
	<option <?php if ($setting_twitter_user_level=='edit_published_posts') { echo 'selected="selected"'; } ?> value="edit_published_posts">Author</option>
	</select></label>
	<br />As the Administrator, you can tweet from the WordPress admin. You can grant your Editors or Authors access to post to your Twitter account too, by lowering the minimum required rank.
	</td></tr>
	
	<tr valign="top">
	<th scope="row"><label for="url_shortener">URL Shortener</label></th>
	<td>
	<select name="url_shortener" id="url_shortener">
	<option value="is.gd" <?php if ($setting_url_shortener=='is.gd') { echo 'selected="selected"'; } ?>>Is.gd</option>
	<option value="bit.ly" <?php if ($setting_url_shortener=='bit.ly') { echo 'selected="selected"'; } ?>>Bit.ly</option>
	<option value="j.mp" <?php if ($setting_url_shortener=='j.mp') { echo 'selected="selected"'; } ?>>J.mp</option>
	<option value="tr.im" <?php if ($setting_url_shortener=='tr.im') { echo 'selected="selected"'; } ?>>Tr.im</option>
	<option value="su.pr" <?php if ($setting_url_shortener=='su.pr') { echo 'selected="selected"'; } ?>>Su.pr</option>
	<option value="ow.ly" <?php if ($setting_url_shortener=='ow.ly') { echo 'selected="selected"'; } ?>>Ow.ly</option>
	<option value="3.ly" <?php if ($setting_url_shortener=='3.ly') { echo 'selected="selected"'; } ?>>3.ly</option>
	<option value="tinyurl" <?php if ($setting_url_shortener=='tinyurl') { echo 'selected="selected"'; } ?>>TinyURL.com</option>
	<option value="yourls" <?php if ($setting_url_shortener=='yourls') { echo 'selected="selected"'; } ?>>YOURLS</option>
	</select>
	<br />Specify which URL shortener should be used by Tweetable.
	</td></tr>
	
	<tr valign="top" id="shortener_login">
	<th scope="row"><label for="shortener_login">Shortener Login</label></th>
	<td>
	<input type="text" name="shortener_login" class="regular-text" value="<?php echo $setting_shortener_login; ?>" />
	<br /><strong>Bit.ly</strong> and <strong>J.mp</strong> require that you enter your username here. <strong>YOURLS</strong> requires that you enter your username and the domain/path of the install, in the form of <em>username@example.org</em>
	</td></tr>
	
	<tr valign="top" id="shortener_apikey">
	<th scope="row"><label for="shortener_apikey">Shortener API Key</label></th>
	<td>
	<input type="password" name="shortener_apikey" class="regular-text" value="<?php echo $setting_shortener_apikey; ?>" />
	<br /><strong>Bit.ly</strong> requires that you enter your account's API key here. You can find it on your <a href="http://bit.ly/account/">account</a> page. <strong>YOURLS</strong> requires that you enter your account password.
	</td></tr>
	
	<script type="text/javascript">
	var selectmenu=document.getElementById('url_shortener');
	var selectvalue=selectmenu.options[selectmenu.selectedIndex].value;
	if (selectvalue != 'bit.ly' && selectvalue != 'j.mp' && selectvalue != 'yourls') {
		document.getElementById('shortener_login').style.display = 'none';
		document.getElementById('shortener_apikey').style.display = 'none';
	}
	selectmenu.onchange=function() {
		var theoption=this.options[this.selectedIndex];
		if (theoption.value == 'bit.ly' || theoption.value == 'j.mp') {
			document.getElementById('shortener_login').style.display = 'table-row';
			document.getElementById('shortener_apikey').style.display = 'table-row';	
		}
		else if (theoption.value == 'yourls') {
			document.getElementById('shortener_login').style.display = 'table-row';
			document.getElementById('shortener_apikey').style.display = 'table-row';	
		} else {
			document.getElementById('shortener_login').style.display = 'none';
			document.getElementById('shortener_apikey').style.display = 'none';
		}
	}
	</script>
	
	<tr valign="top">
	<th scope="row">Tweetmeme Button</th>
	<td><label for="tweetmeme">
	<select name="tweetmeme" id="tweetmeme">
	<option value="0" <?php if ($setting_tweetmeme=='0') { echo 'selected="selected"'; } ?>>Do not display a button</option>
	<option value="1" <?php if ($setting_tweetmeme=='1') { echo 'selected="selected"'; } ?>>Display full button</option>
	<option value="2" <?php if ($setting_tweetmeme=='2') { echo 'selected="selected"'; } ?>>Display compact button</option>
	</select></label>
	<br />Display a <a href="http://tweetmeme.com/static.php?page=button">Tweetmeme</a> &quot;retweet&quot; button on your single post pages.
	</td></tr>
	
	<tr valign="top">
	<th scope="row">Auto-Tweet Posts</th>
	<td><label for="auto_tweet_posts">
	<input type="checkbox" name="auto_tweet_posts" <?php echo $setting_auto_tweet; ?> /> Automatically tweet posts when they are published</label>
	</td></tr>
	
	<tr valign="top">
	<th scope="row"><label for="auto_tweet_prefix">Auto-Tweet Posts Prefix</label></th>
	<td><input type="text" name="auto_tweet_prefix" class="regular-text" value="<?php echo $setting_auto_tweet_prefix; ?>" /><br />If you would like to prefix your automatic tweets with something such as <em>From My Blog:</em>, put it here.
	</td></tr>
	
	<tr valign="top">
	<th scope="row">Campaign Tracking</th>
	<td><label for="google_campaign_tags">
	<input type="checkbox" name="google_campaign_tags" <?php echo $setting_google_campaign_tags; ?> /> Add Google Analytics campaign tags to auto-tweets.</label>
	</td></tr>
	
	<tr valign="top">
	<th scope="row">Remove Stylesheet</th>
	<td><label for="remove_stylesheet">
	<input type="checkbox" name="remove_stylesheet" <?php echo $setting_remove_stylesheet; ?> /> Check this if you want to <strong>not</strong> include the Tweetable stylesheet on your blog.</label>
	</td></tr>
	
	</table>
	
	<input name="issubmitted" type="hidden" value="yes" />
	<p class="submit"><input type="submit" name="Submit" value="Save Changes" /></p>
	</form>
	
	<?php
	tweetable_admin_page_footer();	
}




/*** Twitter Menu Timeline ***/
function tweetable_menu_twitter_timeline($rate_limit) {

	$user_key = get_option('tweetable_access_token');
	$user_key_secret = get_option('tweetable_access_token_secret');
	$twitter = new Twitter_API(get_option('tweetable_app_key'), get_option('tweetable_app_key_secret'));
	
	$friend_tweets = get_option('tweetable_menu_timeline');
	
	if ( $friend_tweets['cache_time'] < (mktime() - 120) ) {
		if ($rate_limit['remaining'] > 4) {
			$friend_tweets_get = $twitter->friends_timeline($user_key, $user_key_secret, '50');
			//echo '<pre>'; print_r($friend_tweets_get); echo '</pre>';
			$count = 0;
			foreach ($friend_tweets_get->status as $tweet) {
				$friend_tweets_new[$count]['created_at'] = (string)$tweet->created_at;
				$friend_tweets_new[$count]['id'] = (string)$tweet->id;
				//$friend_tweets_new[$count]['text'] = (string)$tweet->text;
				$friend_tweets_new[$count]['text'] = preg_replace('/\#([a-zA-Z0-9_]+)/', '<a href="'.tweetable_get_plugin_dir('url').'/dialog.php?show=hashtag&hashtag=\\1&KeepThis=true&amp;TB_iframe=true&amp;height=450&amp;width=680" class="thickbox hashtag" title="Hashtag Search">#\\1</a>', (string)$tweet->text);
				$friend_tweets_new[$count]['source'] = (string)$tweet->source;
				$friend_tweets_new[$count]['in_reply_to_status_id'] = (string)$tweet->in_reply_to_status_id;
				$friend_tweets_new[$count]['in_reply_to_user_id'] = (string)$tweet->in_reply_to_user_id;
				$friend_tweets_new[$count]['favorited'] = (string)$tweet->favorited;
				$friend_tweets_new[$count]['in_reply_to_screen_name'] = (string)$tweet->in_reply_to_screen_name;
				$friend_tweets_new[$count]['user']['id'] = (string)$tweet->user->id;
				$friend_tweets_new[$count]['user']['name'] = (string)$tweet->user->name;
				$friend_tweets_new[$count]['user']['screen_name'] = (string)$tweet->user->screen_name;
				$friend_tweets_new[$count]['user']['profile_image_url'] = (string)$tweet->user->profile_image_url;
				$friend_tweets_new[$count]['user']['url'] = (string)$tweet->user->url;
				$friend_tweets_new[$count]['user']['followers_count'] = (string)$tweet->user->followers_count;
				$friend_tweets_new[$count]['user']['friends_count'] = (string)$tweet->user->friends_count;
				$friend_tweets_new[$count]['user']['created_at'] = (string)$tweet->user->created_at;
				$count++;
			}
			$friend_tweets = array( 'tweets' => $friend_tweets_new, 'cache_time' => mktime() );
			update_option('tweetable_menu_timeline', $friend_tweets);
		}
	}
	
	if ($rate_limit['remaining'] < 6) {
		echo '<div style="width:650px"><strong>Warning:</strong> Your Twitter account has made more than 95 requests to the Twitter API in this hour. To prevent you from running out of requests, the latest tweets have not been fetched.</div>';
	}
	
	echo '<ol id="tweetable-timeline">';
	
	foreach ($friend_tweets['tweets'] as $tweet) {
		echo '<li class="status" id="'.$tweet['id'].'">';
		echo '<span class="twitter_thumb"><img src="'.$tweet['user']['profile_image_url'].'" width="48" height="48" alt="" /></span>';
		echo '<span class="twitter_status">';
		echo '<strong><a class="user" href="http://twitter.com/'.$tweet['user']['screen_name'].'">'.$tweet['user']['screen_name'].'</a></strong> ';
		echo '<span class="status-text">'.make_clickable($tweet['text']).'</span>';
    	$date = date('F j, Y g:i', strtotime($tweet['created_at']));
		echo '<span class="twitter_meta">'.$date.' from '.$tweet['source'].'</span>';
		echo '</span>';
    	echo '<span class="twitter_functions">';
    	echo '<a class="reply" href="#"><img src="'.tweetable_get_plugin_dir('url').'/images/reply.png" alt="Reply" title="Reply" /></a>&nbsp;';
    	echo '<a class="retweet" href="#"><img src="'.tweetable_get_plugin_dir('url').'/images/retweet.png" alt="Retweet" title="Retweet" /></a>';
    	echo '</span>';
		echo '<br style="clear:both" />';
		echo '</li>';
	}
	
	echo '</ol>';

}


?>
