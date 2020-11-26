<template>
    <div class="container">

        <message-component :message="message"></message-component>

        <div class="input-group mb-3">
            <input v-model="text" type="text" class="form-control" aria-describedby="Default">
            <button @click="sendMessage" class="btn-sm btn-outline-primary" type="button">Send!</button>
        </div>
    </div>
</template>

<script>
    export default {
        data (){
            return {message: '', text: ''}
        },
        mounted() {
        },
        methods : {
            sendMessage: function() {
                this.message = this.text
                this.text = ''
            }
        }
    }
</script>
