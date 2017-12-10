if( window.location.href.indexOf('/pageProduct') !== -1 )
{
	Util.addOnLoad(( evt )=>
	{
		Util.stopEvent( evt );
		
		
		let id = Util.getById('pageProductId').value;
		
		Util.ajax
		({
			url			: 'api/v1/getProduct.php'
			,method		: 'POST'
			,dataType	: 'json'
			,data		: { id : id }
		})
		.then((response)=>
		{
			if( !response.result )
			{
				Util.alert( response.msg );
				return;
			}

			//Util.alert('Product Type added Successfully');
			for(let i in response.data.product )
			{
				let t = Util.getFirst('[data-product="'+i+'"]');
				if( t !== null )
				{
					t.textContent = Util.txt2html( response.data.product[i] );
				}
			}

			let reverseParents	= response.data.parents.reverse();
			let s				= '';

			reverseParents.forEach((p)=>
			{
				let attrs		= response.data.product_type_attrs.filter( i => i.product_type_id == p.id );
				let attrInputs	= '';

				attrs.forEach((a)=>
				{
					let value	= response.data.product_attr_values.find( i => i.product_attr_id == a.id );
					if( value !== undefined )
					{
						attrInputs +=
							`<div>
								<div>
									<b>${ Util.txt2html( a.name ) }:</b>
									<span>${ Util.txt2html( value.values) }</span>
								</div>
							</div>`;
					}
				});

				if( attrInputs !== '' )
				{
					s+= `<h2>Values for ${ Util.txt2html( p.name ) }</h2>
						 <div>${ attrInputs }</form>`;
				}
			});

			Util.getById('pageProductValues').innerHTML = s;

		})
		.catch((e)=>
		{
			Util.alert('Ocurrio un error y no se por que');
			console.log( e );
		});
	});
}
