const 
START 			= 0
, DEFAULT_APP_THEME = "dark"
;

var
__come = new Event('come')
, __go = new Event('go');

app.hash = app.storage("hash") || null;
app.theme = app.storage("theme") || DEFAULT_APP_THEME;

app.body = $("body")[0];
app.initial_pragma = START;

bootloader.loaders = { 
	pass 		: 1
	// xhr components
	, splash 	: 0
	// api calls
	, helpers   : 0
	, theme 	: 0
	, worldjson : 0
};

bootloader.loadComponents.add(_=>{
	
	app.call("/themes/get/"+app.theme).then(theme=>{
		
	if(theme.data){
			app.theme = theme = theme.data.json()
			bind(app.color_pallete, theme)
		}else theme = app.colors()
		
		$(".--background").css({ background: theme.BACKGROUND });
		$(".--foreground").css({ background: theme.FOREGROUND });

		// USER IS LOGGED ?
		if(app.hash){
			
			app.load("webroot/views/xhr/splash.php");
			app.exec("webroot/js/helpers.js");

			app.call("var/World/meta.json").then(world => {
				app.data.world = world.data.json();				
				if(app.data.world) bootloader.ready("worldjson");
				else app.error("Error loading World's timeseries...");
			})
		// LOGIN
		}else location.herf = "/login";


		bootloader.ready("theme")
	})
})

bootloader.onFinishLoading.add(nil => tileClickEffectSelector(".-tile"))

app.onPragmaChange.add(x => {
	switch (x) {
		case START: 		/*********/ 
		;
		break;
		case FRACTAL: 		/*********/ 
		;
		break;
		case RELATIONAL:	/*********/ 
		;
		break;
		case COMPARATIVE: 	/*********/ 
		;
		break;
		case DOT2NEWS: 		/*********/ 
		;
		break;
	}
});

// __scroll = new Swipe(app.body);
// __scroll.up(()=>{});
// __scroll.down(()=>{});
// __scroll.right(()=>{ });
// __scroll.left(()=>{ });
// __scroll.fire();

app.initPool.add(_ => {
	if(bootloader) bootloader.loadComponents.fire();
})