<template>
  <v-dialog v-model="dialog" fullscreen hide-overlay transition="dialog-bottom-transition">
    <v-btn slot="activator" color="primary" dark>{{modal.button.text}}</v-btn>
    <v-card>
      <v-toolbar dark color="indigo">
        <v-btn icon dark @click.native="dialog = false">
          <v-icon>close</v-icon>
        </v-btn>
        <v-toolbar-title>{{modal.title}}</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-toolbar-items>
          <v-btn dark flat @click.native="dialog = false">Close</v-btn>
        </v-toolbar-items>
      </v-toolbar>
      <v-card-text style="height: 700px;">
          <iframe :src="modal.fileUrl" 
              frameborder="0" width="100%" height="100%"></iframe>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<script>
  export default {
    data () {
      return {
        dialog: false,
        notifications: false,
        sound: true,
        widgets: false
      }
    },
    props: {
        modal: {
            type: Object,
            default: function() {
                return {
                    button: {
                        text: 'Some Button'
                    },
                    title: 'Some Title',
                    fileUrl: 'Some Url'
                }
            }
        }
    }
  }
</script>