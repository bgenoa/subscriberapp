<template>
  <v-app id="inspire">
    <v-content>
      <v-container fluid fill-height>
        <v-layout align-center justify-center>
          <v-flex xs12 sm8 md4 text-xs-center>
            <img src="img/logo.png" height="100" class="mb-4">
            <v-card class="elevation-12">
              <v-toolbar dark color="primary">
                <v-toolbar-title>Login</v-toolbar-title>
                <v-spacer></v-spacer>
              </v-toolbar>
              <v-card-text>
                <v-form ref="form" v-model="valid">
                  <v-text-field 
                    prepend-icon="person" name="email" label="Email" 
                    type="text" :rules="emailRules" v-model="email"
                    required></v-text-field>
                  <v-text-field 
                    id="password" prepend-icon="lock" name="password" 
                    label="Password" type="password" :rules="passwordRules"
                    v-model="password" required></v-text-field>
                </v-form>
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-card-text v-show="hasLoginErrors" class="red--text">{{loginErrorMessage}}</v-card-text>
                <v-card-text v-show="success" class="green--text">{{successMessage}}</v-card-text>
                <v-card-text v-show="ajaxRunning" class="info--text">{{ajaxMessage}}</v-card-text>
                <v-btn 
                  color="primary" 
                  @click="login" 
                  v-show="!success"
                  :disabled="!valid">Login</v-btn>
              </v-card-actions>
            </v-card>
          </v-flex>
        </v-layout>
      </v-container>
    </v-content>
  </v-app>
</template>

<script>

import axios from 'axios'

  export default {
    data: function() {
        return {
            drawer: null,
            valid: true,
            email: '',
            emailRules: [
                (v) => !!v || 'Email is required',
                (v) => /.+@.+/.test(v) || 'Email must be valid'
            ],
            password: '',
            passwordRules: [
                v => !!v || 'Password is required'
            ],
            hasLoginErrors: false,
            loginErrorMessage: '',
            success: false,
            successMessage: '',
            ajaxRunning: false,
            ajaxMessage: ''
        }    
    },
    methods: {
        login: function() {
          // get the request parameters
          let bodyFormData = new FormData();
          bodyFormData.set('username', this.email);
          bodyFormData.set('password', this.password);

          // statuses
          this.ajaxRunning = true;
          this.hasLoginErrors = false;
          this.success = false;
          this.ajaxMessage = "Authenticating..."

          // make the request through the store
          this.$store.dispatch('getToken', bodyFormData)
          .then((response) => {
            this.ajaxRunning = false;
            if (response.status === 200) {
              // clear errors
              this.loginErrorMessage = '';
              this.hasLoginErrors = false;
              this.success = true;
              this.successMessage = 'Success!  Please wait...';
              
              // emit the post login process to the parent
              this.$emit('post-login');
            }
          })
          .catch((error) => {
            this.ajaxRunning = false;
            this.loginErrorMessage = error.response.data.message;
            this.hasLoginErrors = true;
          });
        }
    }
  }
</script>
