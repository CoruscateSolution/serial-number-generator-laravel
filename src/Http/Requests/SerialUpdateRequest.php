<?php

namespace App\Modules\Admin\Http\Requests\Setting;

use App\Support\APIResponse;
use Illuminate\Foundation\Http\FormRequest;

class SettingUpdateRequest extends FormRequest
{
    use APIResponse;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the Update request of Setting.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'social_links' => 'nullable|array',
            'social_links.facebook' => 'nullable',
            'social_links.twitter' => 'nullable',
            'social_links.pinterest' => 'nullable',
            'social_links.google' => 'nullable',
            'social_links.youtube' => 'nullable',
            'social_links.instagram' => 'nullable',
            'social_links.play_store' => 'nullable',
            'social_links.app_store' => 'nullable',
            'cod_max_price' => 'nullable',
            'shipping_price' => 'nullable',
            'minimum_order_amount' => 'nullable',
            'order_available_in_days' => 'nullable',
            'max_order_qty' => 'nullable',
            'contact_info' => 'nullable|array',
            'contact_info.address' => 'nullable',
            'contact_info.email' => 'nullable',
            'contact_info.working_on' => 'nullable',
            'contact_info.call_me' => 'nullable',
            'contact_info.customer_care' => 'nullable',
            'commission' => 'nullable',
            'range_master' => 'nullable|array',
            'range_master.from' => 'nullable|regex:' . PRICE_REGEX . '',
            'range_master.to' => 'nullable|regex:' . PRICE_REGEX . '',
            'range_master.amount' => 'nullable',
            'emergency_notifiers' => 'nullable|array',
            'emergency_notifiers.*' => 'nullable|distinct|email',
        ];
    }
}
