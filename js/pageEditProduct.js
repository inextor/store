if( window.location.href.indexOf('/pageEditProduct.php') !== -1 )
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

		}).catch((error)=>
		{
			Util.alert( error );
		});


		let editButton	= Util.getById('pageEditProductEditProduct');

		editButton.addEventListener('click',(evt)=>
		{
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