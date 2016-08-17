=== Dynamic Audio Player Basic ===
Contributors: msalsas
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=GHF4VNXSZ64R4
Tags: audio, player, audio player, mp3, ogg, playlist
Requires at least: 3.0.1
Tested up to: 4.6
Stable tag: 2.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Provides an audio player widget 
with a dynamic playlist and shortcodes.
Tracks keep playing during navigation.

== Description ==

Provides an audio player widget 
with a dynamic playlist and shortcodes.
Tracks keep playing during navigation.

You can find an example and a detailed description at <a href="http://dynamicaudioplayer.com/">dynamicaudioplayer.com/</a>.
And some tutorials at <a href="http://dynamicaudioplayer.com/tutorials/">dynamicaudioplayer.com/tutorials/</a>.


== Installation ==


1. Upload `dynamic-audio-player` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the default tracks on `Dynamic Player` settings.
4. Configure the player opions on the widgets menu.

== Frequently Asked Questions ==

= Why should I add two audio files (ogg and mp3) to each track? =

Because of browsers compatibility. Some browsers support mp3 files, while others support ogg. 
Providing both you make sure that the track will be played in almost any browser. 
<a title="developer.mozilla.org" href="https://developer.mozilla.org/en-US/Apps/Build/Audio_and_video_delivery/Cross-browser_audio_basics#Audio_Codec_Support" target="_blank">Here</a> 
you can see the audio codec support for each browser.

= So, does this plugin work with any browser? =

No (but almost). It will work only with HTML5 audio compatible browsers. <a title="caniuse.com" href="http://caniuse.com/#search=audio element" target="_blank">Here</a> you can see a list of supported browsers.

= The widget does not appear in the "widgets" section =

Reload the page. If the widget "Dynamic Audio Player Widget" is not added to the sidebar, add it manually.

== Changelog ==

= 3.0.0 =
- Refactor.
- 40 default tracks.
- Add "do not animate track title" option.

= 2.3.0 =
Add "exclude from Shop page" option (for Woocommerce plugin). 

= 2.2.1 =
Remove weird image on the bottom of the page when the player is not displayed. 

= 2.2.0 =
Add 7 default tracks (12 total default tracks)

= 2.1.0 =
Add "exclude/include posts/pages/home" functionality 

= 2.0.10 =
Small fix.

= 2.0.9 =
Small fix.

= 2.0.8 =
Set widget_dynamic-player-widget option order before getting CSS.

= 2.0.7 =
Set important rule to CSS to avoid jquery-ui overwriting. 

= 2.0.6 =
Fix registering css.

= 2.0.5 =
Small fix.

= 2.0.4 =
Small fix.

= 2.0.3 =
Fix CSS.

= 2.0.2 =
Small fix.

= 2.0.1 =
Improve description and FAQ.

= 2.0.0 =
Integrated the PRO version. Now you can navigate through the site while the tracks keep playing.

= 1.1.2 =
Compatible with 4.2.1

= 1.1.1 =
Fix second error when using an older PHP version than 5.4.

= 1.1.0 =
Fix error fired when using an older PHP version than 5.4.

= 1.0.2 =
Fix rate link

= 1.0.1 =
Fix readme link

= 1.0.0 =
First version


