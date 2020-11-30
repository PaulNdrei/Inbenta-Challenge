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
                messages: [],
            }
        },
        mounted() {
            let $this = this;
            axios.get('http://inbenta-challenge.test/api/conversation/history')
                .then(function (response){
                    let historyMessages = response.data;
                    for (let i = 0; i < historyMessages.length; i++){
                        let user = historyMessages[i].user
                        let isBot = false;
                        if (user === "bot") isBot = true;
                        let tempObject = {content: historyMessages[i].message, bot: isBot }
                        $this.messages.push(tempObject);
                    }
                })
            .catch(error => console.log(error))
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
