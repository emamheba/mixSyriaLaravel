@forelse($all_users as $index => $user)
    <tr>
        <td>{{ $all_users->firstItem() + $index }}</td>
        <td>
            <div class="d-flex justify-content-start align-items-center">
                <div class="avatar-wrapper">
                    <div class="avatar me-2">
                        @if (!empty($user->image))
                            <img src="{{ asset('storage/media/user/' . $user->image) }}" alt="Avatar"
                                class="rounded-circle">
                        @else
                            <span class="avatar-initial rounded-circle bg-label-info">
                                {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="d-flex flex-column">
                    <span class="text-truncate fw-semibold">{{ $user->first_name }}
                        {{ $user->last_name }}</span>
                    <small class="text-muted">{{ '@' . $user->username }}</small>
                </div>
            </div>
        </td>
        <td>{{ $user->email }}</td>
        <td>{{ $user->phone }}</td>
        <td>{{ $user->deleted_at->format('d M Y, h:i A') }}</td>
        <td>
            <div class="d-flex">
                <a href="#" class="btn btn-sm btn-icon btn-success me-2"
                    onclick="if(confirm('Are you sure to restore this user?')) { document.getElementById('restore-form-{{ $user->id }}').submit(); }">
                    <i class="ti ti-refresh"></i>
                </a>
                <form id="restore-form-{{ $user->id }}"
                    action="{{ route('admin.users.restore', $user->id) }}" method="POST"
                    style="display: none;">
                    @csrf
                </form>
                
                <a href="#" class="btn btn-sm btn-icon btn-danger"
                    onclick="if(confirm('Are you sure to permanently delete this user? This action cannot be undone!')) { document.getElementById('permanent-delete-form-{{ $user->id }}').submit(); }">
                    <i class="ti ti-trash"></i>
                </a>
                <form id="permanent-delete-form-{{ $user->id }}"
                    action="{{ route('admin.users.permanent.delete', $user->id) }}" method="POST"
                    style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center">No deleted users found</td>
    </tr>
@endforelse