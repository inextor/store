if( window.location.href.indexOf('pageAdminProducts') !== -1 )
{
	Util.addOnLoad(()=>
	{
		let form	= Util.getById('pageAdminProductsForm');

		form.addEventListener('submit',(evt)=>
		{
			Util.stopEvent( evt );
			pageAdminProductsMakeSearch();		
		});

		Util.ajax({ url : 'api/v1/getProductTypes.php', dataType:'json'}).then((response)=>
		{
			if( !response.result )
			{
				Util.alert( data.msg )
				return;
			}

			console.log( response.data );

			var s = '<option value="">Sin Categoría</a>';
			response.data.forEach((i)=>
			{
				s	+=`<option value='${i.id}' data-path="${i.path}">${i.path}</option>`;
			});

			var parentSelect = Util.getById('pageAdminProductsProductTypeId');
			parentSelect.innerHTML =s;
			pageAdminProductsMakeSearch();
		})
		.catch((e)=>
		{
			console.log('It fails ', e );
		});
	});
}	

function pageAdminProductsMakeSearch()
{

	let form	= Util.getById('pageAdminProductsForm');
	var obj = Util.form2Object( form );

	Util.ajax
	({
		url			: 'api/v1/search'
		,method		: 'POST'
		,dataType	: 'json'
		,data		: obj
	})
	.then(( response )=>
	{
		if( !response.result )
		{
			Util.alert( response.msg );
			return;
		}

		var paginationSrc	= '';
		var product_attrs	= {};
		response.data.product_attrs.forEach( i => product_attrs[ i.id ] = i );

		var s	= '';

		response.data.products.forEach((product)=>
		{
			//var attrs	= response.data.product_attr_values
			//	.filter( value = value.product_id == product.id )
			//	.sort((a ,b)=> a.name.localeCompare( b.name ) );
				
			s+=`<div class="product">
					<div>
						<a class="preview" href="pageProduct.php?id=${product.id}" style="background-image: url(images/engine1.jpg);"></a>
					</div>
					<div class="info_container">
						<a href="pageProduct.php?id=${product.id}" class="name">${product.name}</a>
						<span class="price">$ ${product.price}</span>
					</div>
					<div>
						<a href="pageEditProduct?id=${product.id}">Edit</a>
						<a data-delete-product="${product.id}" href="#">Delete</a>
					</div>
				</div>`;
		});

		Util.getById('pageAdminProductsResults').innerHTML = s;;
	});
}
