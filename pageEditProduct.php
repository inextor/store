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
		<div class="page" id="pageEditProduct">
			<header class="L_header_buttons">
				<h1>AddProductType</h1>
				<a href="#">Login</a>
			</header>
			<main>
				<div class="L_container">
					<form id="pageEditProductForm" method="POST" action="#">
						<input type="hidden" id="pageEditProductId" name="id" value="<?=htmlspecialchars($_REQUEST['id'],ENT_COMPAT)?>">
						<div>Category</div>
						<div>
							<select name="product_type_id" id="pageEditProductProductTypeId"></select>
						</div>
						<div>Name</div>
						<div><input type="text" name="name" required></div>
						<div>qty</div>
						<div><input type="text" name="qty" required></div>
						<div>Price</div>
						<div><input type="text" name="price" required></div>
						<div>Description</div>
						<div>
							<textarea name="description" required></textarea>
						</div>
					</form>
					<div id="pageEditProductFormsContainer"></div>
					<a href="#" id="pageEditProductEditProduct">Save Product</a>
				<div>
			</main>
		</div>
	</body>
</html>
