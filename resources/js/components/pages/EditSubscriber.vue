<template>
    <v-content>
        <v-container grid-list-md text-xs-center>
            <v-card class="my-4">
                <v-sheet
                    class="v-sheet--offset mx-auto"
                    color="green"
                    elevation="5"
                    max-width="calc(100% - 32px)"
                    >
                    <v-card text-xs-center dark class="pa-3 green">
                        <v-layout row align-center>
                            <v-flex xs8 text-xs-left pl-3>
                                <span>
                                    <h2 class="title font-weight-light">Subscriber Details</h2>
                                </span>
                            </v-flex>
                            <v-flex xs4 text-xs-right>
                                <v-btn text-xs-right flat icon color="white" @click="$router.go(-1)">
                                    <v-icon>arrow_back</v-icon>
                                </v-btn>
                            </v-flex>
                        </v-layout>
                    </v-card>
                </v-sheet>
                <v-card-text class="px-5 pt-0">
                    <v-form v-model="subscriberValid" ref="form" mx-5>
                        <alert ref="alert1" v-bind:alert="alert" v-on:alert-dismissed="alertDismissed($event)">{{alert.text}}</alert>
                        <v-text-field label="Email"
                            v-model="subscriberForm.email" 
                            :rules="subscriberForm.emailRules"
                            required></v-text-field>                        
                        <v-text-field label="First Name"
                            v-model="subscriberForm.firstname" 
                            :rules="subscriberForm.fnameRules"
                            required></v-text-field>
                        <v-text-field label="Last Name"
                            v-model="subscriberForm.lastname" 
                            :rules="subscriberForm.lnameRules"
                            required></v-text-field>
                        <v-select
                            :items="stateOptions"
                            label="State"
                            :rules="subscriberForm.stateRules"
                            v-model="subscriberForm.state" 
                            required></v-select>
                        <v-btn @click="subscriberFormSubmit" v-show="subscriberValid" class="success">submit</v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
            <v-card class="my-5">
                <v-sheet
                    class="v-sheet--offset mx-auto"
                    color="green"
                    elevation="5"
                    max-width="calc(100% - 32px)"
                    >
                    <v-card text-xs-center dark class="pa-3 green">
                        <v-layout row align-center>
                            <v-flex xs8 text-xs-left>
                                <span>
                                    <h2 class="title font-weight-light">Fields</h2>
                                </span>
                            </v-flex>                            
                            <v-flex xs4 text-xs-right>
                                <v-btn text-xs-right flat icon color="white">
                                    <v-icon @click="editField(null)">add</v-icon>
                                </v-btn>
                            </v-flex>
                        </v-layout>
                    </v-card>
                </v-sheet>
                <v-card-text class="px-4 pt-0" child-flex>
                    <alert ref="alert2" v-bind:alert="alert2" v-on:alert-dismissed="alert2Dismissed($event)">{{alert2.text}}</alert>
                    <v-data-table
                        :headers="headers"
                        :items="fields"
                        hide-actions
                        class="elevation-1 tr-clickable">
                        <template slot="items" slot-scope="props">
                            <td class="text-xs-left" @click="editField(props.item)">{{ props.item.title }}</td>
                            <td class="text-xs-left" @click="editField(props.item)">{{ props.item.type }}</td>
                            <td class="text-xs-right">
                                <v-btn icon @click="deleteFieldBtn(props.item)">
                                    <v-icon color="red darken-2">cancel</v-icon>
                                </v-btn>
                            </td>
                        </template>
                        <template slot="no-data">
                            No fields to display.
                        </template>
                    </v-data-table>
                    <v-layout row justify-center>
                        <v-dialog v-model="dialog" persistent max-width="500px">
                            <v-card>
                                <v-card-title class="headline grey lighten-2" primary-title>
                                    <span class="headline">Field Details</span>
                                </v-card-title>
                                <v-card-text>
                                    <v-container grid-list-md>
                                        <v-layout wrap>
                                            <v-flex xs12>
                                                <v-form v-model="fieldValid" ref="form2">                                                        
                                                    <v-text-field 
                                                        label="Title" 
                                                        v-model="FieldForm.title" 
                                                        :value="selectedField.title" 
                                                        :rules="FieldForm.titleRules"
                                                        required></v-text-field>
                                                    <v-select
                                                        :items="typeOptions"
                                                        label="Type"
                                                        v-model="FieldForm.type" 
                                                        :value="selectedField.type"
                                                        required></v-select>
                                                </v-form>
                                            </v-flex>
                                        </v-layout>
                                    </v-container>
                                </v-card-text>
                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn color="blue darken-1" flat @click.native="dialog = false">Close</v-btn>
                                    <v-btn v-show="fieldValid" color="blue darken-1" flat @click="submitFieldForm">Save</v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
                        <modal-dialog-no-btn ref="modal2" v-bind:modal="deleteBtn" v-on:dialog-agree="deleteField($event)">
                            Are you sure you want to delete {{selectedField.title}}?</modal-dialog-no-btn>
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
    props: {
        id: Number
    },
    data () {
      return {
        subscriber: {}, 
        fields: [], 
        headers: [
            {text: 'Title', align: 'left', sortable: true, value: 'title', class: ['grey', 'lighten-2']},
            {text: 'Type', align: 'left', sortable: true, value: 'type', class: ['grey', 'lighten-2']},
            {text: 'Delete', align: 'right', sortable: false, value: 'delete', class: ['grey', 'lighten-2']}
        ],
        stateOptions: ['active', 'unsubscribed', 'junk', 'bounced', 'unconfirmed'], 
        typeOptions: ['date', 'number', 'string', 'boolean'], 
        selectedField: {},
        subscriberValid: true,
        subscriberForm: {
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
        dialog: false,
        fieldValid: true,
        FieldForm: {
            id: 0,
            title: '',
            titleRules: [
                (v) => !!v || 'Title is required', 
                (v) => v.length <= 50 || 'Title has a max of 200 characters'
            ],
            type: '',
            typeRules: [
                (v) => !!v || 'Type is required'
            ]
        },
        // subscriber alert
        alert: {
            color: 'info',
            show: false,
            dismissable: true,
            text: ''
        }, 
        // field alert
        alert2: {
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
    methods: {
        getSubscriber() {
            let config = {
                headers: {
                    Accept: 'application/json',
                    Authorization: 'Bearer ' + this.$store.state.access_token
                }
            }
            axios.get('api/subscribers/' + this.id, {
                headers: config.headers
            })
            .then((response) => {
                this.subscriberForm.firstname = response.data.data.firstname;
                this.subscriberForm.lastname = response.data.data.lastname;
                this.subscriberForm.email = response.data.data.email;
                this.subscriberForm.state = response.data.data.state;
            })
            .catch((error) => {
                this.$emit('ajax-unauth', error);
            });
        }, 
        getFields() {
            let config = {
                headers: {
                    Accept: 'application/json',
                    Authorization: 'Bearer ' + this.$store.state.access_token
                }
            }
            axios.get('api/subscribers/' + this.id + '/fields', {
                headers: config.headers
            })
            .then((response) => {
                this.fields = response.data.data;
            })
            .catch((error) => {
                this.$emit('ajax-unauth', error);
            });
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
        subscriberFormSubmit() {
            if (this.$refs.form.validate()) {
                let config = {
                    headers: {
                        Accept: 'application/json',
                        Authorization: 'Bearer ' + this.$store.state.access_token
                    }
                }
                axios.patch('api/subscribers/' + this.id, {
                    email: this.subscriberForm.email,
                    firstname: this.subscriberForm.firstname,
                    lastname: this.subscriberForm.lastname,
                    state: this.subscriberForm.state
                }, config)
                .then(response => {
                    this.drawAlert('info', response.data.message);
                })
                .catch(error => {
                    this.drawAlert('error', error.response.data.message);
                    this.$emit('ajax-unauth', error);
                });            
            }
        },
        drawAlert2(color, text) {
            this.alert2.color = color;
            this.alert2.show = true;
            this.alert2.text = text;
            this.alert2.dismissable = true;
            this.$refs.alert2.show = this.alert2.show;
        },
        alert2Dismissed(val) {
            this.alert2.show = val;
        },
        editField(Field) {
            if(Field != null) {
                this.selectedField = Field;
                this.FieldForm.id = Field.id;
                this.FieldForm.title = Field.title;
                this.FieldForm.type = Field.type;
            }
            else {
                this.selectedField = {};
                this.FieldForm.id = null;
                this.FieldForm.title = '';
                this.FieldForm.type = 'string';
            }
            this.dialog = true;
        },
        deleteFieldBtn(Field) {
            this.selectedField = Field;
            this.deleteBtn.headlineText = 'Delete Field';
            this.deleteBtn.agree = 'dialog-agree';
            this.$refs.modal2.dialog = true;
        },
        deleteField(event) {
            if (event) {
                let config = {
                    headers: {
                        Accept: 'application/json',
                        Authorization: 'Bearer ' + this.$store.state.access_token
                    }
                }
                axios.delete('api/fields/' + this.selectedField.id, config)
                .then(response => {
                    this.drawAlert2('info', response.data.message);
                    this.getFields();
                })
                .catch(error => {
                    this.drawAlert2('error', error.response.data.message);
                    this.$emit('ajax-unauth', error);
                });
            }
        },
        submitFieldForm() {
            this.dialog = false;
            let config = {
                headers: {
                    Accept: 'application/json',
                    Authorization: 'Bearer ' + this.$store.state.access_token
                }
            }
            if(this.FieldForm.id == null) {
                // new field
                axios.post('api/subscribers/' + this.id + '/fields', {
                    title: this.FieldForm.title,
                    type: this.FieldForm.type
                }, config)
                .then(response => {
                    this.drawAlert2('info', response.data.message);
                    this.getFields();
                })
                .catch(error => {
                    this.drawAlert2('error', error.response.data.message);
                    this.$emit('ajax-unauth', error);
                });
            } else {
                // update existing field
                axios.patch('api/fields/' + this.FieldForm.id, {
                    subscriber_id: this.id,
                    title: this.FieldForm.title,
                    type: this.FieldForm.type
                }, config)
                .then(response => {
                    this.drawAlert2('info', response.data.message);
                    this.getFields();
                })
                .catch(error => {
                    this.drawAlert2('error', error.response.data.message);
                    this.$emit('ajax-unauth', error);
                });
            }
        }
    }, 
    mounted() {
        this.getSubscriber();
        this.getFields();
    }
}
</script>