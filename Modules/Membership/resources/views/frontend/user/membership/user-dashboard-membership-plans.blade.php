<div class="container py-5">
  <h3 class="text-center mb-4">Membership Plans</h3>
  <p class="text-center text-muted">Choose the best plan that fits your needs.</p>
  <div class="row g-4">
      @php
          $memberships = \Modules\Membership\app\Models\Membership::where('status', 1)->take(3)->get();
          $user_current_membership = \Modules\Membership\app\Models\UserMembership::where('user_id', auth()->id())
              ->whereDate('expire_date', '>', now())
              ->latest()->first();
      @endphp
      
      <div class="row">
        @foreach($memberships as $membership)
          <div class="col-xl-4 col-md-6 mb-4">
            <div class="card h-100 border-0 shadow-sm @if(!empty($user_current_membership) && $user_current_membership->membership_id === $membership->id) border border-3 border-primary @endif">
              <!-- صورة البطاقة -->
              <div class="card-img-top text-center pt-4">
                <img src="{{asset('assets/img/illustrations/page-pricing-standard.png')}}" alt="Standard Image" height="120">
              </div>
              <!-- محتوى البطاقة -->
              <div class="card-body text-center">
                <h4 class="card-title text-capitalize mb-2">{{ $membership->title }}</h4>
                <p class="card-text mb-3">{{ $membership->description ?? 'أفضل خطة لاحتياجاتك' }}</p>
                <div class="pricing mb-3">
                  <span class="h4 text-primary">{{ float_amount_with_currency_symbol($membership->price) }}</span>
                  <small class="text-muted">/{{ $membership->membership_type?->type }}</small>
                </div>
                <ul class="list-unstyled mb-4">
                  @foreach($membership->features as $feature)
                    <li class="mb-2">
                      @if($feature->status == 'on')
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                      @else
                        <i class="bi bi-x-circle text-muted me-2"></i>
                      @endif
                      {{ $feature->feature }}
                    </li>
                  @endforeach
                </ul>
              </div>
              <!-- ذيل البطاقة مع زر الإجراء -->
              <div class="card-footer bg-transparent border-top-0 pb-4">
                @if($membership->price == 0)
                  @if(empty($user_current_membership))
                    <form action="{{ route('user.membership.buy') }}" method="post">
                      @csrf
                      <input type="hidden" name="membership_id" value="{{ $membership->id }}">
                      <input type="hidden" name="price" value="{{ $membership->price }}">
                      <input type="hidden" name="selected_payment_gateway" value="Trial">
                      <button type="submit" class="btn btn-outline-success w-100">ابدأ الآن</button>
                    </form>
                  @else
                    <a href="{{ url('/user-register') }}" class="btn btn-outline-success w-100">الخطة الحالية</a>
                  @endif
                @else
                  @php
                    if(empty($user_current_membership)){
                      $buttonText = __('Buy Now');
                      $modalTarget = (Auth::check() && Auth::guard('web')->user()) ? '#paymentGatewayModal' : '#loginModal';
                    } else {
                      if($user_current_membership->membership_id === $membership->id){
                        $buttonText = __('Current Plan');
                        $modalTarget = null;
                      } else {
                        $buttonText = __('Upgrade Now');
                        $modalTarget = (Auth::check() && Auth::guard('web')->user()) ? '#paymentGatewayModal' : '#loginModal';
                      }
                    }
                  @endphp
                  @if($modalTarget)
                  <button class="btn btn-primary w-100 choose_membership_plan"
                          data-bs-toggle="modal"
                          data-id="{{ $membership->id }}"
                          data-price="{{ $membership->price }}"
                          data-bs-target="{{ $modalTarget }}">
                    {{ $buttonText }}
                  </button>
                  @else
                    <button class="btn btn-primary w-100" disabled>
                      {{ $buttonText }}
                    </button>
                  @endif
                @endif
              </div>
            </div>
          </div>
        @endforeach
      </div>
       <!-- End row -->
  </div>
</div>
