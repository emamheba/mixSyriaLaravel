<?php

namespace Modules\Membership\app\Console;

use App\Mail\BasicMail;
use App\Models\Backend\Language;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MembershipExpireReminder extends Command
{

    protected $signature = 'membership:membership_expire';
    protected $description = 'Command description.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $defaultLang =  Language::where('default',1)->first();
        if (session()->has('lang')) {
            $current_lang = Language::where('slug',session()->get('lang'))->first();
            if (!empty($current_lang)){
                \Carbon\Carbon::setLocale($current_lang->slug);
                app()->setLocale($current_lang->slug);
            }else {
                session()->forget('lang');
            }
        }else{
            Carbon::setLocale($defaultLang->slug);
            app()->setLocale($defaultLang->slug);
        }

        $all_user = User::with('membershipUser')
            ->where('status',1)
            ->whereHas('membershipUser')
            ->get();

        // if user membership
        foreach ($all_user as $user) {
            $dayList = json_decode(get_static_option('package_expire_notify_mail_days')) ?? [];
            rsort($dayList);

            $expireDate = optional($user->membershipUser)->expire_date;
            if (!$expireDate) {
                continue; // Skip users without an expiration date
            }

            $startDate = Carbon::today();
            foreach ($dayList as $day) {
                $notificationDate = Carbon::parse($expireDate)->subDays($day);
                if ($startDate->diffInDays($notificationDate, false) >= 0) {
                    // Check if it's time to send a notification
                    $daysRemaining = $startDate->diffInDays($expireDate);
                    // if Email Notify day & user membership remaining day same then send email
                    if ($day == $daysRemaining) {
                        try {
                            $subject = __('Membership Expire Reminder');
                            $messageBody = __('Your Membership will expire very soon. Only') . ' ' . $daysRemaining . ' ' . __('Days Left. Please Renew to a plan before expiration');
                            Mail::to($user->email)->send(new BasicMail([
                                'subject' => $subject,
                                'message' => $messageBody
                            ]));
                        } catch (\Exception $e) {
                        }
                    }
                }
            }
        }

        return 0;
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
