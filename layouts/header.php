<!DOCTYPE html>
<html lang=<?= $_REQUEST['lang']; ?>>

<head>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<link rel="shortcut icon" sizes="192x192" href="assets/img/favicon192x192.png" />
	<link rel="apple-touch-icon" sizes="192x192" href="assets/img/favicon192x192.png" />
	<link rel="icon" type="image/png" sizes="48x48" href="assets/img/favicon.png" />

	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-title" content="<?= $app_lang['name']; ?>">
	<meta name="application-name" content="<?= $app_lang['name']; ?>" />


	<title><?= $app_lang['name']; ?></title>
	<link rel="stylesheet" href="assets/css/bootstrap.css" type="text/css">
	<link rel="stylesheet" href="assets/css/prism-okaidia.css" type="text/css">
	<link rel="stylesheet" href="assets/css/custom.min.css" type="text/css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" type="text/css">

</head>


<body>
	<div class="container">


		<?php

		$flash = @$_SESSION['flash'];
		if (is_array($flash)) {

			echo '<div class="alert alert-dismissible alert-' . $flash['type'] . '">
			<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
			' . $flash['message'] . '
			</div>';

			$_SESSION['flash'] = '';
		}

		?>

		<!-- content start -->