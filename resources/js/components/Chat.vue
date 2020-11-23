<template>
    <div class="chat align-item-center">
        <div class="col-md-12">
            <div class="row">
                <div class="container">
                    <h2>Chat Room</h2>
                       <div class="col-md-12">
                           <div class="row">
                               <div class="col-md-4">
                                   <ul class="list-group">
                                       <li class="list-group-item py-3" v-for="usr in users">
                                           <a href="#" @click.prevent="fetchMsg(usr.id)">{{ usr.name }}</a>
                                       </li>
                                   </ul>
                               </div>
                               <div class="col-md-8">
                                   <ul class="list-group" v-chat-scroll>
                                       <li class="list-group-item" v-for="(message,index) in messages" :key="index">
                                           <span class="you" v-if="typeof user[index].name != 'undefined' && self.id == user[index].id">
                                               <span class="row">
                                                       <span class="col-md-2 chat-profile">
                                                           <img src="https://www.salmanauvi.com/uploads/profile/157201353927752366_1857452854329312_6872998793899151964_n.jpg" alt="" width="40px" height="40px">
                                                       </span>
                                                       <span class="col-md-10">
                                                           <span class="name">
                                                               <a href="#" v-if="typeof user[index].name != 'undefined'">You</a>
                                                           </span>
                                                                   {{ message }}

                                                           <p><small class="badge float-right" v-if="messageTime[index]">{{ messageTime[index] }}</small></p>
                                                       </span>
                                               </span>



                                           </span>
                                           <span class="other" v-else>
                                               <span class="col-md-12">
                                                   <span class="row">
                                                       <span class="col-md-2 chat-profile">
                                                            <img src="https://www.salmanauvi.com/uploads/profile/157201353927752366_1857452854329312_6872998793899151964_n.jpg" alt="" width="40px" height="40px">
                                                       </span>
                                                       <span class="col-md-10">
                                                           <span class="other-name">
                                                                   <a href="#" v-if="typeof user[index].name != 'undefined'">{{ user[index].name }}</a>
                                                           </span>
                                                                   {{ message }}

                                                                           <!--                                               <small class="badge float-right" v-if="typeof user[index].name != 'undefined'">{{ user[index].name }}</small>-->
                                                           <p><small class="badge float-right" v-if="messageTime[index]">{{ messageTime[index] }}</small></p>
                                                       </span>
                                                   </span>
                                               </span>

                                           </span>
                                       </li>
                                   </ul>
                                   <div class="chat-form">
                                       <form @submit.prevent.enter="sendMsg()">
                                           <div class="form-group">
                                               <input type="text" name="msg" class="form-control" placeholder="Type.." v-model="msg" id="msg" @keyup="typing()">
                                           </div>
                                           <div class="form-group container">
                                               <div class="text-right">
                                                   <input type="submit" value="Send" class="btn btn-primary">
                                               </div>
                                           </div>
                                       </form>
                                   </div>
                               </div>
                           </div>
                       </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>

    import moment from 'moment';


    export default {
        name: 'Chat',
        data(){
            return{
                msg: null,
                messages:[],
                messageTime:[],
                users:[],
                user:[],
                self:[],
                receiver_id:null,

            }
        },
        mounted() {
            this.getAuthId();
            this.getAllUser();
            this.fetchMsg();

            Echo.private(`chat`)
                .listen('ChatEvent', (e) => {
                    this.messages.push(e.message)
                    this.user.push(e.user)
                    console.log(e);
                });

        },
        methods:{
            getAllUser:function (){
                const url = '/get_all_user';
                axios.get(url).then(response =>{
                    this.users = response.data
                }).catch(error =>{
                    console.log(error)
                })
            },
            fetchMsg:function (user_id){

                this.receiver_id = user_id;
                this.user = [];
                this.messages = [];
                this.messageTime = [];

                const url = '/get_chat';
                axios.get(url,{
                    params:{
                        user_id:user_id,
                    }
                }).then(response=>{
                    response.data.forEach(value =>{
                        this.user.push(value.user);
                        this.messages.push(value.message)
                        this.messageTime.push(moment(value.created_at).format('lll'))
                        console.log(value)
                    })

                })
            },
            getAuthId:function (){
              const url ='/auth_id';
              axios.get(url).then(response =>{
                  this.self = response.data
              }).catch(error =>{
                  console.log(error)
              })
            },
            sendMsg:function (){
                if(this.msg){
                    let temMsg = null;
                    this.messages.push(this.msg);
                    temMsg = this.msg;
                    this.msg = null;
                    this.user.push({
                        name: 'You',
                        sender: 0,
                        id: this.self.id
                    });
                    const url = "/send_message";
                    axios.post(url,{
                        msg:temMsg,
                        receiver_id: this.receiver_id
                    }).then(response =>{
                        temMsg= null;
                    }).catch(error =>{
                        console.log(error)
                    })

                    const address = '/store_message';
                    axios.post(address,{
                        sender_id: this.self.id,
                        receiver_id: this.receiver_id,
                        message: temMsg,
                    })
                }
            },
            typing:function (){
                console.log('Typing..')
            }
        }

    }
</script>


<style>
    .chat{
        width: 100%;
        margin: auto;
        overflow: hidden;
    }
    .chat ul{
        width: 80%;
        height: 320px;
        background-color:#ffffff;
        overflow-y: auto;
    }
    .chat ul li{
        padding: 20px 35px;

    }
    .chat ul li .chat-profile img{
        border-radius:50%;
        height: 30px;
        width: 30px;
    }
    .chat-form{
        width: 80%;
        padding: 10px 0;
    }
    .chat-form #msg{
        height: 100px;
    }

    span.you {
        display: block;
        padding: 10px 25px;
        background: #2980b9;
        color: #ffffff;
        border-radius: 10px;
    }
    span.other {
        display: block;
        padding: 10px 25px;
        background: #bdc3c7;
        color: #2c3e50;
        border-radius: 10px;
        margin: 10px 0;

    }
    span.name a{
        width: 100%;
        display: block;
        color: #2ecc71;
    }
    span.other-name a{
        width: 100%;
        display: block;
        color: #2980b9;
    }

</style>
