<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Entry Tiket_<?php echo $datas[0]["TRUCK_VISIT_ID_NEW"]; ?></title>

    <style type="text/css">
        @page {
            margin: 30px 20px;
        }

        #footer {
            font-size: x-small;
            position: fixed;
            left: 0px;
            bottom: -60px;
            right: 0px;
            height: 70px;
        }

        #footer .page {
            font-weight: bold;
        }

        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table {
            font-size: x-small;
        }

        tfoot tr td {
            font-weight: bold;
            font-size: x-small;
        }

        .gray {
            background-color: lightgray
        }

        .bord {
            border: 3px solid #000000;
            padding: 5px;      
        }

        .truck {
            border: 3px solid #000000;
            padding: 5px;
            padding-bottom : 650px;
        }
        
        .bord1 {
            border: 3px solid #000000;
            padding: 15px;
            margin-top: 20px;
        }
        .bar {
            padding: 0.5mm;
            margin: 0;
            vertical-align: top;
            barWidth: 100%;
        }
    </style>

</head>
<body>
<div id="footer">
    <table align="left">
        <tr>
            <td>Printed: <?php date_default_timezone_set("Asia/Bangkok");
                echo date("l d-M-Y h:i:s a"); ?></td>
        </tr>
    </table>
</div>
<div class="bord">
    <table width="100%">
        <tr>
            <td align="center">
                <h3>Entry Ticket</h3>
            </td>
        </tr>
    </table>
    <br>
    <table width="100%">
        <tr>
            <td>
                PT INDONESIA KENDARAAN TERMINAL Tbk
                <br>
                Jalan Sindang Laut , Cilincing Jakarta Utara
                <br>
                Phone : +622143932251
                <br>
                Fax : +622143932250
            </td>
            <td align="right">
                <div style="position: absolute; left:0; right: 0; top: 0; bottom: 0;">
                    <img src="assets/img/IPCC_LOGO_2.png"
                         style="width: 40mm; height: 25mm; margin: 0;"/>
                </div>
            </td>
        </tr>
    </table>
</div>


<br/>

<div class="truck">
    <table width="100%">
        <tr>
            <td align="center">
            <barcode size="1" height="1" code="<?php echo $datas[0]["TRUCK_VISIT_ID_NEW"]; ?>" type="C128A" />
            <h2><?php echo $datas[0]["TRUCK_VISIT_ID_NEW"]; ?></h2>                
            </td>
        </tr>
    </table>
    <table width="100%">
        <tr>
            <td align="left">
                <table align="left">
                    <tr>
                        <td><h4>Carrier Information</h4></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Trucking Company(STID)</td>
                        <td>:</td>
                        <td><?php echo $datas[0]["TRUCKING_COMPANY_STID"]; ?></td>
                    </tr>
                    <tr>
                        <td>Truck Type(STID)</td>
                        <td>:</td>
                        <td><?php echo $datas[0]["TRUCK_TYPE_STID"]; ?></td>
                    </tr>
                    <tr>
                        <td>Direction</td>
                        <td>:</td>
                        <td><?php echo $datas[0]["DIRECTION"]; ?></td>
                    </tr>
      
                </table>
            </td>
            <td align="left">
                <table align="left">
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Truck Driver</td>
                        <td>:</td>
                        <td><?php echo $datas[0]["TRUCK_DRIVER"]; ?></td>
                    </tr>
                    <tr>
                        <td>STID Number</td>
                        <td>:</td>
                        <td><?php echo $datass[0]["STID_NUMBER"]; ?></td>
                    </tr><tr>
                        <td>License Plate</td>
                        <td>:</td>
                        <td><?php echo $datas[0]["LICENSE_PLATE"]; ?></td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
    <br/>
</div>

<?php for($i=0; $i < count($datas); $i++){ ?> 

<div class="bord1">
    <table width="100%">
        <tr>
      
        </tr>
    </table>
    <table width="100%">
        <tr>
            <td align="left" width="60%">
                <table align="left" width="100%">
                    <tr>                    
                       <td align="center"  style="width: 100%;">                     
                       <barcode size="1" height="1" code="<?php echo $datas[$i]["VIN"]; ?>" type="C128A" style="width: 150pt; height: 100pt;"/>           
                        <h2><?php echo $datas[$i]["VIN"]; ?></h2>
                       </td>                    
                    
                    </tr>
         
      
                </table>
            </td>
            <td align="left" width="40%">
                <table align="left">
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <!-- <td>VESSEL_NAME</td>
                        <td>:</td> -->
                        <td><?php
                        $x = $datas[$i]["VESSEL_NAME_NEW"];
                        if ($x) {
                            echo $x;
                        
                        }
                        
                        if(trim($x) == ''){ 
                            echo "-";
                         }
                        ?></td>
                  				
						
                        </td>
                    </tr>
                    <tr>
                        <!-- <td>TERMINAL</td>
                        <td>:</td> -->
                        <td><?php echo $datas[$i]["DESTINATION_NAME_NEW"]; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br/>
</div>

<?php
}?>


</body>
</html>