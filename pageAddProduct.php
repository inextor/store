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
		<div class="page" id="pageAddProduct">
			<header class="L_header_buttons">
				<h1>AddProductType</h1>
				<a href="#">Login</a>
			</header>
			<main>
				<div class="L_container">
					<div>
						<div data-upload="image">
							<input type="hidden" data-image-id="">
							<div class="image_container">
								<input data-role="none" name="image" accept="image/*" type="file" />
							</div>
							<div class="indicator"></div>
						</div>
						<div data-upload="image">
							<input type="hidden" data-image-id="">
							<div class="image_container">
								<input data-role="none" name="image" accept="image/*" type="file" />
							</div>
							<div class="indicator"></div>
						</div>
						<div data-upload="image">
							<input type="hidden" data-image-id="">
							<div class="image_container">
								<input data-role="none" name="image" accept="image/*" type="file" />
							</div>
							<div class="indicator"></div>
						</div>
					</div>
					<form id="pageAddProductProductForm" method="POST" action="#">
						<div>Category</div>
						<div>
							<select name="product_type_id" id="pageAddProductProductTypeId"></select>
						</div>
						<div>Name:</div>
						<div><input type="text" name="name" required></div>
						<div>qty:</div>
						<div><input type="text" name="qty" required></div>
						<div>Price:</div>
						<div><input type="text" name="price" required></div>
						<div>Video Url:</div>
						<div><input type="text" name="video_url"></div>
						<div>Description</div>
						<div>
							<textarea name="description" required></textarea>
						</div>
					</form>
					<div id="pageAddProductFormsContainer"></div>
					<a href="#" id="pageAddProductAddNewProduct">Add Product</a>
				<div>
			</main>
		</div>
	</body>
</html>
