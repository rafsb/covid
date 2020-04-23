
var
ran = false
;
__load_menu_countries_series__ = function(iter=10){
	if(!iter) return app.error("Error! loading countries content series into main menu...");
	if(!app.data.world||!app.data.world.inner_serie) setTimeout(__load_menu_countries_series__, ANIMATION_LENGTH, --iter);
	if(ran) return;
	ran = true;
	
	// #home .--countrylist
	let
	container = $("#home .--countrylist")[0]
	, uppervalue = app.data.world.inner_serie.extract(function(){ return this.confirmeds.last() }).calc(MAX)
	;

	app.data.world.inner_serie.each(country => {
		let
		name = country.name
		, confirmeds = country.confirmeds.last() || 0
		, deaths = country.deaths.last() || 0
		, row = _("div", "-row -pointer --countryrow", { 
			padding:".25em 1.5em"
			, borderRadius: ".25em"
			, borderBottom:"1px solid #ffffff22"
		}).data({
			name: name
			, confirmeds: confirmeds
			, deaths: deaths
			, serie: country.confirmeds.join(":")
		}).app(
			_("div", "-left -col-6 -ellipsis -content-left").text(name)
		).app(
			_("div", "-left -col-3 -content-right").text(app.n2s(deaths,1))
		).app(
			_("div", "-left -col-3 -content-right").text(app.n2s(confirmeds,1))
		).on("mouseenter", function(){
			
			$(".--countryrow").not(this).css({ background:"transparent" })
			this.css({ background:app.colors().LIGHT1 })
			
			let
			draw = new Graph({
				target: $("#home section.--graph")[0]
				, series: [ this.dataset.serie.split(":") ]
				, noguides: true
				, noxlabels:true
				, yaxis: [ 0, uppervalue/2, uppervalue ]
				, lines: {
					type: "wave"
					, heads : [ "#@WHITE 3 @BLACK 1" ]
				}
				, log:true
			})

			draw.draw()

			tooltips()
		});
		
		container.app(row)
	})

	$("#home .--th > div").each(header => {
		header.on("click", function(){
			$("#home .--th > div").not(this).stop().anime({ background:"none" });
			this.anime({ background:"#ffffff22" })
		})
	}).last().dispatchEvent(( new Event("click") ))
}

bootloader.onFinishLoading.add(nil => __load_menu_countries_series__(10));
bootloader.ready("helpers")