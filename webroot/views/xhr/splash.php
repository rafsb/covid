<template>
	<section id="splash" class="-fixed -view" style="background: @BACKGROUND;filter:invert(1)">
		<!-- SPLASH -->
		<div class="-absolute -centered" style="font-size: 6em;color:@FONT;">
			<img class="-left" style="height:1.5em" src="img/faau-covid.svg">
		</div>
		<!-- PROGRESS BAR -->
		<footer class="-absolute -row -zero-bottom -content-left">
			<div class="-col-1 -left --progressbar" style="height:1em;background:@FONT"></div>
		</footer>
	</section>
	<script type="text/javascript">
			bootloader.onReadyStateChange.add(perc => {
				$("#splash")[0].anime({ filter:"invert("+(1-perc).toFixed(1)+")" }, ANIMATION_LENGTH*4)
				$("#splash .--progressbar")[0].anime({ width: Math.ceil(perc*100)+"%" }, ANIMATION_LENGTH*4)
				bootloader.onReadyStateChange.debug()
			})
			bootloader.onFinishLoading.add(_ => $("#splash")[0].anime({ filter:"invert(0)" }, ANIMATION_LENGTH*2).then(el => el.desappear(ANIMATION_LENGTH*2, true)))
			bootloader.ready("splash")
		</script>
</template>