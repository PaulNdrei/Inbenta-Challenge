<template>
    <div>
        <ul>
            <li v-for="message in messages">
                <span class="senderName" v-if="message.bot">YodaBot: </span>
                <span class="senderName" v-else>Me: </span>
                <span v-html="message.content"></span>

                <ul v-if="message.notFoundOptions">
                    <li v-for="option in message.notFoundOptions">
                        {{option.name}}
                    </li>
                </ul>
                <ul v-if="message.filmOptions">
                    <li v-for="film in message.filmOptions">
                        {{film.title}}
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        props: {message: {content: String, bot: Boolean, notFoundOptions: []}},
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
            let urlRequest = "http://inbenta-challenge.test/api/conversation/history";

            axios.get(urlRequest)
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
            addMessage: function() {
                this.messages.push(this.message)
            }

        }
    }
</script>

<style>
    .senderName{
        font-weight: bold;
    }

</style>
