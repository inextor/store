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
				<h2>Edit Category Type</h2>
				<div class="L_container">
					<label>Category Name</label>
					<input type="text" id="pageEditProductTypeName">
					<a href="">Add new Attribute</a>
					<div id="pageEditProductTypeFormContainers"></div>
					<a href="button" id="pageEditProductTypeSaveButton">Save</a>
				<div>
			</main>
		</div>
	</body>
</html>
