require('./bootstrap');

import './bootstrap'
import Vue from 'vue'
import ArticleLike from './components/ArticleLike.vue'

const app = new Vue({
    el: '#app',
    components: {
        ArticleLike,
    }
})
