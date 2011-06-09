<?php
/*
Plugin Name: Exchange Rate Table
Description: Exchange Rate Table for any currency in the world. Choose the currency to display and the table size and format.
Author: enclick
Version: 1.1
Author URI: http://fx-rate.net
Plugin URI: http://fx-rate.net/wordpress-exchange-rate-plugin/
*/

require_once("functions.php");

static $currency_list;

/**
 * Add function to widgets_init that'll load our widget.
 */

add_action( 'widgets_init', 'load_exchange_rate_table' );

/**
 * Register our widget.
 * 'exchange_rate_table' is the widget class used below.
 *
 */
function load_exchange_rate_table() {
        register_widget( 'exchange_rate_table' );
}



/*******************************************************************************************
*
*       Exchange Rate Table  class.
*       This class handles everything that needs to be handled with the widget:
*       the settings, form, display, and update.
*
*********************************************************************************************/
class exchange_rate_table extends WP_Widget
{

      /*******************************************************************************************
      *
      *
      * Widget setup.
      *
      *
      ********************************************************************************************/
      function exchange_rate_table() {
                #Widget settings
                $widget_ops = array( 'description' => __('Displays an exchange rate table', 'exchange_rate_table') );

                #Widget control settings
                $control_ops = array( 'width' => 200, 'height' => 550, 'id_base' => 'exchange_rate_table' );

                #Create the widget
                $this->WP_Widget( 'exchange_rate_table', __('Exchange Rate Table', 'exchange_rate_table'), $widget_ops, $control_ops );
        }



   	/*******************************************************************************************
        *
        *
        * Update the widget settings.
        *
        *
        *******************************************************************************************/
        function update( $new_instance, $old_instance )
        {
		
		if(empty($currency_list)){
			$file_location = dirname(__FILE__)."/currencies.ser"; 
			if ($fd = fopen($file_location,'r')){
	   	   	   $currency_list_ser = fread($fd,filesize($file_location));
	   	   	   fclose($fd);
			}
			$currency_list = array();
			$currency_list = unserialize($currency_list_ser);
        	}

                $instance = $old_instance;

              	$instance['currency_code'] =  strip_tags(stripslashes($new_instance['currency_code']));
		$currency_code = $instance['currency_code'] ;
	      	$instance['currency_name'] =  strip_tags(stripslashes($currency_list[$currency_code]['currency_name']));
	      	$instance['country_code'] =  strip_tags(stripslashes($currency_list[$currency_code]['country_code']));
              	$instance['title'] =  strip_tags(stripslashes($instance['currency_name'])) ;

              	$instance['length'] = strip_tags(stripslashes($new_instance['length']));
              	$instance['label_type'] = strip_tags(stripslashes($new_instance['label_type']));
              	$instance['background_color'] = strip_tags(stripslashes($new_instance['background_color']));
              	$instance['text_color'] = strip_tags(stripslashes($new_instance['text_color']));

         	return $instance;
        }



     	/*******************************************************************************************
         *
         *      Displays the widget settings controls on the widget panel.
         *      Make use of the get_field_id() and get_field_name() function
         *      when creating your form elements. This handles the confusing stuff.
         *
         *
         ********************************************************************************************/
        function form( $instance )
        {

                #
                #       Set up some default widget settings
		#        

      	   	$defaults = array(
	   		  'currency_code'=>'EUR',
	   		  'currency_name'=>'Euro',
	   		  'title'=>'Euro',
           		  'country_code' => 'EU',
           		  'label_type' => 'country_name',
           		  'label_type' => 'country_name',
           		  'length' => 'continent',
           		  'background_color' => '#173a00',
           		  'text_color' => '#000000'
	   	);


		
            	if(!isset($instance['currency_code']))
                       $instance = $defaults;

		#
		#	PREDEFINED 
		#

		if(empty($currency_list)){
			$file_location = dirname(__FILE__)."/currencies.ser"; 
			if ($fd = fopen($file_location,'r')){
	   	   	   $currency_list_ser = fread($fd,filesize($file_location));
	   	   	   fclose($fd);
			}
			$currency_list = array();
			$currency_list = unserialize($currency_list_ser);
        	}




      		// Extract value from instance

      		$currency_code = htmlspecialchars($instance['currency_code'], ENT_QUOTES);
		$currency_name = htmlspecialchars($instance['currency_name'], ENT_QUOTES);
		$title = $currency_name;
      		$country_code = htmlspecialchars($instance['country_code'], ENT_QUOTES);
      		$length = htmlspecialchars($instance['length'], ENT_QUOTES);
      		$label_type = htmlspecialchars($instance['label_type'], ENT_QUOTES);
      		$background_color = htmlspecialchars($instance['background_color'], ENT_QUOTES);
      		$text_color = htmlspecialchars($instance['text_color'], ENT_QUOTES);


		#
		#
		#	START FORM OUTPUT
		#
		#
		
		echo '<div style="align:center;text-align:center;margin-bottom:10px">';
		echo '<p>Exchange Rate Table<br> by <a href="http://fx-rate.net">fx-rate.net</a></div>';

		// currency code
		echo '<p><label for="' .$this->get_field_id( 'currency_code' ). '">Currency:'.
               '<select id="' .$this->get_field_id( 'currency_code' ). '" name="' .$this->get_field_name( 'currency_code' ). '" style="width:125px">';
	       print_ert_thecurrency_list($currency_code, $currency_list);
	       echo '</select></label></p>';


	       // Set label type
	       echo '<p><label for="' .$this->get_field_id( 'label_type' ). '">'.'Label Type:&nbsp;';
	       echo '<select id="' .$this->get_field_id( 'label_type' ). '" name="' .$this->get_field_name( 'label_type' ). '"  style="width:120px" >';
	       print_ert_label_type_list($label_type);
	       echo '</select></label></p>';


	       // Set Table length
	       echo "\n";
	       echo '<p><label for="' .$this->get_field_id( 'length' ). '">'.'Table Length: &nbsp;';
	       echo '<select id="' .$this->get_field_id( 'length' ). '" name="' .$this->get_field_name( 'length' ). '"  style="width:95px">';
	       print_ert_thelength_list($length);
	       echo '</select></label></p>';

      		// Set Text Clock color
      		echo '<p><label for="' .$this->get_field_id( 'text_color' ). '">'.'Header Text Color: &nbsp;';
       		echo '<select id="' .$this->get_field_id( 'text_color' ). '" name="' .$this->get_field_name( 'text_color' ). '"  style="width:75px" >';
      		print_ert_textcolor_list($text_color);
      		echo '</select></label></p>';

      		// Set Background Clock color
      		echo '<p><label for="' .$this->get_field_id( 'background_color' ). '">'.'Background Color: &nbsp; ';
       		echo '<select id="' .$this->get_field_id( 'background_color' ). '" name="' .$this->get_field_name( 'background_color' ). '"  style="width:75px" >';
      		print_ert_backgroundcolor_list($background_color);
      		echo '</select></label></p>';


	       echo '<label for="' .$this->get_field_id( 'table_title' ). '"> <input type="hidden" id="' .$this->get_field_id( 'table_title' ). '" name="' .$this->get_field_id( 'table_title' ). '" 
	       value="'.$title.'" /> </label>';



    }


    /////////////////////////////////////////////////////////////////////////////////////////////////////
    //
    //	OUTPUT TABLE WIDGET
    //
    /////////////////////////////////////////////////////////////////////////////////////////////////////

     function widget($args , $instance) 
     {

	// Get values 
      	extract($args);


      	// Extract value from vars
      	$currency_code = htmlspecialchars($instance['currency_code'], ENT_QUOTES);
	$currency_name = htmlspecialchars($instance['currency_name'], ENT_QUOTES);
	$title = $currency_name;
      	$country_code = htmlspecialchars($instance['country_code'], ENT_QUOTES);
      	$length = htmlspecialchars($instance['length'], ENT_QUOTES);
      	$label_type = htmlspecialchars($instance['label_type'], ENT_QUOTES);
	$background_color = htmlspecialchars($instance['background_color'], ENT_QUOTES);
	$text_color = htmlspecialchars($instance['text_color'], ENT_QUOTES);

	echo $before_widget; 


	// Output title
	echo $before_title . $title . $after_title; 


	// Output Table

	$country_code = strtolower($country_code);
	$image_url = 'http://fx-rate.net/images/countries/'.$country_code.'.png';

	$target_url= "http://fx-rate.net/$currency_code/";

	$widget_call_string = 'http://fx-rate.net/wp_fx-rates.php?currency='.$currency_code;
	$widget_call_string .="&label_type=". $label_type;
	$widget_call_string .="&length=". $length;

	$title = UCWords($currency_name) . " Exchange Rate";

	if($label_type == "currency_code")
	      $width=130;
	elseif($label_type == "currency_name")
	      $width=210;
	elseif($label_type == "country_name")
	      $width=190;

	echo '<!-Exchange Rate widget - HTML code - fx-rate.net --> ';
	echo '<br style="line-height:15px">';
	echo '<div style="width:' .$width. 'px; border:1px solid #000; background-color:#fff; align:center; text-align:center; padding: 0px 0px; margin: auto; overflow:hidden;">';

	#
	#	HEADER
	#

	echo "\n";
     	echo '<div style="text-align:center;font-size:11px; line-height:16px;font-family: arial; font-weight:bold;background:'.$background_color.';padding: 3px 1px">'; 
	echo "\n";
	echo '<a href="'.$target_url.'" title="'.$title.'" style="text-decoration:none;color:'.$text_color.';font-size:11px; line-height:16px;font-family: arial;"> ';
	echo "\n";
	echo '<img border="" width="16" height="11" style="margin:0;padding:0;border:0;" src = "'.$image_url.'"> </img> &#160;&#160;'.$title .'</a> </div>';

     	echo '<script type="text/javascript" src="'.$widget_call_string.'"></script>';

	echo '</div><!-end of code-->';

	echo $after_widget;



    }

}



?>