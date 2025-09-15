<script src="https://js.pusher.com/7.2/pusher.min.js"></script>

<script>
    class LiveChat {
        pusher;
        channel;
        logEnable;
        appCluster;
        appKey;
        appUrl;

        constructor() {
            this.appKey = "{{env('PUSHER_APP_KEY')}}";
            this.appCluster = "{{env('PUSHER_APP_CLUSTER')}}";
            this.appUrl = "{{env('APP_URL')}}";
            this.pusher = this.createInstance();
            this.channel = null;
        }

        createInstance(){
            this.pusher = null;
            return new Pusher(this.appKey, {
                cluster: this.appCluster,
                channelAuthorization: { 
                    endpoint: `${this.appUrl}/broadcasting/auth`,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                }
            });
        }

        enableLog(){
            Pusher.logToConsole = true;
        }

        createChannel(user_id, member_id, type) {
            if(type === 'user') {
                // User listening for member messages
                this.channel = this.pusher.subscribe(`private-livechat-member-channel.${user_id}.${member_id}`);
            } else {
                // Member listening for user messages  
                this.channel = this.pusher.subscribe(`private-livechat-user-channel.${member_id}.${user_id}`);
            }
        }

        removeChannel(user_id, member_id, type){
            if(type === 'user') {
                this.pusher.unsubscribe(`private-livechat-member-channel.${user_id}.${member_id}`);
            } else {
                this.pusher.unsubscribe(`private-livechat-user-channel.${member_id}.${user_id}`);
            }
        }

        bindEvent(eventName, callback) {
            this.channel.bind(eventName, callback);
        }

        // New method to bind to the consolidated event
        bindMessageEvent(callback) {
            this.channel.bind('livechat-message', callback);
        }
    }
</script>