@forelse($all_reports as $report)
  <tr>
    <td class="text-center">
      <div class="form-check form-check-sm">
        <input class="form-check-input report-checkbox" type="checkbox" value="{{ $report->id }}">
      </div>
    </td>
    <td>{{ $report->id }}</td>
    <td>
      @if($report->listing)
        <a href="{{ route('admin.listings.details', $report->listing_id) }}" class="fw-semibold">
          {{ Str::limit($report->listing->title, 30) }}
        </a>
      @else
        <span class="text-muted">{{ __('Listing Deleted') }}</span>
      @endif
    </td>
    <td>
      @if($report->user)
        <div class="d-flex align-items-center">
          <div class="avatar me-2">
            <span class="avatar-initial rounded-circle bg-label-primary">
              {{ substr($report->user->name, 0, 1) }}
            </span>
          </div>
          <span>{{ $report->user->name }}</span>
        </div>
      @else
        <span class="text-muted">{{ __('User Deleted') }}</span>
      @endif
    </td>
    <td>
      @if($report->reason)
        <span class="badge bg-label-warning">{{ $report->reason->reason }}</span>
      @else
        <span class="text-muted">{{ __('Reason Deleted') }}</span>
      @endif
    </td>
    <td>{{ Str::limit($report->description, 50) }}</td>
    <td>{{ $report->created_at->format('M d, Y') }}</td>
    <td>
      <div class="dropdown">
        <button type="button" class="btn btn-sm dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown">
          <i class="ti ti-dots-vertical"></i>
        </button>
        <div class="dropdown-menu">
          <a class="dropdown-item" href="{{ route('admin.listings.details', $report->listing_id) }}">
            <i class="ti ti-eye me-1"></i> {{ __('View Listing') }}
          </a>
          <form action="{{ route('admin.listing.report.delete', $report->id) }}" method="post" class="d-inline">
            @csrf
            <button type="submit" class="dropdown-item" onclick="return confirm('{{ __('Are you sure you want to delete this report?') }}')">
              <i class="ti ti-trash me-1"></i> {{ __('Delete') }}
            </button>
          </form>
        </div>
      </div>
    </td>
  </tr>
@empty
  <tr>
    <td colspan="8" class="text-center">{{ __('No reports found.') }}</td>
  </tr>
@endforelse
