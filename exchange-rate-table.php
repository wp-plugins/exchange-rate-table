<?php
/*
Plugin Name: Exchange Rate Table
Description: Exchange Rate Table for any currency in the world. Choose the currency to display and the table size and format.
Author: fx-rate.net
Version: 1.0
Author URI: http://fx-rate.net
Plugin URI: http://fx-rate.net/wordpress-exchange-rate-plugin/
*/

include("functions.php");



function exchange_rate_table_init() 
{


     if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
    	   return; 



    function exchange_rate_table_control() 
    {

        $newoptions = get_option('exchange_rate_table');
    	$options = $newoptions;
	$options_flag=0;

      	if(empty($currency_list)){
		$file_location = dirname(__FILE__)."/currencies.ser"; 
		if ($fd = fopen($file_location,'r')){
	   	   $currency_list_ser = fread($fd,filesize($file_location));
	   	   fclose($fd);
		}
		$currency_list = array();
		$currency_list = unserialize($currency_list_ser);
        }


    	if ( empty($newoptions) )
	{
	   $options_flag=1;
      	   $newoptions = array(
	   	'currency_code'=>'EUR',
	   	'currency_name'=>'Euro',
	   	'title'=>'Euro',
           	'country_code' => 'EU',
           	'label_type' => 'currency_name',
           	'length' => 'short',
	   );
	}

	if ( $_POST['exchange-rate-table-submit'] ) {
	     $options_flag=1;
	      $currency_code = strip_tags(stripslashes($_POST['exchange-rate-currency-code']));
              $newoptions['currency_code'] = $currency_code;
	      $newoptions['currency_name'] = $currency_list[$currency_code]['currency_name'];
	      $newoptions['country_code'] = $currency_list[$currency_code]['country_code'];
              $newoptions['title'] = $newoptions['currency_name'] ;
              $newoptions['length'] = strip_tags(stripslashes($_POST['exchange-rate-length']));
              $newoptions['label_type'] = strip_tags(stripslashes($_POST['exchange-rate-label-type']));
        }


      	if ( $options_flag ==1 ) {
              $options = $newoptions;
              update_option('exchange_rate_table', $options);
      	}


      	// Extract value from vars
      	$currency_code = htmlspecialchars($options['currency_code'], ENT_QUOTES);
	$currency_name = htmlspecialchars($options['currency_name'], ENT_QUOTES);
	$title = $currency_name;
      	$country_code = htmlspecialchars($options['country_code'], ENT_QUOTES);
      	$length = htmlspecialchars($options['length'], ENT_QUOTES);
      	$label_type = htmlspecialchars($options['label_type'], ENT_QUOTES);

      	echo '<ul><li style="text-align:center;list-style: none;"><label for="exchange-rate-table-title">Exchange Rate Table<br> by <a href="http://fx-rate.net">fx-rate.net</a></label></li>';

       	// Get currency, length and label type 


       	echo '<li style="list-style: none;"><label for="exchange-rate-currency-code">Currency:'.
               '<select id="exchange-rate-currency_code" name="exchange-rate-currency-code" style="width:125px">';
     	print_thecurrency_list($currency_code, $currency_list);
      	echo '</select></label></li>';

      	// Set label type
      	echo '<li style="list-style: none;"><label for="exchange-rate-label-type">'.'Label Type:&nbsp;';
       	echo '<select id="exchange-rate-label-type" name="exchange-rate-label-type"  style="width:120px" >';
      	print_label_type_list($label_type);
      	echo '</select></label>';
      	echo '</li>';


      	// Set Table length
	echo "\n";
      	echo '<li style="list-style: none;text-align:bottom"><label for="exchange-rate-length">'.'Table Length: &nbsp;'.
         '<select id="exchange-rate-length" name="exchange-rate-length"  style="width:75px">';
      	print_thelength_list($length);
      	echo '</select></label></li>';



      	// Hidden "OK" button
      	echo '<label for="exchange-rate-table-submit">';
      	echo '<input id="exchange-rate-table-submit" name="exchange-rate-table-submit" type="hidden" value="Ok" />';
      	echo '</label>';

        echo '<label for="exchange-rate-table-title"> <input type="hidden" id="exchange-rate-table-title" name="exchange-rate-table-title" value="'.$title.'" /> </label>';

	echo '</ul>';


    }


    /////////////////////////////////////////////////////////////////////////////////////////////////////
    //
    //	OUTPUT TABLE WIDGET
    //
    /////////////////////////////////////////////////////////////////////////////////////////////////////

     function exchange_rate_table($args) 
     {

	// Get values 
      	extract($args);

      	$options = get_option('exchange_rate_table');


      	// Extract value from vars
      	$currency_code = htmlspecialchars($options['currency_code'], ENT_QUOTES);
	$currency_name = htmlspecialchars($options['currency_name'], ENT_QUOTES);
	$title = $currency_name;
      	$country_code = htmlspecialchars($options['country_code'], ENT_QUOTES);
      	$length = htmlspecialchars($options['length'], ENT_QUOTES);
      	$label_type = htmlspecialchars($options['label_type'], ENT_QUOTES);

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



	echo '<!-Exchange Rate widget - HTML code - fx-rate.net --> <div style="width:' .$width. 'px; border:1px solid #000;background-color:#fff;align:center;text-align:center;padding: 0px 0px;margin: 0px 0px;overflow:hidden;">';

     	echo '<div style="text-align:center;font-size:11px; line-height:16px;font-family: arial;color:#173a00; font-weight:bold;background:#c5e554;padding: 3px 1px"> <a href="'.$target_url.'" title="'.$title.'" style="text-decoration:none;color:#173a00;font-size:11px; line-height:16px;font-family: arial;"> <img border="" width="16" height="11" style="margin:0;padding:0;border:0;" src = "'.$image_url.'"> </img> &#160;&#160;'.$title .'</a> </div>';

     	echo'<script type="text/javascript" src="'.$widget_call_string.'"></script></div><!-end of code-->';

	echo $after_widget;


    }
  
    register_sidebar_widget('Exchange Rate Table', 'exchange_rate_table');
    register_widget_control('Exchange Rate Table', 'exchange_rate_table_control', 245, 300);


}


add_action('plugins_loaded', 'exchange_rate_table_init');







?>