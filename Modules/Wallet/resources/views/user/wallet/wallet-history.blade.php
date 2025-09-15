@extends('layouts/layoutMaster')

@section('title', __('Wallet History'))

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-wallet.scss'])
<style>
    .wallet-balance-card {
        background: linear-gradient(45deg, #2b5876, #4e4376);
        color: white;
        border-radius: 1rem;
    }
    .transaction-badge {
        font-size: 0.85em;
        padding: 0.35em 0.7em;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Balance Card -->
    <div class="card mb-4 wallet-balance-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title text-white mb-2">{{ __('Available Balance') }}</h5>
                    <h2 class="text-white mb-0">{{ float_amount_with_currency_symbol($total_wallet_balance ?? 0) }}</h2>
                </div>
                <button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#paymentGatewayModal">
                    <i class="ti ti-wallet me-2"></i>{{ __('Deposit to Wallet') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="card">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center">
            <h5 class="card-title mb-2 mb-md-0">{{ __('Transaction History') }}</h5>
            <div class="w-50 w-md-25">
                <input type="text"
                       id="string_search"
                       class="form-control"
                       placeholder="{{ __('Search by date...') }}"
                       aria-label="Search transactions">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Transaction ID') }}</th>
                        <th class="text-end">{{ __('Amount') }}</th>
                        <th>{{ __('Payment Method') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($user_wallet_histories as $history)
                    <tr>
                        <td>{{ $history->created_at->format('d M Y') }}</td>
                        <td>#{{ $history->id }}</td>
                        <td class="text-end fw-medium">{{ float_amount_with_currency_symbol($history->amount) }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $history->payment_gateway)) }}</td>
                        <td>
                            @if($history->payment_status === 'pending')
                                <span class="badge transaction-badge bg-label-warning">{{ __('Pending') }}</span>
                            @else
                                <span class="badge transaction-badge bg-label-success">{{ __('Completed') }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($history->manual_payment_image)
                            <a href="{{ asset('storage/uploads/deposit_payment_attachments/'.$history->manual_payment_image) }}"
                               class="btn btn-icon btn-outline-secondary btn-sm"
                               target="_blank"
                               data-bs-toggle="tooltip"
                               title="{{ __('View Attachment') }}">
                                <i class="ti ti-file-text"></i>
                            </a>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">{{ __('No transactions found') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($user_wallet_histories->hasPages())
        <div class="card-footer py-3">
            {{ $user_wallet_histories->links() }}
        </div>
        @endif
    </div>
</div>

<x-frontend.payment-gateway.gateway-markup :title="__('Deposit to Wallet')"/>
@endsection

@section('page-script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Real-time Search
        const searchInput = document.getElementById('string_search');
        searchInput.addEventListener('keyup', function() {
            const searchQuery = this.value;

            fetch(`?string_search=${encodeURIComponent(searchQuery)}`)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newBody = doc.querySelector('tbody').innerHTML;
                    document.querySelector('tbody').innerHTML = newBody;
                    initTooltips();
                });
        });

        // Pagination Handling
        document.querySelector('.pagination').addEventListener('click', function(e) {
            e.preventDefault();
            if (e.target.tagName === 'A') {
                fetch(e.target.href)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newBody = doc.querySelector('tbody').innerHTML;
                        const newPagination = doc.querySelector('.pagination').innerHTML;
                        document.querySelector('tbody').innerHTML = newBody;
                        document.querySelector('.pagination').innerHTML = newPagination;
                        initTooltips();
                    });
            }
        });

        // Initialize Bootstrap tooltips
        function initTooltips() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        }
        initTooltips();
    });
</script>
@endsection
