<?php

namespace Botble\Ecommerce\Http\Requests;

use Botble\Ecommerce\Enums\ShippingMethodEnum;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Support\Http\Requests\Request;
use EcommerceHelper;
use Illuminate\Validation\Rule;

class CheckoutRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     *
     */
    public function rules()
    {
        $rules = [
            // 'payment_method'  => 'required|' . Rule::in(PaymentMethodEnum::values()),
            // 'shipping_method' => 'required|' . Rule::in(ShippingMethodEnum::values()),
            // 'payment_method'  => 'required|false',
            // 'shipping_method' => 'required|false',
            'amount'          => 'required|min:0',
        ];

        $rules['address.address_id'] = 'required_without:address.name';
        if (!$this->has('address.address_id') || $this->input('address.address_id') === 'new') {
            $rules['address.name'] = 'required|min:3|max:120';

            $rules['address.phone'] = EcommerceHelper::getPhoneValidationRule();

            $rules['address.email'] = 'required|email|max:60|min:6';
            $rules['address.state'] = 'required|max:120';
            $rules['address.city'] = 'required|max:120';
            $rules['address.address'] = 'required|string';

            if (count(EcommerceHelper::getAvailableCountries()) > 1) {
                $rules['address.country'] = 'required|' . Rule::in(array_keys(EcommerceHelper::getAvailableCountries()));
            }
        }

        if ($this->input('create_account') == 1) {
            $rules['password'] = 'required|min:6';
            $rules['password_confirmation'] = 'required|same:password';
            $rules['address.email'] = 'required|max:60|min:6|email|unique:ec_customers,email';
            $rules['address.name'] = 'required|min:3|max:120';
        }

        $rules = apply_filters(PROCESS_CHECOUT_RULES_REQUEST_ECOMMERCE, $rules);

        return $rules;
    }

    /**
     * @return array
     */
    public function messages()
    {
        $messages = [
            'address.name.required'    => trans('plugins/ecommerce::order.address_name_required'),
            'address.phone.required'   => trans('plugins/ecommerce::order.address_phone_required'),
            'address.email.required'   => trans('plugins/ecommerce::order.address_email_required'),
            'address.email.unique'     => trans('plugins/ecommerce::order.address_email_unique'),
            'address.state.required'   => trans('plugins/ecommerce::order.address_state_required'),
            'address.city.required'    => trans('plugins/ecommerce::order.address_city_required'),
            'address.country.required' => trans('plugins/ecommerce::order.address_country_required'),
            'address.address.required' => trans('plugins/ecommerce::order.address_address_required'),
        ];

        $messages = apply_filters(PROCESS_CHECOUT_MESSAGES_REQUEST_ECOMMERCE, $messages);

        return array_merge(parent::messages(), $messages);
    }

    /**
     * @return array
     */
    public function attributes()
    {
        $attributes = [
            'address.name'    => __('Name'),
            'address.phone'   => __('Phone'),
            'address.email'   => __('Email'),
            'address.state'   => __('State'),
            'address.city'    => __('City'),
            'address.country' => __('Country'),
            'address.address' => __('Address'),
        ];

        return array_merge(parent::attributes(), $attributes);
    }
}
