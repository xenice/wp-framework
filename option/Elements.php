<?php
/**
 * @name        xenice options elements
 * @author      xenice <xenice@qq.com>
 * @version     1.0.0 2019-12-10
 * @link        http://www.xenice.com/
 * @package     xenice
 */
 
namespace xenice\option;

trait Elements
{

    private function text($filed)
    {
        $style = $filed['style']??'regular';
        $label = $filed['label']??'';
        return sprintf( '<label><input type="text" class="%s-text" name="%s" value="%s" />%s</label>', $style, $filed['id'], $filed['value']??'', $label);
    }

    private function number($filed)
    {
        $style = $filed['style']??'small';
        return sprintf( '<input type="number" class="%s-text" name="%s" value="%s" step="%s" min="%s" max="%s" />', 
        $style,$filed['id'], $filed['value'], $filed['step']??1,$filed['min']??0,$filed['max']??'');
    }

    private function textarea($filed)
    {
        $style = $filed['style']??'regular';
        $label = $filed['label']??'';
        return sprintf( '<label><textarea type="textarea" class="%s-text" name="%s" rows="%s" >%s</textarea>%s</label>', $style, $filed['id'], $filed['rows'], $filed['value'], $label);
    }

    private function radio($filed)
    {
        $str = '';
        foreach ( $filed['opts'] as $key => $val ){
            if($key == $filed['value']){
                $str .= sprintf( '<label><input type="radio" name="%s" value="%s" checked />%s</label> &nbsp;&nbsp; ', $filed['id'], $key, $val);
            }
            else{
                $str .= sprintf( '<label><input type="radio" name="%s" value="%s" />%s</label> &nbsp;&nbsp; ', $filed['id'], $key, $val);
            }
        }
        return $str;
    }

    private function selectCategories($filed)
    {
        $cats= get_categories();
        foreach($cats as $cat){
            $filed['opts'][$cat->cat_ID] = $cat->cat_name;
        }
        return $this->select($filed);
    }
    
    private function select($filed)
    {
        $str = '<select name="'.$filed['id'].'" >';
        foreach ( $filed['opts'] as $key => $val ){
            if($key == $filed['value']){
                $str .= sprintf( '<option value ="%s" selected>%s</option>', $key, $val);
            }
            else{
               $str .= sprintf( '<option value ="%s">%s</option>', $key, $val);
            }
        }
        $str .= '</select>';
        return $str;
    }
    
    private function checkbox($filed)
    {
        $str = '';
        if($filed['value']){
            $str .= sprintf( '<label><input type="checkbox" name="%s" checked />%s</label> ', $filed['id'], $filed['label']);
        }
        else{
            $str .= sprintf( '<label><input type="checkbox" name="%s" />%s</label> ', $filed['id'], $filed['label']);
        }
        return $str;
    }
    
    private function img($filed)
    {
        $style = $filed['style']??'regular';
        $label = $filed['label']??'';
        $str = sprintf( '<label><input type="text" class="%s-text" name="%s" value="%s" />%s</label>', $style, $filed['id'], $filed['value']??'', $label);
        $str .= sprintf( '<img style="display:block;max-height:100px" src="%s" />', $filed['value']??'');
        return $str;
    }
    
    private function image($filed)
    {
        $value = $filed['value'];
        $str = sprintf( '<input type="hidden" class="xenice-image" name="%s" value="" />', $filed['id']);
        $str .= '<div class="slide-data"><div>' . _t('URL:').'</div>';
        $str .= sprintf( '<input type="text" class="regular-text" id="%s" value="%s" />', $filed['id'] . '_url', $value['url']??'');
        $str .=  '<div style="margin-top:10px">' . _t('Title:').'</div>';
        $str .= sprintf( '<input type="text" class="regular-text" id="%s" value="%s" />', $filed['id'] . '_title', $value['title']??'');
        $str .=  '<div style="margin-top:10px">' . _t('Image Path:').'</div>';
        $str .= sprintf( '<input type="text" class="regular-text" id="%s" value="%s" /></div>', $filed['id'] . '_path', $value['path']??'');
        $str .= sprintf( '<img class="slide-img" src="%s" />', $value['path']??'');
        return $str;
    }
}