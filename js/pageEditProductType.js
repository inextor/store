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

			Util.getById('pageEditProductTypeName').textContent = Util.text2html( response.data.product_type.name );

			let s = '';
			response.data.product_type_attrs.forEach((i)=>
			{
				let textSelected = i.type == 'text'

				s+= `<form action="#">
						<div>
								<input type="hidden" name="id" value="${i.id}">
								<div>name:<input type="text" name="name" value="${Util.txt2html( i.name )}"></div>
								<div>type:
									<select name="type">
										<option value="text" ${ i.type === 'text' ? 'selected':'' }>text</option>
										<option value="number"  ${ i.type == 'number' ? 'selected':'' }></option>
										<option value="string" ${ i.type = 'string' ? 'selected':'' }></option>
									</select>
								</div>
						</div>
					</form>
				`;
			});

			Util.getById( 'pageEditProductTypeFormContainers' ).innerHTML = s;
		});
	});

	
}
