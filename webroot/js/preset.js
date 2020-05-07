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
app.data.Brazil = {};
app.initial_pragma = START;

bootloader.loaders = { 
	pass 		: 1
	// xhr components
	, splash 	: 0
	// api calls
	, helpers   : 0
	, theme 	: 0
	, worldjson : 0
	, statesarray:0
	, menujs 	: 0
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

			app.call("var/Brazil/total.json").then(world => {				
				app.data.Brazil.serie = world.data.json();
				if(app.data.Brazil) bootloader.ready("worldjson");
				else app.error("Error loading World's timeseries...");
			});

			app.call("content/states/Brazil").then(states => {
				states = states.data.json();				
				if(states.length){
					app.data.Brazil.innerserie = {};
					var
					loaders = {};
					states.each(st => loaders[st] = 0);
					bind(bootloader.loaders, loaders);
					states.each(st => {
						app.call("var/Brazil/"+st+"/meta.json").then(data => {
							app.data.Brazil.innerserie[st] = data.data.json()
							bootloader.ready(st);
						})
					});
					bootloader.ready("statesarray");
				}
				else app.error("Error loading states's timeseries...");
			});

		// LOGIN
		}else location.herf = "/login";

		bootloader.ready("theme")
	})
})

app.onPragmaChange.add(x => {

	if(x === true) return;

	app.last = app.current;
	app.current = x;

	let
	container = $("#home")[0];

	container.get(".--confirmed")[0].text(app.n2s(x.c.last()));
	container.get(".--deaths")[0].text(app.n2s(x.d.last()));

	console.log(x)

});

// __scroll = new Swipe(app.body);
// __scroll.up(()=>{});
// __scroll.down(()=>{});
// __scroll.right(()=>{ });
// __scroll.left(()=>{ });
// __scroll.fire();