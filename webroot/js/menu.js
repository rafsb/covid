window.ran = false;
var
key_seek = new Throttle(e => {
	if(e) {
		$("#home #menu .--statelist ")[0].children.array().each(el => {
			if(!(el.get("div")[1].text().toLowerCase().indexOf(e)+1)) el.desappear();
			else el.appear();
		})
	} else $("#home #menu .--statelist ")[0].children.array().appear()
}, 200)
, __load_menu_countries_series__ = function(){
	if(ran) return;
	ran = true;	
	
	// #home .--statelist
	let
	container = $("#home .--statelist")[0]
	;

	app.data.Brazil.innerserie.each(state => {

		state.content.each(city => {

			if(city.key.indexOf("LOCALIZ")+1) return;

			let
			name = city.key
			, st = state.key
			, c = city.content.series.array().extract(function(){ return this.c })
			, d = city.content.series.array().extract(function(){ return this.d })
			, dc = city.content.series.array().extract(function(){ return this.dc })
			, dd = city.content.series.array().extract(function(){ return this.dd })
			, confirmed = c.calc(MAX)
			, deaths = d.calc(MAX)
			;

			if(confirmed<100) return;

			let
			row = _("div", "-row -pointer --staterow", { 
				padding:".25em .5em"
				, borderRadius: ".25em"
				, borderBottom:"1px solid @WHITE"
				, color: "@WHITE"
				, background: "red"
				, cursor: "pointer"
				, fontSize:".75em"
			}).data({
				name: name
				, state: st
				, confirmed: confirmed
				, deaths: deaths
				, cserie: c.join(":")
				, dserie: d.join(":")
				, dcserie: dc.join(":")
				, ddserie: dd.join(":")
			}).app(
				_("div", "-left -col-2 -content-center", { opacity:.8 }).text(st)
			).app(
				_("div", "-left -col-4 -ellipsis -content-left").text(name.split('/')[0])
			).app(
				_("div", "-left -col-3 -content-right", { opacity:.6 }).text(app.n2s(deaths,1))
			).app(
				_("div", "-left -col-3 -content-right", { opacity:.8 }).text(app.n2s(confirmed,1))
			).on("mouseleave", function(){
				this.css({ filter: "brightness(1)" })
			}).on("mouseenter", function(){
				this.css({ filter: "brightness(1.2)" })
			}).on("mouseenter", function(){
				// console.log($('#home .--staterow').not(this).css({ background:'transparent', color:"@WHITEAA" }));
				app.pragma = {
					name: this.dataset.name
					, state: this.dataset.state
					, c: this.dataset.cserie.split(':')
					, d: this.dataset.dserie.split(':')
					, nc: this.dataset.confirmed
					, nd: this.dataset.deaths
					, dc: this.dataset.dcserie.split(':')
					, dd: this.dataset.ddserie.split(':')
				}
				$('#home .--staterow').not(this).css({ background:'transparent', color:"#FFF" });
				this.css({ background: "#FFF", color:"#000" })

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

	$("#home #menu input")[0].on("keyup", function(key){ 
		if((key.which || key.keyCode) == 13) key_seek.fire(this.value.toLowerCase());
	})
	$("#home #menu .--seekbutton")[0].on("click", function(key){ 
		let
		inp = $("#home #menu input")[0]
		;
		key_seek.fire(inp.value.toLowerCase());
		inp.value = ''
	})

	// $('#home .--staterow').first().dispatchEvent(new Event("click"));
}
;
bootloader.ready("helpers")