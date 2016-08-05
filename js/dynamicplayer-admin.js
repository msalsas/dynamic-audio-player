jQuery(document).ready(function($){
 
    var custom_ogg_uploader;
 
 
    $('.upload_ogg_file_button').click(function(e) {
		var custom_button = $(this);
        e.preventDefault();

        //Extend the wp.media object
        custom_ogg_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose ogg Audio File',
            button: {
                text: 'Choose ogg Audio File'
            },
            multiple: false
        });
 
        //When a file is selected, grab the URL and set it as the text field's value
        custom_ogg_uploader.on('select', function() {
            attachment = custom_ogg_uploader.state().get('selection').first().toJSON();
            custom_button.siblings('.upload_ogg_file').first().val(attachment.url);
        });
 
        //Open the uploader dialog
        custom_ogg_uploader.open();
 
    });
    
    $('.upload_mp3_file_button').click(function(e) {
		var custom_mp3_button = $(this);
        e.preventDefault();

        //Extend the wp.media object
        custom_mp3_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Audio mp3 File',
            button: {
                text: 'Choose Audio mp3 File'
            },
            multiple: false
        });
 
        //When a file is selected, grab the URL and set it as the text field's value
        custom_mp3_uploader.on('select', function() {
            attachment = custom_mp3_uploader.state().get('selection').first().toJSON();
            custom_mp3_button.siblings('.upload_mp3_file').first().val(attachment.url);
        });
 
        //Open the uploader dialog
        custom_mp3_uploader.open();
 
    });
    
    $('.upload_image_file_button').click(function(e) {
		var custom_image_button = $(this);
        e.preventDefault();

        //Extend the wp.media object
        custom_image_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image File',
            button: {
                text: 'Choose Image File'
            },
            multiple: false
        });
 
        //When a file is selected, grab the URL and set it as the text field's value
        custom_image_uploader.on('select', function() {
            attachment = custom_image_uploader.state().get('selection').first().toJSON();
            custom_image_button.siblings('.upload_image_file').first().val(attachment.url);
        });
 
        //Open the uploader dialog
        custom_image_uploader.open();
 
    });
 
 
});
