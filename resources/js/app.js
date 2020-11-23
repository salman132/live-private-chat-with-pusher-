require('./bootstrap');

window.Vue = require('vue');
import Chat from "./components/Chat";
import Vue from 'vue'
import VueChatScroll from 'vue-chat-scroll'
Vue.use(VueChatScroll)

const app = new Vue({
    el: '#app',
    components:{
       Chat:Chat
    }
});
