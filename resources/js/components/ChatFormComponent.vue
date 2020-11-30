<template>
    <div class="container">
        <div class="row">
            <message-component :message="message"></message-component>

            <div class="col-sm-6">
                <span class="inputInfoText">{{inputInfoMessage}}</span>
                <input v-model="text" type="text" class="form-control" aria-describedby="Default">
                <button  @click="sendMessage" class="btn-sm btn-outline-primary" type="button">Send!</button>
            </div>

        </div>
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
                        onUploadProgress: progressUpload => this.inputInfoMessage = "writing...",
                        onDownloadProgress: progressEvent => this.inputInfoMessage = ""
                    }
                    axios.post('http://inbenta-challenge.test/api/conversation/message', {message: tempMessageObject.content}, axiosConfig)
                    .then(response => (this.message = {content: response.data.answer, bot: true}))
                    .catch(error => console.log(error))
                    return;
                }
                this.inputInfoMessage = "Input can not be empty";

            },

        }
    }
</script>

<style>
    .inputInfoText{
        font-size: 0.7rem;
    }
</style>
