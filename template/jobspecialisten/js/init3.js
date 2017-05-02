var b = {
	init: function(){
		this.teamworkdiagram();
	},
	random: function(l, u){
		return Math.floor((Math.random()*(u-l+1))+l);
	},
	teamworkdiagram: function(){
		var r = Raphael('teamworkdiagram', 470, 470),
			rad = 63,
			defaultText = 'Teamwork',
			speed = 250;
		
		r.circle(250, 250, 75).attr({ stroke: 'none', fill: '#00baf2' });
		
		var title = r.text(250, 250, defaultText).attr({
			font: '17px TitilliumWeb Light',
			fill: '#fff'
		}).toFront();
		
		r.customAttributes.arc = function(value, color, rad){
			var v = 3.6*value,
				alpha = v == 360 ? 359.99 : v,
				random = o.random(100, 10),
				a = (random-alpha) * Math.PI/180,
				b = random * Math.PI/180,
				sx = 250 + rad * Math.cos(b),
				sy = 250 - rad * Math.sin(b),
				x = 250 + rad * Math.cos(a),
				y = 250 - rad * Math.sin(a),
				path = [['M', sx, sy], ['A', rad, rad, 0, +(alpha > 180), 1, x, y]];
			return { path: path, stroke: color }
		}
		
		$('.gettea').find('.arc3').each(function(i){
			var t = $(this), 
				color = t.find('.color').val(),
				value = t.find('.percent').val(),
				text = t.find('.text').text();
			
			rad += 30;	
			var z = r.path().attr({ arc: [value, color, rad], 'stroke-width': 26 });
			
			z.mouseover(function(){
                this.animate({ 'stroke-width': 50, opacity: .75 }, 1000, 'elastic');
                if(Raphael.type != 'VML') //solves IE problem
				this.toFront();
				title.stop().animate({ opacity: 0 }, speed, '>', function(){
					this.attr({ text: text + '\n' + value + '%' }).animate({ opacity: 1 }, speed, '<');
				});
            }).mouseout(function(){
				this.stop().animate({ 'stroke-width': 26, opacity: 1 }, speed*4, 'elastic');
				title.stop().animate({ opacity: 0 }, speed, '>', function(){
					title.attr({ text: defaultText }).animate({ opacity: 1 }, speed, '<');
				});	
            });
		});
		
	}
}
$(function(){ b.init(); });
