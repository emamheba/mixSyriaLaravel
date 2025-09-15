<?php


namespace plugins\PageBuilder\Addons\Header;

use plugins\PageBuilder\Fields\ColorPicker;
use plugins\PageBuilder\Fields\IconPicker;
use plugins\PageBuilder\Fields\Image;
use plugins\PageBuilder\Fields\Repeater;
use plugins\PageBuilder\Fields\Slider;
use plugins\PageBuilder\Fields\Switcher;
use plugins\PageBuilder\Fields\Text;
use plugins\PageBuilder\Helpers\RepeaterField;
use plugins\PageBuilder\PageBuilderBase;
use plugins\PageBuilder\Traits\LanguageFallbackForPageBuilder;

class HeaderStyleOne extends PageBuilderBase
{
    use LanguageFallbackForPageBuilder;

    public function preview_image()
    {
        return 'header/01.jpg';
    }

    public function admin_render()
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();
        $widget_saved_values = $this->get_settings();


        $output .= Text::get([
            'name' => 'title',
            'label' => __('Title'),
            'value' => $widget_saved_values['title'] ?? null,
        ]);
        $output .= Text::get([
            'name' => 'subtitle',
            'label' => __('Subtitle'),
            'value' => $widget_saved_values['subtitle'] ?? null,
        ]);

        $output .= Text::get([
            'name' => 'top_title',
            'label' => __('Top Title'),
            'value' => $widget_saved_values['top_title'] ?? null,
        ]);

        $output .= Image::get([
            'name' => 'top_image',
            'label' => __('Top Image'),
            'value' => $widget_saved_values['top_image'] ?? null,
            'dimensions' => '24x25'
        ]);

        $output .= ColorPicker::get([
            'name' => 'header_background_color',
            'label' => __('Background Color'),
            'value' => $widget_saved_values['header_background_color'] ?? null,
        ]);

        $output .= Image::get([
            'name' => 'background_image',
            'label' => __('Background Image'),
            'value' => $widget_saved_values['background_image'] ?? null,
            'dimensions' => '1900x670'
        ]);


        $output .= Repeater::get([
            'settings' => $widget_saved_values,
            'id' => 'banner_left_images_01',
            'fields' => [
                [
                    'type' => RepeaterField::IMAGE,
                    'name' => 'banner_left_images',
                    'label' => __('Left Banner Images (maximus add six images)')
                ],
            ]
        ]);

        $output .= Repeater::get([
            'settings' => $widget_saved_values,
            'id' => 'banner_right_images_02',
            'fields' => [
                [
                    'type' => RepeaterField::IMAGE,
                    'name' => 'banner_right_images',
                    'label' => __('Right Banner Images (maximus add six images)')
                ],
            ]
        ]);

        $output .= Text::get([
            'name' => 'search_button_title',
            'label' => __('Search Button Title'),
            'value' => $widget_saved_values['search_button_title'] ?? null,
        ]);

        $output .= Slider::get([
            'name' => 'padding_top',
            'label' => __('Padding Top'),
            'value' => $widget_saved_values['padding_top'] ?? 260,
            'max' => 500,
        ]);
        $output .= Slider::get([
            'name' => 'padding_bottom',
            'label' => __('Padding Bottom'),
            'value' => $widget_saved_values['padding_bottom'] ?? 190,
            'max' => 500,
        ]);
        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

    public function frontend_render() : string
    {
        $settings = $this->get_settings();

        $padding_top = $settings['padding_top'] ?? '100';
        $padding_bottom = $settings['padding_bottom'] ?? '100';
        $title = $settings['title'] ?? '';
        $subtitle = $settings['subtitle'] ?? '';
        $top_title = $settings['top_title'] ?? '';
        $top_image =  render_image_markup_by_attachment_id($settings['top_image']) ?? '';
        $background_image =  render_background_image_markup_by_attachment_id($settings['background_image']) ?? '';
        $header_background_color = $settings['header_background_color'] ?? '';
        $search_button_title = $settings['search_button_title'] ?? '';
        $banner_left_images_01 = $settings['banner_left_images_01'] ?? '';
        $banner_right_images_02 = $settings['banner_right_images_02'] ?? '';


    return $this->renderBlade('headers.style-one',[
        'padding_top' => $padding_top,
        'padding_bottom' => $padding_bottom,
        'title' => $title,
        'subtitle' => $subtitle,
        'top_title' => $top_title,
        'top_image' => $top_image,
        'background_image' => $background_image,
        'header_background_color' => $header_background_color,
        'search_button_title' => $search_button_title,
        'banner_left_images_01' => $banner_left_images_01,
        'banner_right_images_02' => $banner_right_images_02,

    ]);

}
    public function addon_title()
    {
        return __('Header: 01');
    }
}
