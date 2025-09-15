<table class="dataTablesExample">
    <thead>
    <tr>
        <th>{{ __('ID') }}</th>
        <th>{{ __('Membership Details') }}</th>
        <th>{{ __('Payment Gateway') }}</th>
        <th>{{ __('Payment Status') }}</th>
        <th>{{ __('Status') }}</th>
        <th>{{ __('Purchase Date') }}</th>
        <th>{{ __('Expire Date') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($user_memberships_history as $history)
        <tr>
            <td>{{ $history->id }}</td>
            <td>
                {{ __('Title:') }} {{optional($history->membership)->title}} <br>
                @if($history->price == 0)
                    {{ __('Price:') }} {{float_amount_with_currency_symbol($history->initial_price)}} <br>
                @else
                    {{ __('Price:') }} {{float_amount_with_currency_symbol($history->price)}} <br>
                @endif
                {{ __('Type:') }} {{  $history->membership?->membership_type?->type }} <br>
                {{ __('Listing Limit:') }} {{  $history->listing_limit }} <br>
                {{ __('Gallery Images Per Listing:') }}  {{ $history->gallery_images }} <br>
                {{ __('Featured Listing:') }}  {{ $history->featured_listing }} <br>

                {{ __('Business Hour:') }}
                @if ($history->business_hour == 1)
                    <i class="las la-check-circle text-success fs-4 mx-2"></i>
                @else
                    <i class="las la-times-circle text-danger fs-4 mx-2"></i>
                @endif
                <br>

                {{ __('Enquiry Form:') }}
                @if ($history->enquiry_form == 1)
                    <i class="las la-check-circle text-success fs-4 mx-2"></i>
                @else
                    <i class="las la-times-circle text-danger fs-4 mx-2"></i>
                @endif
                <br>

                {{ __('Membership Badge:') }}
                @if ($history->membership_badge == 1)
                    <i class="las la-check-circle text-success fs-4 mx-2"></i>
                @else
                    <i class="las la-times-circle text-danger fs-4 mx-2"></i>
                @endif
            </td>
            <td>
                @if($history->payment_gateway == 'manual_payment')
                    {{ ucfirst(str_replace('_',' ',$history->payment_gateway)) }}
                @else
                    {{ $history->payment_gateway == 'authorize_dot_net' ? __('Authorize.Net') : ucfirst($history->payment_gateway) }}
                @endif
            </td>
            <td>
                @if($history->payment_status == '' || $history->payment_status == 'cancel')
                    <span class="btn btn-danger btn-sm">{{ __('Cancel') }}</span>
                @elseif($history->payment_status == 'pending')
                    @php
                        $user_membership = \Modules\Membership\app\Models\UserMembership::where('user_id', $history->user_id)->first();
                    @endphp
                    @if($user_membership->payment_status == 'complete')
                        @can('user-membership-manual-payment-status-change')
                        <span class="btn btn-warning btn-sm">{{ ucfirst($history->payment_status) }}</span>
                        <a class="btn btn-sm btn-success history_edit_payment_gateway_modal"
                           data-bs-toggle="modal"
                           data-bs-target="#historyEditPaymentGatewayModal"
                           data-membership_history_id="{{ $history->id }}"
                           data-user_firstname="{{ $history->user?->fullname }}"
                           data-user_email="{{ $history->user?->email }}"
                           data-img_url="{{ $history->manual_payment_image }}">
                            {{ __('Update') }}
                        </a>
                        @endcan
                    @else
                        <span class="btn btn-warning btn-sm">{{ ucfirst($history->payment_status) }}</span>
                    @endif
                @else
                    <span class="btn btn-success btn-sm">{{ ucfirst($history->payment_status) }}</span>
                @endif
            </td>
            <td>
                @if($history->status == 0)
                    <span class="btn btn-danger btn-sm">{{ __('Inactive') }}</span>
                @else
                    <span class="btn btn-success btn-sm">{{ __('Active')  }}</span>
                @endif
            </td>
            <td>{{ $history->created_at->format('Y-m-d') ?? '' }}</td>
            <td>{{ Carbon\Carbon::parse($history->expire_date)->format('Y-m-d') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<x-pagination.laravel-paginate :route="$route" :allData="$user_memberships_history"/>
