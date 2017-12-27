<?php
	include_once(__DIR__.'/php/Web.php');
	use Web\Web;
?><!Doctype html>
<html>
	<header>
		<title>Page Add Product</title>
		<?=Web::getJsTags()?>
		<?=Web::getCssTags()?>
	</header>
	<body>
		<div class="page" id="pageProduct">
			<header class="L_header_buttons">
				<h1>Product </h1>
				<a href="#">Login</a>
			</header>
			<main>
				<input type="hidden" id="pageProductId" value="<?=htmlentities( $_REQUEST['id'],ENT_SUBSTITUTE)?>">
				<div class="L_container">
					<h1 data-product="name"></h1>
					<span data-product="description"></span>
					<span data-product="price"></span>
					<div data-product="video"></div>
					<div id="pageProductValues"></div>
				<div>
			</main>
		</div>
	</body>
</html>
