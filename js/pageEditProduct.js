if( window.location.href.indexOf('/pageEditProduct') !== -1 && window.location.href.indexOf('pageEditProductType') === -1 )
{
	Util.addOnLoad(( evt )=>
	{

		let productInput	= Util.getById('pageEditProductId');
		let product_id 		= productInput.value;

		let form = Util.getById('pageEditProductForm');

		pageEditProductInitProductTypeSelector().then(()=>
		{
			return Util.ajax
			({
				method		: 'POST'
				,url		: 'api/v1/getProduct'
				,dataType	: 'json'
				,data		: { id : product_id }
			})
		})
		.then((response)=>
		{
			if( !response.result )
			{
				throw response.msg;
			}

			return Promise.resolve( response.data );
		})
		.then((response)=>
		{
			return pageEditProductOnCategorySelect( response.product.product_type_id )
			.then(()=>
			{
				return Promise.resolve( response );
			});
		})
		.then((response)=>
		{
			response.product_type_attrs.forEach((i)=>
			{
				let value = response.product_attr_values.find( j =>  i.id == j.product_attr_id );

				if( value !== undefined )
				{
					let input = Util.getFirst('[data-product-attr-id="'+i.id+'"]');
					if( input != null )
						input.value = value.values;
				}
			});

			Util.object2form( response.product, form );
			
			let imgContainers = Array.from(Util.getAll('[data-upload="image"]') );

			response.images.forEach((img, index)=>
			{
				imageContainers[index].querySelector('[data-image-id]').value = img.id;
				imageContainers[index].querySelector('[data-image-id]').value = img.id;
				imageContainers[ index ].querySelector('.image_container').setAttribute("style",'background-image: url(/api/v1/getImageBin.php?id='+img.id+'&width=200&height=200);  background-size: cover;background-position: center center;');
			});


		}).catch((error)=>
		{
			Util.alert( error );
		});


		let editButton	= Util.getById('pageEditProductEditProduct');

		editButton.addEventListener('click',(evt)=>
		{

			let imagesIds	= [];

			var images		= Array.from( Util.getAll('#pageEditProduct [data-upload="image"]') );

			images.forEach((i)=>
			{
				let id = i.querySelector('input[type="hidden"]').value;

				if( id !== '' && id !== null )
				{
					imagesIds.push( id );
				}
			});

			//Do somenthing with the product images
			//
			console.log('Images ids', imagesIds );

			Util.stopEvent( evt );

			if( ! Util.checkFormNativeValidation( form ) )
			{
				Util.lert('Please check the data before send');
				return;
			}

			let attrInputs	= Array.from( Util.getAll('input[data-product-attr-id]') );

			let attrValues	= attrInputs.map( i =>
			{ 
				return { product_attr_id : i.getAttribute('data-product-attr-id'), values : i.value }; 
			});

			let data	=
			{
				product					: Util.form2Object( form )
				,product_attr_values	: attrValues
				,images_ids			 : imagesIds
			};

			Util.ajax
			({
				url			: 'api/v1/editProduct.php'
				,method		: 'POST'
				,data		: data
				,dataType	: 'json'
			})
			.then((response)=>
			{
				if( !response.result )
				{
					throw response.msg;
				}

				Util.alert('Success',()=>
				{ 
				//	window.history.go(-1 ) 
				});
			})
			.catch((e)=>
			{
				if( typeof e === "string" )
				{
					Util.alert( e );
				}
				else if( e instanceof XMLHttpRequest )
				{
					Util.alert( e.statusText );
				}
				else
				{
					Util.alert('An error occurred please try again later');
				}
			});
		})

		let imageContainers	= Array.from( Util.getAll('#pageEditProduct [data-upload="image"]') );

		imageContainers.forEach((i)=>
		{
			console.log('There is a '+i+' Containers');
			let fileInput	= i.querySelector('input[type="file"]');
			let bar 		= i.querySelector('.indicator');

			fileInput.addEventListener('change',(evt)=>
			{
				if( fileInput.files.length > 0 )	
				{
					let data =  new FormData();
					data.append('file',fileInput.files.item( 0 ) );

					Util.ajax
					({

						url			: '/api/v1/addImage.php'
						,headers	: { 'Content-type': 'multipart/form-data' }
						,data		: data
						,dataType	: 'json'
						,method		: 'POST'
						,uploadProgress 	: (evt)=>
						{
							if( !isNaN( evt.loaded ) && !isNaN( evt.total ) )
							{
								let percent = (evt.loaded/evt.total)*100;
								console.log( percent );
								bar.setAttribute('style','width:"'+percent.toFixed(2)+'%');
							}
						}
					})
					.then((response)=>
					{
						if( response.result )
						{
							let ic				= i.querySelector('.image_container');
							ic.setAttribute("style",'background-image: url(/api/v1/getImageBin.php?id='+response.data.id+'&width=200&height=200);  background-size: cover;background-position: center center;');
							let idContainer		= i.querySelector('input[type="hidden"]');
							idContainer.value	= response.data.id;
						}
						else
						{
							alert( response.msg );
						}
					})
					.catch((error)=>
					{
						console.log( error );	
					});
				}
			});     
		});     	

	});
}

function pageEditProductInitProductTypeSelector()
{
	return Util.ajax({ url : 'api/v1/getProductTypes.php', dataType:'json'}).then((response)=>
	{
		if( !response.result  )
			throw response.msg;

		console.log( response.data );

		var s = '<option value="">Sin Categoría</a>';
		response.data.forEach((i)=>
		{
			s	+=`<option value='${i.id}' data-path="${i.path}">${i.path}</option>`;
		});

		var parentSelect = Util.getById('pageEditProductProductTypeId');
		parentSelect.innerHTML =s;

		parentSelect.addEventListener('change',(evt)=>
		{
			let path	= Util.getById('pageEditProductTypePath');
			let options = Array.from( parentSelect.querySelectorAll('option') );
			let f	= options.find( i => i.getAttribute('value') === parentSelect.value );

			if( f !== undefined )
			{
				pageEditProductOnCategorySelect( f.getAttribute('value') );
			}
		});
	})

}

function pageEditProductOnCategorySelect(productTypeId)
{
	if( productTypeId === null )
		return Promise.resolve( true );

	return Util.ajax
	({
		method		: 'POST'
		,url		: 'api/v1/getProductType'
		,dataType	: 'json'
		,data		: { id : productTypeId }
	})
	.then((response)=>
	{
		if( !response.result )
		{
			return Promise.reject('Fails to get ProductTypeCategory 1');
		}

		var s = '';

		let reverseParents = response.data.parents.reverse();
		reverseParents.push( response.data.product_type );

		reverseParents.forEach((p)=>
		{
			let attrs		= response.data.product_type_attrs.filter( i => i.product_type_id == p.id );
			let attrInputs	= '';

			attrs.forEach((a)=>
			{
				attrInputs +=
					`<div>
						<div>${ Util.txt2html( a.name ) }</div>
						<div><input type="text" name="att_${Util.quoteattr( a.id )}" data-product-attr-id="${a.id}"></div>
					</div>`;
			});

			s+= `<h2>Values for ${ Util.txt2html( p.name ) }</h2>
				 <form action="#" id="pageEditProductAttributes">${ attrInputs }</form>`;
		});

		Util.getById('pageEditProductFormsContainer').innerHTML = s;

		console.log( response );

		return Promise.resolve( true );
	});
}
