var testCounter	= 0;

if(  window.location.href.indexOf('pageTest.php') !== -1 )
{
	Util.addOnLoad(()=>
	{
		let name	= Util.getRandomString( 20 );


		Util.ajax
		({
			method		: 'POST'
			,url		: 'api/v1/getProductType'
			,dataType	: 'json'
			,data		: { id : 7 }
		})
		.then((responseGetProductType)=>
		{
			if( !responseGetProductType.result )
			{
				return Promise.reject('Fails to get ProductTypeCategory 1');
			}

			console.log( responseGetProductType );

			return Util.ajax
			({
				method: 'POST', url: 'api/v1/addProductType', dataType:'json', data:{ name: name }
			})
		})
		.then((addProductType1Response)=>
		{
			if( !addProductType1Response.result )
			{
				console.log( addProductType1Response );
				return Promise.reject('Fail to add parent category ( product_type ) ');
			}

			pageTestAddSuccess('Add Product Type ');

			let name2 = Util.getRandomString( 20 );


			return Util.ajax
			({
				method		: 'POST'
				,url		: 'api/v1/addProductType'
				,dataType	: 'json'
				,data:
				{
					name				: name2
					,parent_product_id	: addProductType1Response.data.id
				}
			});
		})
		.then((addProductType2Response)=>
		{
			if( !addProductType2Response.result )
				return Promise.reject('Fail to add the child category ( product type )');

			pageTestAddSuccess('Add Product Sub Type ');
			console.log( addProductType2Response );

			return Util.ajax
			({
				url			: 'api/v1/getProductType.php'
				,method		: 'POST'
				,dataType	: 'json'
				,data		: { id : addProductType2Response.data.id }
			});
		})
		.then((getProductTypeResponse)=>
		{
			if( !getProductTypeResponse.result )
			{
				return Promise.reject('Fails to read the child category');
			}

			pageTestAddSuccess('Read Product type' );

			let attrs = [];

			for(var i=0;i<4;i++)
			{
				attrs.push
				({
					name				: Util.getRandomString( 4 )
					,values_description	: JSON.stringify({type: 'text'})
				});
			}


			return Util.ajax
			({
				url			: 'api/v1/addProductTypeAttrs.php'
				,dataType	: 'json'
				,method		: 'POST'
				,data		: {
					product_type_id	: getProductTypeResponse.data.product_type.id
					,product_attrs	: attrs
				}
			});
		})
		.then((responseAddProductTypeAttrs)=>
		{
			if( !responseAddProductTypeAttrs.result )
			{
				throw responseAddProductTypeAttrs.msg;
			}

			pageTestAddSuccess('Add Product type Attrs' );
		})
		.catch((e)=>
		{
			pageTestAddError( e );
		});
	});
}
//
function pageTestAddSuccess( testName )
{
	testCounter++;
	let div 		= document.createElement('div');
	div.setAttribute('style','color: black;');
	div.innerHTML	= testName+( testCounter )+' <b style="color:green">✓</b>';
	Util.getFirst('main>div.L_container').appendChild( div );
}

function pageTestAddError( testName, description )
{
	testCounter++;
	let div 		= document.createElement('div');
	div.setAttribute('style','color: black;');
	div.innerHTML	= testName+'<b style="color:red">✗</b>';
	Util.getFirst('main>div.L_container').appendChild( div );
}
