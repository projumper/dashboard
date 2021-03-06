@extends('layouts.base')

@section('content')
    <style>
        .blinking {
            animation: blinkingText 1.2s infinite;
        }

        @keyframes blinkingText {
            0% {
                color: #000;
            }
            49% {
                color: #000;
            }
            60% {
                color: white;
            }
            99% {
                color: white;
            }
            100% {
                color: #000;
            }
        }
    </style>
    <div class="row">
        <div class="col-sm-12">
            <input type="text" id="datepicker">
            <input type="hidden" id="week">
        </div>
    </div>

    <br>

    <div class="row" id="monthData">
    </div>

    <br>

    <div class="row">
        <div class="col-12">
            <div id="taskinprogress"></div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-6">
            <h4>
                <div id="weeks-period"></div>
            </h4>
        </div>
        <div class="col-6">
            <div class="btn-group" role="group">
                <button type="button" id="prevWeek" class="btn btn-secondary"><i class="fas fa-chevron-left"></i>
                    Previous
                </button>
                <button type="button" id="nextWeek" class="btn btn-secondary">Next <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        <div class="col-12">
            <div id="week-div"></div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div id="openTasks"></div>
        </div>
    </div>


    <div class="row">
        <div class="col-6">
            <div id="employeeTime"></div>
        </div>
        <div class="col-6">
            <select id="usersList"></select>
            <br>
            <div id="openTasksByAuthor"></div>
        </div>
    </div>

    <div id="no-data-html" style="display: none">
        <div class="card  border-left-secondary">
            <div class="card-body"><h2>No data for this period!</h2></div>
        </div>
    </div>



    <script>
        const SERVER_DATE_FORMAT = 'YYYY-MM-DD'
        const CLIENT_DATE_FORMAT = 'DD.MM.YYYY'
        const API_URL = "{{config('app.api_url')}}"
        const PROJECT = "{{ $project ?? '' }}"

        let employees = {
            '5b586e3bd2a2f82da138e269': 'OLeg',
            '557058:660975c1-9644-4563-bcce-6b0b638207ef': 'Ivan R',
            '557058:8f62e10d-7a55-449c-befb-378361c25e56': 'Edgar',
            '557058:7da53abb-60bb-4ac5-8e14-c5d6186d6117': 'Igor',
            '5eb172d3021ae30ba82474a0': 'Ludwig',
            '557058:e33f889f-36f5-476b-a1a7-f21bb2c74915': 'Ivan G.',
            '5c0e4906dc7a08769e2f2edd': 'Ete',
        }

        // TODO: add all users
        let allUsers = {
            '5b586e3bd2a2f82da138e269': 'OLeg',
            '557058:660975c1-9644-4563-bcce-6b0b638207ef': 'Ivan R',
            '557058:8f62e10d-7a55-449c-befb-378361c25e56': 'Edgar',
            '557058:7da53abb-60bb-4ac5-8e14-c5d6186d6117': 'Igor',
            '5eb172d3021ae30ba82474a0': 'Ludwig',
            '557058:e33f889f-36f5-476b-a1a7-f21bb2c74915': 'Ivan G.',
            '5c0e4906dc7a08769e2f2edd': 'Ete',
        }

        let authorsData = {}
        let cellsBackgrounds = {}

        let SUMCOL = function (instance, columnId) {
            let total = 0;
            for (let j = 0; j < instance.options.data.length; j++) {
                if (Number(instance.records[j][columnId - 1].innerHTML)) {
                    total += Number(instance.records[j][columnId - 1].innerHTML);
                }
            }
            return total;
        }
        let SUMROW = function (instance, rowId) {
            let total = 0;
            for (let j = 1; j < instance.options.columns.length - 1; j++) {
                if (Number(instance.records[rowId - 1][j].innerHTML)) {
                    total += Number(instance.records[rowId - 1][j].innerHTML);
                }
            }
            return total;
        }


        let COLORIZE = function (table, value, status, link, cell) {
            let color = ''
            // TODO: re-check statuses
            switch (status) {
                case 'Selected for Development':
                    color = 'gray'
                    break
                case 'canGoLive':
                    color = 'yellow'
                    break
                case 'In Progress':
                    color = 'blue'
                    break
                case 'Fertig':
                    color = 'green'
                    break
                case 'Done':
                    color = 'green'
                    break
                case 'QA':
                    color = 'orange'
                    break
                case 'danger':
                    color = 'RED'
                    break
                default:
                    color = 'white'

            }

            if (cell) {
                cellsBackgrounds[table][cell] = 'background-color:' + color + ';'
            }
            return '<a' + (status == 'danger' ? ' class="blinking"' : '') + ' target="_blank" href="' + link + '"><span style="color:black">' + decodeURI(value) + '</span></a>';
        }

        $(document).ready(function () {

            $("#datepicker").val(moment().format('YYYY-MM'))
            $("#week").val(moment().startOf('isoweek').format(SERVER_DATE_FORMAT))

            Object.keys(allUsers).forEach(key => {
                $("#usersList").append(new Option(allUsers[key], key));
            })


            $("body").tooltip({selector: '[data-toggle=tooltip]'});


            $("#datepicker").datepicker({
                format: "yyyy-mm",
                viewMode: "months",
                minViewMode: "months",
            });

            $('#datepicker').change(function (event) {
                getMonthData()
                getEmployeeTime()
                getOpenTasks()
            });
            $("#usersList").change(function () {
                getOpenTasksByAuthor()
            })

            $('#prevWeek').on('click', function () {
                $("#week").val(moment($("#week").val(), SERVER_DATE_FORMAT).subtract(7, 'd').format(SERVER_DATE_FORMAT))

                getWeek()
            })
            $('#nextWeek').on('click', function () {
                $("#week").val(moment($("#week").val(), SERVER_DATE_FORMAT).add(7, 'd').format(SERVER_DATE_FORMAT))
                getWeek()
            })

            const getEmployeeTime = () => {
                $('#employeeTime').html('')

                $.ajax({
                    url: API_URL + "/getEmployeeTime/date/" + $('#datepicker').val() + '-01' + (PROJECT ? ('/' + PROJECT) : ''),
                    success: function (result) {

                        jexcel($('#employeeTime').get(0), {
                            data: result,
                            footers: [['Total', , '', '', '', '', '', '=SUMCOL(TABLE(), COLUMN())']],
                            columns: [
                                {type: 'text', title: 'time', width: 120},
                                {type: 'text', title: 'account_jira', width: 320},
                                {type: 'text', title: 'display_name', width: 120},

                            ]
                        });

                    }
                });
            }

            const getTasksInProgress = () => {
                $('#taskinprogress').html('')

                $.ajax({
                    url: API_URL + "/getOpenTasks" + (PROJECT ? ('/' + PROJECT) : ''),
                    success: function (result) {

                        let data = {}
                        let maxTasks = 0
                        result.forEach(task => {
                            if (typeof data[task.employee_code] == 'undefined') {
                                data[task.employee_code] = []
                            }
                            data[task.employee_code].push(task)
                            maxTasks = data[task.employee_code].length > maxTasks ? data[task.employee_code].length : maxTasks
                        });

                        let columns = []
                        let headers = []
                        Object.keys(data).forEach(key => {
                            columns.push({
                                type: 'html',
                                title: 'Task link',
                                width: 80,
                            });
                            columns.push({
                                type: 'html',
                                title: 'Description',
                                width: 120,
                            });
                            columns.push({
                                type: 'html',
                                title: 'Deadline',
                                width: 90,
                            });

                            headers.push({
                                type: 'html',
                                title: employees[key] ?? key,
                                colspan: 3
                            })
                        })

                        let tableData = []
                        for (let i = 0; i < maxTasks; i++) {
                            tableData[i] = [];
                            Object.keys(data).forEach(key => {
                                if (data[key][i]) {
                                    let task = data[key][i];

                                    let status = task['status']

                                    if (task['dealine'] < moment().format(SERVER_DATE_FORMAT)) {
                                        status = 'danger'
                                    }

                                    tableData[i].push('=COLORIZE("progress", "' + task['task_p_id_nr'] + '","' + status + '","' + task['task_link'] + '", CELL())')
                                    // tableData[i].push(task['short_description'])
                                    tableData[i].push('=COLORIZE("progress", "' + encodeURI(task['short_description']) + '","' + status + '","' + task['task_link'] + '", CELL())')
                                    tableData[i].push('=COLORIZE("progress", "' + (task['dealine'] ?? '') + '","' + status + '","' + task['task_link'] + '", CELL())')
                                } else {
                                    tableData[i].push('')
                                    tableData[i].push('')
                                    tableData[i].push('')
                                }
                            })
                        }

                        cellsBackgrounds["progress"] = {}

                        if (!tableData.length) {
                            $('#taskinprogress').html($('#no-data-html').html());
                        } else {
                            let table = jexcel($('#taskinprogress').get(0), {
                                data: tableData,
                                columns: columns,
                                nestedHeaders: [
                                    headers
                                ],
                            });

                            table.setStyle(cellsBackgrounds["progress"])
                        }

                    }
                });
            }

            const getMonthData = () => {
                $('#monthData').html('')
                $.ajax({
                    url: API_URL + "/getMonthData/date/" + $('#datepicker').val() + '-01' + (PROJECT ? ('/' + PROJECT) : ''),
                    success: function (result) {

                        Object.keys(result).forEach(key => {
                            $('#monthData').append('' +
                                '<div class="col">' +
                                '<div class="card">' +
                                '<div class="card-body">' +
                                '<h5 class="card-title">' + key + ' <a href="#" data-toggle="tooltip" data-placement="top" title="Tooltip text"><i class="fas fa-question-circle"></i></a></h5>' +
                                result[key] +
                                '</div>' +
                                '</div>' +
                                '</div>')
                        })
                    }
                });
            }

            const getWeek = () => {
                $('#week-div').html('')
                $('#weeks-period').html(moment($("#week").val(), SERVER_DATE_FORMAT).format(CLIENT_DATE_FORMAT) + '-' + moment($("#week").val(), SERVER_DATE_FORMAT).add(6, 'd').format(CLIENT_DATE_FORMAT))
                $.ajax({
                    url: API_URL + "/getEmployeeWeekPlan/week/" + $("#week").val() + (PROJECT ? ('/' + PROJECT) : ''),
                    success: function (result) {
                        let data = {}
                        let statuses = {}
                        let links = {}

                        for (let i = 0; i < 7; i++) {
                            // Create array where this week dates is keys
                            data[moment($("#week").val(), SERVER_DATE_FORMAT).startOf('isoweek').add(i, 'days').format(SERVER_DATE_FORMAT)] = {}
                        }

                        result.forEach((task => {
                            if (task.employee_code.length) {
                                // Add employee code as array key
                                data[task.start_date][task.employee_code] = []
                            }
                            statuses[task.p_id_nr] = task.status
                            links[task.p_id_nr] = task.task_link
                        }))

                        let maxEstimate = 0;
                        result.forEach((task => {
                            if (task.employee_code.length) {
                                let hours = task.estimated_time / 60 / 60
                                for (let h = 1; h <= hours; h++) {
                                    data[task.start_date][task.employee_code].push(task.p_id_nr)
                                }
                                maxEstimate = maxEstimate >= data[task.start_date][task.employee_code].length ? maxEstimate : data[task.start_date][task.employee_code].length;
                            }
                        }))

                        let keysList = Object.keys(employees)

                        let columns = []
                        for (let i = 0; i < 7; i++) {
                            keysList.forEach((key) => {
                                columns.push({
                                    type: 'html',
                                    title: employees[key] ?? 'Unknown',
                                    width: 80,
                                });
                            });
                        }

                        let tableData = []
                        for (let line = 0; line < maxEstimate; line++) {
                            tableData[line] = []
                            for (let i = 0; i < 7; i++) {
                                let date = moment($("#week").val(), SERVER_DATE_FORMAT).startOf('isoweek').add(i, 'days').format(SERVER_DATE_FORMAT);
                                keysList.forEach((key) => {
                                    if (data[date][key] && data[date][key][line]) {
                                        let nr = data[date][key][line]
                                        tableData[line].push('=COLORIZE("week", "' + nr + '","' + statuses[nr] + '","' + links[nr] + '", CELL())');
                                    } else {
                                        tableData[line].push('');
                                    }
                                })
                            }
                        }

                        let days = [
                            'Montag',
                            'Dienstag',
                            'Mittwoch',
                            'Donnerstag',
                            'Freitag',
                            'Samstag',
                            'Sonntag',
                        ]

                        let headers = [];

                        days.forEach((day, i) => {
                            let columnCount = keysList.length
                            if (columnCount) {
                                headers.push({
                                    type: 'html',
                                    title: day + ' ' + moment($("#week").val(), SERVER_DATE_FORMAT).startOf('isoweek').add(i, 'days').format(CLIENT_DATE_FORMAT),
                                    colspan: columnCount
                                })
                            }
                        })

                        if (!tableData.length) {
                            $('#week-div').html($('#no-data-html').html());
                        } else {
                            cellsBackgrounds["week"] = {}

                            let table = jexcel($('#week-div').get(0), {
                                data: tableData,
                                nestedHeaders: [
                                    headers
                                ],
                                columns: columns
                            });

                            table.setStyle(cellsBackgrounds["week"])
                        }
                    }
                });
            }

            const getOpenTasks = () => {
                $('#openTasks').html('')
                $.ajax({
                    url: API_URL + "/getOpenTasks" + (PROJECT ? ('/' + PROJECT) : ''), success: function (result) {
                        authorsData = {}
                        result.forEach(task => {
                            if (typeof authorsData[task.author_code] == 'undefined') {
                                authorsData[task.author_code] = {}
                            }
                            if (typeof authorsData[task.tester_code] == 'undefined') {
                                authorsData[task.tester_code] = {}
                            }

                            if (typeof authorsData[task.author_code][task.task_p_id_nr] == 'undefined') {
                                authorsData[task.author_code][task.task_p_id_nr] = task
                            }
                            if (typeof authorsData[task.tester_code][task.task_p_id_nr] == 'undefined') {
                                authorsData[task.tester_code][task.task_p_id_nr] = task
                            }

                        })

                        getOpenTasksByAuthor()

                        let data = []
                        let statuses = []

                        // Calculate
                        result.forEach(task => {
                            if (typeof data[task.employee_code] == 'undefined') {
                                data[task.employee_code] = []
                            }
                            if (typeof data[task.employee_code][task.status] == 'undefined') {
                                data[task.employee_code][task.status] = 0
                            }
                            data[task.employee_code][task.status] += 1

                            if (!statuses.includes(task.status)) {
                                statuses.push(task.status)
                            }
                        })

                        let tableData = []
                        Object.keys(data).forEach((key, i) => {
                            tableData[i] = []
                            tableData[i].push(employees[key] ?? 'Unknown')
                            statuses.forEach((status) => {
                                tableData[i].push(data[key][status] ?? 0)
                            })
                            tableData[i].push('=SUMROW(TABLE(), ROW())')
                        })

                        let columns = []
                        let footers = []

                        columns.push(
                            {type: 'text', title: 'User', width: 120},
                        )
                        footers.push('Total')

                        statuses.forEach(status => {
                            columns.push(
                                {type: 'text', title: status, width: 120},
                            )
                            footers.push('=SUMCOL(TABLE(), COLUMN())')
                        })
                        columns.push(
                            {type: 'text', title: 'Total', width: 120},
                        )
                        footers.push('=SUMCOL(TABLE(), COLUMN())')

                        if (!tableData.length) {
                            $('#openTasks').html($('#no-data-html').html());
                        } else {
                            jexcel(document.getElementById('openTasks'), {
                                data: tableData,
                                footers: [footers],
                                columns: columns
                            });
                        }


                    }
                });
            }

            const getOpenTasksByAuthor = () => {
                $("#usersList").val()
                $('#openTasksByAuthor').html('')

                let tableData = []
                if (authorsData[$("#usersList").val()]) {
                    Object.keys(authorsData[$("#usersList").val()]).forEach(key => {
                        let task = authorsData[$("#usersList").val()][key]
                        tableData.push([
                            '=COLORIZE("openTasks","' + key + '","' + task.status + '","' + task.task_link + '")',
                            task.short_description,
                            task.status
                        ])
                    })

                }

                if (!tableData.length) {
                    $('#openTasksByAuthor').html($('#no-data-html').html());
                } else {
                    jexcel(document.getElementById('openTasksByAuthor'), {
                        data: tableData,
                        columns: [
                            {type: 'html', title: 'Link', width: 120},
                            {type: 'text', title: 'Description', width: 320},
                            {type: 'text', title: 'Status', width: 200},
                        ]
                    });
                }
            }

            getMonthData()
            getEmployeeTime()
            getWeek()
            getOpenTasks()
            getTasksInProgress()
        });

    </script>
@stop
