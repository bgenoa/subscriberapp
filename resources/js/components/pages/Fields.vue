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
                            <h2 class="title font-weight-light">Fields</h2>
                        </span>
                    </v-card>
                </v-sheet>
                <v-card-text class="px-4 pt-0" child-flex>
                    <alert ref="alert1" v-bind:alert="alert" v-on:alert-dismissed="alertDismissed($event)">{{alert.text}}</alert>
                    <v-data-table
                        :headers="headers"
                        :items="Fields"
                        hide-actions
                        class="elevation-1 tr-clickable">
                        <template slot="items" slot-scope="props">
                            <td class="text-xs-left" @click="editField(props.item)">{{ props.item.id }}</td>
                            <td class="text-xs-left" @click="editField(props.item)">{{ props.item.subscriber_id }}</td>
                            <td class="text-xs-left" @click="editField(props.item)">{{ props.item.title }}</td>
                            <td class="text-xs-left" @click="editField(props.item)">{{ props.item.type }}</td>
                            <td class="text-xs-right">
                                <v-btn icon @click="deleteFieldBtn(props.item)">
                                    <v-icon color="red darken-2">cancel</v-icon>
                                </v-btn>
                            </td>
                        </template>
                        <template slot="no-data">
                            No Fields to display.
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
                                                <v-form v-model="valid" ref="form">                                                        
                                                    <v-text-field 
                                                        type="number"
                                                        label="Subscriber ID" 
                                                        v-model="FieldForm.subscriber_id" 
                                                        :value="selectedField.subscriber_id" 
                                                        :rules="FieldForm.susbcriberidRules"
                                                        required></v-text-field>
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
                                    <v-btn v-show="valid" color="blue darken-1" flat @click="submitForm">Save</v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
                        <modal-dialog-no-btn ref="modal2" v-bind:modal="deleteBtn" v-on:dialog-agree="deleteField($event)">
                            Are you sure you want to delete {{selectedField.title}} for Subscriber {{selectedField.subscriber_id}}?</modal-dialog-no-btn>
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
                    {text: 'Subscriber ID', align: 'left', sortable: true, value: 'subscriber_id', class: ['grey', 'lighten-2']},
                    {text: 'Title', align: 'left', sortable: true, value: 'title', class: ['grey', 'lighten-2']},
                    {text: 'Type', align: 'left', sortable: true, value: 'type', class: ['grey', 'lighten-2']},
                    {text: 'Delete', align: 'right', sortable: false, value: 'delete', class: ['grey', 'lighten-2']}
                ],
                Fields: [],
                typeOptions: ['date', 'number', 'string', 'boolean'], 
                selectedField: {},
                dialog: false,
                FieldForm: {
                    id: 0,
                    subscriber_id: 0,
                    subscriberidRules: [
                        (v) => !!v || 'Subscriber ID is required'
                    ],
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
            editField(Field) {
                this.selectedField = Field;
                this.FieldForm.subscriber_id = Field.subscriber_id;
                this.FieldForm.title = Field.title;
                this.FieldForm.type = Field.type;
                this.FieldForm.id = Field.id;
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
                        this.drawAlert('info', response.data.message);
                        this.getFields();
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
                axios.patch('api/fields/' + this.FieldForm.id, {
                    subscriber_id: this.FieldForm.subscriber_id,
                    title: this.FieldForm.title,
                    type: this.FieldForm.type
                }, config)
                .then(response => {
                    this.drawAlert('info', response.data.message);
                    this.getFields();
                })
                .catch(error => {
                    this.drawAlert('error', error.response.data.message);
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
                axios.get('api/fields', {
                    headers: config.headers
                })
                .then((response) => {
                    this.Fields = response.data.data;       
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
            this.getFields();
        }
    }
</script>
