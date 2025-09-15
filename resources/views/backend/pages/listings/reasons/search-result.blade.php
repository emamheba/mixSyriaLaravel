@if($all_reasons->count() > 0)
  @foreach($all_reasons as $reason)
    <tr>
      <td>
        <input type="checkbox" class="form-check-input bulk-checkbox" value="{{ $reason->id }}">
      </td>
      <td>{{ $reason->id }}</td>
      <td>{{ $reason->title }}</td>
      <td>
        <button type="button"
                class="btn btn-sm btn-warning edit_reason_btn"
                data-bs-toggle="modal"
                data-bs-target="#editReasonModal"
                data-id="{{ $reason->id }}"
                data-title="{{ $reason->title }}">
          Edit
        </button>
        <form action="{{ route('admin.report.reason.delete', $reason->id) }}"
              method="POST"
              class="d-inline-block"
              onsubmit="return confirm('Are you sure you want to delete this reason?');">
          @csrf
          <button type="submit" class="btn btn-sm btn-danger">
            Delete
          </button>
        </form>
      </td>
    </tr>
  @endforeach
  <tr>
    <td colspan="4">
      {{ $all_reasons->links() }}
    </td>
  </tr>
@else
  <tr>
    <td colspan="4">No reasons found!</td>
  </tr>
@endif
