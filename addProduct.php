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
		<div class="page" id="pageAddProduct">
			<header class="L_header_buttons">
				<h1>Add new product</h1>
				<a href="#">login</a>
			</header>
			<main>
				<div class="L_container">
					<form id="pageAddProductForm" method="POST" action="#">
						<label>Name:</label>
						<input name="name" id="pageAddProductName" type="name">

						<label>Type:</label>
						<select id="pageAddProductType"></select>

						<label>Model:</label>
						<select id="pageAddProductModel"></select>

						<label>Part Name</label>
						<input type="text" name="price"/>
						<label>Price</label>
						<input type="text" name="price"/>
						<label>Description</label>
						<textarea name="description"></textarea>
					</form>
				<div>
			</main>
		</div>
	</body>
</html>
