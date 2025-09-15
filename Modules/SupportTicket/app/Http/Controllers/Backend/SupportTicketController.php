<?php

namespace Modules\SupportTicket\app\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\Backend\AdminNotification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;
use Modules\SupportTicket\app\Models\ChatMessage;
use Modules\SupportTicket\app\Models\Department;
use Modules\SupportTicket\app\Models\Ticket;
use Storage;

class SupportTicketController extends Controller
{
    //show and add ticket
    public function ticket(Request $request)
    {
        if($request->isMethod('post')){
            $user = User::where('id',$request->user)->first();
            $request->validate([
                'title'=> 'required|max:191',
                'department'=> 'required|max:191',
                'priority'=> 'required|max:191',
                'user'=> 'required',
                'description'=> 'required',
            ]);

            // create ticket for specific user
            $ticket = Ticket::create([
                'department_id'=>$request->department,
                'admin_id'=> Auth::guard('admin')->check() ? Auth::guard('admin')->user()->id : NULL,
                'user_id'=> $request->user,
                'title'=>$request->title,
                'priority'=>$request->priority,
                'description'=>$request->description,
            ]);

            // send notification to user
             user_notification($ticket->id,$user->id,'Ticket',__('New Support Ticket'));

            //Email to user
            try {
                $message = get_static_option('support_ticket_message') ?? __('Support Ticket Message');
                $message = str_replace(["@name","@ticket_id"],[$user->fullname,$ticket->id], $message);
                Mail::to($user->email)->send(new BasicMail([
                    'subject' => get_static_option('support_ticket_subject') ?? __('Support Ticket'),
                    'message' => $message
                ]));
            } catch (\Exception $e) {}

            return back()->with((FlashMsg::item_new('New Ticket Successfully Added')));
        }
        $tickets = Ticket::whereHas('user')->latest()->paginate(10);
        $departments = Department::all();
        $users = User::select(['id','first_name','last_name','username'])->get();
        return view('supportticket::backend.ticket.tickets',compact(['tickets','departments','users']));
    }

    //paginate
    public function paginate(Request $request)
    {
        if($request->ajax()){
            if(empty($request->string_search)){
                $tickets = Ticket::whereHas('user')->latest()->paginate(10);
            }else{
                $tickets = Ticket::where(function($q) use ($request){
                    $q->orWhere('id', $request->string_search)
                        ->orWhere('priority',$request->string_search)
                        ->orWhere('status',$request->string_search);
                })->latest()->paginate(10);
            }
            return $tickets->total() >= 1
                ? view('supportticket::backend.ticket.search-result', compact('tickets'))->render()
                : response()->json(['status'=>__('nothing')]);
        }
    }

    //search
    public function search_ticket(Request $request)
    {
        $tickets = Ticket::whereHas('user')->where(function($q) use ($request){
            $q->orWhere('id','LIKE', "%".strip_tags($request->string_search)."%")
                ->orWhere('priority','LIKE',"%".strip_tags($request->string_search)."%")
                ->orWhere('status','LIKE',"%".strip_tags($request->string_search)."%")
                ->orWhere('title','LIKE',"%".strip_tags($request->string_search)."%");
        })->latest()
            ->paginate(10);

        return $tickets->total() >= 1
            ? view('supportticket::backend.ticket.search-result', compact('tickets'))->render()
            : response()->json(['status'=>__('nothing')]);
    }

    //status change
    public function change_status($id)
    {
        $ticket = Ticket::select('status')->where('id',$id)->first();
        $ticket->status == 'open' ? $status = 'close' : $status = 'open';
        Ticket::where('id',$id)->update(['status'=>$status]);
        return redirect()->back()->with(FlashMsg::item_new(__('Status Successfully Changed')));
    }

    //delete ticket
    public function delete_ticket($id)
    {
        ChatMessage::where('ticket_id',$id)->delete();
        Ticket::find($id)->delete();
        return redirect()->back()->with(FlashMsg::error(__('Ticket Successfully Deleted')));
    }

    //delete bulk action
    public function bulk_action(Request $request){
        Ticket::whereIn('id',$request->ids)->delete();
        return redirect()->back()->with(FlashMsg::error(__('Selected Ticket Successfully Deleted')));
    }

    public function ticket_details(Request $request, $id)
    {
        $ticket_details = Ticket::with('user', 'message', 'get_ticket_latest_message')->where('id', $id)->first();
    
        if ($request->isMethod('post')) {
    
            if (empty($request->attachment) && empty($request->message)) {
                $request->validate([
                    'message' => 'required|max:10000',
                ]);
            }
    
            if (!empty($request->attachment) || empty($request->message)) {
                $request->validate([
                    'attachment' => 'mimes:jpg,jpeg,png,gif,pdf,svg,xlsx,xls,txt,webp',
                ]);
            }
    
            $imageName = null;
    
            if($attachment = $request->file('attachment')){
              $imageName = time().'-'.uniqid().'.'.$attachment->getClientOriginalExtension();
              $file_extension = $attachment->getClientOriginalExtension();
              $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
              
              if (in_array($file_extension, $image_extensions)) {
                  $processed_image = Image::make($attachment);
                  $processed_image->resize($processed_image->width(), $processed_image->height(), function ($constraint) {
                      $constraint->aspectRatio();
                  });
                  
                  $attachmentPath = 'tickets/images/' . $imageName;
                  Storage::disk('public')->put($attachmentPath, $processed_image->stream());
              } else {
                  $attachmentPath = 'tickets/files/' . $imageName;
                  $attachment->storeAs('tickets/files', $imageName, 'public');
              }
          }
    
            if ($ticket_details->admin_id === null) {
                $ticket_details->update([
                    'admin_id' => Auth::guard('admin')->user()->id,
                ]);
            }
    
            ChatMessage::create([
                'ticket_id' => $id,
                'message' => $request->message,
                'attachment' => $imageName ?? '',
                'notify' => $request->email_notify,
                'type' => 'admin',
            ]);
    
            user_notification($ticket_details->id, $ticket_details->user_id, 'Ticket Update', __('Ticket updates available'));
    
            if ($request->email_notify == 'on') {
                try {
                    $message = get_static_option('support_ticket_message_email_message') ?? __('Support Ticket Message Email Notify');
                    $message = str_replace(["@name", "@ticket_id"], [$ticket_details->client?->fullname ?? $ticket_details->freelancer?->fullname, $id], $message);
                    Mail::to($ticket_details->client?->email ?? $ticket_details->freelancer?->email)->send(new BasicMail([
                        'subject' => get_static_option('support_ticket_message_email_subject') ?? __('Support Ticket Message Email'),
                        'message' => $message
                    ]));
                } catch (\Exception $e) {
                }
            }
    
            return back();
        }
    
        AdminNotification::where('identity', $id)->update(['is_read' => 1]);
    
        return !empty($ticket_details)
            ? view('supportticket::backend.ticket.details', compact('ticket_details'))
            : back();
    }
    

}
