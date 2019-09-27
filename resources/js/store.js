import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'

Vue.use(Vuex);

export default new Vuex.Store({
    // global state of data for the application
    // $store.state
    state: {
        message: 'Vuex is loaded!',
        access_token: localStorage.getItem('access_token') || null,
        user: {},
        useStorage: true
    },

    // getters
    // $store.getters
    getters: {
        getBearerHeader: state => {
            return 'Bearer ' + state.access_token;
        }
    },

    // mutations
    // $store.mutations
    mutations: {
        // set the access token
        setAccessToken: (state, token) => {
            state.access_token = token;
        },
        // set the user
        setUser: (state, user) => {
            state.user = user;
        }
    },

    // actions
    // $store.actions
    actions: {
        // get a token Promise
        getToken(context, request) {
            return new Promise((resolve, reject) => {
                axios.post('api/login', request)
                .then(response => {
                    //console.log(response);
                    // set the token
                    let accessToken = response.data.access_token;
                    if (context.state.useStorage) {
                        localStorage.removeItem('access_token');
                        localStorage.setItem('access_token', accessToken);
                    }
                    context.commit('setAccessToken', accessToken);
                    resolve(response);
                })
                .catch(error => {
                    //console.log(error);
                    reject(error);
                });
            });
        },

        // get a user Promise
        getUser(context) {
            return new Promise((resolve, reject) => {
                let header = {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + context.state.access_token
                    }
                }
                axios.get('api/user', header)
                .then(response => {
                    //console.log(response);
                    let user = response.data;
                    context.commit('setUser', user);
                    resolve(response);
                })
                .catch(error => {
                    //console.log(error);
                    reject(error);
                });
            });
        },

        // destroy a token Promise
        destroyToken(context) {
            // clear the tokens
            localStorage.removeItem('access_token');
            return new Promise((resolve, reject) => {
                let header = {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + context.state.access_token
                    }
                }
                axios.post('api/logout', null, header)
                .then(response => {
                    //console.log(response);                    
                    resolve(response);
                })
                .catch(error => {
                    //console.log(error);
                    reject(error);
                });
                // update the store regardless of logout response
                context.commit('setAccessToken', null);
                context.commit('setUser', {});
            });
        }
    }
})