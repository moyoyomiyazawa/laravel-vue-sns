require('./bootstrap');

import './bootstrap'
import Vue from 'vue'
import ArticleLike from './components/ArticleLike.vue'
import ArticleTagsInput from './components/ArticleTagsInput.vue'
import FollowButton from './components/FollowButton.vue'

const app = new Vue({
    el: '#app',
    components: {
        ArticleLike,
        ArticleTagsInput,
        FollowButton,
    }
})
