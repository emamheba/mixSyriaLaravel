<div class="container py-4">
  <h3 class="text-center mb-4">Membership Payment History</h3>
  <div class="card shadow-sm">
      <div class="card-body">
          <div class="table-responsive">
              <table class="table table-striped table-hover align-middle text-center">
                  <thead class="table-dark">
                      <tr>
                          <th>{{ __('Plan') }}</th>
                          <th>{{ __('Amount') }}</th>
                          <th>{{ __('Date') }}</th>
                          <th>{{ __('Payment Method') }}</th>
                          <th>{{ __('Payment Status') }}</th>
                          <th>{{ __('Action') }}</th>
                      </tr>
                  </thead>
                  <tbody>
                      @foreach($all_memberships as $membership)
                          <tr>
                              <td><strong>{{ $membership->membership?->membership_type?->type }}</strong></td>
                              <td class="text-primary">{{ float_amount_with_currency_symbol($membership->price) }}</td>
                              <td>{{ $membership->created_at->format('Y-m-d') ?? '' }}</td>
                              <td>
                                  <span class="badge bg-secondary">
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
                                      <span class="badge bg-warning">{{ __('Pending') }}</span>
                                  @else
                                      <span class="badge bg-success">{{ ucfirst($membership->payment_status) }}</span>
                                  @endif
                              </td>
                              <td>
                                  <button class="btn btn-sm btn-outline-primary show_membership_payment_history_modal" 
                                      data-bs-toggle="modal"
                                      data-bs-target="#user_membership_payment_history_modal"
                                      data-membership_history_id="{{ $membership->id }}"
                                      data-membership_type="{{ $membership->membership?->membership_type?->type }}"
                                      data-membership_purchase_date_history="{{ $membership->created_at->format('Y-m-d') ?? '' }}"
                                      data-membership_expire_date_history="{{ Carbon\Carbon::parse($membership->expire_date)->format('Y-m-d') }}"
                                      data-listing_limit="{{ $membership->listing_limit }}"
                                      data-gallery_images="{{ $membership->gallery_images }}"
                                      data-featured_listing="{{ $membership->featured_listing }}"
                                      data-business_hour="{{ $membership->business_hour }}"
                                      data-enquiry_form="{{ $membership->enquiry_form }}"
                                      data-membership_badge="{{ $membership->membership_badge }}">
                                      <i class="fa-solid fa-eye"></i> View
                                  </button>
                              </td>
                          </tr>
                      @endforeach
                  </tbody>
              </table>
          </div>
      </div>
  </div>
  <div class="d-flex justify-content-center mt-4">
      <x-pagination.laravel-paginate :allData="$all_memberships"/>
  </div>
</div>
