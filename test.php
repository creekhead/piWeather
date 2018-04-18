<!doctype HTML>
<html>
	<head>
		<style>
			body{
				background-color: black;
			}
		</style>
	</head>
	<body>
		<script src="js/moonphase.js"></script>

		<div id="moonPhase" class="moonPhase" style="">moon</div>

		<script>
			drawPlanetPhase(document.getElementById('moonPhase'), 0.1, true, {diameter:50, earthshine:0.1, blur:10, lightColour: '#9bf'})
		</script>
	</body>
</html>