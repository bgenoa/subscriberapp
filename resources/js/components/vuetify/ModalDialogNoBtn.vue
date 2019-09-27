<template>
  <v-layout row justify-center>
    <v-dialog v-model="dialog" persistent max-width="290">
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
  </v-layout>
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
