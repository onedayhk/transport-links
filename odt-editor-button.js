(function() {

    var odt_button_img = mainJS.odtUrl + "assets/images/menu_icon_20x20.png";
    tinymce.create("tinymce.plugins.odt_button_plugin", {

        //url argument holds the absolute url of our plugin directory
        init : function(ed, url) {

            // add hong kong transport links button    
            ed.addButton("odt_button", {
                title : "Hong Kong Transport Links",
                cmd : "odt_cmd",
                image : odt_button_img
            });

            //button functionality.
            ed.addCommand("odt_cmd", function() {

                // show dialog
                var windowManager = ed.windowManager.open({
                    // Modal settings
                    title: 'Add Hong Kong Transport Links',
                    width: jQuery( window ).width() * 0.7,
                    height: 200,
                    inline: 1,
                    id: 'odt-add-dialog',
                    body: [
                        {
                            type   : 'selectbox',
                            name   : 'odt-language',
                            id     : 'odt_language',
                            label  : 'Language',
                            options : [
                                'English', 'Chinese'
                            ],
                            style: 'max-width:150px;',
                        },
                        {
                            type: 'textbox',
                            name: 'odt-address',
                            id: 'odt_address',
                            label: 'Search Address',
                        },
                        {
                            type: 'textbox',
                            name: 'odt-building-ref-id',
                            id: 'odt_building_ref_id',
                            hidden: true,
                        },
                        {
                            type: 'textbox',
                            name: 'odt-height',
                            id: 'odt_height',
                            label: 'Height',
                            style: 'max-width:150px;',
                            value: 400,
                        },
                        {
                            type: 'textbox',
                            name: 'odt-width',
                            id: 'odt_width',
                            label: 'Width',
                            style: 'max-width:150px;',
                            value: 760,
                        }

                    ],

                    buttons: [
                        {
                            text: 'Add',
                            id: 'odt-dialog-add-btn',
                            onclick: function( e ) {

                                var odt_language = $('#odt_language').val();
                                var odt_building_ref_id = $('#odt_building_ref_id').val();
                                var odt_height = $('#odt_height').val();
                                var odt_width = $('#odt_width').val();
                                
                                $('#odt_language').prop('disabled', true);
                                $('#odt_address').prop('disabled', true);
                                $('#odt_height').prop('disabled', true);
                                $('#odt_width').prop('disabled', true);

                                $.post(ajaxurl,
                                {
                                    action: 'odt_action',
                                    odt_action: 'odt-get-scripts',
                                    odt_language: odt_language,
                                    odt_building_ref_id: odt_building_ref_id,
                                    odt_height: odt_height,
                                    odt_width: odt_width
                                },
                                function(getScriptsResponse){
                                    $('#odt_language').prop('disabled', false);
                                    $('#odt_address').prop('disabled', false);
                                    $('#odt_height').prop('disabled', false);
                                    $('#odt_width').prop('disabled', false);

                                    console.log(getScriptsResponse);

                                    var obj = JSON.parse(getScriptsResponse);
                                    if(obj.status == 200){
                                        ed.execCommand("mceInsertContent", 0, obj.data);
                                        windowManager.close();
                                    }else{
                                        if(checkJSON(obj.error)){
                                            var errors = '';
                                            for(var i = 0; obj.error.length > i; i++){
                                                errors += obj.error[i]+ '\n';
                                            }
                                            alert('Something went wrong.' + '\nError:\n'+ errors);
                                        }else{
                                            alert('Something went wrong.' + '\nError:'+ obj.error);
                                        }
                                    }
                                });


                                function checkJSON(m) {

                                   if (typeof m == 'object') { 
                                      try{ m = JSON.stringify(m); }
                                      catch(err) { return false; } }

                                   if (typeof m == 'string') {
                                      try{ m = JSON.parse(m); }
                                      catch (err) { return false; } }

                                   if (typeof m != 'object') { return false; }
                                   return true;

                                };
                            },
                        },
                        {
                            text: 'Cancel',
                            id: 'odt-dialog-cancel-btn',
                            onclick: function() {
                              windowManager.close();
                            }
                        }
                    ]
                });

                $('#odt_address').autocomplete({
                    source: function(request, response){

                        $('#odt_address').prop('disabled', true);

                        $.post(ajaxurl,
                        {
                            action: 'odt_action',
                            odt_action: 'odt-search-building-name',
                            keywords: request.term
                        },
                        function(buildingSearchResponse){
                            console.log('Building Search Response: ' + buildingSearchResponse);

                            $('#odt_address').prop('disabled', false);

                            var obj = JSON.parse(buildingSearchResponse);
                            if(obj.status == 200){
                                var index;
                                var buildings = [];
                                for(index = 0; obj.data.length > index; index++){
                                    buildings[index] = {
                                        'id': obj.data[index].building_ref_id,
                                        'value': obj.data[index].building_name
                                    };
                                }
                                response(buildings);
                            }else{
                                var buildings = [];
                                
                                buildings[0] = {
                                        'id': 0,
                                        'value': 'No Results Found.'
                                    };
                                    
                                response(buildings);
                            }
                        });
                    },
                    select: function AutoCompleteSelectHandler(event, ui){
                                $('#odt_address').removeClass('ui-autocomplete-loading');
                                
                                if(ui.item.id > 0){
                                    var building_ref_id = ui.item.id;
                                    $('#odt_building_ref_id').val(building_ref_id);
                                    
                                }else{
                                    event.preventDefault(); 
                                    $('#odt_building_ref_id').val('');
                                }
                            },
                    minLength: 3
                });

            });

        },

        createControl : function(n, cm) {
            return null;
        },

        getInfo : function() {
            return {
                longname : "Hong Kong Transport Links",
                author : "OneDay Group Ltd.",
                version : "1"
            };
        }
    });

    tinymce.PluginManager.add("odt_button_plugin", tinymce.plugins.odt_button_plugin);
})();