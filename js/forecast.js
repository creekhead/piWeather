$( document ).ready(function() {

    $('.weather_minmax').each(function(key, value) {
        $this = $(this)
        var split = $this.html().split("/")
        if( split.length == 2 ){
            $this.html('<span class="top">'+split[0]+'</span><span class="bottom">'+split[1]+'</span>')
        }
    });

    //color MAIN temp
    var temp=100-parseFloat($('.weatherTemp').html());
    $('.weatherTemp').css('color',getGreenToRed(temp));

    //color HIGH temp

    $.each($('.top'), function( index, value ) {
  		var tempMin=parseInt($(value).html())-25;
  		$(value).css('color',getGreenToRed(tempMin))
	});

	$.each($('.bottom'), function( index, value ) {
  		var tempMax=parseInt($(value).html())-25;;
  		$(value).css('color',getGreenToRed(tempMax))
	});

	//make deg smaller
    console.log("make deg smaller");
	$('.weather_minmax .top').each(function(key, value) {
    	$($('.weather_minmax .top')[key]).html(parseInt($($('.weather_minmax .top')[key]).html())+'<b class="degSmaller">°F</b>')
    });
    $('.weather_minmax .bottom').each(function(key, value) {
    	$($('.weather_minmax .bottom')[key]).html(parseInt($($('.weather_minmax .bottom')[key]).html())+'<b class="degSmaller">°F</b>')
    });

    //moonPgase
    var percentageMoon=$(document.getElementById('moonPhase')).attr('percent')*.1;
    var percentIlluminated_js=$(document.getElementById('moonPhase')).attr('percentIlluminated_js');
    console.log("percentageMoon: "+percentageMoon);
    var waxwan=parseInt($(document.getElementById('moonPhase')).attr('phase'));
    drawPlanetPhase(document.getElementById('moonPhase'), percentIlluminated_js, waxwan, {diameter:50, earthshine:0.1, blur:5, lightColour: '#9bf'});
    console.log('MoonPhase: PercentageIlluminated: '+$(document.getElementById('moonPhase')).attr('percent')+' / '+percentIlluminated_js+' WaxWan: '+waxwan);


    //WIND
    var windDegrees=parseInt(jQuery('#windGustCanvas').attr('deg'));
    console.log("windDegrees: "+windDegrees);
    compass = new Compass("windGustCanvas");
    compass.animate(windDegrees); // Set a default value

});



function getGreenToRed(percent){
    r = percent<50 ? 255 : Math.floor(255-(percent*2-100)*255/100);
    g = percent>50 ? 255 : Math.floor((percent*2)*255/100);
    return 'rgb('+r+','+g+',0)';
}



	/********************************************************
	*                    BAROMOTER                       *
	********************************************************/
     $(document).ready(function() {

        setTimeout(function(){
            baromoterDraw();
        }, 50);


     });


    function baromoterimage(ctx, idname, x, y)
    {
        var img = document.getElementById(idname);
        ctx.drawImage(img, x, y);
    }

    function baromoterhand(ctx, value)
    {
        ctx.save();
        ctx.rotate(value);
        baromoterimage(ctx, 'hand', -24.4, -4.4);
        ctx.restore();
    }

    function baromotericon(ctx, value)
    {
        if (value < 1000)
        {
            baromoterimage(ctx, 'rain', -16.4, 0);
        }
        else if (value < 1030)
        {
            baromoterimage(ctx, 'cloudy', -22, 15);
        }
        else
        {
            baromoterimage(ctx, 'sun', -16.4, 0);
        }
    }

    function baromoterDraw()
    {
    	if($('#barometr').length!=1) return;

        var canvas = document.getElementById('barometr');
        var pressure = $(canvas).attr('pressure');
        if (canvas.getContext)
        {
            angle = Math.PI * 2 * (0.005 * pressure - 4.207);
            var ctx = canvas.getContext('2d');
            ctx.save();
            ctx.translate(48, 48);
            baromoterimage(ctx, 'face', -48, -48);
            baromotericon(ctx, pressure);
            //shadow(ctx, angle);
            baromoterhand(ctx, angle);
            ctx.restore();
        }
    }


