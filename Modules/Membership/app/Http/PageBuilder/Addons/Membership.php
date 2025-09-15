<?php

namespace Modules\Membership\app\Http\PageBuilder\Addons;

use Modules\Membership\app\Models\UserMembership;
use plugins\PageBuilder\Fields\ColorPicker;
use plugins\PageBuilder\Fields\Slider;
use plugins\PageBuilder\Fields\Text;
use plugins\PageBuilder\PageBuilderBase;

class Membership extends PageBuilderBase
{
    // This function return the image name of the addon
    public function preview_image()
    {
        return 'membership.png';
    }

    // This function points the location of the image, It accept only module name
    public function setAssetsFilePath()
    {
        return externalAddonImagepath('Membership');
    }

    // This function contains addon settings while using the addon in the page builder
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
        $output .= ColorPicker::get([
            'name' => 'title_text_color',
            'label' => __('Title Text Color'),
            'value' => $widget_saved_values['title_text_color'] ?? null,
            'info' => __('select color you want to show in frontend'),
        ]);
        $output .= Text::get([
            'name' => 'subtitle',
            'label' => __('Subtitle'),
            'value' => $widget_saved_values['subtitle'] ?? null,
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

    // This function will render the addon on frontend, you can get the inputted values passed from the admin_render function
    public function frontend_render()
    {

        $settings = $this->get_settings();
        $title = $settings['title'] ?? '';
        $title_text_color = $settings['title_text_color'] ?? '';
        $explode = explode(" ", $title);
        $title_start = current($explode);
        $title_end = end($explode);
        $subtitle = $settings['subtitle'] ?? '';
        $padding_top = $settings['padding_top'] ?? '';
        $padding_bottom = $settings['padding_bottom'] ?? '';
        $subscription_text = __('You must pay first to buy a subscription');
        $close_text = __('Close');
        $buy_now_text = __('Buy Now');
        $apply = __('Apply');
        $number_of_connect = get_static_option('set_number_of_connect',2);
        $connect_text = sprintf(__('Connect to get order from buyer, each order will deduct %d connect from seller account.'),$number_of_connect);
        $route = '';
        $csrf_token = csrf_token();


        $wallet_gateway = '';
        if (moduleExists('Wallet')) {
            $wallet_gateway = \App\Helpers\PaymentGatewayRenderHelper::renderWalletForm();
        }

        $abc = get_static_option('site_manual_payment_name');
        $abcd = get_static_option('site_manual_payment_description');
        $receipt = __('Receipt');

        $memberships = \Modules\Membership\app\Models\Membership::with('membership_type')->where('status',1)->get();

        $user = auth()->guard('web')->user();
        if ($user){
            $user_current_membership = UserMembership::where('user_id', auth()->guard('web')->user()->id)
                ->whereDate('expire_date', '>', now()) // Check if expire_date is greater than current date
                ->latest()->first();
        }else{
            $user_current_membership = null;
        }


        // readable values must be pass via an array
        $data = [
            'user_current_membership'=> $user_current_membership,
            'settings'=> $settings,
            'title'=> $title,
            'title_text_color'=> $title_text_color,
            'explode'=> $explode,
            'title_start'=> $title_start,
            'title_end'=> $title_end,
            'subtitle'=> $subtitle,
            'padding_top'=> $padding_top,
            'padding_bottom'=> $padding_bottom,
            'subscription_text'=> $subscription_text,
            'close_text'=> $close_text,
            'buy_now_text'=> $buy_now_text,
            'apply'=> $apply,
            'number_of_connect'=> $number_of_connect,
            'connect_text'=> $connect_text,
            'route'=> $route,
            'csrf_token'=> $csrf_token,
            'wallet_gateway'=> $wallet_gateway,
            'abc'=> $abc,
            'abcd'=> $abcd,
            'receipt'=> $receipt,
            'memberships'=> $memberships,
        ];

        // renderView function will render the view file, this function will take three parameter, your view file name, passed array, module name
        return self::renderBlade('membership-plans', $data, 'Membership');
    }

    // This function sets the addon name
    public function addon_title()
    {
        return __("Membership Addon");
    }
}
