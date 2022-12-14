@extends('sample')

@section('title')Главная страница@endsection

@section('content')

    <div id="MainPage">
        <v-app>
            <v-main>

                <!--NEW CODE-->
                <v-card max-width="700">
                    <v-container>
                        <h5 class="ps-5 mb-2"><b>Фильтрация данных</b></h5>
                        <v-form ref="form1">
                            <v-row>
                                <v-col
                                    cols="12"
                                    sm="6"
                                >
                                    <v-text-field
                                        :rules="kod_rules_filter"
                                        label="Код фьючерса"
                                        outlined
                                        dense
                                        v-model = "kod_fut"
                                        clearable
                                        class="ma-0 pa-0"
                                        @change="ShowFilteredTable"
                                    ></v-text-field>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col
                                    cols="12"
                                    sm="6"
                                >
                                    <v-text-field
                                        :rules="date_rules_filter"
                                        label="Дата торгов от"
                                        outlined
                                        dense
                                        v-model = "date_torg_min"
                                        clearable
                                        class="ma-0 pa-0"
                                        @change="ShowFilteredTable"
                                    ></v-text-field>
                                </v-col>

                                <v-col
                                    cols="12"
                                    sm="6"
                                >
                                    <v-text-field
                                        :rules="date_rules_filter"
                                        label="Дата торгов до"
                                        outlined
                                        dense
                                        v-model = "date_torg_max"
                                        clearable
                                        class="ma-0 pa-0"
                                        @change="ShowFilteredTable"
                                    ></v-text-field>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col
                                    cols="12"
                                    sm="6"
                                >
                                    <v-text-field
                                        :rules="price_rules_filter"
                                        label="Максимальная цена от"
                                        outlined
                                        dense
                                        v-model = "price_min"
                                        clearable
                                        class="ma-0 pa-0"
                                        @change="ShowFilteredTable"
                                    ></v-text-field>
                                </v-col>

                                <v-col
                                    cols="12"
                                    sm="6"
                                >
                                    <v-text-field
                                        :rules="price_rules_filter"
                                        label="Максимальная цена до"
                                        outlined
                                        dense
                                        v-model = "price_max"
                                        clearable
                                        class="ma-0 pa-0"
                                        @change="ShowFilteredTable"
                                    ></v-text-field>
                                </v-col>
                            </v-row>

                            <v-row>
                                <v-col
                                    cols="12"
                                    sm="6"
                                >
                                    <v-text-field
                                        :rules="sale_rules_filter"
                                        label="Кол-во продаж от"
                                        outlined
                                        dense
                                        v-model = "numb_sales_min"
                                        clearable
                                        class="ma-0 pa-0"
                                        @change="ShowFilteredTable"
                                    ></v-text-field>
                                </v-col>

                                <v-col
                                    cols="12"
                                    sm="6"
                                >
                                    <v-text-field
                                        :rules="sale_rules_filter"
                                        label="Кол-во продаж до"
                                        outlined
                                        dense
                                        v-model = "numb_sales_max"
                                        clearable
                                        class="ma-0 pa-0"
                                        @change="ShowFilteredTable"
                                    ></v-text-field>
                                </v-col>
                            </v-row>
                            <v-btn
                                color="red darken-1"
                                small
                                outlined
                                @click="ResetTable"
                                class="mt-5 ml-2 mb-1"
                            >
                                Сбросить фильтры
                            </v-btn>
                        </v-form>
                    </v-container>
                </v-card>
                <v-divider></v-divider>
                <!--NEW CODE-->


                <v-card>
                    <v-card-text>
                        <v-data-table
                            :headers="headers"
                            :items="show_tables_info"
                            class="elevation-1"
                            :search="search">
                            <template v-slot:top>
                                <br>
                                <v-btn
                                    class="mx-5"
                                    color="primary"
                                    outlined
                                    small
                                    @click="ExportReport"
                                >
                                    Экспортировать таблицу в файл
                                </v-btn>
                                <br>
                                <br>
                            </template>
                            <template
                                v-slot:item._actions="{ item }">
                                <v-btn
                                    icon @click = "ShowDialogChange(item)">
                                    <v-icon>
                                        mdi-pencil
                                    </v-icon>
                                </v-btn>
                                <v-btn icon @click = "ShowDialogDelete(item)">
                                    <v-icon>
                                        mdi-delete
                                    </v-icon>
                                </v-btn>
                            </template>
                            <!--<template v-slot:footer.page-text>
                                <v-btn
                                    color="primary"
                                    dark
                                    class="ma-2"
                                    @click="buttonCallback">
                                    Button
                                </v-btn>
                            </template>-->

                        </v-data-table>

                    </v-card-text>

                    <v-card-actions>
                        <v-btn
                            block
                            depressed
                            class="transparent font-weight-bold grey--text pa-2 d-flex align-center"
                            icon @click="ShowDialogAdd()"
                        >
                            <v-icon>
                                mdi-plus
                            </v-icon>
                            <span>
                                Добавить запсиь
                            </span>
                        </v-btn>
                    </v-card-actions>

                    <!--NEW CODE-->
                    <v-form ref="form4">
                        <v-text-field
                            :rules="date_rules"
                            outlined
                            dense
                            clearable
                            v-model="Date_1"
                            label="Поиск от"
                            class="mx-4"
                        ></v-text-field>
                        <v-text-field
                            :rules="date_rules"
                            outlined
                            dense
                            clearable
                            v-model="Date_2"
                            label="Поиск до"
                            class="mx-4"
                        ></v-text-field>

                        <v-btn
                            color="primary"
                            text
                            @click="showStatsFun() "

                        >Расчитать статистические характеристики
                        </v-btn>
                    </v-form>
                    <!--NEW CODE-->

                </v-card>
            </v-main>

            <v-dialog
                v-model="dialog_change"
                width="400"
            >
                <v-card>
                    <v-card-title class="text-h5 grey lighten-2">
                        Изменение данных
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-actions>
                        <v-form ref="form2">
                            <v-col>
                                <v-col
                                    cols="auto"
                                    sm="50"
                                    md="10"
                                >
                                    <v-text-field
                                        v-model="Kod"
                                        label="Код"
                                        class="mx4"
                                        disabled>
                                    </v-text-field>
                                </v-col>
                                <v-col
                                    ccols="auto"
                                    sm="50"
                                    md="10"
                                >
                                    <v-text-field
                                        v-model="Exec_data"
                                        label="Дата погошения"
                                        class="mx4"
                                        disabled>

                                    </v-text-field>
                                </v-col>
                                <v-col
                                    cols="auto"
                                    sm="50"
                                    md="10"
                                >
                                    <v-text-field
                                        v-model="Torg_date"
                                        label="Дата торгов"
                                        class="mx4"
                                        disabled>

                                    </v-text-field>
                                </v-col>
                                <v-col
                                    cols="auto"
                                    sm="50"
                                    md="10"
                                >
                                    <v-text-field
                                        :rules="price_rules"
                                        v-model="Quotation"
                                        label="Максимальная цена"
                                        class="mx4">

                                    </v-text-field>
                                </v-col>
                                <v-col
                                    cols="auto"
                                    sm="50"
                                    md="10"
                                >
                                    <v-row>
                                        <v-text-field
                                            :rules="sale_rules"
                                            v-model="Num_contr"
                                            label="Кол-во продаж"
                                            class="mx4">
                                        </v-text-field>
                                        <v-col>
                                            <v-btn
                                                color="primary"
                                                text
                                                @click="ChangeData"
                                            >
                                                Изменить
                                            </v-btn>
                                            <v-btn
                                                color="primary"
                                                text
                                                @click="CloseChangeData"
                                            >
                                                Отмена
                                            </v-btn>
                                        </v-col>
                                    </v-row>
                                </v-col>
                            </v-col>
                        </v-form>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog
                v-model="dialog_delete"
                width="400"
            >
                <v-card>
                    <v-card-title class="text-h5 grey lighten-2">
                        Удаление данных
                    </v-card-title>

                    <v-divider></v-divider>
                    <v-card-text>
                        Вы точно уверены?
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>

                        <v-divider></v-divider>

                        <v-btn
                            color="primary"
                            text
                            icon @click="DeleteData"
                        >
                            удалить
                        </v-btn>

                        <v-spacer></v-spacer>


                        <v-btn
                            color="primary"
                            text
                            @click="dialog_delete = false"
                        >
                            Отмена
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog
                v-model="dialog_add"
                width="300">
                <v-card>
                    <v-card-title class="text-h5 grey lighten-2">
                        Добавление данных
                    </v-card-title>
                    <v-form ref="form3">
                        <v-container>
                            <v-row>
                                <v-col
                                    cols="12"
                                    sm="10"
                                >
                                    <v-text-field
                                        :rules="kod_rules"
                                        v-model="Kod"
                                        label="Код"
                                        class="mx4">
                                    </v-text-field>

                                </v-col>
                            </v-row>
                        </v-container>
                        <v-col
                            cols="auto"
                            sm="10"
                        >
                            <v-text-field
                                :rules="date_rules"
                                v-model="Torg_date"
                                label="Дата торгов"
                                class="mx4"
                            >

                            </v-text-field>
                        </v-col>
                        <v-col
                            cols="auto"
                            sm="10"
                        >
                            <v-text-field
                                :rules="price_rules"
                                v-model="Quotation"
                                label="Максимальная цена"
                                class="mx4">

                            </v-text-field>
                        </v-col>
                        <v-col
                            cols="auto"
                            sm="10"
                        >

                            <v-text-field
                                :rules="sale_rules"
                                v-model="Num_contr"
                                label="Кол-во продаж"
                                class="mx4">
                            </v-text-field>
                        </v-col>
                        <v-card-actions>

                            <v-btn
                                color="primary"
                                text
                                @click="AddData_()"
                            >
                                Добавить
                            </v-btn>
                            <v-btn
                                color="primary"
                                text
                                @click="CloseAddData_"
                            >
                                Отмена
                            </v-btn>
                        </v-card-actions>
                    </v-form>

                </v-card>
            </v-dialog>
            <v-dialog
                v-model="dialog_add_f"
                width="400">
                <v-card>
                    <v-card-title class="text-h5 grey lighten-2">
                        Фьючерс не найден
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-actions>
                        <v-col>
                            <v-col
                                cols="auto"
                                sm="50"
                                md="20"
                            >
                                Вы хотите добавить этот фьючерс?
                            </v-col>
                            <v-col>
                                <v-col
                                    cols="auto"
                                    sm="50"
                                    md="10"
                                >
                                    <v-text-field
                                        v-model="Kod"
                                        label="Код"
                                        class="mx4"
                                    >
                                    </v-text-field>
                                </v-col>
                                <v-btn
                                    color="primary"
                                    text
                                    @click="AddDataF()"
                                >
                                    Добавить
                                </v-btn>
                                <v-btn
                                    color="primary"
                                    text
                                    @click="dialog_add_f = false"
                                >
                                    Отмена
                                </v-btn>
                            </v-col>
                            </v-row>
                        </v-col>

                        </v-col>

                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!--NEW CODE-->
            <v-dialog
                v-model="showStats"
                width="1000"
            >
                <v-card>
                    <v-card-title class="text-h5 grey lighten-2">
                        Статистика
                    </v-card-title>



                    <v-card-actions>


                        <v-data-table
                            :headers="headersStats"
                            :items="Stats"
                            class="elevation-1"

                            :search="search">
                            <template v-slot:top>
                                <br>
                                <v-btn
                                    class="mx-5"
                                    color="primary"
                                    outlined
                                    small
                                    @click="ExportReportStats"
                                >
                                    Экспортировать таблицу в файл
                                </v-btn>
                                <br>
                                <br>
                            </template>
                            <template v-slot:item="i">
                                <!-- Since v-slot:item overrides how each row is rendered, I rebuild the row starting from <tr>. This allows me to add a class to <tr> based on any condition I want (in this case, the calorie count) -->
                                <tr>
                                    <td>@{{i.item.FUSD}}</td>
                                    <td>@{{i.item.Mx}}</td>
                                    <td>@{{i.item.D}}</td>
                                    <td>@{{i.item.V}}</td>
                                    <td :class="{
                      'green lighten-2': i.item.TrendMx > 0,
                      'red lighten-4': i.item.TrendMx <= 0
                      }">               @{{i.item.TrendMx}}</td>
                                    <td :class="{
                      'green lighten-2': i.item.TrendD > 0,
                      'red lighten-4': i.item.TrendD <= 0
                      }">               @{{i.item.TrendD}}</td>
                                </tr>
                            </template>
                        </v-data-table>


                        <v-btn
                            color="primary"
                            text
                            @click="showStats = false"
                        >
                            Выход
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <!--NEW CODE-->
        </v-app>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script>
        new Vue({
            el: '#MainPage',
            vuetify: new Vuetify(),
            data(){
                return{
                    kod_rules_filter:[
                        v => (!v || /(FUSD[_](0[0-9]|1[0-2])[_]\d{2}$)/.test(v)) || 'Future must be valid',
                    ],
                    date_rules_filter:[
                        v => (!v || /^\d{4}[-](0[1-9]|1[0-2])[-](0[1-9]|[12][0-9]|3[01])$/.test(v)) || 'Date must be valid',
                    ],
                    price_rules_filter:[
                        v => (!v || Number(v)) || 'Price must be numeric',
                    ],
                    sale_rules_filter:[
                        v => (!v || Number(v)) || 'Sale must be integer',
                    ],
                    kod_rules:[
                        v => /(FUSD[_](0[0-9]|1[0-2])[_]\d{2}$)/.test(v) || 'Future must be valid',
                    ],
                    date_rules:[
                        v => /^\d{4}[-](0[1-9]|1[0-2])[-](0[1-9]|[12][0-9]|3[01])$/.test(v) || 'Date must be valid',
                    ],
                    price_rules:[
                        v => Number(v) || 'Price must be numeric',
                    ],
                    sale_rules:[
                        v => Number(v) || 'Sale must be integer',
                    ],
                    <!--NEW CODE-->
                    kod_fut:'',
                    date_torg_min:'',
                    price_min:'',
                    numb_sales_min:'',
                    date_torg_max:'',
                    price_max:'',
                    numb_sales_max:'',
                    <!--NEW CODE-->
                    //selected:[],
                    //show_tables_info_:[],
                    answer:[],
                    show_tables_info:[],//информация в таблице
                    show_tables_info_origin:[],
                    dialog_change: false,//диалог на изменение
                    dialog_delete: false,//диалог на удаление
                    dialog_add: false,//диалог на добаление
                    dialog_add_f: false,//диалог на добавление фьючерса
                    showStats: false,
                    search: '',//поиск
                    Kod:'',
                    //Kod1:'FUSD_',
                    Exec_data:'',
                    Torg_date:'',
                    Quotation:'',
                    Num_contr:'',
                    Date_1:'',
                    Date_2:'',
                    Stats:[],
                    headers: [
                        {
                            text: 'Код фьючерса',
                            align: 'start',
                            value: 'kod',
                        },
                        { text: 'Дата погашения', value: 'exec_data' },
                        { text: 'Дата торгов', value: 'torg_date' },
                        { text: 'Максимальная цена', value: 'quotation' },
                        { text: 'Кол-во продаж', value: 'num_contr' },
                        { text: 'Xk', value: 'Xk' },
                        { text: 'Изменить/удалить', value: '_actions'},
                    ],
                    headersStats: [
                        { text: 'FUSD', value: 'FUSD' },
                        { text: 'Mx', value: 'Mx' },
                        { text: 'D', value: 'D' },
                        { text: 'V', value: 'V' },
                        { text: 'TrendMx', value: 'TrendMx'},
                        { text: 'TrendD', value: 'TrendD'},
                    ],
                }
            },
            methods:{
                async ExportReportStats(){
                    let data=new FormData()
                    let FUSD = this.Stats.map(({ FUSD }) => FUSD)
                    let Mx = this.Stats.map(({ Mx }) => Mx)
                    let D = this.Stats.map(({ D }) => D)
                    let V = this.Stats.map(({ V }) => V)
                    let TrendMx = this.Stats.map(({ TrendMx }) => TrendMx)
                    let TrendD = this.Stats.map(({ TrendD }) => TrendD)
                    data.append('FUSD',FUSD)
                    data.append('Mx',Mx)
                    data.append('D',D)
                    data.append('V',V)
                    data.append('TrendMx',TrendMx)
                    data.append('TrendD',TrendD)
                    data.append('Date1',this.Date_1)
                    data.append('Date2',this.Date_2)
                    await fetch('ExportReportStats',{
                        method:'post',
                        headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        body:data
                    })
                    this.GetDownloadStats()
                },
                async ExportReport(){
                    let data=new FormData()
                    data.append('kod',this.kod_fut)
                    data.append('date_torg_min',this.date_torg_min)
                    data.append('date_torg_max',this.date_torg_max)
                    data.append('price_min',this.price_min)
                    data.append('price_max',this.price_max)
                    data.append('numb_sales_min',this.numb_sales_min)
                    data.append('numb_sales_max',this.numb_sales_max)
                    await fetch('ExportReport',{
                        method:'post',
                        headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        body:data
                    })
                    this.GetDownload()
                },
                GetDownloadStats(){
                    let url = new URL(window.location);
                    location = "{{route('GetDownloadStats')}}"
                },
                GetDownload(){
                    let url = new URL(window.location);
                    location = "{{route('GetDownload')}}"
                },
                <!--NEW CODE-->
                ShowFilteredTable(){
                    if(this.$refs.form1.validate()){
                        this.show_tables_info = this.show_tables_info_original
                        if (this.kod_fut){
                            this.show_tables_info = this.show_tables_info.filter(data => data.kod == this.kod_fut)
                        }
                        if (this.date_torg_min){
                            this.show_tables_info = this.show_tables_info.filter(data => data.torg_date >= this.date_torg_min)
                        }
                        if (this.date_torg_max){
                            this.show_tables_info = this.show_tables_info.filter(data => data.torg_date <= this.date_torg_max)
                        }
                        if (this.price_min){
                            this.show_tables_info = this.show_tables_info.filter(data => Number(data.quotation) >= Number(this.price_min))
                        }
                        if (this.price_max){
                            this.show_tables_info = this.show_tables_info.filter(data => Number(data.quotation) <= Number(this.price_max))
                        }
                        if (this.numb_sales_min){
                            this.show_tables_info = this.show_tables_info.filter(data => Number(data.num_contr) >= Number(this.numb_sales_min))
                        }
                        if (this.numb_sales_max){
                            this.show_tables_info = this.show_tables_info.filter(data => Number(data.num_contr) <= Number(this.numb_sales_max))
                        }
                    }
                },
                ResetTable(){
                    this.$refs.form1.reset()
                    this.show_tables_info = this.show_tables_info_original
                },
                <!--NEW CODE-->
                //<!--NEW CODE-->
                showStatsFun(){
                    if(this.$refs.form4.validate()){
                        this.filterTable();
                        this.showStats =true;
                        this.Stats = [];
                        let groupBy = function(xs, key) {
                            return xs.reduce(function(rv, x) {
                                (rv[x[key]] = rv[x[key]] || []).push(x);
                                return rv;
                            }, {});
                        };
                        let t = this.show_tables_info_filter
                        let t_0 = this.show_tables_info_filter.slice(0, -2)
                        let groupByFUSD_t = groupBy(t, 'kod')
                        let groupByFUSD_t_0 = groupBy(t_0, 'kod')
                        //Нахождение среднего для двух периодов, max, min
                        let temp_average_t = [];
                        //let temp_min_t = [];
                        //let temp_max_t = [];
                        let temp_d_t = [];
                        let temp_diff_t = [];
                        let temp_average_t_0 = [];
                        let temp_diff_t_0 = [];
                        //let temp_min_t_t_0 = [];
                        //let temp_max_t_t_0 = [];
                        let temp_FUSD = [];
                        let temp_average_all = [];
                        let temp_diff_all = [];
                        let d;
                        //Тут можно много чего оптимизировать
                        for (let key in groupByFUSD_t) {
                            //console.log(key, groupByFUSD_t[key]);
                            let average = groupByFUSD_t[key].reduce((total, next) => total + parseFloat(next.Xk), 0) / groupByFUSD_t[key].length;
                            let diff = groupByFUSD_t[key].reduce((total, next) => total + (parseFloat(next.Xk) - average)*(parseFloat(next.Xk) - average), 0) / (groupByFUSD_t[key].length-1);
                            //Получение минимума для данного фьючерса
                            /*console.log('min',groupByFUSD_t[key].reduce(function(prev, curr) {
                                return prev.quotation < curr.quotation ? prev : curr;
                            }).quotation)*/
                            /*temp_min_t.push(groupByFUSD_t[key].reduce(function(prev, curr) {
                                return prev.quotation < curr.quotation ? prev : curr;
                            }).quotation);*/
                            //Получение максимума для данного фьючерса
                            /*console.log('max',groupByFUSD_t[key].reduce(function(prev, curr) {
                                return prev.quotation < curr.quotation ? curr : prev;
                            }).quotation)*/
                            /*temp_max_t.push(groupByFUSD_t[key].reduce(function(prev, curr) {
                                return prev.quotation < curr.quotation ? curr : prev;
                            }).quotation)*/
                            //Расчёт размаха max - min
                            d = parseFloat(groupByFUSD_t[key].reduce(function(prev, curr) {
                                return prev.Xk < curr.Xk ? curr : prev;
                            }).Xk) - parseFloat(groupByFUSD_t[key].reduce(function(prev, curr) {
                                return prev.Xk < curr.Xk ? prev : curr;
                            }).Xk)
                            //console.log('d',d)
                            //Добавление размаха
                            temp_d_t.push(d)
                            temp_diff_t.push(diff)
                            //Внесение среднего в массив
                            temp_average_t.push(average)
                            //console.log('average', average)
                            //console.log('diff', diff)
                        }
                        for (let key in groupByFUSD_t_0) {
                            //console.log(key, groupByFUSD_t_0[key]);
                            //console.log(key, groupByFUSD_t_0[key]);
                            temp_FUSD.push(key)
                            let average = groupByFUSD_t_0[key].reduce((total, next) => total + parseFloat(next.Xk), 0) / groupByFUSD_t_0[key].length;
                            let diff = groupByFUSD_t_0[key].reduce((total, next) => total + (parseFloat(next.Xk) - average)*(parseFloat(next.Xk) - average), 0) / (groupByFUSD_t_0[key].length-1);
                            //let sum = (prev, cur) => ({height: prev.height + cur.height});
                            //let avg = arr.reduce(sum).height / arr.length;
                            temp_diff_t_0.push(diff)
                            temp_average_t_0.push(average)
                            //console.log('average', average)
                            //console.log('diff', diff)
                            //console.log('average', average);
                        }
                        for (let i = 0; i < temp_FUSD.length; i++) {
                            //console.log(temp_FUSD[i]);
                            //console.log("d",temp_d_t[i]);
                            //temp_average_all.push(temp_average_t[i] - temp_average_t_0[i])
                            //temp_diff_all.push(temp_diff_t[i] - temp_diff_t_0[i])
                            //console.log("temp_average_all",temp_average_t[i] - temp_average_t_0[i])
                            //console.log("temp_diff_all",temp_diff_t[i] - temp_diff_t_0[i])
                            let props = [
                                ['FUSD', temp_FUSD[i]],
                                ['Mx', Number(temp_average_t[i]).toFixed(6)],
                                ['D', Number(temp_diff_t[i]).toFixed(6)],
                                ['V', Number(temp_d_t[i]).toFixed(6)],
                                ['TrendMx', Number(temp_average_t[i] - temp_average_t_0[i]).toFixed(6)],
                                ['TrendD', Number(temp_diff_t[i] - temp_diff_t_0[i]).toFixed(6)],
                            ]
                            //this.Stats_original.push(Object.fromEntries(props))
                            this.Stats.push(Object.fromEntries(props))
                        }
                        /*
                        Mx:[],
                        D:[],
                        v:[],
                        TrendMx:[],
                        TrendD:[],
                        */
                        //console.log(temp_average_t)
                        //console.log(temp_average_t_0)
                        //const average = t.reduce((a, b) => a + b, 0) / t.length;
                    }
                },
                filterTable(){
                    this.show_tables_info_filter = this.show_tables_info_original
                    if (this.Date_1 != '' && this.Date_2 != ''){
                        temp = this.show_tables_info_original.filter(dates => dates.torg_date >= this.Date_1 && dates.torg_date <= this.Date_2);
                        this.show_tables_info_filter = temp
                    }
                    //this.users = temp
                    //console.log(temp)
                },
                filterTable(){
                    this.show_tables_info_filter = this.show_tables_info_original
                    if (this.Date_1 != '' && this.Date_2 != ''){
                        temp = this.show_tables_info_original.filter(dates => dates.torg_date >= this.Date_1 && dates.torg_date <= this.Date_2);
                        this.show_tables_info_filter = temp
                    }
                    //this.users = temp
                    //console.log(temp)
                },
                //<!--NEW CODE-->
                async ShowUnitedTable(){//Запрос на данные из таблиц
                    this.show_tables_info_ = []
                    await fetch('ShowUnitedTable',{
                        method: 'GET',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                    })
                        .then((response)=>{
                            return response.json()
                        })
                        .then((data)=>{
                            this.show_tables_info_original = data
                            this.show_tables_info = this.show_tables_info_original
                        })
                },
                ShowDialogAdd(){/*Диалог на добаление*/
                    this.Kod=''
                    this.Exec_data=''
                    this.Torg_date=''
                    this.Quotation=''
                    this.Num_contr=''
                    this.dialog_add=true
                },
                ShowDialogChange(item){//диалог на измение
                    this.Kod=item.kod
                    this.Exec_data=item.exec_data
                    this.Torg_date= item.torg_date
                    this.Quotation=Number(item.quotation)
                    this.Num_contr=Number(item.num_contr)
                    this.item=item
                    this.dialog_change=true
                },
                ShowDialogDelete(item){//диалог на удаление
                    this.Kod=item.kod
                    this.Torg_date= item.torg_date
                    this.item=item
                    this.dialog_delete=true
                },
                ChangeData(){//Изменение данных
                    if(this.$refs.form2.validate()){
                        let data=new FormData()
                        data.append('kod',this.Kod)
                        data.append('torg_date',this.Torg_date)
                        data.append('quotation',this.Quotation)
                        data.append('num_contr',this.Num_contr)
                        fetch('ChangeData',{
                            method:'post',
                            headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            body:data
                        })
                        this.ShowUnitedTable();
                        this.dialog_change=false;
                    }
                },
                CloseChangeData(){
                    this.dialog_change = false
                    this.$refs.form2.resetValidation()
                },
                DeleteData(){//удаление данных
                    let data=new FormData()
                    data.append('kod',this.Kod)
                    data.append('torg_date',this.Torg_date)
                    fetch('DeleteData',{
                        method:'post',
                        headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        body:data
                    })
                    this.ShowUnitedTable();
                    this.dialog_delete=false;
                },
                AddData_(){//добавление данных проверка кода
                    if(this.$refs.form3.validate()){
                        let data=new FormData()
                        data.append('kod',this.Kod)
                        fetch('KodCheck',{
                            method:'post',
                            headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            body:data
                        })
                            .then((response)=>{
                                return response.json()
                            })
                            .then((data)=>{
                                this.answer = data
                            })
                        if (this.answer!=0)
                        {
                            this.AddData()
                        }
                        else
                        {
                            this.dialog_add_f=true
                        }
                    }
                },
                CloseAddData_(){
                    this.dialog_add = false
                    this.$refs.form3.resetValidation()
                },
                AddData(){//добавление данных
                    let data=new FormData()
                    data.append('kod',this.Kod)
                    data.append('torg_date',this.Torg_date)
                    data.append('quotation',this.Quotation)
                    data.append('num_contr',this.Num_contr)
                    fetch('AddData',{
                        method:'post',
                        headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        body:data
                    })
                    this.ShowUnitedTable();
                    this.dialog_add=false;
                },
                AddDataF(){//добавление фьючерса
                    var ed1=this.Kod.substr(this.Kod.length-2)
                    var ed2=this.Kod.substring(5,7)
                    this.Exec_data='19'+ed1+'-'+ed2+'-15'
                    let data=new FormData()
                    data.append('kod',this.Kod)
                    data.append('exec_data',this.Exec_data)
                    fetch('AddDataF',{
                        method:'post',
                        headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        body:data
                    })
                    this.dialog_add_f=false;
                },
            },
            mounted: function (){//предзапуск функций
                this.ShowUnitedTable();
            }
        })
    </script>

@endsection
