<template>
    <v-content>
        <v-container grid-list-md text-xs-center>
            <v-card class="mt-4">
                <v-sheet
                    class="v-sheet--offset mx-auto"
                    color="green"
                    elevation="5"
                    max-width="calc(100% - 32px)"
                    >
                    <v-card text-xs-center dark class="pa-3 green">
                        <span>
                            <h2 class="title font-weight-light">Add Subscriber</h2>
                        </span>
                    </v-card>
                </v-sheet>
                <v-card-text class="px-5 pt-0">
                    <v-form v-model="valid" ref="form">
                        <v-text-field label="Email"
                            v-model="email" 
                            :rules="emailRules"
                            required></v-text-field>                        
                        <v-text-field label="First Name"
                            v-model="firstname" 
                            :rules="firstnameRules"
                            required></v-text-field>
                        <v-text-field label="Last Name"
                            v-model="lastname" 
                            :rules="lastnameRules"
                            required></v-text-field>
                        <v-select
                            :items="stateOptions"
                            label="State"
                            :rules="stateRules"
                            v-model="state" 
                            required></v-select>
                        <v-btn @click="submit" v-show="valid" class="success">submit</v-btn>
                        <alert ref="alert1" v-bind:alert="alert" v-on:alert-dismissed="alertDismissed($event)">{{alert.text}}</alert>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-container>
    </v-content>
</template>

<script>
import Alert from '../vuetify/Alert.vue'

export default {
    components: {
        Alert
    },
    data () {
      return {
        valid: false,
        stateOptions: ['active', 'unsubscribed', 'junk', 'bounced', 'unconfirmed'],
        firstname: '',
        firstnameRules: [
            (v) => !!v || 'First Name is required', 
            (v) => v.length <= 50 || 'First Name has a max of 50 characters'
        ],
        lastname: '',
        lastnameRules: [
            (v) => !!v || 'Last Name is required', 
            (v) => v.length <= 50 || 'Last Name has a max of 50 characters'
        ],
        email: '',
        emailRules: [
            (v) => !!v || 'Email is required',
            (v) => /.+@.+/.test(v) || 'Email must be valid', 
            (v) => v.length <= 200 || 'Email has a max of 200 characters'
        ],
        state: '',
        stateRules: [
            (v) => !!v || 'State is required'
        ],     
        alert: {
            color: 'info',
            show: false,
            dismissable: true,
            text: ''
        }
      }
    },
    methods: {
        submit() {
            if (this.$refs.form.validate()) {
                let config = {
                    headers: {
                        Accept: 'application/json',
                        Authorization: 'Bearer ' + this.$store.state.access_token
                    }
                }
                axios.post('api/subscribers', {
                    email: this.email,
                    firstname: this.firstname,
                    lastname: this.lastname,
                    state: this.state,
                }, config)
                .then(response => {
                    this.drawAlert('info', response.data.message);
                    this.$router.push("subscribers")
                })
                .catch(error => {
                    this.drawAlert('error', error.response.data.message);
                    this.$emit('ajax-unauth', error);
                });                
            }
        },
        drawAlert(color, text) {
            this.alert.color = color;
            this.alert.show = true;
            this.alert.text = text;
            this.alert.dismissable = true;
            this.$refs.alert1.show = this.alert.show;
        },
        alertDismissed(val) {
            this.alert.show = val;
        },
    }
}
</script>