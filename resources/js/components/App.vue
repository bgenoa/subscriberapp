<template>
    <v-app>

        <v-slide-y-transition>
            <v-toolbar color="primary" dark app v-show="loggedIn">
                <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
                <v-btn icon large :href="layout.site" target="_blank">
                    <v-img :src="layout.logo" :alt="layout.company"></v-img>
                </v-btn>
                <v-toolbar-title>{{layout.title}}</v-toolbar-title>
                <v-spacer></v-spacer>
            </v-toolbar>
        </v-slide-y-transition>

        <v-navigation-drawer v-model="drawer" temporary app dark>
            <v-list class="pa-1">
                <v-list-tile avatar>
                    <v-list-tile-avatar>
                        <img :src="layout.logo" :alt="layout.company">
                    </v-list-tile-avatar>

                    <v-list-tile-content>
                        <v-list-tile-title>{{this.$store.state.user.email}}</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>

            <v-list class="pt-0" dense>
                <v-divider></v-divider>

                <v-subheader class="mt-3 grey--text text--lighten-1">SUBSCRIBERS</v-subheader>
                <v-list-tile v-for="item in subscriberItems" :key="item.text" :to="{name: item.to}">
                    <v-list-tile-action>
                        <v-icon>{{item.icon}}</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>{{item.text}}</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>

                <template>
                    <v-subheader class="mt-3 grey--text text--lighten-1">FIELDS</v-subheader>
                    <v-list-tile v-for="item in fieldItems" :key="item.text" :to="{name: item.to}">
                        <v-list-tile-action>
                            <v-icon>{{item.icon}}</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>{{item.text}}</v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </template>

                <v-divider class="my-3"></v-divider>

                <template>
                    <v-list-tile @click="logOut()">
                        <v-list-tile-action>
                            <v-icon>lock</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>Logout</v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>
                </template>
                
            </v-list>
        </v-navigation-drawer>

        <v-snackbar
            v-model="snackbar"
            :timeout="snackbarTimeout"
            :top="true"
            >
            {{ snackbarText }}
            <v-btn
                color="red"
                flat
                @click="snackbar = false"
            >
                Close
            </v-btn>
        </v-snackbar>

        <router-view 
            :key="componentKey" 
            v-on:change-route-key="changeRouteKey" 
            v-on:post-login="postLogin"
            v-on:ajax-unauth="ajaxUnauthorized"></router-view>

        <v-slide-y-reverse-transition>
            <v-footer color="primary justify-center" app v-show="loggedIn">
                <span class="white--text">&copy; {{ new Date().getFullYear() }}</span>
            </v-footer>
        </v-slide-y-reverse-transition>

    </v-app>
</template>

<script>
    export default {
        data: () => ({
            time: 0,
            alive: true,
            pulseTime: 60000,
            interval: 0,
            drawer: null,
            componentKey: 0,
            subscriberItems: [
                {icon: 'group', to: 'subscribers', text: 'Subscribers'},
                {icon: 'person_add', to: 'addsubscriber', text: 'Add Subscriber'}
            ],
            fieldItems: [
                {icon: 'input', to: 'fields', text: 'Fields'},
            ],
            layout: {
                logo: 'img/logo.png',
                title: 'Subscriber Management',
                company: 'Placeholder',
                site: 'https://www.google.com/'
            },
            snackbar: false,
            snackbarTimeout: 10000,
            snackbarText: ''
        }),
        methods: {
            changeRouteKey() {
                this.componentKey++;
            },
            keepAlive() {
                if (this.alive) {
                    this.interval = setTimeout(this.getPulse, this.pulseTime);
                } else {
                    clearTimeout(this.interval);
                }
            },
            getPulse() {
                let config = {
                    headers: {
                        Accept: 'application/json',
                        Authorization: 'Bearer ' + this.$store.state.access_token
                    }
                }
                axios.get('api/pulse', {
                    headers: config.headers
                })
                .then((response) => {
                    this.alive = true;
                })
                .catch((error) => {
                    this.ajaxUnauthorized(error);
                });
                this.keepAlive();
            },
            logOut() {
                clearTimeout(this.interval);
                this.$store.dispatch('destroyToken')
                .then((response) => {
                    // wait an extra second for slow writes to storage/cookie to register
                    setTimeout(this.logInRoute, 100);                    
                })
                .catch((error) => {
                    // wait an extra second for slow writes to storage/cookie to register
                    setTimeout(this.logInRoute, 100); 
                });
            },
            postLogin() {
                this.$store.dispatch('getUser')
                .then((response) => {
                    this.keepAlive();
                    this.$router.push({ name:'subscribers' });
                    this.time = this.getTime();
                    this.timeDiff();
                })
                .catch((error) => {
                    this.ajaxUnauthorized(error);
                });
            },
            drawSnackbar(text) {
                this.snackbarText = text;
                this.snackbar = true;
            },
            ajaxUnauthorized(error) {
                if (error.response.status === 401) {
                    this.drawSnackbar("Session expired, please log in again.");
                    this.alive = false;
                }
            },
            getTime() {
                let d = new Date();
                return d.getTime();
            },
            timeDiff() {
                let diff = (this.getTime() - this.time) / 1000;
                if (diff > 3600) {
                    this.getPulse();
                }
                this.time = this.getTime();
                setTimeout(this.timeDiff, 1000);
            },
            logInRoute() {
                this.$router.push({ name: 'login' });
            }
        },
        mounted() {
            if (this.loggedIn) {
                this.$store.dispatch('getUser');
                this.keepAlive();
            }
        },
        watch: {
            alive (val) {
                if (val === false) {
                    this.logOut();
                }
            }
        },
        computed: {
            loggedIn() {
                return this.$store.state.access_token !== null;
            }
        }
    }
</script>
