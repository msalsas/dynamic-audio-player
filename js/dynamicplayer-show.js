(function($){
	if($.fn.dynamicAudioPlayer) {
		$("#dynamic-player-container").dynamicAudioPlayer({
			dynTotalWidth: dynamic_options.dynTotalWidth,
			dynPosition: dynamic_options.dynPosition,
			dynPlaylistVisible: dynamic_options.dynPlaylistVisible,
			dynPlaylistHeight: dynamic_options.dynPlaylistHeight,
			dynAutoplayEnabled: dynamic_options.dynAutoplayEnabled,
			dynPlayerMarginFrom: dynamic_options.dynPlayerMarginFrom,
			dynPlayerMargin: dynamic_options.dynPlayerMargin,
			dynPlayerHorMarginFrom: dynamic_options.dynPlayerHorMarginFrom,
			dynPlayerHorMargin: dynamic_options.dynPlayerHorMargin,
			dynDoNotAnimateTitle: dynamic_options.dynDoNotAnimateTitle,
			dynUsingFancyBox: dynamic_options.dynUsingFancyBox,
			dynTitle: dynamic_options.dynTitle,
			dynArtist: dynamic_options.dynArtist,
			dynAlbum: dynamic_options.dynAlbum,
			dynDate: dynamic_options.dynDate,
			dynOggFile: dynamic_options.dynOggFile,
			dynMp3File: dynamic_options.dynMp3File,
			dynImageFile: dynamic_options.dynImageFile
		});
	}
})(jQuery);
