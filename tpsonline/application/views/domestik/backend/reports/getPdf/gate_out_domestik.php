<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Laporan Entry Ticket <?= $visitID; ?></title>

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
            font-size: 12px;
        }

        table tr td {
            font-weight: bold;
        }

        tfoot tr td {
            font-weight: bold;
            font-size: x-small;
        }

        .gray {
            background-color: lightgray
        }

        .bord {
            border: 3px solid dodgerblue;
            padding: 5px;
        }
    </style>

</head>

<body>

    <div class="bord">
        <table width="100%">
            <tr>
                <td width="20%"></td>
                <td width="60%" align="center">
                    <h3>Loading List</h3>
                    <h3>Surat Jalan</h3>
                </td>
                <td width="20%" align="right">
                    <div style="position: relative; left:0; right: 0; top: 0; bottom: 0;">
                        <img src="assets/img/ipc_logo.png" style="width: 20mm; height: 10mm; margin: 0;" />
                    </div>
                </td>
            </tr>
        </table>
        <table style="margin-bottom: 20px;" width="100%">
            <tr>
                <td>PT INDONESIA KENDARAAN TERMINAL</td>
            </tr>
            <tr>
                <td>Jalan Sindang Laut , Cilincing Jakarta Utara</td>
            </tr>
            <tr>
                <td>Phone : +622143932251</td>
            </tr>
            <tr>
                <td>Fax : +622143932250</td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 10px;"></div>

    <div class="bord">
        <table width="100%">
            <tr>
                <td style="width: 60%;" align="left">
                    <table width="100%">
                        <tr>
                            <td colspan="3" style="width: 30%; font-size: 15px; padding-right: 7px;">Carrier information</td>
                        </tr>
                        <tr>
                            <td style="width: 30%;">Carrier</td>
                            <td align="center" style="width: 10%;">:</td>
                            <td style="width: 60%;"></td>
                        </tr>
                        <tr>
                            <td style="width: 30%;">Date/Time of Arrival</td>
                            <td align="center" style="width: 10%;">:</td>
                            <td style="width: 60%;"></td>
                        </tr>
                        <tr>
                            <td style="width: 30%;">Transport Mode</td>
                            <td align="center" style="width: 10%;">:</td>
                            <td style="width: 60%;">TRUCK</td>
                        </tr>
                        <tr>
                            <td style="width: 30%;">Number of Vehicles</td>
                            <td align="center" style="width: 10%;">:</td>
                            <td style="width: 60%;"></td>
                        </tr>
                        <tr>
                            <td style="width: 30%;">Truck Driver</td>
                            <td align="center" style="width: 10%;">:</td>
                            <td style="width: 60%;"></td>
                        </tr>
                        <tr>
                            <td style="width: 30%;">License Plate</td>
                            <td align="center" style="width: 10%;">:</td>
                            <td style="width: 60%;"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Load Lanes</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 40%;" align="left">
                    <table width="100%">
                        <tr>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="padding: 0 0 10px 15px;" align="left">
                                Visit ID:
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <barcode size="1" height="1" code="" type="C128A" />
                            </td>
                        </tr>
                        <tr>
                            <td align="center"></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 10px;"></div>

        <div class="bord">
            <table width="100%">
                <tbody>
                    <tr>
                        <td style="font-size: 25px; width: 10%;" rowspan="3" align="center"></td>
                        <td style="width: 10%;">VIN:</td>
                        <td style="width: 60%;">
                            <barcode size="1" height="1" code="" type="C128A" />
                        </td>
                        <td style="width: 25%; padding-left: 7px;">Delivery to Information</td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center"></td>
                        <td align="left">
                            <table width="100%">
                                <tbody>
                                    <tr>
                                        <td>Consignee:</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table width="100%">
                <tbody>
                    <tr>
                        <td align="left" style="width: 40%;">
                            <table width="100%">
                                <tbody>
                                    <tr>
                                        <td style="font-size: 15px;" colspan="3">Vehicle Details</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 25%;">Weight</td>
                                        <td style="width: 5%;" align="center">:</td>
                                        <td style="width: 70%;"></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 25%;">Width</td>
                                        <td style="width: 5%;" align="center">:</td>
                                        <td style="width: 70%;"></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 25%;">Height</td>
                                        <td style="width: 5%;" align="center">:</td>
                                        <td style="width: 70%;"></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 25%;">m<sup>2</sup></td>
                                        <td style="width: 5%;" align="center">:</td>
                                        <td style="width: 70%;"></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 25%;">Notes</td>
                                        <td style="width: 5%;" align="center">:</td>
                                        <td style="width: 70%;"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td align="left" style="width: 60%;">
                            <table style="width: 100%">
                                <tbody>
                                    <tr>
                                        <td colspan="3">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>Brand</td>
                                        <td style="width: 5%;" align="center">:</td>
                                        <td style="width: 70%;"></td>
                                    </tr>
                                    <tr>
                                        <td>Model</td>
                                        <td style="width: 5%;" align="center">:</td>
                                        <td style="width: 70%;"></td>
                                    </tr>
                                    <tr>
                                        <td>Color</td>
                                        <td style="width: 5%;" align="center">:</td>
                                        <td style="width: 70%;"></td>
                                    </tr>
                                    <tr>
                                        <td>SPPB Date</td>
                                        <td style="width: 5%;" align="center">:</td>
                                        <td style="width: 70%;"></td>
                                    </tr>
                                    <tr>
                                        <td>SPPB No</td>
                                        <td style="width: 5%;" align="center">:</td>
                                        <td style="width: 70%;"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style=""></div>
    

    <div class="bord">
        <table width="100%">
            <tr>
                <td align="right">
                    Signature:____________________________________________________________
                </td>
            </tr>
        </table>
    </div>
    <div id="footer">
        <table>
            <tr>
                <td align="left">Printed:</td>
            </tr>
        </table>
    </div>

</body>

</html>