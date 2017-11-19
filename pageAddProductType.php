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
		<div class="page" id="pageAddProductType">
			<header class="L_header_buttons">
				<h1>AddProductType</h1>
				<a href="#">Login</a>
			</header>
			<main>
				<div class="L_container">
					<form id="pageAddProductTypeForm" method="POST" action="#">
						<label>Name:</label>
						<input name="name" id="pageAddProductTypeName" type="name">
						<label>Product Parent Id: <span id="pageAddProductTypePath"></span></label>
						<select id="pageAddProductTypeParentId" name="parent_product_type_id">
							<option value="">Sin Categoría</option>
						</select>
						<div>
							<input type="submit" value="Add">
						</div>
					</form>
				<div>
			</main>
		</div>
	</body>
</html>
