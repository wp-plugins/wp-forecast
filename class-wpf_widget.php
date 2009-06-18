<?php

if ( !class_exists('wpf_widget') )
{
    class wpf_widget extends WP_Widget
    {
	function wpf_widget()
	{
	    $widget_ops = array('classname' => 'wp_forecast_widget',
				'description' => 'WP Forecast Widget');
	    $control_ops = array('width' => 300, 'height' => 150);
	    $this->WP_Widget('wp-forecast', 'WP Forecast', 
			     $widget_ops, $control_ops);
	}
	
	function widget( $args, $instance )
	{ 
	    pdebug(1,"Start of function wp_forecast_widget ()");
	    
	    extract($args);
	    extract($instance);
  
	    $wpf_vars=get_wpf_opts($wpfcid);
	    if (!empty($language_override)) {
		$wpf_vars['wpf_language']=$language_override;
	    }
	    $weather=unserialize(get_option("wp-forecast-cache".$wpfcid));
	    
	    show($wpfcid,$args,$wpf_vars);
	     
	    pdebug(1,"End of function wp_forecast_widget ()");
	}
	
	function update( $new_instance, $old_instance )
	{ 
	    pdebug(1,"Start of wpf_widget::update()");
	    
	    return $new_instance;
	    
	    pdebug(1,"End of wpf_widget::update()");
	}
	
	function form( $instance )
	{
	    pdebug(1,"Start of wpf_widget::form()");
	    
	    $count = get_option('wp-forecast-count');
	    
            // get translation 
	    $locale = get_locale();
	    if ( empty($locale) )
		$locale = 'en_US';
	    if(function_exists('load_textdomain')) 
		load_textdomain("wp-forecast_".$locale,ABSPATH . 
				"wp-content/plugins/wp-forecast/lang/".$locale.".mo");

	    $title  = esc_attr($instance['title']);
	    $wpfcid = esc_attr($instance['wpfcid']);

	    // code for widget title form 
	    $out  = "";
	    $out .= '<p><label for="'. $this->get_field_id('title') . '" >';
	    $out .= __("Title:","wp-forecast_".$locale);
	    $out .= '<input class="widefat" id="'. 
		$this->get_field_id('title') . '" name="'. 
		$this->get_field_name('title') . 
		'" type="text" value="'. $title.'" /></label></p>';
	   	    
            // print out widget selector
	    $out .='<p><label for ="'. $this->get_field_id('wpfcid') . '" >';
	    $out .= __('Available widgets',"wp-forecast_".$locale);
	    $out .= "<select name='". $this->get_field_name("wpfcid") ."' size='1' >";
	    for ($i=0;$i<$count;$i++) {
		$id = get_widget_id( $i );
		$out .="<option value='".$id."' ";
		if ( $wpfcid == $id )
		    $out .=" selected='selected' ";
		$out .=">".$id."</option>";
	    }
	    $out .= "</select></p>";
	    echo $out;
	    
	    pdebug(1,"End of wpf_widget::form()");
	}
    }
    
}
?>