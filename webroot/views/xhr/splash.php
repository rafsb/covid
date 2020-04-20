<template>
	<section id="splash" class="-fixed -view" style="background: @BACKGROUND;filter:invert(1)">
		<!-- SPLASH -->
		<div class="-absolute -centered" style="font-size: 6em;color:@FONT;">
			<img class="-left" style="height:1.5em" src="img/icons/faau-square.svg">
			<div class="-left" style="padding:.125em 0;"><f style="font-weight:lighter;margin-right:.25em">FAAU | </f><b>COVID-19</b></div>
		</div>
		<!-- PROGRESS BAR -->
		<footer class="-absolute -row -zero-bottom -content-left">
			<div class="-col-1 -left --progressbar" style="height:1em;background:@FONT"></div>
		</footer>
		<script type="text/javascript">
			bootloader.onReadyStateChange.add(perc => {
				$("#splash")[0].anime({ filter:"invert("+(1-perc/100)+")" })
				$("#splash .--progressbar")[0].anime({ width: perc+"%" })

			})
			bootloader.onFinishLoading.add(nil => $("#splash")[0].anime({ filter:"invert(0)" }).then(el => el.desappear(ANIMATION_LENGTH, true)))
			bootloader.ready("splash")
		</script>
	</section>
</template>