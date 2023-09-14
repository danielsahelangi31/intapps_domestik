<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Return Cargo <?php echo $datas["RC_NO_REQ"]; ?></title>

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
                <h3>Return Cargo</h3>
            </td>
        </tr>
    </table>
    <br>
    <table width="100%">
        <tr>
            <td>
                PT INDONESIA KENDARAAN TERMINAL
                <br>
                Jalan Sindang Laut , Cilincing Jakarta Utara
                <br>
                Phone : +622143932251
                <br>
                Fax : +622143932250
            </td>
            <td align="right">
                <div style="position: absolute; left:0; right: 0; top: 0; bottom: 0;">
                    <img src="assets/img/ikt_logo.png"
                         style="width: 40mm; height: 25mm; margin: 0;"/>
                </div>
            </td>
        </tr>
    </table>
</div>


<br/>

<div class="bord">
    <table width="100%">
        <tr>
            <td align="center">
                <h2><?php echo $datas["RC_NO_REQ"]; ?></h2>
            </td>
        </tr>
    </table>
    <table width="100%">
        <tr>
            <td align="left">
                <table align="left">
                    <tr>
                        <td>Return Cargo Information</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>VIN</td>
                        <td>:</td>
                        <td><?php echo $datas["VIN"]; ?></td>
                    </tr>
                    <tr>
                        <td>Model</td>
                        <td>:</td>
                        <td><?php echo $datas["MODEL"]; ?></td>
                    </tr>
                    <tr>
                        <td>Car Maker</td>
                        <td>:</td>
                        <td><?php echo $datas["MAKER"]; ?></td>
                    </tr>
                    <tr>
                        <td>Request Date</td>
                        <td>:</td>
                        <td><?php echo $datas["CREATED_DT"]; ?></td>
                    </tr>
                    <tr>
                        <td>Damage Status</td>
                        <td>:</td>
                        <td><?php echo $datas["DAMAGE_STATUS"] == 0 ? 'No' : 'Yes'; ?></td>
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
                        <td>Truck</td>
                        <td>:</td>
                        <td><?php echo $datas["LICENSEPLATE"]; ?></td>
                    </tr>
                    <tr>
                        <td>Driver</td>
                        <td>:</td>
                        <td><?php echo $datas["DRIVER"]; ?></td>
                    </tr><tr>
                        <td>Carrier</td>
                        <td>:</td>
                        <td><?php echo $datas["CARRIER"]; ?></td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
    <br/>
</div>

</body>
</html>