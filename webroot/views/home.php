<!--
	HOME
 -->

<?php
$files = IO::scan("img/backgrounds");
$bg = $files[random_int(0,sizeof($files)-1)];
?>

<div id="home" class="-fixed -view -no-scrolls" style="
 	color:white;
 	background-image:url('img/backgrounds/<?=$bg?>');
 	background-size:cover;
 	background-attachment: fixed;
 	background-position: center center ;
 	padding:1.5em;
 	opacity:0;">
 	<div class="-wrapper -left -no-scrolls" style="background:inherit;border-radius: .5em;box-shadow: 0 0 .5em #000000AA">
 		<blur></blur>

 		<!-- MENU -->
 		<div id='menu' class="-left -zero -bar -col-2 -content-center" style="background:#000000AA">
			<img class="-row -inverted" style="max-height:2.5em;padding:.5em;margin-top:1em" src="img/faau-covid.svg"/>
			<div class="-row" style="padding:1em 1.5em 2em;">
				<input type="text" name="seek_a_state" class="-col-10 -left -roboto" style="font-size:1em;padding:.45em 1em;border-radius:.25em 0 0 .25em;color:white;background:#ffffff22;border:none">
				<div class="-left -col-2 -content-center -pointer --seekbutton" style="background:#ffffff44;;border-radius: 0 .25em .25em 0">
					<img src="img/icons/search.svg" class="-inverted" style="padding:.5em;height:2em"/>
				</div>
			</div>
			<div class="-row -content-center --th" style="background:rgba(0,0,0,.16);padding:0 .5em;color:#ffffffAA">
				<div class="-col-2 -pointer -left -ellipsis -no-scrolls" data-sort="name" style="padding:.5em 0" onclick="
					$('#home .--statelist')[0].children.array().sort(function(x,y){ 
						return x.dataset.state > y.dataset.state ? 1 : -1
					}).each(s=>s.raise())
				"><div class="-wrapper">UF</div></div>
				<div class="-col-4 -pointer -left -ellipsis -no-scrolls" data-sort="name" style="padding:.5em 0" onclick="
					$('#home .--statelist')[0].children.array().sort(function(x,y){ 
						return x.dataset.name > y.dataset.name ? 1 : -1
					}).each(s=>s.raise())
				"><div class="-wrapper">CIDADE</div></div>
				<div class="-col-3 -pointer -left -ellipsis -no-scrolls" data-sort="deaths" style="padding:.5em 0;"
					onclick="$('#home .--statelist')[0].sort_by_dataset(this.dataset.sort, 'desc')"><div class="-wrapper">MORTES</div></div>
				<div class="-col-3 -pointer -left -ellipsis -no-scrolls" data-sort="confirmed" style="padding:.5em 0"
					onclick="$('#home .--statelist')[0].sort_by_dataset(this.dataset.sort, 'desc')"><div class="-wrapper">CASOS</div></div>
			</div>
			<nav class="-row -scrolls --statelist" style="padding:.5em; height:calc(100% - 12em);border-top:1px solid #ffffff44;background-image:linear-gradient(to top right, rgba(0,0,0,.32), transparent, transparent)"></nav>
 		</div>

 		<!-- CONTAINER -->
 		<div id='container' class="-left -zero -bar -col-10" style="background:#ffffffDD">

 			<!-- RIGHT BAR -->
 			<nav class="-right -bar -col-2 -content-center" style="padding:1em">
 				<div class="-absolute -zero -bar" style="width:1px;background-image: linear-gradient(to bottom, transparent, #00000044, transparent)"></div>
 				<span class="-row" style="font-size: 2em;padding:.5em;color:#00000088">TOTAIS</span>
 				<div class="-row" style="color:#00000088; padding:.5em">INFECTADOS</div>
 				<div class="-row --confirmed" data-num='1' style="color:#000000DD;font-size: 2em"></div>
 				<div class="-row" style="color:#00000088; padding:.5em;margin-top:1em">MORTES</div>
 				<div class="-row --deaths"  data-num='1' style="color:#000000DD;font-size: 2em"></div>
 			</nav>

 			<!-- HOME -->
 			<div class="-absolute -zero -bar -col-10 --home -scrolls" style="color:#000000AA">
 				<!-- HEADER -->
 				<header class="-row" style="height:3em;padding:1.5em;">
 						<span class="-left -pointer" style="font-size: 2em" onclick="window.app.pragma = true">BRASIL</span>
 						<span class="-left --cityname" style="text-transform: uppercase;font-size: 2em;;font-weight:lighter"></span>
 				</header>

 				<!-- SIR -->
				<div class="-row -content-left" style="padding:1.5em">
					<span>S.I.R CUSTOMIZADO (PREDIÇÃO C/ I.A)</span>
					<div class="-absolute -col-4 -zero-bottom" style="height:1px;background-image: linear-gradient(to right, #00000044, transparent)"></div>
				</div>
 				<div class="-row">
 					<!-- ACUMULATED CASES -->
 					<div class="-row -left" style="padding: 2em 1em 2em;height:24em">
 						<span class="-right" style="padding:0 1em">SÉRIE TOTAL</span>
 						<div class="-wrapper --home-sir-graph" style="margin:1em 0"></div>
 					</div>
 				</div>

 				<!-- CONFIRMED -->
 				<div class="-left -col-6">
					<div class="-row -content-left" style="padding:1.5em;margin-top:2em">
						<span>CASOS CONFIRMADOS (ÚLTIMOS 60 DIAS)</span>
						<div class="-absolute -col-4 -zero-bottom" style="height:1px;background-image: linear-gradient(to right, #00000044, transparent)"></div>
					</div>
	 				<div class="-row">
	 					<!-- ACUMULATED CASES -->
	 					<div class="-row -left" style="padding: 2em 1em 2em;height:16em">
	 						<span class="-right" style="padding:0 1em">ACUMULADOS</span>
	 						<div class="-wrapper --home-accumulated-infected-graph" style="margin:1em 0"></div>
	 					</div>
	 					<!-- DAILY CASES -->
	 					<div class="-row -left" style="padding: 2em 2em 2em 1em;height:8em">
	 						<span class="-right" style="padding:0 1em">DIÀRIOS</span>
	 						<div class="-wrapper --home-daily-infected-graph" style="margin:1em 0"></div>
	 					</div>
	 				</div>
	 			</div>

 				<!-- DEATHS-->
 				<div class="-left -col-6">
	 				<div class="-row -content-left" style="padding:1.5em;margin-top:2em">
						<span>MORTES (ÚLTIMOS 60 DIAS)</span>
						<div class="-absolute -col-4 -zero-bottom" style="height:1px;background-image: linear-gradient(to right, #00000044, transparent)"></div>
					</div>
	 				<div class="-row">
	 					<!-- ACUMULATED CASES -->
	 					<div class="-row -left" style="padding: 2em 1em 2em;height:16em">
	 						<span class="-right" style="padding:0 1em">ACUMULADOS</span>
	 						<div class="-wrapper --home-accumulated-deaths-graph" style="margin:1em 0"></div>
	 					</div>
	 					<!-- DAILY CASES -->
	 					<div class="-row -left" style="padding: 2em 2em 2em 1em;height:8em">
	 						<span class="-right" style="padding:0 1em">DIÀRIOS</span>
	 						<div class="-wrapper --home-daily-deaths-graph" style="margin:1em 0"></div>
	 					</div>
	 				</div>
	 			</div>
 				<div class="-row" style="height:24em"></div>
 			</div>
 		</div>
 	</div>
 </div>
 <script>

 	bootloader.onFinishLoading.add(_ => $("#home")[0].appear())

 </script>