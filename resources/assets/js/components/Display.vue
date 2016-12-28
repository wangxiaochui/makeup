<template>
    <div id="main">
        <div class="page" style="overflow:hidden">
            <div id="photo">

            </div>
        </div>

    </div>
</template>

<script>
    export default {
        data() {
            return {
                'data' : null,
                'wh' : 1
            }
        },
        created() {
            this.$http.get('/makeup').then(({data}) =>{
                var data = JSON.parse(data);
                this.data = data['data'];
                this.wh = data['wh']
            },({err})=>{

            })
        }
    }
</script>