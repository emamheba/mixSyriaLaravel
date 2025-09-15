<table class="table">
    <thead class="table-head-light">
    <tr>
        <th>{{ __('Payment Gateway') }}</th>
        <th>{{ __('Payment Status') }}</th>
        <th>{{ __('Deposit Amount') }}</th>
        <th>{{ __('Deposit Date') }}</th>
    </tr>
    </thead>
    <tbody>
    @forelse($user_wallet_histories as $history)
        <tr>
            <td>
                @if($history->payment_gateway == 'manual_payment')
                    {{ ucfirst(str_replace('_',' ',$history->payment_gateway)) }}
                @else
                    {{ $history->payment_gateway == 'authorize_dot_net' ? __('Authorize.Net') : ucfirst($history->payment_gateway) }}
                @endif
            </td>
            <td>
                @if($history->payment_status == '' || $history->payment_status == 'cancel')
                    <span class="status cancel-status">{{ __('Cancel') }}</span>
                @elseif($history->payment_status == 'pending')
                    <span class="status pending-status">{{ __('Pending') }}</span>
                @elseif($history->payment_status == 'complete')
                    <span class="status accepted-status">{{ __('Completed') }}</span>
                @endif
            </td>
            <td>{{ float_amount_with_currency_symbol($history->amount) }}</td>
            <td>{{ $history->created_at }}</td>
        </tr>
        @empty
            <tr class="text-center">
                <td colspan="4">{{ __('No wallet history found.') }}</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="deposit-history-pagination mb-4">
    <x-pagination.laravel-paginate :allData="$user_wallet_histories"/>
</div>
