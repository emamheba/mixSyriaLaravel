{{-- resources/views/membership/backend/enquiry/search-result.blade.php --}}
<table class="table border-top">
  <thead>
      <tr>
          @can('enquiry-form-bulk-delete')
              <th>
                  <div class="form-check">
                      <input class="form-check-input select-all" type="checkbox" id="selectAll">
                  </div>
              </th>
          @endcan
          <th>#</th>
          <th>{{ __('Name') }}</th>
          <th>{{ __('Email') }}</th>
          <th>{{ __('Phone') }}</th>
          <th>{{ __('Message') }}</th>
          <th>{{ __('Date') }}</th>
          <th>{{ __('Actions') }}</th>
      </tr>
  </thead>
  <tbody>
      @foreach($all_enquiries as $enquiry)
      <tr>
          @can('enquiry-form-bulk-delete')
              <td>
                  <div class="form-check">
                      <input class="form-check-input select-checkbox" type="checkbox" value="{{ $enquiry->id }}">
                  </div>
              </td>
          @endcan
          <td>{{ $enquiry->id }}</td>
          <td>
              <div class="d-flex align-items-center">
                  <div class="avatar me-2">
                      <span class="avatar-initial rounded bg-label-primary">
                          {{ Str::substr($enquiry->name, 0, 1) }}
                      </span>
                  </div>
                  <div>
                      <span class="fw-semibold">{{ $enquiry->name }}</span>
                  </div>
              </div>
          </td>
          <td>{{ $enquiry->email }}</td>
          <td>{{ $enquiry->phone }}</td>
          <td>
              <span class="text-truncate d-inline-block" style="max-width: 150px;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $enquiry->message }}">
                  {{ Str::limit($enquiry->message, 30) }}
              </span>
          </td>
          <td>{{ $enquiry->created_at->format('M d, Y') }}</td>
          <td>
              <div class="dropdown">
                  <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                      <i class="ti ti-dots-vertical"></i>
                  </button>
                  <div class="dropdown-menu">
                      @can('enquiry-form-delete')
                          <a class="dropdown-item" href="#" 
                             onclick="if(confirm('{{ __('Are you sure you want to delete this enquiry?') }}')){ document.getElementById('delete-form-{{ $enquiry->id }}').submit(); }">
                              <i class="ti ti-trash me-1"></i> {{ __('Delete') }}
                          </a>
                          <form id="delete-form-{{ $enquiry->id }}" action="{{ route('admin.enquiry.form.delete', $enquiry->id) }}" method="POST" style="display: none;">
                              @csrf
                          </form>
                      @endcan
                  </div>
              </div>
          </td>
      </tr>
      @endforeach
  </tbody>
</table>

<div class="d-flex justify-content-center mt-3">
  {{ $all_enquiries->links() }}
</div>
