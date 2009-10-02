<?php

require_once('../../../wp-load.php');
require_once('mh_twitter_class.php');

if(!empty($_POST)) {

	$user_key = get_option('tweetable_access_token');
	$user_key_secret = get_option('tweetable_access_token_secret');
	$twitter_user = get_option('tweetable_twitter_user');
	$twitter = new Twitter_API(get_option('tweetable_app_key'), get_option('tweetable_app_key_secret'));

	switch($_POST['do']) {
	
		case 'update-status':
			if ($_POST['token'] != md5($user_key)) {
				exit("I don't think so, hacker...\n");
			}
			$status = stripslashes($_POST['tweet']);
			$in_reply_to_status = $_POST['in_reply_to_status'];
			$latest = $twitter->update_status($status, $user_key, $user_key_secret, $in_reply_to_status);
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
			$date = date('F j, Y g:i', strtotime($latest_tweet['tweet']['created_at']));
			echo '<strong>Latest: </strong>'.$latest_tweet['tweet']['text'].' <em>'.$date.'</em>';
		break;
		
		case 'shorten-url':
			$theurl = rawurlencode($_POST['theurl']);
			$shortener = get_option('tweetable_url_shortener');
			$shortener_login = get_option('tweetable_shortener_login');
			$shortener_apikey = get_option('tweetable_shortener_apikey');
			$shorturl = $twitter->shorten_url($theurl, $shortener, $shortener_apikey, $shortener_login);
			echo $shorturl;
		break;
		
		case 'search-add':
			$new = stripslashes($_POST['thekeyword']);
			$searches = get_option('tweetable_saved_searches');
			//$searches[] = $new;
			array_unshift($searches, $new);
			update_option('tweetable_saved_searches', $searches);
			echo $new;
		break;
		
		case 'search-delete':
			$delete = stripslashes($_POST['thesearch']);
			$delete = explode('-', $delete);
			$delete = $delete[1];
			$searches = get_option('tweetable_saved_searches');
			//$key = array_search($delete, $searches);
			//unset($searches[$key]);
			unset($searches[$delete]);
			update_option('tweetable_saved_searches', $searches);
			echo $delete;
		break;
	
		default:
			return FALSE;
		break;
	
	}

}

?>