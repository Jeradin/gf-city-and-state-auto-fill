<?php
/*
Plugin Name: Gravity Forms - City and State Auto-fill
Plugin URI: http://github.com/Jeradin/
Description: Auto populate Address City and state from entered ZIP code
Version: 1.0
Author: Geet Jacobs
Author URI: http://anagr.am
License: GPL

------------------------------------------------------------------------
Copyright 2012-2013 Rocketgenius Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/


//------------------------------------------

if (class_exists("GFForms")) {
    GFForms::include_addon_framework();

    class GFCityandStateAutofill extends GFAddOn {

        protected $_version = "1.1";
        protected $_min_gravityforms_version = "1.8";
        protected $_slug = "gf-city-and-state-auto-fill";
        protected $_path = "gf-city-and-state-auto-fill/gf-city-and-state-auto-fill.php";
        protected $_full_path = __FILE__;
        protected $_title = "Gravity Forms - City and State Auto-fill";
        protected $_short_title = "City and State Auto-fill";


		public function pre_init(){
            parent::pre_init();
            // add tasks or filters here that you want to perform during the class constructor - before WordPress has been completely initialized
        }

        public function init(){
            parent::init();
            //add_filter( 'gform_submit_button', array($this, 'form_submit_button'), 10, 2 );
        }

        public function init_admin(){
            parent::init_admin();
			add_action( 'gform_editor_js', array($this, 'gf_auto_city_state_autofill_gform_editor_js') );
			add_action( 'gform_field_advanced_settings', array($this, 'gf_auto_city_state_autofill_field_settings'), 10, 2 );
			add_filter( 'gform_tooltips', array($this,'gf_auto_city_state_autofill_field_tooltips') );
        }

        public function init_frontend(){
            parent::init_frontend();
			//Use below if I figure out how to rearrange addess input fields.
			//add_filter( 'gform_pre_render', array($this,'gf_auto_city_state_autofill_format') );
			// Add individual page script for form
			add_action('gform_register_init_scripts', array($this, 'add_page_script'));

        }

        public function init_ajax(){
            parent::init_ajax();
            // add tasks or filters here that you want to perform only during ajax requests
        }


		// Add a custom setting to the tos advanced field
		function gf_auto_city_state_autofill_field_settings( $position, $form_id=null ){

		    // Create settings on position 50 (right after Field Label)
		    if( $position == 50 ){ ?>

		    <li class="auto_city_state_autofill_setting field_setting">
		    <input type="checkbox" id="field_auto_city_state_autofill" onclick="SetFieldProperty('auto_city_state_autofill', this.checked);" />
				<label for="field_auto_city_state_autofill"  class="inline">
		            <?php _e("Populate by ZIP code", "gravityforms"); ?>
		            <?php gform_tooltip("form_field_auto_city_state_autofill"); ?>
		        </label>
			</li>


		    <?php
		    }
		}

		//Filter to add a new tooltip
		function gf_auto_city_state_autofill_field_tooltips($tooltips){
		   $tooltips["form_field_auto_city_state_autofill"] = "<h6>Populate by ZIP code</h6> Check this box to have a ZIP code populate address.";

		   return $tooltips;
		}

		// Now we execute some javascript technicalitites for the field to load correctly
		function gf_auto_city_state_autofill_gform_editor_js(){ ?>
			<script type='text/javascript'>
				jQuery(document).ready(function($) {
			        //Alter the setting offered for the address input type
			        fieldSettings["address"] = fieldSettings["address"] + ", .auto_city_state_autofill_setting"; // this will show all fields that

					//binding to the load field settings event to initialize the checkbox
					$(document).bind("gform_load_field_settings", function(event, field, form){
						//console.log(field["defaultCountry"]);
						$("#field_auto_city_state_autofill").attr("checked", field["auto_city_state_autofill"] == true);

					});
			    });
			</script>

		<?php
		}

		//Trying to change format/order of address fields.
		function gf_auto_city_state_autofill_format($form) {

			//loop through results
			foreach($form['fields'] as &$field) {

				// if this is not an address field, skip
				if(RGFormsModel::get_input_type($field) != 'address')
					continue;

				//Check if the right setting is checked and default country is US
				if( $field["auto_city_state_autofill"] && ( $field["defaultCountry"]=="United States" || $field["defaultCountry"]=="Canada") ){

					add_filter("gform_field_content", array($this, "auto_pop_change_field_order"), 10, 5);

					/*
					echo "<pre>";
					var_dump($field["inputs"]);
					echo "</pre>";
					*/


				}else{
					//no else value
				}


			}

			return $form;
		}

		//Related to the above function
		function auto_pop_change_field_order($content, $field, $value, $lead_id, $form_id){
			// Currently only applies to most common field types, but could be expanded.
		  //echo '<pre>';var_dump($field);echo '</pre>';
			if( $field["type"] == 'address') {
				//$content = str_replace('<input ', '<input class=\'form-control\' ', $content);
				//$content = str_replace('<select ', '<select class=\'form-control\' ', $content);
			}

			return $content;

		} // End auto_pop_change_field_order()


		function add_page_script($form){
			self::log_debug('Adding page script to '.$form['id']);

			//loop through results
			foreach($form['fields'] as &$field) {

				// if this is not an address field, skip
				if(RGFormsModel::get_input_type($field) != 'address')
					continue;

				//Only add Javescript if Auto populate is checked AND
				if( $field["auto_city_state_autofill"] && ( $field["defaultCountry"]=="United States" || $field["defaultCountry"]=="Canada") ){
							//$field["inputs"][5]["isHidden"] //The country code field
							if($field["defaultCountry"]=="United States" ){$countryCode='us';}else{$countryCode='ca';};
							$script = '(function($){' .
										'$( "#input_'. $form['id'] .'_'.$field['id'].'_3_container" ).hide();'.
						                '$( "#input_'. $form['id'] .'_'.$field['id'].'_4_container" ).hide();'.
						                '$( "#input_'. $form['id'] .'_'.$field['id'].'_5_container" ).prepend("<span id=\'message_'. $form['id'] .'_'.$field['id'].'_5_container\'>'.
                                                'Enter a ZIP code to populate your City and State.'.
                                            '</span>");'.
									//  getting country code if set
										'var country="'.$countryCode.'",'.
										'zipValid = {us: /[0-9]{5}(-[0-9]{4})?/,ca: /^([A-Z][0-9][A-Z])\s*([0-9][A-Z][0-9])$/};'.
									   //Look at adding other countries in the future
									   /*'if($( "#input_'. $form['id'] .'_'.$field['id'].'_6" ).val() != "" ){'.
									   'country = $( "#input_'. $form['id'] .'_'.$field['id'].'_6" ).val();'.
									   '$.ajax({'.
							                'url: "http://restcountries.eu/rest/v1/name/" + country,'. //http://zip.getziptastic.com/v2/
							                'cache: false,'.
							                'dataType: "json",'.
							                'type: "GET",'.
							                //data: el.val(),'.
							                'success: function(result, success) {'.
							                    'country = result[0].alpha2Code;'.
							                '}'.
							            '});'.

						   '};'.*/
						   //End getting country code if set
							'$( "#input_'. $form['id'] .'_'.$field['id'].'_5" ).on("keyup paste", function() {'.
						        'var el = $(this);'.
						        'if (el.val().length >= 5 && zipValid[country].test(el.val()) ) {'.
						            '$.ajax({'.
						                'url: "//api.zippopotam.us/"+country+"/" + el.val(),'. //http://zip.getziptastic.com/v2/
						                'cache: false,'.
						                'dataType: "json",'.
						                'type: "GET",'.
						                //'data: el.val(),'.
						                'success: function(result, success) {'.
						                	'places = result["places"][0];;'.
						                	'$( "#input_'. $form['id'] .'_'.$field['id'].'_3_container" ).slideDown();'.
											'$( "#input_'. $form['id'] .'_'.$field['id'].'_4_container" ).slideDown();'.
											'$( "#message_'. $form['id'] .'_'.$field['id'].'_5_container" ).hide();'.
						                    '$( "#input_'. $form['id'] .'_'.$field['id'].'_3" ).val(places["place name"]);'.
						                    '$( "#input_'. $form['id'] .'_'.$field['id'].'_4" ).val(places["state"]);'.

						                '}'.
						            '});'.
						        '}'.
						    '});'.
					    '})(jQuery);';


				    GFFormDisplay::add_init_script($form['id'], 'auto-pop-address', GFFormDisplay::ON_PAGE_RENDER, $script);

				}else{
					//no else value
				}
			}

			return $form;
		}
    }
    new GFCityandStateAutofill();
}