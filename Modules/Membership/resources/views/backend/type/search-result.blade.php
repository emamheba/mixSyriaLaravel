{{-- resources/views/membership/backend/type/search-result.blade.php --}}
<table class="table border-top">
  <thead>
      <tr>
          <th>#</th>
          <th>{{ __('Type') }}</th>
          <th>{{ __('Validity (days)') }}</th>
          <th>{{ __('Actions') }}</th>
      </tr>
  </thead>
  <tbody>
      @forelse($all_types as $index => $type)
          <tr>
              <td>{{ $all_types->firstItem() + $index }}</td>
              <td>{{ $type->type }}</td>
              <td>{{ $type->validity }}</td>
              <td>
                  <div class="dropdown">
                      <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                          <i class="ti ti-dots-vertical"></i>
                      </button>
                      <div class="dropdown-menu">
                          <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#editTypeModal{{ $type->id }}">
                              <i class="ti ti-pencil me-1"></i> {{ __('Edit') }}
                          </a>
                          <a class="dropdown-item text-danger" href="#" onclick="if(confirm('{{ __('Are you sure to delete this type?') }}')) { document.getElementById('delete-type-{{ $type->id }}').submit(); }">
                              <i class="ti ti-trash me-1"></i> {{ __('Delete') }}
                          </a>
                          <form id="delete-type-{{ $type->id }}" action="{{ route('admin.membership.type.delete', $type->id) }}" method="POST" style="display:none;">
                              @csrf
                          </form>
                      </div>
                  </div>
              </td>
          </tr>
      @empty
          <tr>
              <td colspan="4" class="text-center">{{ __('No membership types found') }}</td>
          </tr>
      @endforelse
  </tbody>
</table>

{{-- Pagination --}}
<div class="d-flex justify-content-center mt-3">
  {{ $all_types->links() }}
</div>