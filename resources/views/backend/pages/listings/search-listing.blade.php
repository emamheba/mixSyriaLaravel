@if($all_listings->total() > 0)
<table class="dataTablesExample table">
    <thead>
    @can('user-listing-bulk-delete')
        <th class="no-sort">
            <div class="mark-all-checkbox">
                <input type="checkbox" id="select-all-checkbox" class="form-check-input">
            </div>
        </th>
    @endcan
    <th>{{ __('ID') }}</th>
    <th>{{ __('Image') }}</th>
    <th>{{ __('Title') }}</th>
    <th>{{ __('Category') }}</th>
    @if(empty(get_static_option('google_map_settings_on_off')))
        <th>{{ __('Country') }}</th>
    @endif
    <th>{{ __('Price') }}</th>
    <th>{{ __('User Name') }}</th>
    <th>{{ __('Create Date') }}</th>
    <th>{{ __('Published Date') }}</th>
    <th>{{ __('Publishing Status') }}</th>
    <th>
        {{ __('Status') }}
        @can('user-listing-approved')
            <span>
                <x-status.all-status-change :url="route('admin.listings.user.all.approved')"/>
            </span>
        @endcan
    </th>
    <th>{{ __('Action') }}</th>
    </thead>
    <tbody>
    @foreach($all_listings as $data)
        <tr>
            @can('user-listing-bulk-delete')
                <td>
                    <input type="checkbox" class="bulk-item-checkbox form-check-input" value="{{ $data->id }}" data-id="{{ $data->id }}">
                </td>
            @endcan

            <!-- ID -->
            <td>{{ $data->id }}</td>

            <!-- Image -->
            <td>
                {!! render_image_markup_by_attachment_id($data->image,'','thumb') !!}
            </td>

            <!-- Title -->
            <td>{{ $data->title }}</td>

            <!-- Category -->
            <td>{{ optional($data->category)->name }}</td>

            <!-- Country -->
            @if(empty(get_static_option('google_map_settings_on_off')))
                <td>{{ optional($data->country)->country }}</td>
            @endif

            <!-- Price -->
            <td>{{ float_amount_with_currency_symbol($data->price) }}</td>

            <!-- User Name -->
            <td>{{ optional($data->user)->fullname }}</td>

            <!-- Create Date -->
            <td><strong class="subCap">{{ $data->created_at->diffForHumans() }}</strong></td>

            <!-- Published Date -->
            <td>
                @if($data->published_at)
                    {{ \Carbon\Carbon::parse($data->published_at)->format('F j, Y') }}
                @else
                    {{ __('Not published') }}
                @endif
            </td>

            <!-- Publishing Status -->
            <td>
                @if($data->is_published === 1)
                    <span class="alert alert-success">{{ __('Published') }}</span>
                @else
                    <span class="alert alert-warning">{{ __('Unpublished') }}</span>
                @endif

                @can('user-listing-published-status-change')
                    <span class="my-2">
                        <x-status.admin-listing-published-change :url="route('admin.listings.published.status.change', $data->id)"/>
                    </span>
                @endcan
            </td>

            <!-- Status -->
            <td>
                @if($data->status == 1)
                    <span class="alert alert-success">{{ __('Approved') }}</span>
                @else
                    <span class="alert alert-warning">{{ __('Pending') }}</span>
                @endif

                @can('user-listing-status-change')
                    <span class="my-2">
                        <x-status.status-change :url="route('admin.listings.status.change', $data->id)"/>
                    </span>
                @endcan
            </td>

            <!-- Action -->
            <td>
                <x-icon.view-icon :url="route('admin.listings.details', $data->id)"/>
                @can('user-listing-delete')
                    <x-popup.delete-popup :url="route('admin.listings.delete', $data->id)"/>
                @endcan
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<!-- Pagination -->
<div class="custom_pagination mt-5 d-flex justify-content-end">
    {{ $all_listings->links() }}
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // تحديد الكل
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const itemCheckboxes = document.querySelectorAll('.bulk-item-checkbox');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            itemCheckboxes.forEach(function(checkbox) {
                checkbox.checked = isChecked;
            });
        });
    }
    
    // تحديث حالة زر تحديد الكل عند تغيير العناصر الفردية
    itemCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.bulk-item-checkbox:checked').length;
            const totalCount = itemCheckboxes.length;
            
            if (selectAllCheckbox) {
                if (checkedCount === 0) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                } else if (checkedCount === totalCount) {
                    selectAllCheckbox.checked = true;
                    selectAllCheckbox.indeterminate = false;
                } else {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = true;
                }
            }
        });
    });
});
</script>

@else
    <div class="alert alert-warning text-center mt-2">
        {{ __('No listings found') }}
    </div>
@endif