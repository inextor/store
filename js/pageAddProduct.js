if( window.location.href.indexOf('/pageAddProduct.php') !== -1 )
{
	Util.addOnLoad(( evt )=>
	{
		pageAddProductInitProductTypeSelector();

		let addButton	= Util.getById('pageAddProductAddNewProduct');
		let form		= Util.getById('pageAddProductProductForm');

		addButton.addEventListener('click',(evt)=>
		{
			Util.stopEvent( evt );

			if( !Util.checkFormNativeValidation( form ) )
			{
				Util.lert('Please check the data before send');
				return;
			}

			let imagesIds	= [];

			var images		= Array.from( Util.getAll('#pageAddProduct [data-upload="image"]') );

			images.forEach((i)=>
			{
				let id = i.querySelector('input[type="hidden"]').value;

				if( id !== '' && id !== null )
				{
					imagesIds.push( id );
				}
			});

			//Do somenthing with the product images




			let attrInputs	= Array.from( Util.getAll('input[data-product-attr-id]') );

			let attrValues	= attrInputs.map( i =>
			{ 
				return { product_attr_id : i.getAttribute('data-product-attr-id'), values : i.value }; 
			});

			let data	=
			{
				product				 : Util.form2Object( form )
				,product_attr_values : attrValues
				,images_ids			 : imagesIds
			};

			Util.ajax
			({
				url			: 'api/v1/addProduct.php'
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
					window.location.href= 'pageDashboard.php';
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

		/* IMAGES */

		let imageContainers	= Array.from( Util.getAll('#pageAddProduct [data-upload="image"]') );

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

function pageAddProductInitProductTypeSelector()
{
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

		var parentSelect = Util.getById('pageAddProductProductTypeId');
		parentSelect.innerHTML =s;

		parentSelect.addEventListener('change',(evt)=>
		{
			let path	= Util.getById('pageAddProductTypePath');
			let options = Array.from( parentSelect.querySelectorAll('option') );
			let f	= options.find( i => i.getAttribute('value') === parentSelect.value );

			if( f !== undefined )
			{
				pageAddProductOnCategorySelect( f.getAttribute('value') );
			}
		});
	})
	.catch((e)=>
	{
		console.log('It fails ', e );
	});
}

function pageAddProductOnCategorySelect(productTypeId)
{
	Util.ajax
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
				 <form action="#" id="pageAddProductAttributes">${ attrInputs }</form>`;
		});

		Util.getById('pageAddProductFormsContainer').innerHTML = s;

		console.log( response );

	})
	.catch((e)=>
	{
		etil.alert( e );
	});
}
