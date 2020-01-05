<?php
/**
 * @name        Xenice Loader
 * @author      xenice <xenice@qq.com>
 * @version     1.0.0 2019-08-16
 * @link        http://www.xenice.com/
 * @package     xenice
 */
 
namespace xenice\theme;

class Loader extends Base
{
    public function __construct()
	{
	    date_default_timezone_set(get_option('timezone_string'));
	    
	    Theme::alias([
	        // model
	        'article' => 'xenice\model\ArticleModel',
	        'page' => 'xenice\model\PageModel',
	        'category' => 'xenice\model\CategoryModel',
	        'tag' => 'xenice\model\TagModel',
	        'comment' => 'xenice\model\CommentModel',
            'option' => 'xenice\model\OptionModel',
            'user' => 'xenice\model\UserModel',
            
            // pointer
            'article_pointer' => 'xenice\model\pointer\ArticlePointer',
            'category_pointer' => 'xenice\model\pointer\CategoryPointer',
            'tag_pointer' => 'xenice\model\pointer\TagPointer',
	        'comment_pointer' => 'xenice\model\pointer\CommentPointer',
	        'user_pointer' => 'xenice\model\pointer\UserPointer',
	        
            // template
            'template' => 'xenice\view\Template',

        ], true);
        
	    if(is_admin()){
	        add_filter( 'theme_templates', [$this, 'addTemplates']);
		}
	}
	
	public function addTemplates($templates)
	{
		$dir = VIEW_DIR . '/page';
		$arr = scandir($dir);
		foreach($arr as $name){
			$file = $dir . '/' . $name;
			if(is_dir($dir . '/' . $file)){
				continue;
			}
			if (!preg_match( '|Template Name:(.*)$|mi', @file_get_contents( $file ), $header ) ) {
				continue;
			}
			$templates[substr($name,0,-4)] = $header[1];
			
		}
		return $templates;
	}
}