<template>
    <div class="container">
        <ul>
            <li v-for="text in history" v-bind:key="text">
                {{text}}
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        props: {message: String},
        watch: {
            'message': function(){
                this.addMessage()
            }
        },
        data (){
            return {response: '', history: ['']}
        },
        mounted() {
        },
        methods : {
            addMessage: function() {this.history.push(this.message)}
        }
    }
</script>
