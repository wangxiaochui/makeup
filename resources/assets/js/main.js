/**
 * Created by Administrator on 2016/12/28.
 */
require('./bootstrap');
import Vuex from 'vuex'
import App from './App.vue'
import VueRouter from "vue-router"
import store from './vuex/store'

//开启debug模式
Vue.config.debug = true;
Vue.use(Vuex);
//Vue.use(ElementUI)
Vue.use(VueRouter);

import example from './components/Example.vue'
import display from './components/Display.vue'
// 创建一个路由器实例
// 并且配置路由规则
const router = new VueRouter({
    mode: 'history',
    // history: false,
    hashbang: true,
    base: __dirname,
    routes: [
        {
            path: '/makeup',
            component: display,
        },
        {
            path: '/makeup/new',
            component: display
        },
        {
            path: '/makeup/test',
            component: example
        }
    ]
});

//注册全局组件
//Vue.component('list-data',listcomponent);
//Vue.component('bar-item', barItem)
//Vue.component('cart', cart)

//路由前处理
router.beforeEach((to, from , next)=>{
    next()
});

const app = new Vue({
    name : 'app',
    router: router,
    store,
    render: h => h(App)
}).$mount('#app')
