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
 	opacity:0;
 	display:none;">
 	<div class="-wrapper -left -no-scrolls" style="background:inherit;border-radius: .5em;box-shadow: 0 0 .5em #000000AA">
 		<blur></blur>
 		<!-- MENU -->
 		<div class="-left -zero -bar -col-2 -content-center" style="background:#000000AA">
 			<div class="-inverted" style="font-size: 1.5em;color:black;padding:1em 0 .5em;">
				<img class="-left" style="height:1.5em" src="img/icons/faau-square.svg">
				<div class="-left" style="padding:.125em 0;"><f style="font-weight:lighter;margin-right:.25em">FAAU | <b style="top:-2px">COVID-19</b></f></div>
			</div>
			<div class="-row" style="padding:1em 1.5em 2em;">
				<input type="text" name="seek_a_state" class="-col-10 -left -roboto" style="font-size:1em;padding:.45em 1em;border-radius:.25em 0 0 .25em;color:white;background:#ffffff22;border:none">
				<div class="-left -col-2 -content-center -pointer" style="background:#ffffff44;;border-radius: 0 .25em .25em 0">
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
 		<div class="-left -zero -bar -col-10" style="background:#ffffffDD">

 			<nav class="-right -bar -col-3 -content-right" style="padding:1em">
 				<div class="-absolute -zero -bar" style="width:1px;background-image: linear-gradient(to bottom, transparent, #00000044, transparent)"></div>
 				<div class="-row" style="color:#00000088; padding:1em .5em">CASOS ACUMULADOS</div>
 				<div class="-row --confirmed" style="color:#000000DD;font-size: 2em"></div>
 				<div class="-row" style="color:#00000088; padding:1em .5em">MORTES</div>
 				<div class="-row --deaths" style="color:#000000DD;font-size: 2em"></div>
 			</nav>

 			<div class="-right -bar -col-9 -content-left">
 				
 			</div>
 			
 		</div>
 	</div>
 </div>
 <script>
 	app.exec("webroot/js/menu.js").then(nil => bootloader.ready("menujs"));
 	bootloader.onFinishLoading.add(nil => $("#home")[0].appear())
 </script>