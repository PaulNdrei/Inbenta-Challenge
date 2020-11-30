<template>
    <div class="container">
        <message-component class="col-sm-8" :message="message"></message-component>

        <span class="inputInfoText">{{inputInfoMessage}}</span>
        <form v-on:submit.prevent class="form-inline">
            <input v-model="text" type="text" class="form-control col-sm-6">
            <button  @click="sendMessage" class="btn btn-primary m-2" type="submit">Send!</button>
        </form>
    </div>
</template>

<script>
    export default {
        data (){
            return {
                message: {content: String, bot: Boolean},
                text: '',
                inputInfoMessage: ''
            }
        },
        mounted() {
        },
        methods : {
            sendMessage: function() {
                if (this.text){
                    let tempMessageObject = {content: this.text, bot: false};
                    this.message = tempMessageObject;
                    this.text = '';

                    let axiosConfig = {
                        onUploadProgress: progressUpload => this.inputInfoMessage = "YodaBot is writing...",
                        onDownloadProgress: progressEvent => this.inputInfoMessage = ""
                    }
                    let urlRequest = "https://inbenta-challenge.test/api/conversation/message";

                    axios.post(urlRequest, {message: tempMessageObject.content}, axiosConfig)
                    .then(response => (

                        this.message = {content: response.data.answer, bot: true})
                    )
                    .catch(error => console.log(error))
                    return;
                }
                this.inputInfoMessage = "Input can not be empty...";

            },

        }
    }
</script>

<style>
    .inputInfoText{
        font-size: 0.7rem;
        font-style: italic;
    }
</style>
