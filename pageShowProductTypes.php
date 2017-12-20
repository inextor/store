<?php
	include_once(__DIR__.'/php/Web.php');
	use Web\Web;
?><!Doctype html>
<html>
	<head>
		<?=Web::getJsTags()?>
		<?=Web::getCssTags()?>
	</head>
	<body>
		<div class="page" id="pageShowProductTypes">
			<header class="L_header_buttons">
				<h1>Product Types</h1>
				<a href="#">Login</a>
			</header>
			<main>
				<div class="L_container" id="pageShowProductTypesContainer"><div>
			</main>
		</div>
	</body>
</html>
