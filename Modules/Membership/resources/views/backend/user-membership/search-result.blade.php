<table class="table border-top">
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
  @forelse($all_memberships as $membership)
      <tr>
          <td>{{ $membership->id }}</td>
          <td>
              <div class="membership-details">
                  <span class="fw-semibold d-block">{{ optional($membership->membership)->title }}</span>
                  
                  <span class="badge bg-label-primary mb-1">
                      {{ $membership->price == 0 ? float_amount_with_currency_symbol($membership->initial_price) : float_amount_with_currency_symbol($membership->price) }}
                  </span>
                  
                  <div class="text-muted small">
                      <div><span class="fw-semibold">Type:</span> {{ $membership->membership?->membership_type?->type }}</div>
                      <div><span class="fw-semibold">Listings:</span> {{ $membership->listing_limit }}</div>
                      <div><span class="fw-semibold">Images/Listing:</span> {{ $membership->gallery_images }}</div>
                      <div><span class="fw-semibold">Featured Listings:</span> {{ $membership->featured_listing }}</div>
                  </div>
                  
                  <div class="mt-1">
                      <span class="me-2"><i class="ti {{ $membership->business_hour == 1 ? 'ti-clock text-success' : 'ti-clock-off text-muted' }}"></i> Business Hours</span>
                      <span class="me-2"><i class="ti {{ $membership->enquiry_form == 1 ? 'ti-forms text-success' : 'ti-forms text-muted' }}"></i> Enquiry Form</span>
                      <span><i class="ti {{ $membership->membership_badge == 1 ? 'ti-badge text-success' : 'ti-badge text-muted' }}"></i> Badge</span>
                  </div>
              </div>
          </td>
          <td>
              <div class="d-flex flex-column">
                  <div class="d-flex align-items-center">
                      <div class="avatar avatar-sm me-2">
                          <span class="avatar-initial rounded-circle bg-label-info">
                              {{ substr(optional($membership->user)->first_name, 0, 1) }}{{ substr(optional($membership->user)->last_name, 0, 1) }}
                          </span>
                      </div>
                      <span class="fw-semibold">{{ optional($membership->user)->fullname }}</span>
                  </div>
                  <small class="text-muted">{{ optional($membership->user)->email }}</small>
                  <small class="text-muted">{{ optional($membership->user)->phone }}</small>
              </div>
          </td>
          <td>
              <span class="badge bg-label-info">
                  @if($membership->payment_gateway == 'manual_payment')
                      {{ ucfirst(str_replace('_',' ',$membership->payment_gateway)) }}
                  @else
                      {{ $membership->payment_gateway == 'authorize_dot_net' ? __('Authorize.Net') : ucfirst($membership->payment_gateway) }}
                  @endif
              </span>
          </td>
          <td>
              @if($membership->payment_status == '' || $membership->payment_status == 'cancel')
                  <span class="badge bg-danger">{{ __('Cancel') }}</span>
              @elseif($membership->payment_status == 'pending')
                  <div>
                      <span class="badge bg-warning mb-1">{{ ucfirst($membership->payment_status) }}</span>
                      @can('user-membership-manual-payment-status-change')
                          <a class="btn btn-sm btn-success edit_payment_gateway_modal"
                              data-bs-toggle="modal"
                              data-bs-target="#editPaymentGatewayModal"
                              data-membership_id="{{ $membership->id }}"
                              data-user_firstname="{{ $membership->user?->fullname }}"
                              data-user_email="{{ $membership->user?->email }}"
                              data-img_url="{{ $membership->manual_payment_image }}">
                              <i class="ti ti-pencil me-1"></i>{{ __('Update') }}
                          </a>
                      @endcan
                  </div>
              @else
                  <span class="badge bg-success">{{ ucfirst($membership->payment_status) }}</span>
              @endif
          </td>
          <td>
              @if($membership->status == 0)
                  <span class="badge bg-danger">{{ __('Inactive') }}</span>
              @else
                  <span class="badge bg-success">{{ __('Active') }}</span>
              @endif
          </td>
          <td>{{ $membership->created_at->format('Y-m-d') ?? '' }}</td>
          <td>{{ Carbon\Carbon::parse($membership->expire_date)->format('Y-m-d') }}</td>
          <td>
              <div class="dropdown">
                  <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                      <i class="ti ti-dots-vertical"></i>
                  </button>
                  <div class="dropdown-menu">
                      @can('user-membership-status-change')
                          <a class="dropdown-item swal_status_change" href="javascript:void(0)">
                              <i class="ti ti-exchange me-1"></i>{{ __('Change Status') }}
                          </a>
                          <form action="{{ route('admin.user.membership.status', $membership->id) }}" method="post" style="display:none;">
                              @csrf
                              <button type="submit" class="swal_form_submit_btn"></button>
                          </form>
                      @endcan
                      
                      <a class="dropdown-item" href="{{ route('admin.user.membership.history', $membership->user_id) }}">
                          <i class="ti ti-history me-1"></i>{{ __('History') }}
                      </a>
                      
                      <a class="dropdown-item" href="{{ route('admin.user.membership.email.sent', $membership->id) }}">
                          <i class="ti ti-mail me-1"></i>{{ __('Send Email') }}
                      </a>
                  </div>
              </div>
          </td>
      </tr>
  @empty
      <tr>
          <td colspan="9" class="text-center">{{ __('No memberships found') }}</td>
      </tr>
  @endforelse
  </tbody>
</table>

<div class="d-flex justify-content-center mt-3 custom_pagination" data-route="{{ $route }}">
  {{ $all_memberships->links() }}
</div>