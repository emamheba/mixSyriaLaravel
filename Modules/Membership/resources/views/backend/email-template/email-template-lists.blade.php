<tr>
    <td><strong class="serial-number"></strong></td>
    <td>
        {{__('User Free Membership')}} <br>
        <span class="mt-2"><b class="text-info">{{__('Notes:')}}</b> {{ __('For the User Free Membership.') }}</span>
    </td>
    <td>
        <x-icon.edit-icon :url="route('admin.email.user.membership.free.template')"/>
    </td>
</tr>
<tr>
    <td><strong class="serial-number"></strong></td>
    <td>
        {{__('User Membership Purchase')}} <br>
        <span class="mt-2"><b class="text-info">{{__('Notes:')}}</b> {{ __('For the User Membership.') }}</span>
    </td>
    <td>
        <x-icon.edit-icon :url="route('admin.email.user.membership.purchase.template')"/>
    </td>
</tr>
<tr>
    <td><strong class="serial-number"></strong></td>
    <td>
        {{__('User Membership Renew')}} <br>
        <span class="mt-2"><b class="text-info">{{__('Notes:')}}</b> {{ __('For the User Membership Renew.') }}</span>
    </td>
    <td>
        <x-icon.edit-icon :url="route('admin.email.user.membership.renew.template')"/>
    </td>
</tr>
<tr>
    <td><strong class="serial-number"></strong></td>
    <td>
        {{__('Membership Active Email To User')}} <br>
        <span class="mt-2"><b class="text-info">{{__('Notes:')}}</b> {{ __('For the User Membership Active.') }}</span>
    </td>
    <td>
        <x-icon.edit-icon :url="route('admin.email.user.membership.active.template')"/>
    </td>
</tr>
<tr>
    <td><strong class="serial-number"></strong></td>
    <td>
        {{__('Membership Inactive Email To User')}} <br>
        <span class="mt-2"><b class="text-info">{{__('Notes:')}}</b> {{ __('For the User Membership Inactive.') }}</span>
    </td>
    <td>
        <x-icon.edit-icon :url="route('admin.email.user.membership.inactive.template')"/>
    </td>
</tr>
<tr>
    <td><strong class="serial-number"></strong></td>
    <td>
        {{__('Manual Membership Payment Complete Email To User')}} <br>
        <span class="mt-2"><b class="text-info">{{__('Notes:')}}</b> {{ __('For the User Membership Manual Payment Complete.') }}</span>
    </td>
    <td>
        <x-icon.edit-icon :url="route('admin.email.user.membership.manual.payment.complete.template')"/>
    </td>
</tr>
<tr>
    <td><strong class="serial-number"></strong></td>
    <td>
        {{__('Manual Membership Payment Complete Email To Admin')}} <br>
        <span class="mt-2"><b class="text-info">{{__('Notes:')}}</b> {{ __('For the Manual Membership Payment Complete.') }}</span>
    </td>
    <td>
        <x-icon.edit-icon :url="route('admin.email.user.membership.manual.payment.complete.to.admin.template')"/>
    </td>
</tr>
