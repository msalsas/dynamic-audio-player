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
			dynTitle: [dynamic_options.dynTitle[0], dynamic_options.dynTitle[1], dynamic_options.dynTitle[2], dynamic_options.dynTitle[3], dynamic_options.dynTitle[4], dynamic_options.dynTitle[5], dynamic_options.dynTitle[6], dynamic_options.dynTitle[7], dynamic_options.dynTitle[8], dynamic_options.dynTitle[9], dynamic_options.dynTitle[10], dynamic_options.dynTitle[11]],
			dynArtist: [dynamic_options.dynArtist[0], dynamic_options.dynArtist[1], dynamic_options.dynArtist[2], dynamic_options.dynArtist[3], dynamic_options.dynArtist[4], dynamic_options.dynArtist[5], dynamic_options.dynArtist[6], dynamic_options.dynArtist[7], dynamic_options.dynArtist[8], dynamic_options.dynArtist[9], dynamic_options.dynArtist[10], dynamic_options.dynArtist[11]],
			dynAlbum: [dynamic_options.dynAlbum[0], dynamic_options.dynAlbum[1], dynamic_options.dynAlbum[2], dynamic_options.dynAlbum[3], dynamic_options.dynAlbum[4], dynamic_options.dynAlbum[5], dynamic_options.dynAlbum[6], dynamic_options.dynAlbum[7], dynamic_options.dynAlbum[8], dynamic_options.dynAlbum[9], dynamic_options.dynAlbum[10], dynamic_options.dynAlbum[11]],
			dynDate: [dynamic_options.dynDate[0], dynamic_options.dynDate[1], dynamic_options.dynDate[2], dynamic_options.dynDate[3], dynamic_options.dynDate[4], dynamic_options.dynDate[5], dynamic_options.dynDate[6], dynamic_options.dynDate[7], dynamic_options.dynDate[8], dynamic_options.dynDate[9], dynamic_options.dynDate[10], dynamic_options.dynDate[11]],
			dynOggFile: [dynamic_options.dynOggFile[0],  dynamic_options.dynOggFile[1], dynamic_options.dynOggFile[2],  dynamic_options.dynOggFile[3], dynamic_options.dynOggFile[4], dynamic_options.dynOggFile[5], dynamic_options.dynOggFile[6], dynamic_options.dynOggFile[7], dynamic_options.dynOggFile[8], dynamic_options.dynOggFile[9], dynamic_options.dynOggFile[10], dynamic_options.dynOggFile[11]],
			dynMp3File: [dynamic_options.dynMp3File[0], dynamic_options.dynMp3File[1], dynamic_options.dynMp3File[2], dynamic_options.dynMp3File[3], dynamic_options.dynMp3File[4], dynamic_options.dynMp3File[5], dynamic_options.dynMp3File[6], dynamic_options.dynMp3File[7], dynamic_options.dynMp3File[8], dynamic_options.dynMp3File[9], dynamic_options.dynMp3File[10], dynamic_options.dynMp3File[11]],
			dynImageFile: [dynamic_options.dynImageFile[0], dynamic_options.dynImageFile[1], dynamic_options.dynImageFile[2], dynamic_options.dynImageFile[3], dynamic_options.dynImageFile[4], dynamic_options.dynImageFile[5], dynamic_options.dynImageFile[6], dynamic_options.dynImageFile[7], dynamic_options.dynImageFile[8], dynamic_options.dynImageFile[9], dynamic_options.dynImageFile[10], dynamic_options.dynImageFile[11]]		
		});
	}
})(jQuery);
