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
		<div class="page" id="pageAdminProducts">
			<header class="L_header_buttons">
				<h1>Product </h1>
				<a href="#">Login</a>
			</header>
			<main>
				<input type="hidden" id="pageAdminProductsId"> 
				<div class="L_container">
					<form id="pageAdminProductsForm" method="POST" action="#">
						<select id="pageAdminProductsProductTypeId">
							<option value="">Categories</option>
						</select>
						<input type="" name="search">
						<input type="submit">
					<form>
					<div id="pageAdminProductsResults"></div>
				<div>
			</main>
		</div>
	</body>
</html>
