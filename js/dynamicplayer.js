// Dynamic Audio Player
// by Manolo Salsas

/*  Copyright 2014 Manolo Salsas  (email : manolez@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/		

(function( $ ) {
  $.fn.dynamicAudioPlayer = function(options) {
	    function supports_ogg_audio() {
			 var a = document.createElement('audio');
		 	return !!(a.canPlayType && a.canPlayType('audio/ogg; codecs="vorbis"').replace(/no/, ''));
		}
		
		function supports_mp3_audio() {
			var a = document.createElement('audio');
			 return !!(a.canPlayType && a.canPlayType('audio/mpeg;').replace(/no/, ''));
		}

		var supportOgg = supports_ogg_audio();
		var supportMp3 = supports_mp3_audio();
		var animateTimeout;
		
		String.prototype.trunc = String.prototype.trunc ||
			function(n){
			  return this.length>n ? this.substr(0,n-1)+'...' : this;
			};

		// Set default options
	  	var options = $.extend({
		  dynTotalWidth: 'Regular',
		  dynPosition: 'Fixed',
		  dynPlaylistVisible: 'false',
		  dynPlaylistHeight: '165',
		  dynAutoplayEnabled: 'false',
		  dynPlayerMarginFrom: 'top',	
		  dynPlayerMargin: '35',
		  dynPlayerHorMarginFrom: 'centered',	
		  dynPlayerHorMargin: '0'			  
		}, options);
		
     this.each(function() {
		 $("#dynamic-player-sidebar").show();
		 var dynThisPlayer = $(this);
		 var playerHtml = '';
		 var totalSongs = 0;
		 var dynPlaying = false;
		 var currentRow = false;
		 var currentTime = 0;
		 var currentVolume;
		 var PlayingIndex = 0;
		 var playingRow = false;
		 var playingOnRow = false;
		
		Object.size = function(obj) {
			var size = 0, key;
			for (key in obj) {
				if (obj.hasOwnProperty(key)) size++;
			}
			return size;
		};
		
		// Create global HTML5 audio object
		var htmlSound = new Audio();

		//Set player position 
		if(options.dynPlayerMarginFrom == "bottom") 
			$("#dynamic-player-sidebar").css({'bottom' : parseInt(options.dynPlayerMargin,10) + 90 + 'px'});
		else if(options.dynPlayerMarginFrom == "top") 
			$("#dynamic-player-sidebar").css({'top' : options.dynPlayerMargin + 'px'});
		if(options.dynPosition == "Absolute")
			$("#dynamic-player-sidebar").css({'position' : 'absolute'});
		else 
			$("#dynamic-player-sidebar").css({'position' : 'fixed'});
			
		var dynamicMargin = isNaN(parseInt(options.dynPlayerHorMargin, 10)) ? 0 : parseInt(options.dynPlayerHorMargin, 10)
		if(options.dynTotalWidth === "Large") dynamicMargin += 116;
		else dynamicMargin += 88;

		if(options.dynPlayerHorMarginFrom == "centered") 
			$("#dynamic-player-sidebar-inner").css({'margin' : "auto"});
		else if(options.dynPlayerHorMarginFrom == "right") 
			$("#dynamic-player-sidebar-inner").css({'margin-right' : dynamicMargin + 'px', "float": "right"});
		else 
			$("#dynamic-player-sidebar-inner").css({"margin-left" : dynamicMargin + 'px', "float": "left"});
		///////////////////////////////////////////////////////////////
		
		// Display markup for the player

		$("#dynamic-player-container").prepend("<div class='dynamic-lcd-screen dynamic-image'><div class='dynamic-playing-song'><span class='dynamic-playing-title'></span><span class='dynamic-playing-artist'></span><span class='dynamic-playing-album'></span><span class='dynamic-playing-date'></span></div></div><div class='dynamic-song-position dynamic-inline'>0:00</div><div class='dynamic-song-duration dynamic-inline'>0:00</div><div class='dynamic-image dynamic-position-scrubber-image dynamic-inline'><div class='dynamic-image dynamic-position-scrubber'></div></div>");	
		
		// Initialize sliders

		dynThisPlayer.find(".dynamic-volume-slider").slider({
			range: "max",
			min: 0,
			max: 1,
			value: 0.5,
			step: 0.01,
			slide: function( event, ui ) {
				htmlSound.volume = ui.value;
			}
		});	
		
		dynThisPlayer.find(".dynamic-position-scrubber").slider();
		
		///////////////////////////////////////////////////////////////
		
	
		// Animate control buttons when clicked
		dynThisPlayer.find(".dynamic-play, .dynamic-next, .dynamic-previous").on("mousedown", function (e) {
			if(e.which == 1)
				$(this).css("opacity", "0.6");
		});
		$(".dynamic-play.outside, .dynamic-mute, .dynamic-maximize").on("mousedown", function (e) {
			if(e.which == 1)
				$(this).css("opacity", "0.6");
		});
		
		dynThisPlayer.find(".dynamic-play, .dynamic-next, .dynamic-previous").on("mouseup", function () {
			$(this).css("opacity", "1");
		})
		$(".dynamic-play.outside, .dynamic-mute, .dynamic-maximize").on("mouseup", function () {
			$(this).css("opacity", "1");
		})
		
		///////////////////////////////////////////////////////////////

		// Configure playlist visibility
		
		if(options.dynPlaylistVisible == 'false')
			dynThisPlayer.find(".dynamic-playlist-container").hide();

		else
			$(".dynamic-maximize").addClass('dynamic-minimize');

		
		$(".dynamic-maximize").on("click", function () {
			if(dynThisPlayer.find(".dynamic-playlist-container").css("display") == "none") {
				dynThisPlayer.find(".dynamic-playlist-container").slideDown();
				$(this).addClass('dynamic-minimize');
			} else {
				$(this).removeClass('dynamic-minimize');
				dynThisPlayer.find(".dynamic-playlist-container").slideUp();
			}
		});
		

		// Set height of playlist
		dynThisPlayer.find(".dynamic-playlist-container").css("height", options.dynPlaylistHeight + "px");
		dynThisPlayer.find(".dynamic-playlist-container").css("background-size", "auto " + options.dynPlaylistHeight + "px" );

		////////////////////////////////////////////////////////////////

		// Attach custom scrollbar to playlist
		dynThisPlayer.find(".dynamic-playlist-container").jScrollPane({
			autoReinitialise: true,
			scrollbarWidth: 0,
			scrollbarMargin: 0,
			showArrows: true
		});
		dynThisPlayer.find(".jspVerticalBar").css("display", "none");

		////////////////////////////////////////////////////////////////
		
		
		// Calculate and format song duration/current song position
		function convertMilliseconds (ms, p) {

			var pattern = p || "hh:mm:ss",
				arrayPattern = pattern.split(":"),
				clock = [ ],
				hours = Math.floor ( ms / 3600000 ), // 1 Hour = 36000 Milliseconds
				minuets = Math.floor (( ms % 3600000) / 60000), // 1 Minutes = 60000 Milliseconds
				seconds = Math.floor ((( ms % 360000) % 60000) / 1000) // 1 Second = 1000 Milliseconds
	
	
	
			// build the clock result
			function createClock(unit){
	
	
			// match the pattern to the corresponding variable
			if (pattern.match(unit)) {
				if (unit.match(/h/)) {
					addUnitToClock(hours, unit);
				}
				if (unit.match(/m/)) {
					addUnitToClock(minuets, unit);
				}
				if (unit.match(/s/)) {
					addUnitToClock(seconds, unit);
				};
				}
			}
	
			function addUnitToClock(val, unit){
	
				if ( val < 10 && unit.length === 2) {
					val = "0" + val;
				}
	
				clock.push(val); // push the values into the clock array
	
			}
	
	
			// loop over the pattern building out the clock result
			for ( var i = 0, j = arrayPattern.length; i < j; i ++ ){
	
				createClock(arrayPattern[i]);
	
			}
	
			return {
				hours : hours,
				minuets : minuets,
				seconds : seconds,
				clock : clock.join(":")
			};
	
		}
		
		///////////////////////////////////////////////////////////////		
		
		
		
		//Get playlist
		$.post(DynamicAjax.url, {nonce : DynamicAjax.nonce, action : 'dynamicAjax456534' , dynamicSearchTracks : true}, function(response) {
		
			if(response) 
			{
				//Use stored playlist
				response = $.parseJSON(response);		

				currentRow = dynThisPlayer.find("#dynamic-playlist li").first();	
				for(var i=0; i<=Object.size(response); i++) {
					if( typeof response !== "undefined" && typeof response[i] !== "undefined" ) {
						currentRow.attr("data-title", response[i].title);
						currentRow.attr("data-artist", response[i].artist);
						if(typeof response[i].album !== "undefined") 
							currentRow.attr("data-album", response[i].album);
						if(typeof response[i].date !== "undefined") 
						currentRow.attr("data-album-date", response[i].date);
						currentRow.attr("data-mp3", response[i].mp3);
						currentRow.attr("data-ogg", response[i].ogg);
						currentRow.attr("data-image", response[i].image);
						currentRow.text(currentRow.attr("data-title") + ' - ' + currentRow.attr("data-artist"));
						var newLi = $('#dynamic-playlist li').first().clone(true);
						$('#dynamic-playlist').append(newLi.clone(true));

						if( typeof response[i].playing !== "undefined" && typeof response[i].time !== "undefined" )
						{
							playingOnRow = response[i].playing;
							playingRow = currentRow;		
							currentTime = response[i].time;
							currentVolume = response[i].volume;
						} 
						
						currentRow = currentRow.next();
					}
				}
			
				currentRow = playingRow;
				
				songPlay(currentRow);
				$(htmlSound).on('canplay', function() {
					htmlSound.currentTime = currentTime;
					htmlSound.volume = currentVolume;
					dynThisPlayer.find( ".dynamic-volume-slider" ).slider("option", "value", currentVolume);
					if( playingOnRow == 'true' ) {
						$(htmlSound).on('canplaythrough canplay', function() {
							htmlSound.play();
						});
						$(".dynamic-play").addClass("dynamic-pause");					
					} else 
						$(".dynamic-play").removeClass("dynamic-pause");
						
					$(htmlSound).off("canplay")
				});	
										
				$('#dynamic-playlist li').last().remove();		
				
			} else {
				//Use default playlist
				if(typeof $("#dynamic-playlist li").first().attr("data-ogg") !== "undefined" || typeof $("#dynamic-playlist li").first().attr("data-mp3") !== "undefined")  {
					currentRow = dynThisPlayer.find("#dynamic-playlist li").first();	
					for(var i=0; i<=12; i++) {
						if( typeof options.dynOggFile[i] !== "undefined" && options.dynOggFile[i] || typeof options.dynMp3File[i] !== "undefined" && options.dynMp3File[i] ) {
							currentRow.attr("data-title", options.dynTitle[i]);
							currentRow.attr("data-artist", ' ( ' + options.dynArtist[i] + ' ) ');
							if(options.dynAlbum[i]) 
								currentRow.attr("data-album", ' - Album: ' + options.dynAlbum[i]);
							if(options.dynDate[i])
							currentRow.attr("data-album-date", ' - Date: ' + options.dynDate[i]);
							currentRow.attr("data-mp3", options.dynMp3File[i]);
							currentRow.attr("data-ogg", options.dynOggFile[i]);
							currentRow.attr("data-image", options.dynImageFile[i]);
							currentRow.text(currentRow.attr("data-title") + ' - ' + currentRow.attr("data-artist"));
							var newLi = $('#dynamic-playlist li').first().clone(true);
							$('#dynamic-playlist').append(newLi.clone(true));
							currentRow = currentRow.next();
						}
					}

					$('#dynamic-playlist li').last().remove();
					
					currentRow = dynThisPlayer.find("#dynamic-playlist li").first();
					
				}
				songPlay(currentRow);
				if(options.dynAutoplayEnabled == 'true' && currentRow) {
					$(".dynamic-play").addClass("dynamic-pause");
					$(htmlSound).on('canplaythrough canplay', function() {
						htmlSound.play();
					});
				}					
			}	
			 	
		});
		

		//Save playlist, current playing song, current time and current volume
		//before clicking a link or submitting a form
		$("a").on('click', onClickLinkOrSubmitForm);
		$("form").on('submit', onClickLinkOrSubmitForm);
		
		function onClickLinkOrSubmitForm(e)
		{
			e.preventDefault();	
			dynamicData = {};
			var tracksCounter = 0;
			dynThisPlayer.find('#dynamic-playlist li').each(function(index, value) {
				tracksCounter++;
				if(tracksCounter >= 40) return false;
				value = $(value);
				dynamicData[index] = {};
				
				dynamicData[index].title = value.attr('data-title');
				dynamicData[index].artist = value.attr('data-artist');
				dynamicData[index].album = value.attr('data-album');
				dynamicData[index].date = value.attr('data-album-date');
				dynamicData[index].ogg = value.attr('data-ogg');
				dynamicData[index].mp3 = value.attr('data-mp3');
				dynamicData[index].image = value.attr('data-image');
				if(value.hasClass("dynamic-playing")) {
					dynamicData[index].playing = true;
					dynamicData[index].time = htmlSound.currentTime;
					dynamicData[index].volume = htmlSound.volume;
					if(! $(".dynamic-play").hasClass("dynamic-pause")) 
						dynamicData[index].playing = false;
				}

				
			});

			var thisLink = $(this);
			$.post(DynamicAjax.url, {nonce : DynamicAjax.nonce, action : 'dynamicAjax456534' , dynamicTracks : dynamicData}, function(res) {
				if(thisLink.prop("tagName") == "A")	
					window.location = thisLink.attr("href");
				else
					thisLink.off("submit");
					thisLink.submit();
			});			
		}


		function eventListenerFunction() {
	
			var previousRow = dynThisPlayer.find("#dynamic-playlist .dynamic-playing");
			currentRow = previousRow.next().length == 0 ? dynThisPlayer.find("#dynamic-playlist li").first() : previousRow.next();

			songPlay(currentRow);
			$(htmlSound).on('canplaythrough canplay', function() {
				htmlSound.play();
			});
		}
		
		$(htmlSound).on("ended", eventListenerFunction);
		
		//Song play
		function songPlay (currentRow) {
			
			currentRow = $(currentRow);
			
			htmlSound.src = supports_ogg_audio() && currentRow.attr("data-ogg") ? currentRow.attr("data-ogg") : currentRow.attr("data-mp3");

			htmlSound.load();
			
			$(htmlSound).on('canplaythrough canplay', function() {
				$(htmlSound).off('canplaythrough canplay');
				currentRow.siblings(".dynamic-playing").removeClass("dynamic-playing");
				
				dynThisPlayer.find( ".dynamic-position-scrubber" ).bind( "slide", function(event, ui) {
					htmlSound.currentTime = ui.value;
				});

				dynThisPlayer.find(".dynamic-playing-artist").text(currentRow.attr("data-artist"));
				dynThisPlayer.find(".dynamic-playing-title").text(currentRow.attr("data-title"));
				dynThisPlayer.find(".dynamic-playing-album").text(currentRow.attr("data-album"));
				dynThisPlayer.find(".dynamic-playing-date").text(currentRow.attr("data-album-date"));
				
				
				if(dynThisPlayer.find(".dynamic-playing-song").text().length > 140) {
					dynThisPlayer.find(".dynamic-playing-artist").text(currentRow.attr("data-artist").trunc(30));
					if(dynThisPlayer.find(".dynamic-playing-song").text().length > 140) {
						dynThisPlayer.find(".dynamic-playing-title").text(currentRow.attr("data-title").trunc(30));
						if(dynThisPlayer.find(".dynamic-playing-song").text().length > 140) {
							dynThisPlayer.find(".dynamic-playing-album").text(currentRow.attr("data-album").trunc(30));
							dynThisPlayer.find(".dynamic-playing-date").text(currentRow.attr("data-album-date").trunc(30));
						}
					}			
				}
				
				if(currentRow.attr("data-image") !== '' && typeof currentRow.attr("data-image") !== "undefined") {
					$(".dynamic-playing-image img").attr("src", currentRow.attr("data-image"));
					$(".dynamic-playing-image").show();
				} else {
					$(".dynamic-playing-image").hide();
				}
				$(htmlSound).off('timeupdate');
				$(htmlSound).on("timeupdate", function() {
					
					var newVolume = dynThisPlayer.find( ".dynamic-volume-slider" ).slider("option", "value");
					htmlSound.volume = newVolume;
					
					var duration = htmlSound.duration * 1000;
					var durationTime = convertMilliseconds(duration, "mm:ss");
					dynThisPlayer.find(".dynamic-song-duration").html(durationTime.clock );
					
					var position = htmlSound.currentTime * 1000;
					var positionTime = convertMilliseconds(position, "mm:ss");
					dynThisPlayer.find(".dynamic-song-position").html(positionTime.clock );
					
					dynThisPlayer.find( ".dynamic-position-scrubber" ).slider("option", "max", duration/1000);
					dynThisPlayer.find( ".dynamic-position-scrubber" ).slider("option", "value", position/1000);
					
				});
				
				currentRow.addClass("dynamic-playing");

				
				window.clearTimeout(animateTimeout);
				$(".dynamic-playing-song").stop();
			
				
				$(".dynamic-playing-song").css({"margin-left": "0"});
				
				if(options.dynTotalWidth == "Small") var widthConstant = 3.4;
				else if(options.dynTotalWidth == "Large") var widthConstant = 1.3;
				else var widthConstant = 1.6;
				
				var animateMargin = '-' + Math.round(dynThisPlayer.find(".dynamic-playing-song").text().length * widthConstant) + '%';

				animateTimeout = window.setTimeout( function() { animateSongData(10000, animateMargin) }, 2000 );
			
			});
						
			function animateSongData(time, animateMargin) {
				
				$(".dynamic-playing-song").animate({
					"margin-left": animateMargin
					
							
				}, {
					duration: time,
					queue: false,
					easing: 'linear',
					complete: function() { $(".dynamic-playing-song").stop(); $(".dynamic-playing-song").css({"margin-left": "100%"}); animateSongData(20000, animateMargin) }		
					
				} );
			}
	
		}///// End songPlay()
		

		
		
		// Next Button
		dynThisPlayer.find(".dynamic-next").on("click", function () {

			currentRow = $("#dynamic-playlist .dynamic-playing").next();
			
			$("#dynamic-playlist .dynamic-playing").removeClass("dynamic-playing");
			
			if(currentRow.length == 0)
				currentRow = $("#dynamic-playlist li").first();

			songPlay(currentRow);
			
			$(htmlSound).on('canplaythrough canplay', function() {
				if(dynThisPlayer.find(".dynamic-pause").length > 0)
				{
					htmlSound.play();			
				}
			});
		});
		
		// Previous Button
		dynThisPlayer.find(".dynamic-previous").on("click", function () {
		
			currentRow = $("#dynamic-playlist .dynamic-playing").prev();
			
			$("#dynamic-playlist .dynamic-playing").removeClass("dynamic-playing");
			
			if(currentRow.length == 0)
				currentRow = $("#dynamic-playlist li").last();

			songPlay(currentRow);

			$(htmlSound).on('canplaythrough canplay', function() {
				if(dynThisPlayer.find(".dynamic-pause").length > 0)
				{
					htmlSound.play();								
				}
			});
		});
		
		// Play button	
		dynThisPlayer.find(".dynamic-play").on("click", function () {

			// Pause song
			if($(this).hasClass("dynamic-pause"))
			{
				htmlSound.pause();
				$(this).removeClass("dynamic-pause");
				
			}
			// Play song
			else if($("#dynamic-playlist li").length > 0 && $("#dynamic-playlist li").first().attr("data-mp3") !== "" || $("#dynamic-playlist li").first().attr("data-ogg") !== "")
			{
				$(this).addClass("dynamic-pause");
				if($("#dynamic-playlist .dynamic-playing").length === 0) {
					songPlay($("#dynamic-playlist li").first());
					htmlSound.play();
				}
				else {
					htmlSound.play();
					$(this).addClass("dynamic-pause");
					
					htmlSound.addEventListener("timeupdate", function() {
						var newVolume = dynThisPlayer.find( ".dynamic-volume-slider" ).slider("option", "value");
						htmlSound.volume = newVolume;
						
						var duration = htmlSound.duration * 1000;
						var durationTime = convertMilliseconds(duration, "mm:ss");
						dynThisPlayer.find(".dynamic-song-duration").html(durationTime.clock );
						
						var position = htmlSound.currentTime * 1000;
						var positionTime = convertMilliseconds(position, "mm:ss");
						dynThisPlayer.find(".dynamic-song-position").html( positionTime.clock );
						
						dynThisPlayer.find( ".dynamic-position-scrubber" ).slider("option", "max", duration/1000);
						dynThisPlayer.find( ".dynamic-position-scrubber" ).slider("option", "value", position/1000);
					
					});
				}
			}
					
		});
		
		
		// Click to play song
		dynThisPlayer.find("#dynamic-playlist li").on("click", function () {
			currentRow = $(this);
			songPlay(currentRow); 
			$(".dynamic-play").addClass("dynamic-pause");		
			$(htmlSound).on('canplaythrough canplay', function() {
				htmlSound.play();
			}); 						
		});
		
		//Volume
		function volumeTime () {

			var newVolume = dynThisPlayer.find( ".dynamic-volume-slider" ).slider("option", "value");
			htmlSound.volume = newVolume;
			
			var duration = htmlSound.duration * 1000;
			var durationTime = convertMilliseconds(duration, "mm:ss");
			dynThisPlayer.find(".dynamic-song-duration").html(durationTime.clock );
			
			var position = htmlSound.currentTime * 1000;
			var positionTime = convertMilliseconds(position, "mm:ss");
			dynThisPlayer.find(".dynamic-song-position").html(positionTime.clock );
			
			dynThisPlayer.find( ".dynamic-position-scrubber" ).slider("option", "max", duration/1000);
			dynThisPlayer.find( ".dynamic-position-scrubber" ).slider("option", "value", position/1000);

		}
		
		//Play songs from shortcode buttons
		$(document).on('click', '.dynamic-play-button',function() {
			$('.dynamic-play').addClass('dynamic-pause');
			var clon = $('#dynamic-playlist li').first().clone(true);
			$('#dynamic-playlist li').remove();

			$('#dynamic-playlist').append(clon.clone(true));
			
			var dynamicTitle = $('#dynamic-playlist li').first();
			
			dynamicTitle.attr('data-mp3', $(this).siblings(".dynamic-single-mp3-src").text() ); 	
			dynamicTitle.attr('data-ogg', $(this).siblings(".dynamic-single-ogg-src").text() ); 
			dynamicTitle.attr('data-artist', $(this).siblings(".dynamic-single-artist").text() ); 
			dynamicTitle.attr('data-title', $(this).siblings(".dynamic-single-title").text() ); 
			dynamicTitle.attr('data-album', $(this).siblings(".dynamic-single-album").text() ); 
			dynamicTitle.attr('data-album-date', $(this).siblings(".dynamic-single-date").text() ); 
			dynamicTitle.attr('data-image', $(this).siblings(".dynamic-single-image").text() ); 

			if(dynamicTitle.attr("data-title") === '') {
				dynamicTitle.attr('data-title', "Unknown title");
			}
			
			if(dynamicTitle.attr("data-artist") === '') {
					dynamicTitle.attr('data-artist', " ( Unknown artist )");
			}

			dynamicTitle.text( dynamicTitle.attr("data-title") + ' - ' + dynamicTitle.attr("data-artist") ); 
	
			totalSongs = $('#dynamic-playlist').length;
			currentRow = $("#dynamic-playlist li").first();

			songPlay(currentRow); 

			$(htmlSound).on('canplaythrough canplay', function() {
				htmlSound.play(); 
			});
		});

		//Add songs from shortcode buttons
		$(document).on('click', '.dynamic-add-button',function() {
			var clon = $('#dynamic-playlist li').first().clone(true);

			$('#dynamic-playlist').append(clon.clone(true));
			var dynamicTitle = $('#dynamic-playlist li').last();
												
			dynamicTitle.removeClass('dynamic-playing'); 
			dynamicTitle.attr('data-mp3', $(this).siblings(".dynamic-single-mp3-src").text() ); 	
			dynamicTitle.attr('data-ogg', $(this).siblings(".dynamic-single-ogg-src").text() ); 
			dynamicTitle.attr('data-artist', $(this).siblings(".dynamic-single-artist").text() ); 	
			dynamicTitle.attr('data-title', $(this).siblings(".dynamic-single-title").text() ); 
			dynamicTitle.attr('data-album', $(this).siblings(".dynamic-single-album").text() ); 
			dynamicTitle.attr('data-album-date', $(this).siblings(".dynamic-single-date").text() ); 
			dynamicTitle.attr('data-image', $(this).siblings(".dynamic-single-image").text() ); 
			
			if(dynamicTitle.attr("data-title") === '') {
				dynamicTitle.attr('data-title', "Unknown title");
			}
			
			if(dynamicTitle.attr("data-artist") === '') {
				dynamicTitle.attr('data-artist', "Unknown artist");
			}
			
			dynamicTitle.text( dynamicTitle.attr('data-title') + ' - ' + dynamicTitle.attr('data-artist') ); 

			if( $('#dynamic-playlist li').first().attr("data-mp3") == '' && $('#dynamic-playlist li').first().attr("data-ogg") == '' ) {
				$('#dynamic-playlist li').first().remove();
				currentRow = $('#dynamic-playlist li').first();
				if ( supports_ogg_audio() && $(currentRow).attr("data-ogg"))
					htmlSound.src = $(currentRow).attr("data-ogg");
				else
					htmlSound.src = $(currentRow).attr("data-mp3");
			}

			totalSongs = $('#dynamic-playlist').length;
		});
		
		return this;
		////////////////////////////////////////////////////////////////
    });/// End return this.each(function () {})

  };
})( jQuery );
