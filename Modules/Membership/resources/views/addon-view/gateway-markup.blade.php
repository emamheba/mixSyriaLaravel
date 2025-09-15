<div class="modal fade" id="paymentGatewayModal" tabindex="-1" aria-labelledby="paymentGatewayModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('user.membership.buy') }}" method="post" enctype="multipart/form-data">
            <input type="hidden" name="membership_id" id="membership_id">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    @if(Auth::guard('web')->check())
                       <h4>{{ __('Buy membership') }}</h4>
                    @else
                        <x-notice.general-notice :description="__('Notice: Please login as a user to buy a membership.')" />
                    @endif
                </div>
                <div class="modal-body">
                    <div class="confirm-payment payment-border">
                        <div class="single-checkbox">
                            <div class="checkbox-inlines">
                                <label class="checkbox-label load_after_login" for="choose">
                                    @if (Auth::check() && Auth::user()->user_wallet?->balance > 0)
                                        @if (moduleExists('Wallet'))
                                             {!! \App\Helpers\PaymentGatewayRenderHelper::renderWalletForm() !!}
                                        @endif
                                        <span class="wallet-balance mt-2 d-block">{{ __('Wallet Balance:') }}
                                            <strong class="main-balance">{{ float_amount_with_currency_symbol(Auth::user()->user_wallet?->balance) }}</strong></span>
                                        <br>
                                        <span class="display_balance"></span>
                                        <br>
                                        <span class="deposit_link"></span>
                                    @endif
                                    {!! \App\Helpers\PaymentGatewayRenderHelper::renderPaymentGatewayForForm() !!}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="btn-wrapper">
                        <button type="button" class="red-global-close-btn" data-bs-dismiss="modal">{{ __('Close') }}</button>
                        @if (Auth::guard('web')->check())
                            <button type="submit" class="red-global-btn buy_membership" id="confirm_buy_membership_load_spinner">
                                {{ __('Buy Now') }} <span id="buy_membership_load_spinner"></span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
