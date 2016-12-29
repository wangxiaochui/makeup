<template>
    <div id="main" >
        <div v-for="(styles,index) in styleDatas" class="page" :style="pageStyle">
            <div v-for="style in styles" class="photo" :style="style.div">
                <img :src='style.path' :style='style.img' />
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        data() {
            return {
                data : null,
                wh : 1,
                width : 48,
                bg : ''
            }
        },
        created() {
            this.$http.get('/makeup?is_use_temp=1&temp_id=1').then(({data}) =>{
                var data = JSON.parse(data);
                this.data = data['data'];
                this.wh = data['wh'];
                this.bg = data['bg'];
            },({err})=>{

            })
        },

        computed:{
            styleDatas : function(){
               let data = this.data
               var style = new Array();
               var all = new Array();
               for (var k in data){
                   let pageData = data[k];
                    all[k] = new Array();
                   for(let pk in pageData){
                       let divStyle = {
                           width : pageData[pk]['width']+'%',
                           height : pageData[pk]['height']+'%',
                           position : 'absolute',
                           top : pageData[pk]['top']+'%',
                           left : pageData[pk]['left']+'%',
                           overflow:'hidden',
                           transform:"rotate("+pageData[pk]['rotate']+"deg)",
                           MsTransform:"rotate("+pageData[pk]['rotate']+"deg)",
                           mozTransform:"rotate("+pageData[pk]['rotate']+"deg)",
                           webkitTransform:"rotate("+pageData[pk]['rotate']+"deg)",

                           borderRadius : pageData[pk]['radius']+'%'


                       };
                       let imgStyle = {
                            width : pageData[pk]['relative_width']+'%',
                            height : pageData[pk]['relative_height']+'%',
                            position : 'relative',
                            top : '-'+pageData[pk]['relative_cut_top']+'%',
                            left : '-'+pageData[pk]['relative_cut_left']+'%'
                       }

                       let all_style = {div : divStyle, img : imgStyle, path:pageData[pk]['path']};

                        all[k][pk] = all_style;
                   }
               }

               return all;
            },
            pageStyle : function(){
                let totalWidth = $('#app').width();
                let real_width = (this.width*totalWidth)/100;
                let real_height = real_width/this.wh;

                return {width:real_width+'px', height:real_height+'px',backgroundImage:"url("+this.bg+")",backgroundSize: '100% 100%' };
            },

        }

    }
</script>