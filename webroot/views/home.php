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
 		<div class="-left -zero -bar -col-3 -content-center" style="background:#000000AA"> 			
 			<div class="-inverted" style="font-size: 2em;color:black;padding:.5em 0;">
				<img class="-left" style="height:1.5em" src="img/icons/faau-square.svg">
				<div class="-left" style="padding:.125em 0;"><f style="font-weight:lighter;margin-right:.25em">FAAU | </f><b>COVID-19</b></div>
			</div>
			<div class="-row" style="padding:2em;">
				<input type="text" name="seek_a_country" class="-col-10 -left -roboto" style="font-size:1em;padding:.45em 1em;border-radius:.25em 0 0 .25em;color:white;background:#ffffff22;border:none">
				<div class="-left -col-2 -content-center -pointer" style="background:#ffffff44;;border-radius: 0 .25em .25em 0">
					<img src="img/icons/search.svg" class="-inverted" style="padding:.5em;height:2em"/>
				</div>
			</div>
			<div class="-row -content-center --th" style="background:rgba(0,0,0,.16);padding:0 .5em">
				<div class="-col-6 -pointer -left -ellipsis -no-scrolls" data-sort="name" style="padding:.5em 0"
					onclick="
					$('#home .--countrylist')[0].children.array().sort(function(x,y){ 
						return x.dataset.name > y.dataset.name ? 1 : -1
					}).each(s=>s.raise())
				"><div class="-wrapper">COUNTRY</div></div>
				<div class="-col-3 -pointer -left -ellipsis -no-scrolls" data-sort="deaths" style="padding:.5em 0;"
					onclick="$('#home .--countrylist')[0].sort_by_dataset(this.dataset.sort, 'desc')"><div class="-wrapper">DEATHS</div></div>
				<div class="-col-3 -pointer -left -ellipsis -no-scrolls" data-sort="confirmeds" style="padding:.5em 0"
					onclick="$('#home .--countrylist')[0].sort_by_dataset(this.dataset.sort, 'desc')"><div class="-wrapper">CONFIRMED</div></div>
			</div>
			<nav class="-row -scrolls --countrylist" style="padding:.5em; height:calc(100% - 13em);border-top:1px solid #ffffff44;"></nav>
 		</div>

 		<!-- CONTAINER -->
 		<div class="-left -zero -bar -col-9" style="background:#ffffffDD">
 			<div class="-row" style="height:50%">
 				<section class="--graph -left -col-8 -bar -no-scrolls" style="background: #ffffff22"></section>
 				<div class="-left -col-4 -bar -no-scrolls">
 					<section class="--graph -row -no-scrolls" style="height:50%;background: #AAAAAA22"></section>
 					<section class="--graph -row -no-scrolls" style="height:50%;background: #ffffff22"></section>
 				</div>
 			</div>
 			<section class="--graph -row -no-scrolls" style="height:50%;background: #AAAAAA22"></section>
 		</div>
 	</div>
 </div>

 <script type="text/javascript">
 	bootloader.onFinishLoading.add(nil => $("#home")[0].appear())	
 </script>