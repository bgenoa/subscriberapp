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
                            <h2 class="title font-weight-light">Subscribers</h2>
                        </span>
                    </v-card>
                </v-sheet>
                <v-card-text class="px-4 pt-0" child-flex>
                    <alert ref="alert1" v-bind:alert="alert" v-on:alert-dismissed="alertDismissed($event)">{{alert.text}}</alert>
                    <v-data-table
                        :headers="headers"
                        :items="subscribers"
                        hide-actions
                        class="elevation-1 tr-clickable">
                        <template slot="items" slot-scope="props">
                            <td class="text-xs-left" @click="editSubscriber(props.item)">{{ props.item.id }}</td>
                            <td class="text-xs-left" @click="editSubscriber(props.item)">{{ props.item.email }}</td>
                            <td class="text-xs-left" @click="editSubscriber(props.item)">{{ props.item.state }}</td>
                            <td class="text-xs-right">
                                <v-btn icon @click="deleteSubscriberBtn(props.item)">
                                    <v-icon color="red darken-2">cancel</v-icon>
                                </v-btn>
                            </td>
                        </template>
                        <template slot="no-data">
                            No subscribers to display.
                        </template>
                    </v-data-table>
                    <v-layout row justify-center>
                        <v-dialog v-model="dialog" persistent max-width="500px">
                            <v-card>
                                <v-card-title class="headline grey lighten-2" primary-title>
                                    <span class="headline">Subscriber Details</span>
                                </v-card-title>
                                <v-card-text>
                                    <v-container grid-list-md>
                                        <v-layout wrap>
                                            <v-flex xs12>
                                                <v-form v-model="valid" ref="form">                                                        
                                                    <v-text-field 
                                                        label="First Name" 
                                                        v-model="subscriberForm.firstname" 
                                                        :value="selectedSubscriber.firstname" 
                                                        :rules="subscriberForm.fnameRules"
                                                        required></v-text-field>
                                                    <v-text-field 
                                                        label="Last Name" 
                                                        v-model="subscriberForm.lastname" 
                                                        :value="selectedSubscriber.lastname" 
                                                        :rules="subscriberForm.lnameRules"
                                                        required></v-text-field>
                                                    <v-text-field 
                                                        label="Email" 
                                                        v-model="subscriberForm.email" 
                                                        :value="selectedSubscriber.email"
                                                        :rules="subscriberForm.emailRules"
                                                        required></v-text-field>
                                                    <v-select
                                                        :items="stateOptions"
                                                        label="State"
                                                        v-model="subscriberForm.state" 
                                                        :value="selectedSubscriber.state"
                                                        required></v-select>
                                                </v-form>
                                            </v-flex>
                                        </v-layout>
                                    </v-container>
                                </v-card-text>
                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn color="blue darken-1" flat @click.native="dialog = false">Close</v-btn>
                                    <v-btn v-show="valid" color="blue darken-1" flat @click="submitForm">Save</v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
                        <modal-dialog-no-btn ref="modal2" v-bind:modal="deleteBtn" v-on:dialog-agree="deleteSubscriber($event)">
                            Are you sure you want to delete {{selectedSubscriber.email}}?</modal-dialog-no-btn>
                    </v-layout>
                </v-card-text>
            </v-card>
        </v-container>
    </v-content>
</template>

<script>
    import Alert from '../vuetify/Alert.vue'
    import ModalDialogNoBtn from '../vuetify/ModalDialogNoBtn.vue'

    export default {
        components: {
            Alert, 
            ModalDialogNoBtn
        }, 
        data () {
            return {
                headers: [
                    {text: 'ID', align: 'left', sortable: true, value: 'id', class: ['grey', 'lighten-2']},
                    {text: 'Email', align: 'left', sortable: true, value: 'email', class: ['grey', 'lighten-2']},
                    {text: 'State', align: 'left', sortable: true, value: 'state', class: ['grey', 'lighten-2']},
                    {text: 'Delete', align: 'right', sortable: false, value: 'delete', class: ['grey', 'lighten-2']}
                ],
                subscribers: [],
                stateOptions: ['active', 'unsubscribed', 'junk', 'bounced', 'unconfirmed'], 
                selectedSubscriber: {},
                dialog: false,
                subscriberForm: {
                    id: 0,
                    firstname: '',
                    fnameRules: [
                        (v) => !!v || 'First Name is required', 
                        (v) => v.length <= 50 || 'First Name has a max of 50 characters'
                    ],
                    lastname: '',
                    lnameRules: [
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
                    ]
                },
                valid: true,
                alert: {
                    color: 'info',
                    show: false,
                    dismissable: true,
                    text: ''
                },
                deleteBtn: {
                    headlineText: '',
                    agree: ''
                }
            }
        },
        props: {
            // props here
        },
        methods: {
            editSubscriber(subscriber) {
                this.selectedSubscriber = subscriber;
                this.$router.push({ name: 'editsubscriber', params: { id: this.selectedSubscriber.id } });
            },
            deleteSubscriberBtn(subscriber) {
                this.selectedSubscriber = subscriber;
                this.deleteBtn.headlineText = 'Delete Subscriber';
                this.deleteBtn.agree = 'dialog-agree';
                this.$refs.modal2.dialog = true;
            },
            deleteSubscriber(event) {
                if (event) {
                    let config = {
                        headers: {
                            Accept: 'application/json',
                            Authorization: 'Bearer ' + this.$store.state.access_token
                        }
                    }
                    axios.delete('api/subscribers/' + this.selectedSubscriber.id, config)
                    .then(response => {
                        this.drawAlert('info', response.data.message);
                        this.getSubscribers();
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
            submitForm() {
                this.dialog = false;
                let config = {
                    headers: {
                        Accept: 'application/json',
                        Authorization: 'Bearer ' + this.$store.state.access_token
                    }
                }
                axios.patch('api/subscribers/' + this.subscriberForm.id, {
                    firstname: this.subscriberForm.firstname,
                    lastname: this.subscriberForm.lastname,
                    email: this.subscriberForm.email, 
                    state: this.subscriberForm.state
                }, config)
                .then(response => {
                    this.drawAlert('info', response.data.message);
                    this.getSubscribers();
                })
                .catch(error => {
                    this.drawAlert('error', error.response.data.message);
                    this.$emit('ajax-unauth', error);
                });
            },
            getSubscribers() {
                let config = {
                    headers: {
                        Accept: 'application/json',
                        Authorization: 'Bearer ' + this.$store.state.access_token
                    }
                }
                axios.get('api/subscribers', {
                    headers: config.headers
                })
                .then((response) => {
                    this.subscribers = response.data.data;       
                })
                .catch((error) => {
                    this.$emit('ajax-unauth', error);
                });
            },
        },
        computed: {
            // computed here
        },
        mounted() {
            this.getSubscribers();
        }
    }
</script>
