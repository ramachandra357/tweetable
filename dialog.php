<?php
require_once('../../../wp-load.php');

switch($_GET['show']) {

	case 'documentation':
		$title = 'Documentation';
		documentation($title);
	break;
	
	case 'hashtag':
		$hashtag = $_GET['hashtag'];
		$title = 'Hashtag Search: '.$hashtag;
		hashtag($title, $hashtag);
	break;
	
	default:
		$title = 'Documentation';
		documentation($title);
	break;
	
}



function documentation($title) {
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $title; ?></title>
<style type="text/css">
body {
	margin: 20px;
	background-color: #f9f9f9;
}
h1,h2,h3,h4 {
	margin: 0px;
	padding: 0px;
}
code {
	background-color: #EDEDFF;
}
</style>
</head>
<body>
	<?php
	$readme = file_get_contents('readme.txt');
	$readme = make_clickable(nl2br(wp_specialchars($readme)));
	$readme = preg_replace('/`(.*?)`/', '<code>\\1</code>', $readme);
	$readme = preg_replace('/[\040]\*\*(.*?)\*\*/', ' <strong>\\1</strong>', $readme);
	$readme = preg_replace('/[\040]\*[^\040](.*?)\*/', ' <em>\\1</em>', $readme);
	$readme = preg_replace('/=== (.*?) ===/', '<h2>\\1</h2>', $readme);
	$readme = preg_replace('/== (.*?) ==/', '<h3>\\1</h3>', $readme);
	$readme = preg_replace('/= (.*?) =/', '<h4>\\1</h4>', $readme);
	echo $readme;
	echo '</body></html>';
}



function hashtag($title, $hashtag) {
	if ( !defined('WP_CONTENT_URL') ) { define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content'); }
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $title; ?></title>
<link rel='stylesheet' href='http://wp.ntugo.com/wp-admin/load-styles.php?c=1&amp;dir=ltr&amp;load=global' type='text/css' media='all' />
<link rel='stylesheet' id='colors-css'  href='http://wp.ntugo.com/wp-admin/css/colors-fresh.css?ver=20090610' type='text/css' media='all' />
<link rel="stylesheet" href="<?php echo tweetable_get_plugin_dir('url'); ?>/admin_css.css" type="text/css" />
<style>
.search-div {
	margin-left: 15px;
}
</style>
</head>
<body>
	<?php
	$twitter = new Twitter_API();
	echo '<div class="search-div">';
	echo '<h3>#'.$hashtag.'</h3>';
	echo '<div class="twitter_timeline">';
	echo '<ol id="tweetable-timeline">';
	$results = $twitter->search($hashtag, 'en', '30');
	foreach ($results->entry as $tweet) {
		$tweet->content = preg_replace('/<a\shref=\"([^\"]*)\">\#<b>(.*)<\/b><\/a>/siU', '<a href="'.tweetable_get_plugin_dir('url').'/dialog.php?show=hashtag&hashtag=\\2&KeepThis=true&amp;TB_iframe=true&amp;height=450&amp;width=680" class="thickbox hashtag" title="Hashtag Search">#\\2</a>', $tweet->content);
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
    	echo '</span>';
		echo '<br style="clear:both" />';
		echo '</li>';
	}
	echo '</ol>';
	echo '</div>';
	echo '</div>';
	echo '</body></html>';
}

?>