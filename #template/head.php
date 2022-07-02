<!Doctype html>
<html lang="zh-tw">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<title><?=isset($title) ? $title : '';?></title>
		<meta name="description" content="<?=isset($description) ? $description : $title;?>" />
		<meta name="keywords" content="<?=isset($keywords) ? $keywords : $title;?>" />
		<meta name="subject" content="<?=isset($subject) ? $subject : $title;?>" />
		<meta name="robots" content="<?=isset($robots) ? $robots : 'index, follow';?>">
		<meta name="author" content="scai" />
		<link rel="stylesheet" href="/resource/w3css/w3pro.css" />
		<link rel="stylesheet" href="/resource/Font-Awesome/css/font-awesome.css" />
		<?php if(isset($libCss)): ?>
			<?php foreach($libCss as $css): ?>
				<link rel="stylesheet" href="/resource/<?=$css;?>" />
			<?php endforeach; ?>
		<?php endif; ?>
		<link rel="stylesheet" href="/resource/global.css" />
		<?php if(isset($customCss)): ?>
			<?php foreach($customCss as $css): ?>
				<link rel="stylesheet" href="/<?=$css;?>" />
			<?php endforeach; ?>
		<?php endif; ?>
	</head>
	<body>
