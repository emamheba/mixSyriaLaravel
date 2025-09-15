@foreach($tickets as $ticket)
    <tr>
        <td>
            <div class="bulk-checkbox-wrapper">
                <input type="checkbox" class="bulk-checkbox" name="bulk_delete[]" value="{{$ticket->id}}">
            </div>
        </td>
        <td>{{$ticket->id}}</td>
        <td>{{$ticket->title}}</td>
        <td>
            @if($ticket->user)
                {{$ticket->user->first_name}} {{$ticket->user->last_name}}
            @else
                {{__('User Deleted')}}
            @endif
        </td>
        <td>
            @php
                $department = \Modules\SupportTicket\app\Models\Department::find($ticket->department_id);
            @endphp
            {{$department ? $department->name : __('Not Found')}}
        </td>
        <td>
            <span class="badge
                @if($ticket->priority == 'high')
                    bg-danger
                @elseif($ticket->priority == 'medium')
                    bg-warning
                @else
                    bg-success
                @endif
            ">
                {{ucfirst($ticket->priority)}}
            </span>
        </td>
        <td>
            <span class="badge
                @if($ticket->status == 'open')
                    bg-primary
                @else
                    bg-secondary
                @endif
            ">
                {{ucfirst($ticket->status)}}
            </span>
        </td>
        <td>{{date('d M Y', strtotime($ticket->created_at))}}</td>
        <td>
            <a href="{{route('admin.ticket.details', $ticket->id)}}" class="btn btn-primary btn-sm mb-2">
                <i class="ti ti-eye"></i>
            </a>
            <a href="#" class="btn btn-danger btn-sm mb-2 delete-ticket" data-bs-toggle="modal" data-bs-target="#delete_ticket_modal" data-id="{{$ticket->id}}">
                <i class="ti ti-trash"></i>
            </a>
            <form action="{{route('admin.ticket.status', $ticket->id)}}" method="post" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm {{$ticket->status == 'open' ? 'btn-danger' : 'btn-success'}}">
                    {{$ticket->status == 'open' ? __('Close') : __('Open')}}
                </button>
            </form>
        </td>
    </tr>
@endforeach
<tr>
    <td colspan="9">
        <div class="pagination-wrapper">
            {{$tickets->links()}}
        </div>
    </td>
</tr>

<!-- Delete Ticket Modal -->
<div class="modal fade" id="delete_ticket_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('Delete Ticket')}}</h5>
                <button type="button" class="close" data-bs-dismiss="modal"><span>×</span></button>
            </div>
            <form action="" id="delete_ticket_form" method="post">
                @csrf
                <div class="modal-body">
                    <p>{{__('Are you sure you want to delete this ticket?')}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-danger">{{__('Delete')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.delete-ticket').on('click', function() {
            var ticketId = $(this).data('id');
            $('#delete_ticket_form').attr('action', '{{route("admin.ticket.delete", "")}}/'+ticketId);
        });
    });
</script>