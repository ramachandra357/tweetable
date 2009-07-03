<?php
require_once('../../../wp-load.php');

switch($_GET['show']) {

	case 'documentation':
		$title = 'Documentation';
		documentation();
	break;
	
	default:
		$title = 'Documentation';
		documentation();
	break;
	
}



function documentation() {
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $title; ?></title>
<style type="text/css">
body {
	margin: 20px;
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
	$readme = preg_replace('/[\040]\*(.*?)\*/', ' <em>\\1</em>', $readme);
	$readme = preg_replace('/=== (.*?) ===/', '<h2>\\1</h2>', $readme);
	$readme = preg_replace('/== (.*?) ==/', '<h3>\\1</h3>', $readme);
	$readme = preg_replace('/= (.*?) =/', '<h4>\\1</h4>', $readme);
	echo $readme;
	echo '</body></html>';
}

?>