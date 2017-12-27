if( window.location.href.indexOf('pageLogin') !== -1 )
{
	Util.addOnLoad(( evt )=>
	{
		Util.stopEvent( evt );
		
		let form = Util.getById('pageLoginForm');

		form.addEventListener('submit',(evtSubmit)=>
		{
			Util.stopEvent( evtSubmit );
			let obj = Util.form2object( form );

			Util.ajax
			({
				url		: 'api/v1/login.php'
				,data	: obj
				,method	: 'POST'
			})
			.then((response)=>
			{
				if( !response.result )
					throw response.msg;

				//Go werever you go
			})
			.catch((e)=>
			{
				if( typeof e === "string" )
				{
					Util.alert( e );
				}
				else if( e instanceof Error )
				{
					Util.alert( e.message );
				}
				else
				{
					Util.alert( e.message );
				}
			});
		});
	});
}
