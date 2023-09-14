<!DOCTYPE html>
<html lang="id">
<head>
    <?php $this->load->view('backend/elements/basic_head') ?>
    <style>
        #dateFrom {
            margin-bottom:20px;
        }

        .labelDate {
            margin-right: 10px;
        }

        .inputDate {
            margin-right: 15px;
        }
</style>
</head>

<body>
<div id="wrap">
    <?php $this->load->view('domestik/backend/components/header_domestik') ?>

    <div class="container" style="max-width: 1800px;">

        <h2>E-Ticket List Domestik</h2>
        <hr />
        <div id="dateFrom">
            <label class="labelDate">From</label>
            <input id="from" class="inputDate" type="date">
            <label class="labelDate">To</label>
            <input id="to" class="inputDate" type="date">
            <button id="saringData" class="btn btn-primary">Cari</button>
        </div>
        <table class="table table-striped table-condensed" id="eticket_list">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Truck Code</th>
                    <th>License Plate</th>
                    <th>Truck Visit ID</th>
                    <th>Direction</th>
                    <th>Brand</th>
                    <th>Last Change</th>
                    <th>Action</th>
                </tr>
            </thead>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Truck Code</th>
                    <th>License Plate</th>
                    <th>Truck Visit ID</th>
                    <th>Direction</th>
                    <th>Brand</th>
                    <th>Last Change</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- /.container -->
</div>

<?php $this->load->view('domestik/backend/elements/footer_domestik') ?>

<script type="text/javascript">
    var table;

    $(document).ready(function() {

        $('#eticket_list thead tr:eq(1) th').each( function (n) {
            console.log(n);
            var title = $(this).text();
            if( n > 0 && n <= 6) {
                $(this).html( '<input type="text" placeholder="Search '+title+'" class="column_search"  />' );
                if(n == 6) {
                    $(this).html( '<input type="date" placeholder="Search '+title+'" id="date_search"  />' );
                }
            }
        } );

        //datatables
        var table = $('#eticket_list').DataTable({
            // "searching": false,
            "processing": true,
            "serverSide": false,
            "deferRender": true,           
            "bInfo" : false,
            "dom": 'Bfrtip',
            // "dom": '<input ype="text">', 
            "buttons": [
                'colvis',
                'pageLength'
            ],
            "order": [],
            "ajax": {
                "url": '<?php echo site_url('domestik/eticket_list_domestik/getData'); ?>',
                "type": "POST"
            },
            "columnDefs": [
                {
                    "targets": [0,1],
                    "width": "30px"
                }
            ]        

        });

        $('#saringData').click(function() {
            var from = $('#from').val();
            var to = $('#to').val(); 

            if(from == "" && to == "") {
                alert("kolom from dan to harus diisi");
            } else if(from == "") {
                alert("kolom from harus diisi"); 
            } else if(to == "") {
                alert("kolom to harus diisi"); 
            } else {
                var dateFrom = new Date(from);
                var dateTo = new Date(to);
                
                if(dateFrom.getDate() > dateTo.getDate()) {
                    alert("tanggal awal harus lebih kecil daripada tanggal akhir"); 
                } else {
                    table.clear(); 
                    table.destroy();
                    table = $('#eticket_list').DataTable({
                        // "searching": false,
                        "processing": true,
                        "serverSide": false,
                        "deferRender": true,           
                        "bInfo" : false,
                        "dom": 'Bfrtip',
                        // "dom": '<input ype="text">', 
                        "buttons": [
                            'colvis',
                            'pageLength'
                        ],
                        "order": [],
                        "ajax": {
                            "url": '<?php echo site_url('domestik/eticket_list_domestik/getData'); ?>',
                            "type": "POST",
                            "data": {
                                "from": $('#from').val(),
                                "to": $('#to').val()
                            }
                        },
                        "columnDefs": [
                            {
                                "targets": [0,1],
                                "width": "30px"
                            }
                        ]        
        
                    });
                }
            }

        });

        // Apply the text search
         $('#eticket_list thead').on( 'keyup', '.column_search', function () { 
            table
                .column( $(this).parent().index() )
                .search( this.value )
                .draw();
        } );

        // Apply the date search
        $( '#eticket_list thead'  ).on( 'change', "#date_search",function () {
            
            var input = document.getElementById("date_search").value;
            var inputStr = input.toString();
            var res = inputStr.split("-"); // turn the date into a list format (Split by / if needed)
            var months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL", 
            "AUG", "SEP", "OCT", "NOV", "DEC"];
            
            var date = new Date(input);
            // console.log(months[res[1]-1]) 
            var search = date.getDate()+'-'+months[res[1]-1]+'-'+date.getFullYear().toString().substr(-2);
            console.log(search);
            table
                .column( $(this).parent().index() )
                .search( search )
                .draw();
        } );

    });

</script>

</body>
</html>