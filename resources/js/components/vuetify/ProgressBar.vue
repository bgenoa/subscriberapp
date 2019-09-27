<template>
  <v-progress-linear 
    v-bind:value="bar.value" 
    v-show="bar.show" 
    v-bind:indeterminate="bar.indeterminate"
    v-bind:color="bar.color"
    v-bind:background-color="bar.backgroundColor"></v-progress-linear>
</template>

<script>
  export default {
    props: {
        bar: {
            type: Object,
            default () {
                return {
                    value: 50,
                    show: true,
                    indeterminate: false,
                    color: 'info',
                    backgroundColor: 'blue lighten-4'
                }
            }
        }
    }
  }
</script>