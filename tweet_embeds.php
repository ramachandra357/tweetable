<?php

/**
* Adds the ability to embed tweets in posts.
*/



/*** oEmbed ***/
wp_oembed_add_provider('https://twitter.com/*/statuses/*', 'https://api.twitter.com/1/statuses/oembed.{format}', false);



/*** Shortcode ***/
add_shortcode('twitterstatus', 'tweetable_twitterstatus_shortcode');


function tweetable_twitterstatus_shortcode($attributes) {
	if (empty($attributes['url'])) return '';
	global $wp_embed;
	return $wp_embed->shortcode($attributes, $attributes['url']);
}



?>