/**
 * Created by Administrator on 2016/12/29.
 */
import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)
export default new Vuex.Store({
    state : {
        count :0,
        test : 'haahh',
        exm : {a:'aaaa',b:'bbbbb'}
    },
    getters :{
        countLength : state=>state.count.toString(),
    },
    mutations : {
        increment(state) {
            state.count++;
        }
    }

})