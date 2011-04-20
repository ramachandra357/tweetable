<?php

/*
File Name: PHP Twitter API Class
Author: Matt Harzewski (redwall_hp)
Author URL: http://www.webmaster-source.com
License: LGPL
*/

if ( !class_exists('TwitterOAuth') ) {
	require_once('OAuth/twitterOAuth.php');
}

class Twitter_API {




function __construct($consumer_key='', $consumer_secret='') {

	if ($consumer_key != '' && $consumer_secret != '') {
		$this->consumer_key = $consumer_key;
		$this->consumer_secret = $consumer_secret;
		$this->oauth_on = TRUE;
	} else {
		$this->oauth_on = FALSE;
	}

}



//Get the 20 most recent tweets from a user.
public function user_timeline($user, $auth_user='', $auth_pass='') {

	$url = "http://twitter.com/statuses/user_timeline/{$user}.xml";
	$response = $this->send_request($url, 'GET', '', $auth_user, $auth_pass);
	try {
		$xml = new SimpleXmlElement($response);
	}
	catch (Exception $e) {
		$xml = new stdClass;
		$xml->status = array("0" => array("status" => ""));
		$xml->tw_error = "<strong>Error:</strong> Could not load tweets.";
	}
	return $xml;

}



//Get the latest status update from a user.
public function latest_tweet($user, $auth_user='', $auth_pass='') {

	$url = "http://twitter.com/statuses/user_timeline/{$user}.xml";
	$response = $this->send_request($url, 'GET', '', $auth_user, $auth_pass);
	$xml = new SimpleXmlElement($response);
	return $xml->status[0];

}



//Get the recent activity of a users' friends. #Auth
public function friends_timeline($auth_user, $auth_pass, $count='10') {

	$url = "http://twitter.com/statuses/friends_timeline.xml?count={$count}";
	$response = $this->send_request($url, 'GET', '', $auth_user, $auth_pass);
	$xml = new SimpleXmlElement($response);
	return $xml;

}



//Get the newest replies/mentions of a user. #Auth
public function mentions($auth_user, $auth_pass) {

	$url = "http://twitter.com/statuses/mentions.xml";
	$response = $this->send_request($url, 'GET', '', $auth_user, $auth_pass);
	$xml = new SimpleXmlElement($response);
	return $xml;

}



//The 20 most recent tweets from the entire Twitterverse. Updates every 60 seconds. #NoLimit
public function public_timeline($auth_user='', $auth_pass='') {

	$url = "http://twitter.com/statuses/public_timeline.xml";
	$response = $this->send_request($url, 'GET', '', $auth_user, $auth_pass);
	$xml = new SimpleXmlElement($response);
	return $xml;

}



//Show a single status update by its ID.
public function show_single($id, $auth_user='', $auth_pass='') {

	$url = "http://twitter.com/statuses/show/{$id}.xml";
	$response = $this->send_request($url, 'GET', '', $auth_user, $auth_pass);
	$xml = new SimpleXmlElement($response);
	return $xml;

}



//Update a user's status. #Auth #NoLimit
public function update_status($status, $auth_user, $auth_pass, $in_reply_to_status_id='') {

	$url = "http://twitter.com/statuses/update.xml";
	//$data = "status={$status}";
	$data['status'] = $status;
	if (isset($in_reply_to_status_id)) {
		//$data = $data."&in_reply_to_status_id={$in_reply_to_status_id}";
		$data['in_reply_to_status_id'] = $in_reply_to_status_id;
	}
	$response = $this->send_request($url, 'POST', $data, $auth_user, $auth_pass);
	if ($response != 401) {
		$xml = new SimpleXmlElement($response);
	} else {
		$xml = "401 - Authentication Error";
	}
	return $xml;

}



//Delete a status message (by ID). #Auth #NoLimit
public function destroy_status($id, $auth_user, $auth_pass) {

	$url = "http://twitter.com/statuses/destroy/{$id}.xml";
	$data['id'] = $id;
	$response = $this->send_request($url, 'POST', $data, $auth_user, $auth_pass);
	if ($response != 401) {
		$xml = new SimpleXmlElement($response);
	} else {
		$xml = "401 - Authentication Error";
	}
	return $xml;

}



//Returns extended user data via the users/show API method.
public function user_info($user, $auth_user='', $auth_pass='') {

	$url = "http://twitter.com/users/show.xml?screen_name={$user}";
	$response = $this->send_request($url, 'GET', '', $auth_user, $auth_pass);
	$xml = new SimpleXmlElement($response);
	return $xml;

}



//Returns a user's friends.
public function user_friends($user, $page, $auth_user='', $auth_pass='') {

	$url = "http://twitter.com/statuses/friends.xml?screen_name={$user}&page={$page}";
	$response = $this->send_request($url, 'GET', '', $auth_user, $auth_pass);
	$xml = new SimpleXmlElement($response);
	return $xml;

}



//Returns a user's followers. #Auth
public function user_followers($user, $auth_user, $auth_pass, $page='') {

	$url = "http://twitter.com/statuses/followers.xml?screen_name={$user}&page={$page}";
	$response = $this->send_request($url, 'GET', '', $auth_user, $auth_pass);
	$xml = new SimpleXmlElement($response);
	return $xml;

}



//Send a direct message. The recipient must be following you. #Auth #NoLimit
public function send_direct_message($recipient, $message, $auth_user, $auth_pass) {

	$url = "http://twitter.com/direct_messages/new.xml";
	$data['text'] = $message;
	$data['user'] = $recipient;
	$response = $this->send_request($url, 'POST', $data, $auth_user, $auth_pass);
	if ($response != 401) {
		$xml = new SimpleXmlElement($response);
	} else {
		$xml = "401 - Authentication Error";
	}
	return $xml;

}



//Follow a user. #Auth #NoLimit
public function follow_user($user, $auth_user, $auth_pass) {

	$url = "http://twitter.com/friendships/create/{$user}.xml?follow=true";
	$response = $this->send_request($url, 'POST', '', $auth_user, $auth_pass);
	if ($response != 401) {
		$xml = new SimpleXmlElement($response);
	} else {
		$xml = "401 - Authentication Error";
	}
	return $xml;

}



//UnFollow a user. #Auth #NoLimit
public function unfollow_user($user, $auth_user, $auth_pass) {

	$url = "http://twitter.com/friendships/destroy/{$user}.xml";
	$response = $this->send_request($url, 'POST', '', $auth_user, $auth_pass);
	if ($response != 401) {
		$xml = new SimpleXmlElement($response);
	} else {
		$xml = "401 - Authentication Error";
	}
	return $xml;

}



//Does a user follow another user? #Auth
public function does_follow($user_a, $user_b, $auth_user, $auth_pass) {

	$url = "http://twitter.com/friendships/exists.xml?user_a={$user_a}&user_b={$user_b}";
	$response = $this->send_request($url, 'GET', '', $auth_user, $auth_pass);
	if ($response != 401) {
		$xml = new SimpleXmlElement($response);
	} else {
		$xml = "401 - Authentication Error";
	}
	return $xml;

}



//Returns a list of a users' friends' ids.
public function friends_ids($user, $auth_user='', $auth_pass='') {

	$url = "http://twitter.com/friends/ids.xml?screen_name={$user}";
	$response = $this->send_request($url, 'GET', '', $auth_user, $auth_pass);
	$xml = new SimpleXmlElement($response);
	return $xml;

}



//Returns a list of a users' followers' ids.
public function followers_ids($user, $auth_user='', $auth_pass='') {

	$url = "http://twitter.com/followers/ids.xml?screen_name={$user}";
	$response = $this->send_request($url, 'GET', '', $auth_user, $auth_pass);
	$xml = new SimpleXmlElement($response);
	return $xml;

}



//Verify a user's credentials #Auth
public function verify_credentials($auth_user, $auth_pass) {

	$url = "http://twitter.com/account/verify_credentials.xml";
	$response = $this->send_request($url, 'GET', '', $auth_user, $auth_pass);
	$xml = new SimpleXmlElement($response);
	return $xml;

}



//Check the rate limit status of a user or the current IP. #Auth #NoLimit
public function rate_limit_status($auth_user, $auth_pass) {

	$url = "http://twitter.com/account/rate_limit_status.xml";
	if (isset($auth_user) && isset($auth_pass)) {
		$response = $this->send_request($url, 'GET', '', $auth_user, $auth_pass);
	} else {
		$response = $this->send_request($url, 'GET');
	}
	$xml = new SimpleXmlElement($response);
	return $xml;

}



//Search API. No authentication whatsoever, and nobody knows the limits...
public function search($query, $lang='en', $results_per_page='15', $page='1', $since_id='') {

	$query = urlencode($query);
	$url = "http://search.twitter.com/search.atom?q={$query}&lang={$lang}&rpp={$results_per_page}&page={$page}&since_id={$since_id}";
	$response = $this->send_request($url, 'GET');
	$xml = new SimpleXmlElement($response);
	return $xml;

}



//Sends HTTP requests for other functions.
private function send_request($url, $method='GET', $data='', $auth_user='', $auth_pass='') {

	if ($this->oauth_on && $auth_user != '') {
		$response = $this->oauth_request($url, $method, $auth_user, $auth_pass, $data);
	}
	else {
		$ch = curl_init($url);
		if (strtoupper($method)=="POST") {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
		if (ini_get('open_basedir') == '' && ini_get('safe_mode') == 'Off'){
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		}
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		if ($auth_user != '' && $auth_pass != '') {
			curl_setopt($ch, CURLOPT_USERPWD, "{$auth_user}:{$auth_pass}");
		}
		$response = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($httpcode != 200) {
			return $httpcode;
		}
	}
	return $response;

}



//Get OAuth authorization link
public function oauth_authorize_link() {

	$oauth = new TwitterOAuth($this->consumer_key, $this->consumer_secret);
	$oauth_token = $oauth->getRequestToken();
	$request_link = $oauth->getAuthorizeURL($oauth_token);
	$data = array( "request_link" => $request_link, "request_token" => $oauth_token['oauth_token'], "request_token_secret" => $oauth_token['oauth_token_secret'] );
	return $data;

}



//Acquire OAuth user token
public function oauth_get_user_token($request_token, $request_token_secret) {

	$oauth = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $request_token, $request_token_secret);
	$tokens = $oauth->getAccessToken();
	$user_token = array ( "access_token" => $tokens['oauth_token'], "access_token_secret" => $tokens['oauth_token_secret'] );
	return $user_token;

}



//Send an API request via OAuth
public function oauth_request($url, $method, $user_access_key, $user_access_secret, $data) {
	$oauth = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $user_access_key, $user_access_secret);
	//$thedata = array();
	//parse_str($data, $thedata);
	$response = $oauth->OAuthRequest($url, $data, $method);
	return $response;

}



//Shorten long URLs with is.gd or bit.ly.
public function shorten_url($the_url, $shortener='is.gd', $api_key='', $user='') {

	if (($shortener=="bit.ly" || $shortener=="j.mp") && isset($api_key) && isset($user)) {
		$url = "http://api.bitly.com/v3/shorten?longUrl={$the_url}&domain={$shortener}&login={$user}&apiKey={$api_key}&format=xml";
		$response = $this->send_request($url, 'GET');
		$the_results = new SimpleXmlElement($response);

		if ($the_results->status_code == '200') {
			$response = $the_results->data->url;
		} else {
			$response = "";
		}

	} elseif ($shortener=="su.pr") {
		$url = "http://su.pr/api/simpleshorten?url={$the_url}";
		$response = $this->send_request($url, 'GET');
	} elseif ($shortener=="tr.im") {
		$url = "http://api.tr.im/api/trim_simple?url={$the_url}";
		$response = $this->send_request($url, 'GET');
	} elseif ($shortener=="3.ly") {
		$url = "http://3.ly/?api=mh4829510392&u={$the_url}";
		$response = $this->send_request($url, 'GET');
	} elseif ($shortener=="ow.ly") {
		$url = "http://www.pluginspark.com/hosted/shorten_url.php?shortener=ow.ly&url={$the_url}";
		$response = $this->send_request($url, 'GET');		
	} elseif ($shortener=="tinyurl") {
		$url = "http://tinyurl.com/api-create.php?url={$the_url}";
		$response = $this->send_request($url, 'GET');
	} elseif ($shortener=="yourls" && isset($api_key) && isset($user)) {
		//Pass a string in the form of "user@domain.com" as the username, and the password as the API key
		$yourls = explode('@', $user);
		$url = "http://{$yourls[1]}/yourls-api.php?username={$yourls[0]}&password={$api_key}&format=simple&action=shorturl&url={$the_url}";
		$response = $this->send_request($url, 'GET');
	} else {
		$url = "http://is.gd/api.php?longurl={$the_url}";
		$response = $this->send_request($url, 'GET');
	}
	return trim($response);

}



//Shrink a tweet and accompanying URL down to 140 chars.
public function fit_tweet_auto($message, $url) {

	$message_length = strlen($message);
	$url_length = strlen($url);
	if ($message_length + $url_length > 140) {
		$shorten_message_to = $message_length - $url_length;
		$shorten_message_to = $shorten_message_to - 4;
		$message = $message." ";
		$message = substr($message, 0, $shorten_message_to);
		$message = substr($message, 0, strrpos($message,' '));
		$message = $message."...";
	}
	return $message." ".$url;

}



//Shrink a tweet and accompanying URL down to fit in 140 chars.
public function fit_tweet($message, $url) {

	$message = $message." ";
	$message = substr($message, 0, 100);
	$message = substr($message, 0, strrpos($message,' '));
	if (strlen($message) > 100) { $message = $message."..."; }
	return $message." ".$url;

}




}

?>
