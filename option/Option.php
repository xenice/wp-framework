<?php
/**
 * @name        xenice options
 * @author      xenice <xenice@qq.com>
 * @version     1.0.0 2019-09-26
 * @link        http://www.xenice.com/
 * @package     xenice
 */
 
namespace xenice\option;

use xenice\theme\Base;
use xenice\theme\Theme;

class Option extends Base
{
    private $key = "xenice_options"; // Database option key
    private $options  = [];
    private $defaults = [];
    
    use Elements;
    
    public function __construct()
    {
        if(is_file(OPTIONS_FILE)){
            $this->defaults = require(OPTIONS_FILE);
        }
        $this->defaults = Theme::call('xenice_options_init', $this->defaults);
        $this->get();
        add_action('after_switch_theme', [$this, 'active']);
        add_action( 'admin_menu', [$this, 'menu']);

    }
    
    public function active()
    {
        global $pagenow;
        if ( 'themes.php' == $pagenow && isset( $_GET['activated'] ) ) {
            // Insert option on first activation
            if(!get_option($this->key)){
                $options = [];
                $arr = $this->defaults;
                foreach($arr as $val){
                    $k1 = $val['id'];
                    foreach($val['fields'] as $field){
                        if(isset($field['fields'])){
                            foreach($field['fields'] as $f){
                                $k2 = $f['id'];
                                $options[$k1][$k2] = $f['value'];
                            }
                        }
                        else{
                            $k2 = $field['id'];
                            $options[$k1][$k2] = $field['value'];
                        }
                    }
                }
                add_option($this->key, $options);
            }
        }
    }

    public function menu()
    {
        if(!isset($this->options[0]['id'])){
            return;
        }
        
        // main menu id
        $id = 'xenice_' . $this->options[0]['id'];
        
        add_menu_page( _t('Theme'), _t('Theme'), 'manage_options', $id, [$this, $this->options[0]['id']], 'dashicons-admin-customizer',59);
        foreach($this->options as $option){
            add_submenu_page( $id, $option['name'], $option['name'], 'edit_themes', 'xenice_' . $option['id'], [$this,$option['id']]);
        }
    }

	public function __call($method, $args)
    {
        $key = array_search($method, array_column($this->options, 'id'));
        if($key === false){
            throw new \Exception('Call to undefined method ' . get_called_class() . '::' . $method);
        }
        $option = $this->options[$key];
        if(!isset($option['submit'])){
            $option['submit'] = _t('Save Changes');
        }
        if(isset($_POST['xenice_option_key']) && check_admin_referer('xenice-options-update')){
            // Delete useless elements
            $data = $_POST;
            unset($data['_wpnonce']);
            unset($data['_wp_http_referer']);
            unset($data['xenice_option_key']);
            Theme::bind('xenice_options_result',[$this,'post']);
            if(Theme::call('xenice_options_save', $_POST['xenice_option_key'], $data)){
                if($this->set($_POST['xenice_option_key'],$data)){
                    $this->get();
                    $option = $this->options[$key];
                    $result = 'true';
                }
                else{
                    $result = 'false';
                }
                Theme::call('xenice_options_result', $result);
            }

        }
        
        if(isset($_POST['result'])){
            if($_POST['result'] == 'true'){
                 ?>
                <div id="message" class="updated notice is-dismissible">
                <p><strong><?=$option['title']?> <?=_t('save success')?></strong></p>
                <button type="button" class="notice-dismiss"><span class="screen-reader-text"><?=_t('Ignore this notice')?></span></button>
                </div>
                <?php
            }
            else{
                 ?>
                <div id="message" class="error settings-error notice is-dismissible">
                <p><strong><?=$option['title']?> <?=_t('save failed')?></strong></p>
                <button type="button" class="notice-dismiss"><span class="screen-reader-text"><?=_t('Ignore this notice')?></span></button>
                </div>
                <?php
            }
        }
        
        ?>
        <style>
        .wrap .slide-img{
            margin:20px 0 0 0;
            max-height:135px;
        }
        
        .wrap .small-text,.wrap input[type="checkbox"],.wrap input[type="radio"]{
            margin-right:8px;
        }
        
        @media screen and (min-width:768px){
            .wrap .slide-data{
                float:left;
                margin-right:20px;
            }
            .wrap .regular-text{
                margin-right:8px;
            }
            .wrap label textarea{
                vertical-align: top;
            }
        }
        </style>
        <div class="wrap">
            <h2><?=$option['title']?></h2>

            <form method="post" action="" id="xenice_option_form">
                <?php wp_nonce_field('xenice-options-update'); ?>
                <input type="hidden" name="xenice_option_key" value="<?=$option['id']?>">
                <table class="form-table">
                    <tbody>
                    <?php
                    $str = '';
                    foreach ( $option['fields'] as $field ) {
                        $top = '<tr valign="top"><th scope="row"><label>'.$field['name'].'</label></th><td><p>';
                        
                        if(isset($field['fields'])){
                            $main = '';
                            foreach($field['fields'] as $f){
                                $main .= '<p>' . call_user_func_array([$this,$f['type']],[$f]) . '</p>';
                            }
                        }
                        else{
                            $main = call_user_func_array([$this,$field['type']],[$field]);
                        }
                        
                        $bottom = '</p>';
                        if ( isset($field['desc']) && $field['desc']) {
                            $bottom .= '<p class="description">'.$field['desc'] . '</p>';
                        }
                        $bottom .= '</td></tr>';
                        
                        
                        $str .= $top . $main . $bottom;
                    }
                    echo $str;
                    ?>
                    </tbody>
                </table>
                <p class="submit">
                    <input type="submit" class="button-primary" value="<?=$option['submit']?>"/>
                </p>
            </form>
        </div>
        <script>
            jQuery(function($){
              $("#xenice_option_form").submit(function(e){
                // image
                $('.xenice-image').each(function(){
                    var value = '';
                    var id = this.name;
                    value += '"url":' + '"' + $('#' + id + '_url').attr('value') + '",';
                    value += '"title":' + '"' + $('#' + id + '_title').attr('value') + '",';
                    value += '"path":' + '"' + $('#' + id + '_path').attr('value') + '"';
                    value  = '{' + value + '}';
                    this.value = value;
                });
                
                //e.preventDefault();
              });
            })
            
        </script>
    <?php
    }
    
    public function post($result)
    {
        echo "<form style='display:none;' id='form_result' name='form_result' method='post' action=''>
            <input name='result' type='text' value='$result' /></form>
            <script type='text/javascript'>document.form_result.submit();</script>";
    }
    
    /**
     * get options
     */
    private function get()
    {
        $arr1 = $this->defaults;
        $arr2 = get_option($this->key);
        
        foreach($arr1 as $key1=>$val){
            $k1 = $val['id'];
            foreach($val['fields'] as $key2=>$field){
                if(isset($field['fields'])){
                    foreach($field['fields'] as $key3=>$f){
                        $k2 = $f['id'];
                        if(isset($arr2[$k1][$k2])){
                            $arr1[$key1]['fields'][$key2]['fields'][$key3]['value'] =  $arr2[$k1][$k2];
                        }
                    }
                }
                else{
                    $k2 = $field['id'];
                    if(isset($arr2[$k1][$k2])){
                        $arr1[$key1]['fields'][$key2]['value'] =  $arr2[$k1][$k2];
                    }
                }
            }
        }
        $this->options = $arr1;
    }
    
    /**
     * set options
     */
    private function set($id, $fields)
    {
        $checkboxs = $this->names($id, 'checkbox');
        foreach($checkboxs as $checkbox){
            // Checkbox is not submitted when unchecked
            if(!isset($fields[$checkbox])){
                $fields[$checkbox] = false;
            }
            else{
                $fields[$checkbox] = true;
            }
        }
        
        $textareas = $this->names($id, 'textarea');
        foreach($textareas as $textarea){
            $fields[$textarea] = stripslashes($fields[$textarea]);
        }
        
        $images = $this->names($id, 'image');
        foreach($images as $image){
            $fields[$image] = json_decode(stripslashes($fields[$image]), true);
        }
        
        Theme::call('xenice_options_set', $id, $fields);
        $arr = get_option($this->key)?:[];
        if(isset($arr[$id]) && $arr[$id] == $fields){
            return true;
        }
        $arr[$id] = $fields;
        return update_option($this->key, $arr);
    }
    
    /**
     * Get names of the specified type
     */
    private function names($id, $type)
    {
        $arr = [];
        $key = array_search($id, array_column($this->defaults, 'id'));
        $fields = $this->defaults[$key]['fields'];
        foreach($fields as $field){
            if(isset($field['fields'])){
                foreach($field['fields'] as $f){
                    if($f['type'] == $type){
                        $arr[] = $f['id'];
                    }
                }
            }
            else{
                if($field['type'] == $type){
                    $arr[] = $field['id'];
                }
            }
        }
        return $arr;
    }
}