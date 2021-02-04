<!-- https://bossanova.uk/jexcel/v4/examples/spreadsheet-formulas -->

<div id="spreadsheet"></div>

<select id="ma">
    <option value="557058:e33f889f-36f5-476b-a1a7-f21bb2c74915">Ivan</option>
    <option value="557058:660975c1-9644-4563-bcce-6b0b638207ef">Ivan R</option>
    <option value="5b586e3bd2a2f82da138e269">OLeg</option>
</select>

<input type="text" value="2020-12-12" id="date">

<button>GO</button>

<script src="https://momentjs.com/downloads/moment.min.js"></script>
<script src="https://bossanova.uk/jexcel/v4/jexcel.js"></script>
<script src="https://bossanova.uk/jsuites/v3/jsuites.js"></script>
<link rel="stylesheet" href="https://bossanova.uk/jsuites/v3/jsuites.css" type="text/css"/>
<link rel="stylesheet" href="https://bossanova.uk/jexcel/v4/jexcel.css" type="text/css"/>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    let employees = {
        '5b586e3bd2a2f82da138e269': 'OLeg',
        '557058:660975c1-9644-4563-bcce-6b0b638207ef': 'Ivan R',
        '557058:8f62e10d-7a55-449c-befb-378361c25e56': 'Edgar',
    }

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

    $(document).ready(function () {
        $("button").click(function () {

            let selector = document.getElementById('ma');

            $.ajax({
                url: "{{ config('app.api_url') }}/getEmployeeWeekPlan/week/this", success: function (result) {
                    let data = {}

                    for (let i = 0; i < 7; i++) {
                        // Create array where this week dates is keys
                        data[moment().startOf('isoweek').add(i, 'days').format('YYYY-MM-DD')] = {}
                    }

                    result.forEach((task => {
                        if (task.employee_code.length) {
                            // Add employee code as array key
                            data[task.start_date][task.employee_code] = []
                        }
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

                    console.log('Data', data);

                    let keysList = Object.keys(employees)
                    // Object.keys(data).forEach(date => {
                    //     Object.keys(data[date]).forEach((key) => {
                    //         if (Object.keys(employees).includes(key)) {
                    //             if (!keysList.includes(key)) {
                    //                 keysList.push(key)
                    //             }
                    //         }
                    //     });
                    // })

                    let columns = []
                    for (let i = 0; i < 7; i++) {
                        let date = moment().startOf('isoweek').add(i, 'days').format('YYYY-MM-DD');
                        keysList.forEach((key) => {
                            columns.push({
                                type: 'html',
                                title: employees[key] ?? 'Unknown',
                                width: 80,
                            });
                        });
                    }

                    console.log('Columns', columns);

                    let tableData = []
                    for (let line = 0; line < maxEstimate; line++) {
                        tableData[line] = []
                        for (let i = 0; i < 7; i++) {
                            let date = moment().startOf('isoweek').add(i, 'days').format('YYYY-MM-DD');
                            keysList.forEach((key) => {
                                if (data[date][key] && data[date][key][line]) {
                                    tableData[line].push(data[date][key][line]);
                                } else {
                                    tableData[line].push('');
                                }
                            })
                        }
                    }

                    console.log(tableData);

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
                                title: day + ' ' + moment().startOf('isoweek').add(i, 'days').format('DD.MM.YYYY'),
                                colspan: columnCount
                            })
                        }
                    })


                    jexcel(document.getElementById('spreadsheet'), {
                        data: tableData,
                        nestedHeaders: [
                            headers
                        ],
                        columns: columns
                    });
                }
            });

            $.ajax({
                url: "{{ config('app.api_url') }}/getOpenTasks", success: function (result) {

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

                    // Format
                    let tableData = []
                    statuses.forEach((status, i) => {
                        tableData[i] = []
                        tableData[i].push(status)

                        Object.keys(data).forEach(key => {
                            if (data[key][status]) {
                                tableData[i].push(data[key][status])
                            } else {
                                tableData[i].push(0)
                            }
                        })

                        tableData[i].push('=SUMROW(TABLE(), ROW())')
                    })

                    let columns = []
                    let footers = []

                    columns.push(
                        {type: 'text', title: 'Status', width: 120},
                    )
                    footers.push('Total')

                    Object.keys(data).forEach(key => {
                        columns.push(
                            {type: 'text', title: employees[key] ?? 'Unknown', width: 120},
                        )
                        footers.push('=SUMCOL(TABLE(), COLUMN())')
                    })
                    columns.push(
                        {type: 'text', title: 'Total', width: 120},
                    )
                    footers.push('=SUMCOL(TABLE(), COLUMN())')

                    console.log(data);
                    console.log(statuses);
                    console.log(tableData);

                    jexcel(document.getElementById('spreadsheet'), {
                        data: tableData,
                        footers: [footers],
                        columns: columns
                    });


                }
            });


        });
    });

</script>
