<!DOCTYPE html>
<html lang="id">
<head>
    <?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
<div id="wrap">
    <?php $this->load->view('vessel/backend/components/header_vessel') ?>

    <div class="container" >

        <h2>Vessel List</h2>
        <hr />
        <div style="overflow-x:scroll">
            <table class="table table-striped table-condensed" id="vessel_list">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Status</th>
                        <th>Vessel</th>
                        <th>External Reference in</th>
                        <th>Reference Number</th>
                        <th>Estimated</th>
                        <th>Estimated Arrival at Port</th>
                        <th>Est.depature</th>
                        <th>Arrival</th>
                        <th>Depature</th>
                        <th>Draft</th>
                        <th>Expected no. of VINs loading</th>
                        <th>Expected no. of VINs unloading</th>
                        <th>Port of Destination</th>
                        <th>Terminal</th>
                        <th>Actions</th>
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Status</th>
                        <th>Vessel</th>
                        <th>External Reference in</th>
                        <th>Reference Number</th>
                        <th>Estimated</th>
                        <th>Estimated Arrival at Port</th>
                        <th>Est.depature</th>
                        <th>Arrival</th>
                        <th>Depature</th>
                        <th>Draft</th>
                        <th>Expected no. of VINs loading</th>
                        <th>Expected no. of VINs unloading</th>
                        <th>Port of Destination</th>
                        <th>Terminal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>1</th>
                        <th>Pelajar</th>
                        <th>Alken Princess</th>
                        <th>External Reference in</th>
                        <th>Reference Number</th>
                        <th>2022-02-20</th>
                        <th>2022-02-20</th>
                        <th>2022-02-20</th>
                        <th>2022-02-20</th>
                        <th>2022-02-20</th>
                        <th>Draft</th>
                        <th>Expected no. of VINs loading</th>
                        <th>Expected no. of VINs unloading</th>
                        <th>Port of Destination</th>
                        <th>Terminal</th>
                        <th>Actions</th>
                    </tr>
                    <tr>
                        <th>2</th>
                        <th>Perkerja</th>
                        <th>Indah Abadi</th>
                        <th>External Reference in</th>
                        <th>Reference Number</th>
                        <th>2022-02-21</th>
                        <th>2022-02-21</th>
                        <th>2022-02-21</th>
                        <th>2022-02-21</th>
                        <th>2022-02-21</th>
                        <th>Draft</th>
                        <th>Expected no. of VINs loading</th>
                        <th>Expected no. of VINs unloading</th>
                        <th>Port of Destination</th>
                        <th>Terminal</th>
                        <th>Actions</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div><!-- /.container -->
    <!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Announce Vessel</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form>
        <div class="form-group">
            <label for="exampleFormControlSelect1">Vessel</label>
            <select class="form-control" id="exampleFormControlSelect1">
                <option>pilih</option>
                <option>2</option>
            </select>
        </div>
        <div class="form-group">
            <label for="exampleFormControlSelect1">Terminal</label>
            <select class="form-control" id="exampleFormControlSelect1">
                <option>pilih</option>
                <option>2</option>
            </select>
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">External Reference In</label>
            <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">Reference Number (outbond)</label>
            <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">Estimated Arrival at Port</label>
            <input type="date" class="form-control">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">Est.departure</label>
            <input type="date" class="form-control">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">Draft</label>
            <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">Port of Origin</label>
            <select class="form-control" id="exampleFormControlSelect1">
                <option>pilih</option>
                <option>2</option>
            </select>
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">Port of Transit</label>
            <select class="form-control" id="exampleFormControlSelect1">
                <option>pilih</option>
                <option>2</option>
            </select>
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">Port of Destination</label>
            <select class="form-control" id="exampleFormControlSelect1">
                <option>pilih</option>
                <option>2</option>
            </select>
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">Expected no. of VINs loading</label>
            <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">Expected no. of VINs unloading</label>
            <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
        </div>
      </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</div>
</div>

<?php $this->load->view('vessel/backend/elements/footer_vessel') ?>

<script type="text/javascript">

    var table;

    $(document).ready(function() {
        // Setup - add a text input to each footer cell
        $('#vessel_list thead tr:eq(1) th').each( function (n) {
            var title = $(this).text();
            if( n > 4 && n < 10 ) {
                $(this).html( '<input type="date" placeholder="Search '+title+'" class="date_search"  />' );
            } else {
                $(this).html( '<input type="text" placeholder="Search '+title+'" class="column_search"  />' );
            }
        } );

        //datatables
        var table = $('#vessel_list').DataTable({
            "orderCellsTop": true,
            "fixedHeader": true,
            "pageLength": 100,
            "processing": true,
            "serverSide": false,
            "deferRender": true,
            "bInfo" : false,
            "dom": 'Bfrtip',
            "buttons": [
                {
                    text: "Announce Vessel",
                    action: function (e, node, config){
                        $('#myModal').modal('show')
                    }
                },
                'colvis',
                'pageLength'
            ],
            // "ajax": {
            //     "url": '<?php // echo site_url('eticket/eticket_list/get_items'); ?>',
            //     "type": "POST"
            // },
            // "columnDefs": [
            //     {
            //         "targets": [0,2,9],
            //         "orderable": false,
            //         "searchable": false,
            //     },
            //     {
            //         "targets": [3,4],
            //         "orderable": true,
            //         "visible": false,
            //         "searchable": false,
            //     },
            // ],
            //Set column definition initialisation properties.


            
        });
        
        // Apply the text search
        $( '#vessel_list thead'  ).on( 'keyup', ".column_search",function () { 
            table
                .column( $(this).parent().index() )
                .search( this.value )
                .draw();
        } );

        // Apply the date search
        $( '#vessel_list thead'  ).on( 'change', ".date_search",function () {
            
            table
                .column( $(this).parent().index() )
                .search( this.value )
                .draw();
        } );

    });

</script>

</body>
</html>