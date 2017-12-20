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
		<div class="page" id="pageDashboard">
			<header class="L_header_buttons">
				<h1>AddProductType</h1>
				<a href="#">Login</a>
			</header>
			<main>
				<div class="L_container">
					<a href="pageAdminProducts.php">Products</a>
					<a href="pageShowProductTypes.php">Categories</a>
					<a href="pagePromos.php">Promos</a>
					<a href="pageOrders.php">Orders</a>
				<div>
			</main>
		</div>
	</body>
</html>
