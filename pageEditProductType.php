<?php
	include_once(__DIR__.'/php/Web.php');
	use Web\Web;
?><!Doctype html>
<html>
	<header>
		<?=Web::getJsTags()?>
		<?=Web::getCssTags()?>
	</header>
	<body>
		<div class="page" id="pageEditProductType">
			<header class="L_header_buttons">
				<h1>Edit Product Type</h1>
				<a href="#">Login</a>
			</header>
			<main>
				<div class="L_container">
					<h2 id="pageEditProductTypeName"></h2>
					<a href="">Add new Attribute</a>
					<div id="pageEditProductTypeFormContainers"></div>
				<div>
			</main>
		</div>
	</body>
</html>
