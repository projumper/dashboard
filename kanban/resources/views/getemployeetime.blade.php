
<!-- https://bossanova.uk/jexcel/v4/examples/spreadsheet-formulas -->

<div id="spreadsheet"></div>

<select id="ma">
    <option value="557058:e33f889f-36f5-476b-a1a7-f21bb2c74915">Ivan</option>
    <option value="557058:660975c1-9644-4563-bcce-6b0b638207ef">Ivan R</option>
    <option value="5b586e3bd2a2f82da138e269">OLeg</option>
</select>

<input type="text" value="2020-12-12" id="date">

<button>GO</button>

<script src="https://bossanova.uk/jexcel/v4/jexcel.js"></script>
<script src="https://bossanova.uk/jsuites/v3/jsuites.js"></script>
<link rel="stylesheet" href="https://bossanova.uk/jsuites/v3/jsuites.css" type="text/css" />
<link rel="stylesheet" href="https://bossanova.uk/jexcel/v4/jexcel.css" type="text/css" />


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>

    var data;

    $(document).ready(function(){




        $("button").click(function(){

            var selector = document.getElementById('ma');
            var value = selector[selector.selectedIndex].value;

            var datetoselect = document.getElementById('date').value;


            //alert(datetoselect);

            $.ajax({url: "{{ config('app.api_url') }}/getEmployeeTime/date/"+datetoselect, success: function(result){

                    data = JSON.stringify(result);
                    data = JSON.parse(data);
                    // alert(data[0].id);


                    jexcel(document.getElementById('spreadsheet'), {
                        data:data,
                        footers: [['Total',, '','','','','','=SUMCOL(TABLE(), COLUMN())']],
                        columns: [
                            { type: 'text', title:'time', width:120 },
                            { type: 'text', title:'account_jira', width:320},
                            { type: 'text', title:'display_name', width:120 },

                        ]
                    });

                }});
        });
    });

</script>
