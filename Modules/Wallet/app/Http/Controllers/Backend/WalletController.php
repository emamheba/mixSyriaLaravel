<?php

namespace Modules\Wallet\app\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use App\Http\Controllers\Controller;
use App\Mail\BasicMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Modules\Wallet\app\Models\Wallet;
use Modules\Wallet\app\Models\WalletHistory;

class WalletController extends Controller
{

    public function deposit_settings(Request $request)
    {
        $request->validate([
            'deposit_amount_limitation_for_user'=>'numeric|gt:0',
        ],
            [
                'deposit_amount_limitation_for_user.numeric'=>'Please enter only numeric value.'
            ]);
        if($request->isMethod('post')){
            $fields = ['deposit_amount_limitation_for_user'];
            foreach ($fields as $field) {
                update_static_option($field, $request->$field);
            }
            toastr_success(__('Update Success'));
            return back();
        }
        return view('wallet::backend.deposit-settings');
    }

    public function wallet_lists()
    {
        $wallet_lists = Wallet::with('user')->latest()->paginate(10);
        return view('wallet::backend.wallet-lists.wallet-lists',compact('wallet_lists'));
    }

    public function change_status($id)
    {
        $job = Wallet::find($id);
        $job->status === 1 ? $status = 0 : $status = 1;
        Wallet::where('id',$id)->update(['status'=>$status]);
        return redirect()->back()->with(FlashMsg::item_new('Status Changed Success'));
    }

    public function wallet_history()
    {
        $wallet_history_lists = WalletHistory::with('user')->latest()->where('payment_status','!=','')->paginate(10);
        $users = User::select('id', 'first_name', 'last_name', 'phone')->get();
        return view('wallet::backend.wallet-history.history',compact('wallet_history_lists', 'users'));
    }

    public function wallet_history_status($id)
    {
        $wallet_history = WalletHistory::find($id);
        $status = $wallet_history->payment_status === 'pending' ? 'complete' : '';
        WalletHistory::where('id',$id)->update(['payment_status'=>$status]);
        $wallet = Wallet::select(['id','user_id','balance'])->where('user_id',$wallet_history->user_id)->first();
        Wallet::where('user_id',$wallet->user_id)->update([
            'balance'=>$wallet->balance+$wallet_history->amount,
        ]);
        return redirect()->back()->with(FlashMsg::item_new('Status Changed Success'));
    }


    public function depositCreateByAdmin(Request $request)
    {
        $limit_deposit_amount = get_static_option('deposit_amount_limitation_for_user') ?? 50000;
        $request->validate([
            'amount'=>'required|integer|min:10|max:"'.$limit_deposit_amount.'"',
            'user_id'=>'required',
        ]);

        //get user_id and deposit amount
        $user_id = $request->user_id;
        $total = $request->amount;

        if($request->selected_payment_gateway == 'manual_payment'){
            $payment_status='pending';
        }else{
            $payment_status='';
        }

        // first check if user wallet empty create wallet
        if (!empty($user_id)){
            $user_wallet = Wallet::where('user_id',$user_id)->first();
            if(empty($user_wallet)){
                Wallet::create([
                    'user_id' => $user_id,
                    'balance' => 0,
                    'status' => 0,
                ]);
            }
        }

        // create wallet history
        $deposit = WalletHistory::create([
            'user_id' => $user_id,
            'amount' => $total,
            'payment_gateway' => $request->selected_payment_gateway,
            'payment_status' => 'complete',
            'status' => 1,

        ]);

        $last_deposit_id = $deposit->id;
        $email = optional($deposit->user)->email;
        // update user wallet balance
        $user_wallet->balance += $request->amount;
        $user_wallet->save();

        if($request->selected_payment_gateway === 'added_by_admin') {
            try {
                $message_body = __('Hello, New User deposit credited by Admin').'</br>'.'<span class="verify-code">'.__('Deposit ID:').$last_deposit_id.'</span>';
                Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                    'subject' => __('New Deposit Added'),
                    'message' => $message_body
                ]));
                Mail::to($email)->send(new BasicMail([
                    'subject' => __('Deposit added by admin'),
                    'message' => __('Manual deposit success. Your wallet credited by admin #').$last_deposit_id
                ]));
            } catch (\Exception $e) {

            }
        }
        toastr_success(__('Manual deposit success. User wallet credited'));
        return back();
    }

    function pagination_history(Request $request)
    {
        if($request->ajax()){
            $wallet_history_lists = WalletHistory::latest()->paginate(10);
            return view('wallet::backend.wallet-history.search-result', compact('wallet_history_lists'))->render();
        }
    }

    // search category
    public function search_wallet_history(Request $request)
    {
        $wallet_history_lists= WalletHistory::where('amount', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
        if($wallet_history_lists->total() >= 1){
            return view('wallet::backend.wallet-history.search-result', compact('wallet_history_lists'))->render();
        }else{
            return response()->json([
                'status'=>__('nothing')
            ]);
        }
    }

    function pagination(Request $request)
    {
        if($request->ajax()){
            $wallet_lists = Wallet::latest()->paginate(10);
            return view('wallet::backend.wallet-lists.search-result', compact('wallet_lists'))->render();
        }
    }

    public function search_wallet(Request $request)
    {
        $wallet_lists= Wallet::where('balance', 'LIKE', "%". strip_tags($request->string_search) ."%")->paginate(10);
        if($wallet_lists->total() >= 1){
            return view('wallet::backend.wallet-lists.search-result', compact('wallet_lists'))->render();
        }else{
            return response()->json([
                'status'=>__('nothing')
            ]);
        }
    }

    public function wallet_history_filter(Request $request)
    {
        $query = WalletHistory::with('user')->latest()->where('payment_status', '!=', '');
        
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->status) {
            $query->where('payment_status', $request->status);
        }
        
        $wallet_history_lists = $query->paginate(10);
        
        return view('wallet::backend.wallet-history.search-result', compact('wallet_history_lists'));
    }
}
