<template>
    <div class="container">
        <ul>
            <li v-for="message in messages">
                <span class="senderName" v-if="message.bot">YodaBot: </span>
                <span class="senderName" v-else>Me: </span>
                {{message.content}}
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        props: {message: {content: String, bot: Boolean}},
        watch: {
            'message': function(){
                this.addMessage()
            }
        },
        data (){
            return {
                response: '',
                messages: [],
            }
        },
        mounted() {
        },
        methods : {

            addMessage: function() {this.messages.push(this.message)}
        }
    }
</script>

<style>
    .senderName{
        font-weight: bold;
    }

</style>
