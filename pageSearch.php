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
		<div class="page" id="pageSearch">
			<header class="L_header_buttons">
				<h1>Product </h1>
				<a href="#">Login</a>
			</header>
			<main>
				<input type="hidden" id="pageSearchId"> 
				<div class="L_container">
					<form id="pageSearchForm" method="POST" action="#">
						<select id="pageSearchProductTypeId">
							<option value="">Categories</option>
						</select>
						<input type="" name="search">
						<input type="submit">
					<form>
					<div id="pageSearchResults"></div>
				<div>
			</main>
		</div>
	</body>
</html>
