if( window.location.href.indexOf('pageSearch') !== -1 )
{
	Util.addOnLoad(()=>
	{
		let form	= Util.getById('pageSearchForm');

		form.addEventListener('submit',(evt)=>
		{
			Util.stopEvent( evt );
			pageSearchMakeSearch( form );		
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

			var parentSelect = Util.getById('pageSearchProductTypeId');
			parentSelect.innerHTML =s;

			Array.from( Util.getAll('a[data-more-images]') ).forEach((i)=>
			{
				i.addEventListener((evt)=>
				{
					Util.stopEvent( evt );
					var product_id      = i.getAttribute('data-more-images');
    				var p_item          = i.parentElement

    				p_item.classList.toggle('click');

    				if( p_item.classList.contains('click') )
    				    i.firstElementChild.textContent = 'Menos fotos';
    				else
    				    i.firstElementChild.textContent = 'Más fotos';
				});
			});
		})
		.catch((e)=>
		{
			console.log('It fails ', e );
		});
	});
}	

function pageSearchMakeSearch()
{
	let form	= Util.getById('pageSearchForm');
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
		
			
			let images		= response.data.images.filter(i => i.product_image.product_id == product.id );
			let image_url	=  images.length === 0  ? '/images/image.png' : `/api/v1/getImage.php?id=${images[0].image.id}`;

			let otherImages	= images.filter((i,index) => index > 0 );
			let icode		= '';

			otherImages.forEach((i)=>
			{
				icode += `<a data-image-product="${product.id}" style="background-image: url(/api/v1/getImage.php?id=${i.image.id});" href="#"></a>`;
			});

			s+=`<div class="product_item">
					<div>
						<a class="preview" data-product-preview="${product.id}" href="pageProduct.php?id=${product.id}" style="background-image: url(${image_url});"></a>
						<div class="mini_imagen">${icode}</div>
						<a href="pageProduct.php?id=4018" class="show_more">
							<div>Ver más</div>
						</a>
						<a data-more-images="${product.id}" href="#" class="show_less">
							<div>Más fotos</div>
						</a>
					</div>
					<div class="info_container">
						<a href="pageProduct.php?id=${product.id}" class="name">${product.name}</a>
						<div class="price">$ ${product.price}</div>
					</div>
					<a data-add-car="${product.id}" href="#" class="car"></a>
				</div>`;
		});

		Util.getById('pageSearchResults').innerHTML = s;;
	});

}
