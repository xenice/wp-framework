<?php
/**
 * @name        xenice admin optimize
 * @description Admin optimize
 * @author      xenice <xenice@qq.com>
 * @version     1.0.0 2019-10-02
 * @link        http://www.xenice.com/
 * @package     xenice
 */
 
namespace xenice\optimize;

use xenice\theme\Theme;

class AdminOptimize
{
    public function __construct()
    {
        // wordpress
		take('disable_auto_save') && add_action('wp_print_scripts', [$this, 'disableAutoSave']);;
		take('disable_post_revision') && remove_action('post_updated','wp_save_post_revision' );
		
		// xenice
		take('enable_link') && add_filter( 'pre_option_link_manager_enabled', '__return_true' );
		take('enable_code_escape') && add_filter( 'content_save_pre', [$this, 'replaceCodeTags'], 9 ); 
    }
    
    
    public function disableAutoSave()
    {
        wp_deregister_script('autosave'); 
    }
	
    public function escapeCode($arr)
    {
    	$output = htmlspecialchars($arr[2], ENT_NOQUOTES, get_bloginfo('charset'), false); 
    	if (! empty($output)) {
    		return  $arr[1] . $output . $arr[3];
    	}
    	else
    	{
    		return  $arr[1] . $arr[2] . $arr[3];
    	}
    	
    }
    
    public function replaceCodeTags($data)
    {
    	$data = preg_replace_callback('@(<code.*>)(.*)(</code>)@isU', [$this,'escapeCode'], $data);
    	return $data;
    }
    
}