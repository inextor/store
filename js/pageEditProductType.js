if( window.location.href.indexOf('/pageEditProductType') !== -1 )
{
	Util.addOnLoad(( evt )=>
	{
		Util.stopEvent( evt );

		var params	= Util.getSearchParameters();

		Util.ajax
		({
			url			: 'api/v1/getProductType'
			,method		: 'POST'
			,dataType	: 'json'
			,data		:
			{ 
				id: params['id'] 
			}
		})
		.then((response)=>
		{
			if( !response.result )
			{
				Util.alert( response.msg  );
				return;
			}

			Util.getById('pageEditProductTypeName').value = response.data.product_type.name;

			let s = '';
			response.data.product_type_attrs.forEach((i)=>
			{
				let textSelected = i.type == 'text'

				s+= `<form action="#" data-attr="${i.id}">
						<div>
								<input type="hidden" name="id" value="${i.id}">
								<div>name:<input type="text" name="name" value="${Util.txt2html( i.name )}"></div>
								<div>type:
									<select name="values_description">
										<option value="text" ${ i.type === 'text' ? 'selected':'' }>text</option>
										<option value="number"  ${ i.type == 'number' ? 'selected':'' }>Number</option>
										<option value="string" ${ i.type = 'string' ? 'selected':'' }>String</option>
									</select>
								</div>
						</div>
					</form>
				`;
			});

			Util.getById( 'pageEditProductTypeFormContainers' ).innerHTML = s;
		});

		Util.getById('pageEditProductTypeSaveButton').addEventListener('click',(evt)=>
		{
			Util.stopEvent( evt );
			let params	= Util.getSearchParameters();

			let attrArray	= Array.from( Util.getAll('form[data-attr]') );
			let values		= attrArray.map( i => Util.form2Object( i ) );


			let request = 
			{
				id			: params['id'] 
				,name		: Util.getById('pageEditProductTypeName')
				,attributes	: values
			};

			Util.ajax
			({
				url		: 'api/v1/editProductType'
				,data	: request
				,method	: 'POST'
			}).then((response)=>
			{
				
			});
		});
	});
}
