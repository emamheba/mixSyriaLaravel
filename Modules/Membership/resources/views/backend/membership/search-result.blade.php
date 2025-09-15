<table class="dataTablesExample">
  <thead>
  <tr>
      <th>{{ __('ID') }}</th>
      <th>{{ __('Membership Details') }}</th>
      <th>{{ __('User Details') }}</th>
      <th>{{ __('Payment Gateway') }}</th>
      <th>{{ __('Payment Status') }}</th>
      <th>{{ __('Status') }}</th>
      <th>{{ __('Purchase Date') }}</th>
      <th>{{ __('Expire Date') }}</th>
      <th>{{ __('Action') }}</th>
  </tr>
  </thead>
  <tbody>
  @foreach($all_memberships as $membership)
      <tr>
          <td>{{ $membership->id }}</td>
          <td>
              {{ __('Title:') }} {{optional($membership->membership)->title}} <br>
              @if($membership->price == 0)
                  {{ __('Price:') }} {{float_amount_with_currency_symbol($membership->initial_price)}} <br>
              @else
                  {{ __('Price:') }} {{float_amount_with_currency_symbol($membership->price)}} <br>
              @endif
              {{ __('Type:') }} {{  $membership->membership?->membership_type?->type }} <br>
              {{ __('Listing Limit:') }} {{  $membership->listing_limit }} <br>
              {{ __('Gallery Images Per Listing:') }}  {{ $membership->gallery_images }} <br>
              {{ __('Featured Listing:') }}  {{ $membership->featured_listing }} <br>

              {{ __('Business Hour:') }}
              @if ($membership->business_hour == 1)
                  <i class="las la-check-circle text-success fs-4 mx-2"></i>
              @else
                  <i class="las la-times-circle text-danger fs-4 mx-2"></i>
              @endif
              <br>

              {{ __('Enquiry Form:') }}
              @if ($membership->enquiry_form == 1)
                  <i class="las la-check-circle text-success fs-4 mx-2"></i>
              @else
                  <i class="las la-times-circle text-danger fs-4 mx-2"></i>
              @endif
              <br>

              {{ __('Membership Badge:') }}
              @if ($membership->membership_badge == 1)
                  <i class="las la-check-circle text-success fs-4 mx-2"></i>
              @else
                  <i class="las la-times-circle text-danger fs-4 mx-2"></i>
              @endif
          </td>

          <td>
              {{ __('Name:') }} {{  optional($membership->user)->fullname }} <br>
              {{ __('Email:') }} {{  optional($membership->user)->email }} <br>
              {{ __('Phone:') }} {{ optional($membership->user)->phone}}
          </td>

          <td>
              @can('user-membership-manual-payment-status-change')
                  @if($membership->payment_gateway == 'manual_payment')
                      {{ ucfirst(str_replace('_',' ',$membership->payment_gateway)) }}
                  @else
                      {{ $membership->payment_gateway == 'authorize_dot_net' ? __('Authorize.Net') : ucfirst($membership->payment_gateway) }}
                  @endif
              @endcan
          </td>
          <td>
              @if($membership->payment_status == '' || $membership->payment_status == 'cancel')
                  <span class="btn btn-danger btn-sm">{{ __('Cancel') }}</span>
              @elseif($membership->payment_status == 'pending')
                  @can('user-membership-manual-payment-status-change')
                      <span class="btn btn-warning btn-sm">{{ ucfirst($membership->payment_status) }}</span>
                      <a class="btn btn-sm btn-success edit_payment_gateway_modal"
                          data-bs-toggle="modal"
                          data-bs-target="#editPaymentGatewayModal"
                          data-membership_id="{{ $membership->id }}"
                          data-user_firstname="{{ $membership->user?->fullname }}"
                          data-user_email="{{ $membership->user?->email }}"
                          data-img_url="{{ $membership->manual_payment_image }}">
                          {{ __('Update') }}
                      </a>
                  @endcan
              @else
                  <span class="btn btn-success btn-sm">{{ ucfirst($membership->payment_status) }}</span>
              @endif
          </td>
          <td>
              @if($membership->status == 0)
                  <span class="btn btn-danger btn-sm">{{ __('Inactive') }}</span>
              @else
                  <span class="btn btn-success btn-sm">{{ __('Active')  }}</span>
              @endif
          </td>
          <td>{{ $membership->created_at->format('Y-m-d') ?? '' }}</td>
          <td>{{ Carbon\Carbon::parse($membership->expire_date)->format('Y-m-d') }}</td>
          <td>
              @can('user-membership-status-change')
                  <x-status.table.status-change :title="__('Change Status')" :url="route('admin.user.membership.status',$membership->id)"/>
              @endcan
                <br>
              <a class="cmnBtn btn_5 btn_bg_info radius-5 mt-2" href="{{ route('admin.user.membership.history',$membership->user_id) }}">{{ __('History') }}</a>
              <a href="{{ route('admin.user.membership.email.sent', $membership->id) }}" class="cmnBtn btn_5 btn_bg_secondary radius-5 mt-2">{{ __('Send Email')}}</a>
          </td>
      </tr>
  @endforeach
  </tbody>
</table>
<x-pagination.laravel-paginate :route="$route" :allData="$all_memberships"/>
