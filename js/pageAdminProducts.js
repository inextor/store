if( window.location.href.indexOf('pageAdminProducts') !== -1 )
{
	Util.addOnLoad(()=>
	{
		let form	= Util.getById('pageAdminProductsForm');

		form.addEventListener('submit',(evt)=>
		{
			Util.stopEvent( evt );


			pageAdminProductsMakeSearch( 0 );
		});

		Util.ajax({ url : 'api/v1/getProductTypes.php', dataType:'json'}).then((response)=>
		{
			if( !response.result )
			{
				Util.alert( data.msg )
				return;
			}

			//console.log( response.data );

			var s = '<option value="">Sin Categoría</a>';
			response.data.forEach((i)=>
			{
				s	+=`<option value='${i.id}' data-path="${i.path}">${i.path}</option>`;
			});

			var parentSelect = Util.getById('pageAdminProductsProductTypeId');
			parentSelect.innerHTML =s;


			let querySearchObj	= pageAdminProductsParseUrl();
			let page	= typeof querySearchObj.page !== "undefined" ? querySearchObj.page : 0;

			pageAdminProductsMakeSearch( page );
		})
		.catch((e)=>
		{
			console.log('It fails ', e );
		});
	});
}	

function pageAdminProductsMakeSearch(page)
{
	let form	= Util.getById('pageAdminProductsForm');
	var obj = Util.form2Object( form );
	obj.page	= page;

	Util.ajax
	({
		url			: 'api/v1/search'
		,method		: 'POST'
		,dataType	: 'json'
		,data		: obj
	})
	.then(( response )=>
	{
		if( typeof response.sql !== 'undefined' )
			console.log( response.sql );

		if( !response.result )
		{
			Util.alert( response.msg );
			return;
		}

		let paginationString=  Util.getPagination
		({
		   	totalRows           : response.data.total
		   	,rowsPerPage        : 30 
		   	,currentPage        : page
		   	,link_format        : '<a href="pageAdminProducts.html?page=PAGE_NUMBER">PAGE_TITLE</a>'
		   	,current_page_format: '<a href="#" class="active">PAGE_TITLE</a>'
		   	,nextTitle          : '→'
		   	,prevTitle          : '←'
		   	,disable_next_format: '<a href="pageAdminProducts.html?page=PAGE_NUMBER">PAGE_TITLE</a>'
		   	,disable_prev_format: '<a href="pageAdminProducts.html?page=PAGE_NUMBER">PAGE_TITLE</a>'
		});

		var paginationSrc	= '';
		var product_attrs	= {};
		response.data.product_attrs.forEach( i => product_attrs[ i.id ] = i );

		var s	= '';

		response.data.products.forEach((product)=>
		{
			
			//var attrs	= response.data.product_attr_values
			//	.filter( value = value.product_id == product.id )
			//	.sort((a ,b)=> a.name.localeCompare( b.name ) );
				
			let dateCreated = product.created;
			let description	= Util.txt2html( product.description.substring(0, 100 ));

			s+=`<div class="product">
					<div>
						<div>Product</div>
						<div><img src="/images/product.svg"></div>
					</div>
					<div>
						<div class="info">Title & description</div>
						<div>
							<div>${Util.txt2html(product.name)}</div>
							<div>${description}</div>
						</div>
					</div>
					<div>
						<div>Publish Date</div>
						<div>${dateCreated}</div>
					</div>
					<div class="small">
						<div>Quantity</div>
						<div>${product.qty}</div>
					</div>
					<div class="small">
						<div>Price</div>
						<div>${product.price}</div>
					</div>
					<div class="small">
						<div>Actions</div>
						<div class="actions">
							<a href="pageEditProduct?id=${product.id}">Pause</a>
							<a href="pageEditProduct?id=${product.id}">Enable</a>
							<a data-delete-product="${product.id}" href="#">Delete</a>
							<a href="pageEditProduct.php?id=${product.id}" class="name">Edit</a>
						</div>
					</div>
				</div>`;
		});

		Util.getById('pageAdminProductsResults').innerHTML = s;;
		let deleteButtons	= Array.from( Util.getAll('[data-delete-product]') );
		deleteButtons.forEach((i)=>
		{
			i.addEventListener('click',(evt)=>
			{
				Util.stopEvent( evt );
				pId = i.getAttribute('data-delete-product');

				if( confirm('Are you shure you want to delete the product') )
				{
					Util.ajax
					({ 
						url 		: 'api/v1/deleteProduct.php'
						,method		: 'POST'
						,dataType	:'json'
						,data		: { id : pId }
					})
					.then((response)=>
					{
						if( !response.result )
						{
							Util.alert( data.msg )
							return;
						}

						Util.alert('Success');
						i.parentElement.parentElement.remove();
					})
				}
			});
		});
	});
}

					


function pageAdminProductsParseUrl() 
{
	var query = location.search.substr(1);
	var result = {};
	query.split("&").forEach(function(part) {
	  var item = part.split("=");
	  result[item[0]] = decodeURIComponent(item[1]);
	});
	return result;
}
