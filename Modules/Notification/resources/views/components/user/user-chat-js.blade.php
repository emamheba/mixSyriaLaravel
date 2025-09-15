<script>

    @if(request()->has('member_id'))
        $(document).ready(function (){
            $('.chat_item[data-member-id={{ request()->member_id }}]').trigger('click').addClass("active")
        })
    @endif

    /*
    ========================================
        Chat Click and Active Class
    ========================================
    */
    let oldChannelName = "";
    let liveChat, channelName;
    liveChat = new LiveChat();

    $(document).on('click', '.chat_item', function() {
        //: first need to remove all active class and after that add active class to clicked item
        $(this).siblings().removeClass('active');
        $('#user-message-footer').removeClass('d-none');
        $(this).addClass('active');
        $('.chat_wrapper__contact__close, .body-overlay').removeClass('active');
        //: now fetch all old conversation from request with header and body
        fetch_chat_data($(this).attr("data-member-id"));

        $("#chat_body").attr("data-current-member", $(this).attr("data-member-id"))

        channelName = {
            member_id: $(this).attr("data-member-id"),
            user_id: "{{ auth('web')->id() }}",
            type: "user"
        };

        if(member_list["member_id_" + channelName.member_id] != true){
            // initialize livechat js
            liveChat.createChannel(channelName.user_id, channelName.member_id, channelName.type);

            // Updated event listener for consolidated event
            liveChat.bindEvent('livechat-message', function (data){
                // Check if this message is for the current chat
                if($("#chat_body").attr("data-current-member") == data.sender_id) {
                    // Determine which template to use based on sender type
                    if(data.sender_type === 'vendor') {
                        // Message from vendor to user - use user view
                        $.ajax({
                            url: "{{ route('user.get.message.template') }}",
                            type: "POST",
                            data: {
                                message: data.message,
                                livechat: data.livechat,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                $("#chat_body").append(response);
                                scrollToBottom();
                            }
                        });
                    }
                    
                    if (document.getElementById("chat-alert-sound") != undefined){
                        var alert_sound = document.getElementById("chat-alert-sound");
                        alert_sound.play();
                    }
                }
            });

            member_list["member_id_" + channelName.member_id] = true;
            oldChannelName = channelName;
        }

        $(this).find(".chat_wrapper__contact__list__time .badge").fadeOut();
    });

    $(document).on("click","#user-send-message-to-member", function (){

        //: prepare chat post data
        let file = $('#user-message-footer #message-file')[0].files[0];

        let form = new FormData();
        form.append('message', $('#user-message-footer #message').val());
        form.append('file', file !== undefined ? file : '');
        form.append('from_user', '1');
        form.append('member_id', $("#livechat-message-header").attr('data-member-id'));
        form.append('from', "chatbox");
        form.append('_token', "{{ csrf_token() }}");

        let messages_ = $('#user-message-footer #message').val();
        if(messages_ != '' || file !== undefined){
            $('#user-message-footer #message').val('');
            $('#user-message-footer #message-file').val('');
            $('#user-message-footer .show_uploaded_file').text('');
            send_ajax_request("post", form, "{{ route("user.message.send") }}", function (){}, function (response){
                $("#chat_body").append(response);
                scrollToBottom();
            }, function (){})
        }else{
            return false;
        }
    });

    $(document).on("click",".load-more-pagination", function (){
        let el = $(this);
        let page = parseInt(el.attr('data-page'));
        let nextPage = page + 1;

        fetch_chat_data($('#livechat-message-header').attr('data-member-id'), nextPage, function (){
            el.attr("data-page",nextPage);
        });
    });

    function fetch_chat_data(member_id, page = 1, callback){
        //: hare call an api for fetching data from database if no data available then new item will be inserted
        let formData;

        formData = new FormData();
        formData.append("member_id", member_id);
        formData.append("_token", "{{ csrf_token() }}");
        formData.append("from_user", 1)

        send_ajax_request("post", formData,`{{ route("user.fetch.chat.member.record") }}?page=${page}`,function (){

        }, function (response){

            if(page > 1) {
                $("#chat_body").children().not(":first").prepend(response.body);
            }else{
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
            $('.unseen_message_count_'+member_id).addClass("d-none")
            $('.reload_unseen_message_count').load(location.href + ' .reload_unseen_message_count')
        }, function (){

        })
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
        };
    })();
</script>