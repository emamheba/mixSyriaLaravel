{{-- resources/views/wallet/backend/wallet-lists/search-result.blade.php --}}
<table class="table table-bordered">
  <thead class="table-light">
    <tr>
      <th>{{ __('ID') }}</th>
      <th>{{ __('User') }}</th>
      <th>{{ __('Balance') }}</th>
      <th>{{ __('Status') }}</th>
      <th>{{ __('Actions') }}</th>
    </tr>
  </thead>
  <tbody>
    @forelse($wallet_lists as $wallet)
    <tr>
      <td>{{ $wallet->id }}</td>
      <td>
        <div class="d-flex justify-content-start align-items-center">
          <div class="avatar-wrapper">
            <div class="avatar me-2">
              <span class="avatar-initial rounded-circle bg-label-info">
                {{ substr($wallet->user->first_name ?? 'U', 0, 1) }}
              </span>
            </div>
          </div>
          <div class="d-flex flex-column">
            <span class="fw-semibold">{{ $wallet->user->first_name ?? '' }} {{ $wallet->user->last_name ?? '' }}</span>
            <small class="text-muted">{{ $wallet->user->email ?? __('No email') }}</small>
          </div>
        </div>
      </td>
      <td>{{ number_format($wallet->balance, 2) }}</td>
      <td>
        @if($wallet->status == 1)
        <span class="badge bg-label-success">{{ __('Active') }}</span>
        @else
        <span class="badge bg-label-danger">{{ __('Inactive') }}</span>
        @endif
      </td>
      <td>
        <form action="{{ route('admin.wallet.status', $wallet->id) }}" method="post">
          @csrf
          <button type="submit" class="btn btn-sm {{ $wallet->status == 1 ? 'btn-label-danger' : 'btn-label-success' }}">
            {{ $wallet->status == 1 ? __('Deactivate') : __('Activate') }}
          </button>
        </form>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="5" class="text-center">{{ __('No wallets found') }}</td>
    </tr>
    @endforelse
  </tbody>
</table>

<div class="d-flex justify-content-end mt-3">
  <nav aria-label="Page navigation">
    {{ $wallet_lists->links('pagination::bootstrap-4', ['class' => 'pagination-wallet']) }}
  </nav>
</div>
