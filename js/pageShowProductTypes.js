if( window.location.href.indexOf('/pageShowProductTypes') !== -1 )
{
	Util.addOnLoad(( evt )=>
	{
		Util.ajax({ url: 'api/v1/getProductTypes.php', method: 'POST', dataType:'json' })
		.then((response)=>
		{
			if( !response.result )
				return;


			response.data.sort((a,b)=>
			{
				if( a.parent_product_type_id === b.parent_product_type_id )
				{
					let result = a.path.localeCompare( b.path);

					if( result === 0 )
						return 0;

					return result < 0 ? -1 : 1;
				}

				let result = 0;

				if( a.parent_product_type_id === null )
				{
					return a.name.localeCompare( b.path ) > 0 ? 1 : -1;
				}

				if( b.parent_product_type_id === null )
				{
					return a.path.localeCompare( b.name ) < 0 ? -1: 1;
				}

				return 0;
			});

			let s	= '';

			response.data.forEach((i)=>
			{
				let c = i.parent_product_type_id === null ? '' : 'child'
				s	+=`<a href='pageEditProductType.php?id=${i.id}' class="${c}">${Util.txt2html( i.path )}</a>`;
			});

			Util.getById('pageShowProductTypesContainer').innerHTML = s;
		})
		.catch((e)=>
		{
			console.log( e );
			Util.alert('An error occurred please try again later');
		});
	});
}
