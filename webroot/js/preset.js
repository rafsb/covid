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
	, helpers    : 0
	, theme 	 : 0
	, worldjson  : 0
	, statesarray: 0
	, menujs 	 : 0
	, sir:0
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
			
			app.load("/webroot/views/xhr/splash.php");
			app.exec("/webroot/js/helpers.js");

			app.call("/var/Brazil/total.json").then(world => {				
				app.data.Brazil.serie = world.data.json();
				if(app.data.Brazil.serie) bootloader.ready("worldjson");
				else app.error("Error loading World's timeseries...");
			});

			app.call("/content/states/Brazil").then(states => {
				states = states.data.json();				
				if(states.length){
					states.each(s => bootloader.loaders[s] = false);
					app.data.Brazil.innerserie = {};
					states.each(st => {
						bootloader.loaders[st] = 0;
						app.call("var/Brazil/"+st+"/meta.json").then(data => {
							app.data.Brazil.innerserie[st] = data.data.json()
							bootloader.ready(st);
						})
						bootloader.ready("statesarray");
					});
				}
				else app.error("Error loading states's timeseries...");

				app.call("/webroot/js/menu.js").then(_ => {
					bootloader.onFinishLoading.add(__load_menu_countries_series__);
					bootloader.ready("menujs")
				});

				app.call("var/Brazil/csir.json").then(x => {
					app.data.Brazil.csir = x.data.json()
					bootloader.ready("sir")
				})

			});

		// LOGIN
		}else location.herf = "/login";

		bootloader.ready("theme")
	})
})

app.onPragmaChange.add(x => {

	if(!bootloader.ready()) return;//setTimeout(x => app.pragma = x, ANIMATION_LENGTH, x);

	app.last = app.current;
	app.current = x;

	let
	container = $("#home")[0]
	, conf = container.get(".--confirmed")[0]
	, deat = container.get(".--deaths")[0]
	, qtty = 60
	, sir_color = app.colors("MIDNIGHT_BLUE") 
	, infect_color = app.colors("PETER_RIVER") 
	, death_color = app.colors("ALIZARIN")
	;

	$("#home .--cityname")[0].html(x===true ? "" : "<div class='-left' style='padding:0 .5em;transform:translateY(-.05em)'>|</div>"+x.name)
	
	clearInterval(app.dx);
	clearInterval(app.cx);

	let
	confirmed_infected = x.c  || []
	, confirmed_deaths   = x.d  || []
	, daily_infected     = x.dc || []
	, daily_deaths       = x.dd || []
	, labels  = app.data.Brazil.serie.keys().extract(x => x.split("-").slice(1).join("/")).last(qtty)
	, sir
	;
	
	if(x === true) {
		$('#home .--staterow').data({ selected:0 }).css({ background:'transparent', color:"@WHITEAA" });

		app.data.Brazil.serie.each(x => {
			labels.push(x.key.split("-").slice(1).join("/"));
			confirmed_infected.push(x.content.c);
			confirmed_deaths.push(x.content.d);
			daily_infected.push(x.content.dc);
			daily_deaths.push(x.content.dd);
		});

		//sir_k = [ "SIR" ] 
		sir = app.data.Brazil.csir || {};

	} else {
		sir = app.data.Brazil.innerserie[x.state][x.name].csir || {};
	}
	
	confirmed_infected = confirmed_infected.cast(NUMBER).last(qtty);
	confirmed_deaths   = confirmed_deaths.cast(NUMBER).last(qtty);
	daily_infected     = daily_infected.cast(NUMBER).last(qtty);
	daily_deaths       = daily_deaths.cast(NUMBER).last(qtty);

	let
	acc_conf_graph = new Graph({
		target: $("#home .--home-accumulated-infected-graph").at().empty()
		, series: [ confirmed_infected ]
		, labels: labels
		, names: [ "BRASIL" ]
		, lines: { css: { color: infect_color } }
		, type: "smooth"
	})
	, day_conf_graph = new Graph({
		target: $("#home .--home-daily-infected-graph").at().empty()
		, series: [ daily_infected ]
		, labels: labels
		, names: [ "BRASIL" ]
		, lines: { css: { color: infect_color } }
		, type: "bars"
	})
	, acc_deat_graph = new Graph({
		target: $("#home .--home-accumulated-deaths-graph").at().empty()
		, series: [ confirmed_deaths ]
		, labels: labels
		, names: [ "BRASIL" ]
		, lines: { css: { color: death_color } }
		, type: "smooth"
	})
	, day_deat_graph = new Graph({
		target: $("#home .--home-daily-deaths-graph").at().empty()
		, series: [ daily_deaths ]
		, labels: labels
		, names: [ "BRASIL" ]
		, lines: { css: { color: death_color } }
		, type: "bars"
	})
	; 

	let
	csir_graph = new Graph({
		target: $("#home .--home-sir-accumulated-graph").at().empty()
		, series: [ sir.susceptible, sir.infected, sir.recovered, sir.deaths ]
		, names:  [ "não infectados", "infectados", "recuperados", "mortes" ]
		, lines: { css: { 
			color: [ "#888", "#D3531388", "#2C97DD88", "#f008" ] 
			, "stroke-width": [ 2, 6, 2, 2 ]
			, "stroke-dasharray" : [ 1, 0, 4, 4 ]
		} }
		, labels: app.iter(100)
		, type: "line"
	})
	, dsir_graph = new Graph({
		target: $("#home .--home-sir-deaths-graph").at().empty()
		, series: [ sir.daily_deaths ]
		, names: [ "mortes diárias" ]
		, labels: app.iter(100)
		, lines: { css: { color: "#f00D" } }
		, type: "bars"
	})
	, dcsir_graph = new Graph({
		target: $("#home .--home-sir-daily-infected-graph").at().empty()
		, series: [ sir.daily_infected ]
		, names: [ "infectados diários" ]
		, labels: app.iter(100)
		, lines: { css: { color: "#D35313" } }
		, type: "bars"
	})
	;

	let
	deathpeak = sir.daily_deaths.indexOf(sir.daily_deaths.calc(MAX))
	, infectpeak = sir.daily_infected.indexOf(sir.daily_infected.calc(MAX))
	, dpl = csir_graph.node.get(".--iter"+deathpeak)[0].mimic().remClass("-hint-plate").attr({ width: 1 }).css({ fill: "#f00D", opacity:.64 })
	, ipl = csir_graph.node.get(".--iter"+infectpeak)[0].mimic().remClass("-hint-plate").attr({ width: 1 }).css({ fill: "#D35313", opacity:.64 })
	;

	csir_graph.node.app(dpl).app(ipl)
	.app(_S("text", "--text", { x: dpl.getAttribute("x")*1+12, y: 12 }, { stroke: "#f00D" }).text("PICO DE MORTES"))
	.app(_S("text", "--text", { x: ipl.getAttribute("x")*1+12, y: 12 }, { fill: "#D35313" }).text("PICO DE CASOS DIÁRIOS"));

	app.cx = setInterval(xc => { 
			
		let
		n = container.get(".--confirmed")[0].dataset.num*1
		;

		if(n==xc) return clearInterval(app.cx);
		
		if(n > xc) n = n-1000 > xc ? n-1000 : (n-100 > xc ? n-100 : (n-10 > xc ? n-10 : n-1));
		if(n < xc) n = n+1000 < xc ? n+1000 : (n+100 < xc ? n+100 : (n+10 < xc ? n+10 : n+1));

		// console.log(n, (n).nerdify())

		container.get(".--confirmed")[0].dataset.num = n;
		container.get(".--confirmed")[0].text(n.nerdify());

	}, 10, confirmed_infected.last())
	
	app.dx = setInterval(xd => { 
		
		let
		n = container.get(".--deaths")[0].dataset.num*1
		;

		if(n==xd) return clearInterval(app.dx);
		
		if(n > xd) n = n-1000 > xd ? n-1000 : (n-100 > xd ? n-100 : (n-10 > xd ? n-10 : n-1));
		if(n < xd) n = n+1000 < xd ? n+1000 : (n+100 < xd ? n+100 : (n+10 < xd ? n+10 : n+1));

		container.get(".--deaths")[0].dataset.num = n;
		container.get(".--deaths")[0].text(n.nerdify());

	}, 10, confirmed_deaths.last())	

});

// __scroll = new Swipe(app.body);
// __scroll.up(()=>{});
// __scroll.down(()=>{});
// __scroll.right(()=>{ });
// __scroll.left(()=>{ });
// __scroll.fire();