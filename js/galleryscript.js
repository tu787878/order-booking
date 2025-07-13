jQuery(document).ready( function($) {

      jQuery('input#myprefix_media_manager').click(function(e) {

             e.preventDefault();
             var image_frame;
             if(image_frame){
                 image_frame.open();
             }
             // Define image_frame as wp.media object
             image_frame = wp.media({
                           title: 'Select Media',
                           multiple : false,
                           library : {
                                type : 'image',
                            }
                       });

                       image_frame.on('close',function() {
                          // On close, get selections and save to the hidden input
                          // plus other AJAX stuff to refresh the image preview
                          var selection =  image_frame.state().get('selection');
                          var gallery_ids = new Array();
                          var my_index = 0;
                          selection.each(function(attachment) {
                             gallery_ids[my_index] = attachment['id'];
                             my_index++;
                          });
                          var ids = gallery_ids.join(",");
                          jQuery('input#myprefix_image_id').val(ids);
                          Refresh_Image(ids);
                       });

                      image_frame.on('open',function() {
                        // On open, get the id from the hidden input
                        // and select the appropiate images in the media manager
                        var selection =  image_frame.state().get('selection');
                        var ids = jQuery('input#myprefix_image_id').val().split(',');
                        ids.forEach(function(id) {
                          var attachment = wp.media.attachment(id);
                          attachment.fetch();
                          selection.add( attachment ? [ attachment ] : [] );
                        });

                      });

                    image_frame.open();
     });

     jQuery('input#myprefix_media_manager_popup').click(function(e) {

      e.preventDefault();
      var image_frame;
      if(image_frame){
          image_frame.open();
      }
      // Define image_frame as wp.media object
      image_frame = wp.media({
                    title: 'Select Media',
                    multiple : false,
                    library : {
                         type : 'image',
                     }
                });

                image_frame.on('close',function() {
                   // On close, get selections and save to the hidden input
                   // plus other AJAX stuff to refresh the image preview
                   var selection =  image_frame.state().get('selection');
                   var gallery_ids = new Array();
                   var my_index = 0;
                   selection.each(function(attachment) {
                      gallery_ids[my_index] = attachment['id'];
                      my_index++;
                   });
                   var ids = gallery_ids.join(",");
                   jQuery('input#myprefix_image_id_popup').val(ids);
                   Refresh_Image_Popup(ids);
                });

               image_frame.on('open',function() {
                 // On open, get the id from the hidden input
                 // and select the appropiate images in the media manager
                 var selection =  image_frame.state().get('selection');
                 var ids = jQuery('input#myprefix_image_id_popup').val().split(',');
                 ids.forEach(function(id) {
                   var attachment = wp.media.attachment(id);
                   attachment.fetch();
                   selection.add( attachment ? [ attachment ] : [] );
                 });

               });

             image_frame.open();
});

jQuery('input#myprefix_media_manager_popup_homepage').click(function(e) {

  e.preventDefault();
  var image_frame;
  if(image_frame){
      image_frame.open();
  }
  // Define image_frame as wp.media object
  image_frame = wp.media({
                title: 'Select Media',
                multiple : false,
                library : {
                     type : 'image',
                 }
            });

            image_frame.on('close',function() {
               // On close, get selections and save to the hidden input
               // plus other AJAX stuff to refresh the image preview
               var selection =  image_frame.state().get('selection');
               var gallery_ids = new Array();
               var my_index = 0;
               selection.each(function(attachment) {
                  gallery_ids[my_index] = attachment['id'];
                  my_index++;
               });
               var ids = gallery_ids.join(",");
               jQuery('input#myprefix_image_id_popup_homepage').val(ids);
               Refresh_Image_Popup_HomePage(ids);
            });

           image_frame.on('open',function() {
             // On open, get the id from the hidden input
             // and select the appropiate images in the media manager
             var selection =  image_frame.state().get('selection');
             var ids = jQuery('input#myprefix_image_id_popup_homepage').val().split(',');
             ids.forEach(function(id) {
               var attachment = wp.media.attachment(id);
               attachment.fetch();
               selection.add( attachment ? [ attachment ] : [] );
             });

           });

         image_frame.open();
});

});

// Ajax request to refresh the image preview
function Refresh_Image(the_id){
        var data = {
            action: 'myprefix_get_image',
            id: the_id
        };

        jQuery.get(ajaxurl, data, function(response) {

            if(response.success === true) {
                jQuery('#myprefix-preview-image').replaceWith( response.data.image );
            }
        });
}

// Ajax request to refresh the image preview
function Refresh_Image_Popup(the_id){
  var data = {
      action: 'myprefix_get_image_popup',
      id: the_id
  };

  jQuery.get(ajaxurl, data, function(response) {

      if(response.success === true) {
          jQuery('#myprefix-preview-image-popup').replaceWith( response.data.image );
      }
  });
}

// Ajax request to refresh the image preview
function Refresh_Image_Popup_HomePage(the_id){
  var data = {
      action: 'myprefix_get_image_popup_homepage',
      id: the_id
  };

  jQuery.get(ajaxurl, data, function(response) {

      if(response.success === true) {
          jQuery('#myprefix-preview-image-popup-homepage').replaceWith( response.data.image );
      }
  });
}