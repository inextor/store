
if( window.location.href.indexOf('/pageAddProductType.php') !== -1 )
{
	console.log('HELLL  YEAGH');
	Util.addOnLoad(( evt )=>
	{
		Util.stopEvent( evt );
		pageAddProductTypeLoad();

		let form = Util.getById('pageAddProductTypeForm');

		form.addEventListener('submit',(evt)=>
		{
			Util.stopEvent( evt );
			let data = Util.form2Object( form );

			Util.ajax
			({
				url			: 'api/v1/addProductType.php'
				,method		: 'POST'
				,dataType	: 'json'
				,data		: data
			})
			.then((response)=>
			{
				if( !response.result )
				{
					Util.alert( response.msg );
					return;
				}

				pageAddProductTypeLoad();

				form.reset();

				Util.alert('Product Type added Successfully');
			})
			.catch((e)=>
			{
				Util.alert('Ocurrio un error y no se por que');
			});
		});
	});
}

function pageAddProductTypeLoad()
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

		var parentSelect = Util.getById('pageAddProductTypeParentId');
		parentSelect.innerHTML =s;
		/*
		parentSelect.addEventListener('change',(evt)=>
		{
			let path	= Util.getById('pageAddProductTypePath');
			let options = parentSelect.querySelectorAll('option');

			for(let i=0;i<options.length;i++)
			{
				if( options[i].getAttribute('value') === parentSelect.value )
				{
					path.textContent	= options[ i ].getAttribute('data-path');
					return;
				}
			}
			path.textContent ='';
		});
		*/
	})
	.catch((e)=>
	{
		console.log('It fails ', e );
	});

}
