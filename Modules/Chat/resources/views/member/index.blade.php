@extends('layouts/layoutMaster')

@section('title', 'Chat - Apps')

{{-- Vendor Styles --}}
@section('vendor-style')
  @vite('resources/assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.scss')
@endsection

{{-- Page Styles --}}
@section('page-style')
  @vite('resources/assets/vendor/scss/pages/app-chat.scss')
@endsection

{{-- Vendor Scripts --}}
@section('vendor-script')
  @vite('resources/assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.js')
@endsection

{{-- Page Scripts --}}
@section('page-script')
  @vite('resources/assets/js/app-chat.js')
  <script>
    $(document).ready(function () {
      // في حال تم إرسال user_id من الرابط، نفعل التحديد تلقائيًا
      @if(request()->has('user_id'))
        $('.chat_item[data-user-id={{ request()->user_id }}]').trigger('click').addClass("active");
      @endif

      let oldChannelName = "";
      let liveChat = new LiveChat();
      let channelName;

      // عند الضغط على جهة اتصال لتحميل المحادثة الخاصة بها
      $(document).on('click', '.chat_item', function() {
          $(this).siblings().removeClass('active');
          $('#member-message-footer').removeClass('d-none');
          $(this).addClass('active');
          $('.chat_wrapper__contact__close, .body-overlay').removeClass('active');

          // جلب بيانات المحادثة للجهة المختارة
          fetch_chat_data($(this).attr("data-user-id"));
          $("#chat_body").attr("data-current-user", $(this).attr("data-user-id"));

          channelName = {
              user_id: $(this).attr("data-user-id"),
              member_id: "{{ auth('web')->id() }}",
              type: "member"
          };

          if(user_list["user_id_" + channelName.user_id] != true){
              liveChat.createChannel(channelName.user_id, channelName.member_id, channelName.type);
              liveChat.bindEvent('livechat-user-' + channelName.user_id, function (data){
                  if($("#chat_body").attr("data-current-user") == data.livechat?.user?.id) {
                      $("#chat_body").append(data.messageBlade);
                      scrollToBottom();
                  }
                  if (document.getElementById("chat-alert-sound")){
                      var alert_sound = document.getElementById("chat-alert-sound");
                      alert_sound.play();
                  }
              });
              user_list["user_id_" + channelName.user_id] = true;
              oldChannelName = channelName;
          }
          $(this).find(".chat_wrapper__contact__list__time .badge").fadeOut();
      });

      // إرسال الرسالة عند الضغط على زر "Send"
      $(document).on("click","#member-send-message-to-user", function (){
          let file = $('#member-message-footer #message-file')[0].files[0];
          let form = new FormData();
          form.append('message', $('#member-message-footer #message').val());
          form.append('file', file !== undefined ? file : '');
          form.append('from_user', '2');
          form.append('user_id', $("#livechat-message-header").attr('data-user-id'));
          form.append('from', "chatbox");
          form.append('_token', "{{ csrf_token() }}");
          let messages_ = $('#member-message-footer #message').val();
          if(messages_ != '' || file !== undefined){
              $('#member-message-footer #message').val('');
              $('#member-message-footer #message-file').val('');
              $('#member-message-footer .show_uploaded_file').text('');
              send_ajax_request("post", form, "{{ route('member.message.send') }}", function (){}, function (response){
                  $("#chat_body").append(response);
                  scrollToBottom();
              }, function (){})
          } else {
              return false;
          }
      });

      // تحميل المزيد من الرسائل (pagination)
      $(document).on("click",".load-more-pagination", function (){
          let el = $(this);
          let page = parseInt(el.attr('data-page'));
          let nextPage = page + 1;
          fetch_chat_data($('#livechat-message-header').attr('data-user-id'), nextPage, function (){
              el.attr("data-page", nextPage);
          });
      });

      function fetch_chat_data(user_id, page = 1, callback){
          let formData = new FormData();
          formData.append("user_id", user_id);
          formData.append("_token", "{{ csrf_token() }}");
          formData.append("from_user", 2);
          send_ajax_request("post", formData, `{{ route('member.fetch.chat.user.record') }}?page=${page}`, function (){}, function (response){
              $('.unseen_message_count_' + user_id).addClass("d-none");
              $('.reload_unseen_message_count').load(location.href + ' .reload_unseen_message_count');
              if(page > 1) {
                  $("#chat_body").children().not(":first").prepend(response.body);
              } else {
                  let loadmore = `
                      <div class="pagination d-flex justify-content-center mb-3">
                          <button data-page="1" class="btn btn-info load-more-pagination">{{ __("Load More") }}</button>
                      </div>`;
                  $("#chat_body").html((response.allow_load_more ? loadmore : '') + response.body);
                  $("#chat_header").html(response.header);
                  scrollToBottom();
              }
              $("#vendor-message-footer").removeClass("d-none");
              $("#chat_header").removeClass("d-none");
              if (typeof callback === "function") {
                  callback();
              }
          }, function (){});
      }

      function scrollToBottom(){
          const scrollingElement = (document.querySelector(".messageShow") || document.body);
          let scrollSmoothlyToBottom = document.querySelector(".messageShow");
          $(scrollingElement).animate({
              scrollTop: scrollSmoothlyToBottom.scrollHeight,
          }, 500);
      }

      (function (){
          let uploadImage = document.querySelector(".show_uploaded_file");
          let inputTag = document.querySelector(".inputTag");
          if(inputTag != null) {
              inputTag.addEventListener('change', ()=> {
                  let inputTagFile = document.querySelector(".inputTag").files[0];
                  uploadImage.innerText = inputTagFile.name;
              });
          }
      })();
    });
  </script>
@endsection

@section('content')
<div class="app-chat card overflow-hidden">
  <div class="row g-0">
    <!-- Sidebar Left (ملف المستخدم - معلومات شخصية وإعدادات) -->
    <div class="col app-chat-sidebar-left app-sidebar overflow-hidden" id="app-chat-sidebar-left">
      <div class="chat-sidebar-left-user sidebar-header d-flex flex-column justify-content-center align-items-center flex-wrap px-6 pt-12">
        <div class="avatar avatar-xl avatar-online chat-sidebar-avatar">
          <img src="{{ asset('img/avatars/1.png') }}" alt="Avatar" class="rounded-circle">
        </div>
        <h5 class="mt-4 mb-0">{{ auth()->user()->fullname ?? 'John Doe' }}</h5>
        <span>{{ __('Member') }}</span>
        <i class="ti ti-x ti-lg cursor-pointer close-sidebar" data-bs-toggle="sidebar" data-overlay data-target="#app-chat-sidebar-left"></i>
      </div>
      <div class="sidebar-body px-6 pb-6">
        <div class="my-6">
          <label for="chat-sidebar-left-user-about" class="text-uppercase text-muted mb-1">About</label>
          <textarea id="chat-sidebar-left-user-about" class="form-control chat-sidebar-left-user-about" rows="3" maxlength="120">Your status or bio goes here...</textarea>
        </div>
        <div class="my-6">
          <p class="text-uppercase text-muted mb-1">Status</p>
          <div class="d-grid gap-2 pt-2 text-heading ms-2">
            <div class="form-check form-check-success">
              <input name="chat-user-status" class="form-check-input" type="radio" value="active" id="user-active" checked>
              <label class="form-check-label" for="user-active">Online</label>
            </div>
            <div class="form-check form-check-warning">
              <input name="chat-user-status" class="form-check-input" type="radio" value="away" id="user-away">
              <label class="form-check-label" for="user-away">Away</label>
            </div>
            <div class="form-check form-check-danger">
              <input name="chat-user-status" class="form-check-input" type="radio" value="busy" id="user-busy">
              <label class="form-check-label" for="user-busy">Do not Disturb</label>
            </div>
            <div class="form-check form-check-secondary">
              <input name="chat-user-status" class="form-check-input" type="radio" value="offline" id="user-offline">
              <label class="form-check-label" for="user-offline">Offline</label>
            </div>
          </div>
        </div>
        <div class="my-6">
          <p class="text-uppercase text-muted mb-1">Settings</p>
          <ul class="list-unstyled d-grid gap-4 ms-2 pt-2 text-heading">
            <li class="d-flex justify-content-between align-items-center">
              <div>
                <i class='ti ti-lock ti-md me-1'></i>
                <span>Two-step Verification</span>
              </div>
              <div class="form-check form-switch mb-0 me-1">
                <input type="checkbox" class="form-check-input" checked />
              </div>
            </li>
            <li class="d-flex justify-content-between align-items-center">
              <div>
                <i class='ti ti-bell ti-md me-1'></i>
                <span>Notification</span>
              </div>
              <div class="form-check form-switch mb-0 me-1">
                <input type="checkbox" class="form-check-input" />
              </div>
            </li>
            <li>
              <i class="ti ti-user-plus ti-md me-1"></i>
              <span>Invite Friends</span>
            </li>
            <li>
              <i class="ti ti-trash ti-md me-1"></i>
              <span>Delete Account</span>
            </li>
          </ul>
        </div>
        <div class="d-flex mt-6">
          <button class="btn btn-primary w-100" data-bs-toggle="sidebar" data-overlay data-target="#app-chat-sidebar-left">
            Logout <i class="ti ti-logout ti-16px ms-2"></i>
          </button>
        </div>
      </div>
    </div>
    <!-- /Sidebar Left -->

    <!-- Chat & Contacts (جهات الاتصال الديناميكية) -->
    <div class="col app-chat-contacts app-sidebar flex-grow-0 overflow-hidden border-end" id="app-chat-contacts">
      <div class="sidebar-header h-px-75 px-5 border-bottom d-flex align-items-center">
        <div class="d-flex align-items-center me-6 me-lg-0">
          <div class="flex-shrink-0 avatar avatar-online me-4">
            <img class="user-avatar rounded-circle cursor-pointer" src="{{ asset('img/avatars/1.png') }}" alt="Avatar">
          </div>
          <div class="flex-grow-1 input-group input-group-merge">
            <span class="input-group-text" id="basic-addon-search31">
              <i class="ti ti-search"></i>
            </span>
            <input type="text" class="form-control chat-search-input" placeholder="Search..." aria-label="Search..." aria-describedby="basic-addon-search31">
          </div>
        </div>
        <i class="ti ti-x ti-lg cursor-pointer position-absolute top-50 end-0 translate-middle d-lg-none d-block" data-overlay data-bs-toggle="sidebar" data-target="#app-chat-contacts"></i>
      </div>
      <div class="sidebar-body">
        <!-- قائمة جهات الاتصال الديناميكية -->
        <ul class="list-unstyled chat-contact-list py-2 mb-0" id="chat-list">
          @if($member_chat_list->count() > 0)
            @foreach($member_chat_list as $member_chat)
              <li class="chat-contact-list-item chat_item" data-user-id="{{ $member_chat->user->id }}">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 avatar avatar-online">
                    @if($member_chat->user->image)
                      {!! render_image_markup_by_attachment_id($member_chat->user->image, '', 'thumb') !!}
                    @else
                      <x-image.user-no-image />
                    @endif
                  </div>
                  <div class="chat-contact-info flex-grow-1 ms-4">
                    <div class="d-flex justify-content-between align-items-center">
                      <h6 class="chat-contact-name text-truncate m-0 fw-normal">{{ $member_chat->user->fullname }}</h6>
                      <small class="text-muted">
                        {{ $member_chat->user->check_online_status ? $member_chat->user->check_online_status->diffForHumans() : '' }}
                      </small>
                    </div>
                    <small class="chat-contact-status text-truncate">
                      @if($member_chat->member_unseen_msg_count > 0)
                        {{ $member_chat->member_unseen_msg_count }} new messages
                      @endif
                    </small>
                  </div>
                </div>
              </li>
            @endforeach
          @else
            <li class="chat-contact-list-item">
              <h6 class="text-muted mb-0">No Chats Found</h6>
            </li>
          @endif
        </ul>
      </div>
    </div>
    <!-- /Chat & Contacts -->

    <!-- Chat History (المحادثة) -->
    <div class="col app-chat-history">
      <div class="chat-history-wrapper">
        <!-- رأس المحادثة (يُحمّل عبر AJAX) -->
        <div class="chat-history-header border-bottom">
          <div class="d-flex justify-content-between align-items-center">
            <div id="chat_header">
              @if(isset($data))
                {!! $header ?? '' !!}
              @endif
            </div>
            <div class="d-flex align-items-center">
              <i class="ti ti-phone ti-md cursor-pointer d-sm-inline-flex d-none me-1 btn btn-sm btn-text-secondary btn-icon rounded-pill"></i>
              <i class="ti ti-video ti-md cursor-pointer d-sm-inline-flex d-none me-1 btn btn-sm btn-text-secondary btn-icon rounded-pill"></i>
              <i class="ti ti-search ti-md cursor-pointer d-sm-inline-flex d-none me-1 btn btn-sm btn-text-secondary btn-icon rounded-pill"></i>
              <div class="dropdown">
                <button class="btn btn-sm btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="true" id="chat-header-actions">
                  <i class="ti ti-dots-vertical ti-md"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="chat-header-actions">
                  <a class="dropdown-item" href="javascript:void(0);">View Contact</a>
                  <a class="dropdown-item" href="javascript:void(0);">Mute Notifications</a>
                  <a class="dropdown-item" href="javascript:void(0);">Block Contact</a>
                  <a class="dropdown-item" href="javascript:void(0);">Clear Chat</a>
                  <a class="dropdown-item" href="javascript:void(0);">Report</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- محتوى المحادثة -->
        <div class="chat-history-body">
          <div class="messageShow">
            <div class="chat-wrapper-details-inner user-chat-body" id="chat_body">
              @if(isset($data))
                @foreach($data->messages as $message)
                  <x-chat::member.message :$message :$data />
                @endforeach
              @else
                <p class="text-center text-muted py-3">Select a chat to view messages</p>
              @endif
            </div>
          </div>
        </div>
        <!-- نموذج إرسال الرسالة -->
        <div class="chat-history-footer shadow-xs" id="member-message-footer">
          <form class="form-send-message d-flex justify-content-between align-items-center">
            <input class="form-control message-input border-0 me-4 shadow-none" placeholder="Type your message here..." id="message">
            <div class="message-actions d-flex align-items-center">
              <i class="speech-to-text ti ti-microphone ti-md btn btn-sm btn-text-secondary btn-icon rounded-pill cursor-pointer"></i>
              <label for="message-file" class="form-label mb-0">
                <i class="ti ti-paperclip ti-md cursor-pointer btn btn-sm btn-text-secondary btn-icon rounded-pill mx-1"></i>
                <input type="file" id="message-file" hidden>
              </label>
              <button class="btn btn-primary d-flex send-msg-btn" type="button" id="member-send-message-to-user">
                <span class="align-middle d-md-inline-block d-none">Send</span>
                <i class="ti ti-send ti-16px ms-md-2 ms-0"></i>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- /Chat History -->

    <!-- Sidebar Right (معلومات جهة الاتصال - يمكن جعله ثابت أو ديناميكي حسب الحاجة) -->
    <div class="col app-chat-sidebar-right app-sidebar overflow-hidden" id="app-chat-sidebar-right">
      <div class="sidebar-header d-flex flex-column justify-content-center align-items-center flex-wrap px-6 pt-12">
        <div class="avatar avatar-xl avatar-online chat-sidebar-avatar">
          <img src="{{ asset('img/avatars/4.png') }}" alt="Avatar" class="rounded-circle">
        </div>
        <h5 class="mt-4 mb-0">Felecia Rower</h5>
        <span>NextJS Developer</span>
        <i class="ti ti-x ti-lg cursor-pointer close-sidebar" data-bs-toggle="sidebar" data-overlay data-target="#app-chat-sidebar-right"></i>
      </div>
      <div class="sidebar-body p-6 pt-0">
        <div class="my-6">
          <p class="text-uppercase mb-1 text-muted">About</p>
          <p class="mb-0">It is a long established fact that a reader will be distracted by the readable content.</p>
        </div>
        <div class="my-6">
          <p class="text-uppercase mb-1 text-muted">Personal Information</p>
          <ul class="list-unstyled d-grid gap-4 mb-0 ms-2 py-2 text-heading">
            <li class="d-flex align-items-center">
              <i class='ti ti-mail ti-md'></i>
              <span class="align-middle ms-2">josephGreen@email.com</span>
            </li>
            <li class="d-flex align-items-center">
              <i class='ti ti-phone-call ti-md'></i>
              <span class="align-middle ms-2">+1(123) 456 - 7890</span>
            </li>
            <li class="d-flex align-items-center">
              <i class='ti ti-clock ti-md'></i>
              <span class="align-middle ms-2">Mon - Fri 10AM - 8PM</span>
            </li>
          </ul>
        </div>
        <div class="my-6">
          <p class="text-uppercase text-muted mb-1">Options</p>
          <ul class="list-unstyled d-grid gap-4 ms-2 py-2 text-heading">
            <li class="cursor-pointer d-flex align-items-center">
              <i class='ti ti-badge ti-md'></i>
              <span class="align-middle ms-2">Add Tag</span>
            </li>
            <li class="cursor-pointer d-flex align-items-center">
              <i class='ti ti-star ti-md'></i>
              <span class="align-middle ms-2">Important Contact</span>
            </li>
            <li class="cursor-pointer d-flex align-items-center">
              <i class='ti ti-photo ti-md'></i>
              <span class="align-middle ms-2">Shared Media</span>
            </li>
            <li class="cursor-pointer d-flex align-items-center">
              <i class='ti ti-trash ti-md'></i>
              <span class="align-middle ms-2">Delete Contact</span>
            </li>
            <li class="cursor-pointer d-flex align-items-center">
              <i class='ti ti-ban ti-md'></i>
              <span class="align-middle ms-2">Block Contact</span>
            </li>
          </ul>
        </div>
        <div class="d-flex mt-6">
          <button class="btn btn-danger w-100" data-bs-toggle="sidebar" data-overlay data-target="#app-chat-sidebar-right">
            Delete Contact <i class='ti ti-trash ti-16px ms-2'></i>
          </button>
        </div>
      </div>
    </div>
    <!-- /Sidebar Right -->

    <div class="app-overlay"></div>
  </div>
</div>

<!-- صوت تنبيه الدردشة -->
<audio id="chat-alert-sound" style="display: none">
  <source src="{{ asset('storage/uploads/chat_image/sound/facebook_chat.mp3') }}" />
</audio>
@endsection
