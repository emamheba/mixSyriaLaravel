<table id="districts-table" class="table border-top">
  <thead>
    <tr>
      <th width="80">{{ __('ID') }}</th>
      <th>{{ __('District') }}</th>
      <th>{{ __('City') }}</th>
      <th>{{ __('State') }}</th>
      <th width="100">{{ __('Status') }}</th>
      <th width="120">{{ __('Actions') }}</th>
    </tr>
  </thead>
  <tbody>
    @forelse($all_districts as $district)
    <tr>
      <td><span class="badge bg-label-secondary">{{ $district->id }}</span></td>
      <td>
        <div class="d-flex align-items-center">
            <div class="avatar avatar-sm me-3"><span class="avatar-initial bg-label-primary rounded"><i class="ti ti-building-skyscraper"></i></span></div>
            <div>
              <h6 class="mb-0">{{ $district->district }}</h6>
<small class="text-muted">
  {{ __('Created') }}:
  @if($district->created_at)
    {{ $district->created_at->format('M d, Y') }}
  @else
    {{ __('N/A') }} {{-- يمكنك تغيير "N/A" إلى أي نص تفضله عند عدم توفر التاريخ --}}
  @endif
</small>            </div>
        </div>
      </td>
      <td>{{ $district->city->city ?? __('N/A') }}</td>
      <td>{{ $district->state->state ?? __('N/A') }}</td>
      <td>
        <form action="{{ route('admin.district.status', $district->id) }}" method="POST">
          @csrf
          <button type="submit" class="btn btn-sm {{ $district->status == 1 ? 'btn-success' : 'btn-warning' }} status-btn">
            <i class="ti ti-{{ $district->status == 1 ? 'check' : 'clock' }}"></i>
          </button>
        </form>
      </td>
      <td>
        <div class="d-flex">
          <a class="btn btn-sm btn-icon edit-district" href="#" 
             data-bs-toggle="modal" data-bs-target="#editDistrictModal"
             data-district='@json($district)'>
            <i class="ti ti-pencil"></i>
          </a>
          <form id="delete-form-{{ $district->id }}" action="{{ route('admin.delete.district', $district->id) }}" method="POST">
            @csrf
            <a href="#" class="btn btn-sm btn-icon text-danger delete-item" data-form-id="delete-form-{{ $district->id }}">
              <i class="ti ti-trash"></i>
            </a>
          </form>
        </div>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="6" class="text-center py-5">
        <h5 class="mb-1">{{ __('No districts found') }}</h5>
      </td>
    </tr>
    @endforelse
  </tbody>
</table>

@if($all_districts->hasPages())
<div class="card-footer d-flex justify-content-end">
  {{ $all_districts->links() }}
</div>
@endif