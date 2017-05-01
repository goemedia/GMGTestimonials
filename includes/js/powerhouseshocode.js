jQuery(document).ready(function($) {

    tinymce.create('tinymce.plugins.wpse72394_plugin', {
        init : function(ed, url) {
                // Register command for when button is clicked
                ed.addCommand('wpse72394_insert_shortcode', function() {
                    selected = tinyMCE.activeEditor.selection.getContent();

                    if( selected ){
                        //If text is selected when button is clicked
                        //Wrap shortcode around it.
                        content =  selected+'[gmg-reviews title="" showstars="true" content="true" random="false" maxshown="5"]';
                    }else{
                        content =  '[gmg-reviews title="" showstars="true" content="true" random="false" maxshown="5"]';
                    }

                    tinymce.execCommand('mceInsertContent', false, content);
                });

            // Register buttons - trigger above command when clicked
            ed.addButton('wpse72394_button', {title : 'Insert GMG Testimonials Slider', cmd : 'wpse72394_insert_shortcode', image: url + '/images/powerhouse-short.png' });
        },   
    });

    // Register our TinyMCE plugin
    // first parameter is the button ID1
    // second parameter must match the first parameter of the tinymce.create() function above
    tinymce.PluginManager.add('wpse72394_button', tinymce.plugins.wpse72394_plugin);
});