{{-- resources/views/wallet/backend/wallet-history/search-result.blade.php --}}
<table class="table table-bordered">
  <thead class="table-light">
    <tr>
      <th>{{ __('ID') }}</th>
      <th>{{ __('User') }}</th>
      <th>{{ __('Amount') }}</th>
      <th>{{ __('Payment Gateway') }}</th>
      <th>{{ __('Payment Status') }}</th>
      <th>{{ __('Date') }}</th>
      <th>{{ __('Actions') }}</th>
    </tr>
  </thead>
  <tbody>
    @forelse($wallet_history_lists as $history)
    <tr>
      <td>{{ $history->id }}</td>
      <td>
        <div class="d-flex justify-content-start align-items-center">
          <div class="avatar-wrapper">
            <div class="avatar me-2">
              <span class="avatar-initial rounded-circle bg-label-info">
                {{ substr($history->user->first_name ?? 'U', 0, 1) }}
              </span>
            </div>
          </div>
          <div class="d-flex flex-column">
            <span class="fw-semibold">{{ $history->user->first_name ?? '' }} {{ $history->user->last_name ?? '' }}</span>
            <small class="text-muted">{{ $history->user->email ?? __('No email') }}</small>
          </div>
        </div>
      </td>
      <td>{{ number_format($history->amount, 2) }}</td>
      <td>
        @if($history->payment_gateway == 'added_by_admin')
          <span class="badge bg-label-primary">{{ __('Added By Admin') }}</span>
        @elseif($history->payment_gateway == 'manual_payment')
          <span class="badge bg-label-info">{{ __('Manual Payment') }}</span>
        @else
          <span class="badge bg-label-secondary">{{ ucfirst($history->payment_gateway) }}</span>
        @endif
      </td>
      <td>
        @if($history->payment_status == 'complete')
          <span class="badge bg-label-success">{{ __('Complete') }}</span>
        @elseif($history->payment_status == 'pending')
          <span class="badge bg-label-warning">{{ __('Pending') }}</span>
        @else
          <span class="badge bg-label-secondary">{{ ucfirst($history->payment_status) }}</span>
        @endif
      </td>
      <td>{{ \Carbon\Carbon::parse($history->created_at)->format('M d, Y H:i') }}</td>
      <td>
        @if($history->payment_status == 'pending')
          <form action="{{ route('admin.wallet.history.status', $history->id) }}" method="post">
            @csrf
            <button type="submit" class="btn btn-sm btn-success">{{ __('Complete') }}</button>
          </form>
        @else
          <button type="button" class="btn btn-sm btn-secondary" disabled>{{ __('Completed') }}</button>
        @endif
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="7" class="text-center">{{ __('No transaction history found') }}</td>
    </tr>
    @endforelse
  </tbody>
</table>

<div class="d-flex justify-content-end mt-3">
  <nav aria-label="Page navigation">
    {{ $wallet_history_lists->links('pagination::bootstrap-4', ['class' => 'pagination-history']) }}
  </nav>
</div>