@extends('layouts/layoutMaster')

@section('title', __('Wallet Deposit Settings'))

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">{{ __('Wallet Deposit Settings') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.wallet.deposit.settings') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="deposit_amount_limitation_for_user">{{ __('Deposit Amount Limitation For User') }}</label>
                                <input type="number" name="deposit_amount_limitation_for_user" id="deposit_amount_limitation_for_user" class="form-control" value="{{ get_static_option('deposit_amount_limitation_for_user') ?? '' }}">
                                @error('deposit_amount_limitation_for_user')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">{{ __('Set maximum amount a user can deposit at once') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary mt-4">
                                <i class="ti ti-device-floppy me-1"></i> {{ __('Save Changes') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
