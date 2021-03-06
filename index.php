<?
error_reporting(E_ALL);
ini_set('display_errors',1);
define('INC_DIR', __DIR__.'/inc/');

function sethead($msg, $code = 404) { http_response_code($code); echo '<p>'.$msg.'</p>'; }
function getlang($a) { return $a ? ucfirst($a) : J_LANG; }
function buildMenu($items, $current = null) {
	foreach($items as $url => $name) {
		if(!$name) $name = ucfirst($url);
		$active = isset($current[$url]) ? ' class="active"' : '';
		echo '<li'.$active.'><a href="/'.$url.'">'.$name.'</a></li>'.PHP_EOL.str_repeat("\t",3);
	}
}
function showAlert($msg, $ok = null) {
	echo '<div class="alert alert-'.($ok ? 'success' : 'fail').'" role="alert">'.
	'<strong>'.($ok ? 'Well done!' : 'Whoa!').'</strong> '.$msg.
	'</div>'.PHP_EOL;
}

$doi = array(
	'addr' => 'https://doi.org/',
	'pref' => '00.00000/',
	'name' => 'j.name'
);
define('DOI_ADDR', $doi['addr'].$doi['pref']);
const J_NAME = 'Journal Name';
const J_ABBR = 'J. Name';
const J_LANG = 'Eng';
const J_YEAR = 2000;
$meta = 'Dummy journal description';

$path = preg_match('/[\w\-.]+/',$_SERVER['REQUEST_URI'],$path) ? $path[0] : '';
$page = array(
	'home' => '',
	'archive' => '',
	'authors' => 'For authors',
	'editorial' => 'Editorial Board',
	'subscription' => '',
	'contacts' => ''
);
$assist = array(
	'tools' => '',
	'login' => ''
);
$param = array(
	'q' => 'Search results for',
	'sec' => 'Articles',
	'vol' => '',
	'issue' => '',
	'page' => ''
);
$prefix = array();
$desc = '';
$i = 0;

ob_start();
include INC_DIR.'usermodule.php';
include INC_DIR.'postabs.php';

$all = $page + $assist;
unset($all['home']);
if(isset($all[$path])) {
	foreach($param as $k => $val) {
		if(!$val) $val = ucfirst($k);
		else if($i++) {
			if(!$prefix && !empty($_GET[$k])) $prefix[] = $val;
			continue;
		}
		if(!empty($_GET[$k])) $prefix[] = $val.' '.$_GET[$k];
	}
	if(!$prefix) $prefix[] = $all[$path] ? $all[$path] : ucfirst($path);
} else if(!$path) {
	$path = 'home';
	$desc = $meta;
}
$current = array($path => true);

$template = './pages/'.$path;
if(is_file($template.'.html')) include $template.'.html';
elseif(is_file($template.'.php')) include $template.'.php';
else sethead('Page not found');
$output = ob_get_contents();
ob_end_clean();

if($prefix) $prefix[] = '- ';
isset($mysqli) && $mysqli->close();
?><!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?=implode(' ', $prefix).J_NAME?></title>
	<link href="/assets/style.css" rel="stylesheet">
	<meta name="description" content="<?=$desc?>">
</head>
<body>
	<div class="container">
		<div class="header">
			<ul>
			<?
				buildMenu($assist);
			?></ul>
			<div class="issn">ISSN xxxx-xxxx (print), ISSN xxxx-xxxx (online)</div>
			<div class="title"><a href="/"><img src="/img/logo.gif" alt="logo"></a><span><?=J_NAME?></span></div>
		</div>
		<ul class="nav">
			<?
				buildMenu($page, $current);
			?></ul>
		<div class="page <?=$path?>">
			<?
			echo $output;
			?>
		</div>
		<div class="footer">
			<span>&copy; 2015 <?=J_NAME?></span>
		</div>
	</div>
	<script type="text/javascript" src="/assets/script.js"></script>
</body>
</html>
