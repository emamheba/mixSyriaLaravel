<script>
    @if(request()->has('user_id'))
    $(document).ready(function (){
        $('.chat_item[data-user-id={{ request()->user_id }}]').trigger('click').addClass("active")
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
        //first need to remove all active class and after that add active class to clicked item
        $(this).siblings().removeClass('active');
        $('#member-message-footer').removeClass('d-none');
        $(this).addClass('active');
        $('.chat_wrapper__contact__close, .body-overlay').removeClass('active');
        // now fetch all old conversation from request with header and body
        fetch_chat_data($(this).attr("data-user-id"));

        $("#chat_body").attr("data-current-user", $(this).attr("data-user-id"))

        channelName = {
            user_id: $(this).attr("data-user-id"),
            member_id: "{{ auth('web')->id() }}",
            type: "member"
        };

        if(user_list["user_id_" + channelName.user_id] != true){

            //: initialize livechat js
            liveChat.createChannel(channelName.user_id, channelName.member_id, channelName.type);

            // Updated event listener for consolidated event
            liveChat.bindEvent('livechat-message', function (data){
                // Check if this message is for the current chat
                if($("#chat_body").attr("data-current-user") == data.sender_id) {
                    // Determine which template to use based on sender type
                    if(data.sender_type === 'user') {
                        // Message from user to member - use member view
                        $.ajax({
                            url: "{{ route('member.get.message.template') }}",
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

            user_list["user_id_" + channelName.user_id] = true;
            oldChannelName = channelName;
        }

        $(this).find(".chat_wrapper__contact__list__time .badge").fadeOut();
    });

    $(document).on("click","#member-send-message-to-user", function (){
        //: prepare chat post data
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

            send_ajax_request("post", form, "{{ route("member.message.send") }}", function (){}, function (response){
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

        fetch_chat_data($('#livechat-message-header').attr('data-user-id'), nextPage, function (){
            el.attr("data-page",nextPage);
        });
    });

    function fetch_chat_data(user_id, page = 1, callback){
        //: hare call a api for fetching data from database if no data available then new item will be inserted
        let formData;

        formData = new FormData();
        formData.append("user_id", user_id);
        formData.append("_token", "{{ csrf_token() }}");
        formData.append("from_user", 2)

        send_ajax_request("post", formData,`{{ route("member.fetch.chat.user.record") }}?page=${page}`,function (){

        }, function (response){
            $('.unseen_message_count_'+user_id).addClass("d-none")
            $('.reload_unseen_message_count').load(location.href + ' .reload_unseen_message_count')

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