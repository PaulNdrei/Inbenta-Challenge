<template>
    <div class="container">

        <message-component :message="message"></message-component>

        <div class="input-group mb-3">
            <span>{{inputInfoMessage}}</span>
            <br><input v-model="text" type="text" class="form-control" aria-describedby="Default">
            <button  @click="sendMessage" class="btn-sm btn-outline-primary" type="button">Send!</button>

        </div>
    </div>
</template>

<script>
    export default {
        data (){
            return {
                message: '',
                text: '',
                inputInfoMessage: ''
            }
        },
        mounted() {
        },
        methods : {
            sendMessage: function() {
                if (this.text){
                    this.message = this.text;
                    this.text = '';
                    return;
                }
                this.inputInfoMessage = "Input can not be empty";

                
                
            }
        }
    }
</script>
