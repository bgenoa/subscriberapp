<template>
  <div>
    <v-alert v-model="show" v-bind:dismissible="alert.dismissable" 
      v-bind:type="alert.color">
      <slot></slot>
    </v-alert>
  </div>
</template>

<script>
  export default {
    data () {
      return {
        show: false
      }
    },
    props: {
        alert: {
            type: Object,
            default: function () {
                return {
                    color: 'success',
                    dismissable: true,
                    show: true
                }
            }
        }
    },
    watch: {
      show (val) {
        this.$emit('alert-dismissed', val);
      } 
    }
  }
</script>