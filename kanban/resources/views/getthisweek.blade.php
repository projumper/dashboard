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
    // TODO: get real full list from jira
    let employees = {
        '557058:e33f889f-36f5-476b-a1a7-f21bb2c74915': 'Ivan',
        '557058:e33f889f-36f5-476b-a1a7-f21bb2c74916': 'Edgar',
    }

    $(document).ready(function () {
        $("button").click(function () {

            let selector = document.getElementById('ma');
            // let value = selector[selector.selectedIndex].value;
            // let datetoselect = document.getElementById('date').value;

            $.ajax({
                url: "{{ config('app.api_url') }}/getEmployeeWeekPlan", success: function (result) {
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

                    let tableData = []
                    for (let line = 0; line < maxEstimate; line++) { // TODO: get max estimate
                        tableData[line] = {}
                        for (let i = 0; i < 7; i++) {
                            let date = moment().startOf('isoweek').add(i, 'days').format('YYYY-MM-DD');
                            Object.keys(data[date]).forEach((key) => {
                                if (data[date][key][line]) {
                                    tableData[line][i] = (tableData[line][i] ?? '') + '<div><b>' + (employees[key] ?? '?') + '</b><br/>' + data[date][key][line] + '</div>'
                                }
                            })
                        }
                    }

                    jexcel(document.getElementById('spreadsheet'), {
                        data: tableData,
                        columns: [
                            {
                                type: 'html',
                                title: 'Montag ' + moment().startOf('isoweek').add(0, 'days').format('DD.MM.YYYY'),
                                width: 180
                            },
                            {
                                type: 'html',
                                title: 'Dienstag ' + moment().startOf('isoweek').add(1, 'days').format('DD.MM.YYYY'),
                                width: 180
                            },
                            {
                                type: 'html',
                                title: 'Mittwoch ' + moment().startOf('isoweek').add(2, 'days').format('DD.MM.YYYY'),
                                width: 180
                            },
                            {
                                type: 'html',
                                title: 'Donnerstag ' + moment().startOf('isoweek').add(3, 'days').format('DD.MM.YYYY'),
                                width: 180
                            },
                            {
                                type: 'html',
                                title: 'Freitag ' + moment().startOf('isoweek').add(4, 'days').format('DD.MM.YYYY'),
                                width: 180
                            },
                            {
                                type: 'html',
                                title: 'Samstag ' + moment().startOf('isoweek').add(5, 'days').format('DD.MM.YYYY'),
                                width: 180
                            },
                            {
                                type: 'html',
                                title: 'Sonntag ' + moment().startOf('isoweek').add(6, 'days').format('DD.MM.YYYY'),
                                width: 180
                            }

                        ]
                    });
                }
            });
        });
    });

</script>