<template>
  <v-dialog v-model="dialog" persistent max-width="350">
    <v-btn slot="activator" :color="modal.button.color" dark>{{modal.button.text}}</v-btn>
    <v-card>
      <v-card-title class="headline grey lighten-2">{{modal.headlineText}}</v-card-title>
      <v-card-text>
          <slot></slot>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="green darken-1" flat @click.native="dialog = false">No</v-btn>
        <v-btn color="red darken-1" flat @click="agree(true)">Yes</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  export default {
    data () {
      return {
        dialog: false
      }
    },
    props: {
        modal: {
            type: Object,
            default: function() {
                return {
                    headlineText: 'Some Headline',
                    button: {
                        text: 'Some Button',
                        color: 'red'
                    },
                    agree: 'dialog-agree'
                }
            }
        }
    },
    methods: {
        agree(value) {
            //console.log('component value = ' + value);
            this.dialog = false;
            this.$emit(this.modal.agree, value);
        }
    },
    mounted () {
        //console.log('agree method name = ' + this.modal.agree);
    }
  }
</script>
