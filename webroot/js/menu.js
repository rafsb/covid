
var
ran = false
;
__load_menu_countries_series__ = function(iter=10){
	if(ran) return;
	if(!iter) return app.error("Error! loading countries content series into main menu...");
	if(!app.data.Brazil||!app.data.Brazil.innerserie||!app.data.Brazil.innerserie.keys().length) setTimeout(__load_menu_countries_series__, ANIMATION_LENGTH, --iter);
	ran = true;	
	
	// #home .--statelist
	let
	container = $("#home .--statelist")[0]
	;

	app.data.Brazil.innerserie.each(state => {

		state.content.each(city => {

			if(city.key.indexOf("CASO SEM")+1) return;

			// console.log(city.content.series.array());
			
			let
			name = city.key
			, st = state.key
			, c = city.content.series.array().extract(function(){ return this.c })
			, d = city.content.series.array().extract(function(){ return this.d })
			, confirmed = c.calc(MAX)
			, deaths = d.calc(MAX)
			;

			let
			row = _("div", "-row -pointer --staterow", { 
				padding:".25em .5em"
				, borderRadius: ".25em"
				, borderBottom:"1px solid @LIGHT2"
				, display: confirmed < 100 ? "none" : "inline-block"
			}).data({
				name: name
				, state: st
				, confirmed: confirmed
				, deaths: deaths
				, cserie: c.join(":")
				, dserie: d.join(":")
			}).app(
				_("div", "-left -col-2 -content-center").text(st)
			).app(
				_("div", "-left -col-4 -ellipsis -content-left", { color:"@WHITE" }).text(name.split('/')[0])
			).app(
				_("div", "-left -col-3 -content-right", { color: "@WHITEAA" }).text(app.n2s(deaths,1))
			).app(
				_("div", "-left -col-3 -content-right", { color: "@WHITE" }).text(app.n2s(confirmed,1))
			).on("mouseleave", function(){
				if(this.dataset.selected!='1') this.style.background = "transparent"
			}).on("mouseenter", function(){
				if(this.dataset.selected!='1') this.css({ background: "@LIGHT1" })
			}).on("click", function(){
				$('#home .--staterow').not(this).data({ selected:0 }).css({ background:'transparent', color:"@WHITEAA" });
				this.data({ selected: 1}).css({ background: "@LIGHT2", color:"@WHITE" })
				app.pragma = {
					name: this.dataset.name
					, c: this.dataset.cserie.split(':')
					, d: this.dataset.dserie.split(':')
					, nc: this.dataset.confirmed
					, nd: this.dataset.deaths
				}
			});

			container.app(row)
		})
	})

	$("#home .--th > div").each(header => {
		header.on("click", function(){
			$("#home .--th > div").not(this).stop().anime({ background:"none" });
			this.anime({ background:"@LIGHT3" })
		})
	}).last().dispatchEvent(( new Event("click") ));

	$('#home .--staterow').first().dispatchEvent(new Event("click"));
}
	
bootloader.onFinishLoading.add(nil => __load_menu_countries_series__(10));
bootloader.ready("helpers")