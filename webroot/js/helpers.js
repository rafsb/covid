
var
ran = false
;
__load_countries_series__ = function(iter=10){
	if(!iter) return app.error("Error! loading countries content series into main menu...");
	if(!app.data.world) setTimeout(__load_countries_series__, ANIMATION_LENGTH, --iter);
	if(ran) return;
	ran = true;
	
	// #home .--countrylist
	let
	container = $("#home .--countrylist")[0]
	;

	app.data.world.inner_serie.each(country => {
		let
		name = country.name
		, confirmeds = country.confirmeds.last() || 0
		, deaths = country.deaths.last() || 0
		;
		container.app(
			_("div", "-row -pointer --countryrow", { 
				padding:".25em 1.5em"
				, borderRadius: ".25em"
				, borderBottom:"1px solid #ffffff22"
			}).data({
				name: name
				, confirmeds: confirmeds
				, deaths: deaths
			}).app(
				_("div", "-left -col-6 -content-left").text(name)
			).app(
				_("div", "-left -col-3 -content-right").text(app.n2s(deaths,1))
			).app(
				_("div", "-left -col-3 -content-right").text(app.n2s(confirmeds,1))
			)
		)
	})

	$("#home .--th > div").each(header => {
		header.on("click", function(){
			$("#home .--th > div").not(this).stop().anime({ background:"none" });
			this.anime({ background:"#ffffff22" })
		})
	}).last().dispatchEvent((new Event("click")))
}

bootloader.onFinishLoading.add(nil => __load_countries_series__(10));
bootloader.ready("helpers")