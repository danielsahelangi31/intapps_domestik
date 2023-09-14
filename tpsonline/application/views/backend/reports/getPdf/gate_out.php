<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Laporan Entry Ticket <?php echo $title; ?></title>

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
                            <td style="width: 60%;"><?php echo $inform->CARRIER ? $inform->CARRIER : 'No Carrier'; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 30%;">Date/Time of Arrival</td>
                            <td align="center" style="width: 10%;">:</td>
                            <td style="width: 60%;"><?php echo $inform->ARRIVAL_DATE ? $inform->ARRIVAL_DATE : "-"; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 30%;">Transport Mode</td>
                            <td align="center" style="width: 10%;">:</td>
                            <td style="width: 60%;">TRUCK</td>
                        </tr>
                        <tr>
                            <td style="width: 30%;">Number of Vehicles</td>
                            <td align="center" style="width: 10%;">:</td>
                            <td style="width: 60%;"><?php echo $vehicles['COUNT'] ? $vehicles['COUNT'] : "-"; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 30%;">Truck Driver</td>
                            <td align="center" style="width: 10%;">:</td>
                            <td style="width: 60%;"><?php echo $inform->DRIVER ? $inform->DRIVER : 'No Driver'; ?></td>
                        </tr>
                        <tr>
                            <td style="width: 30%;">License Plate</td>
                            <td align="center" style="width: 10%;">:</td>
                            <td style="width: 60%;"><?php echo $inform->TRANSPORTMEANNAME ? $inform->TRANSPORTMEANNAME : 'No License Plate'; ?></td>
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
                                <barcode size="1" height="1" code="<?php echo $inform->TNR; ?>" type="C128A" />
                            </td>
                        </tr>
                        <tr>
                            <td align="center"> <?php echo $inform->TNR; ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 10px;"></div>

    <?php
    $no = 1;
    foreach ($gateOut as $row) { ?>

        <div class="bord">
            <table width="100%">
                <tbody>
                    <tr>
                        <td style="font-size: 25px; width: 10%;" rowspan="3" align="center"><?php echo $no++; ?></td>
                        <td style="width: 10%;">VIN:</td>
                        <td style="width: 60%;">
                            <barcode size="1" height="1" code="<?php echo $row->VIN; ?>" type="C128A" />
                        </td>
                        <td style="width: 25%; padding-left: 7px;">Delivery to Information</td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center"><?php echo $row->VIN; ?></td>
                        <td align="left">
                            <table width="100%">
                                <tbody>
                                    <tr>
                                        <td>Consignee:</td>
                                        <td><?php echo $row->CONSIGNEE; ?></td>
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
                                        <td style="width: 70%;"><?php echo $row->WEIGHT ? $row->WEIGHT : "-"; ?></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 25%;">Width</td>
                                        <td style="width: 5%;" align="center">:</td>
                                        <td style="width: 70%;"><?php echo $row->WIDTH ? $row->WIDTH : "-"; ?></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 25%;">Height</td>
                                        <td style="width: 5%;" align="center">:</td>
                                        <td style="width: 70%;"><?php echo $row->HEIGHT ? $row->HEIGHT : "-"; ?></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 25%;">m<sup>2</sup></td>
                                        <td style="width: 5%;" align="center">:</td>
                                        <td style="width: 70%;"><?php echo $row->PERSEGI ? $row->PERSEGI : "-"; ?></td>
                                    </tr>
                                    <tr>
                                        <td style="width: 25%;">Notes</td>
                                        <td style="width: 5%;" align="center">:</td>
                                        <td style="width: 70%;"><?php echo $row->NOTES ? $row->NOTES : "-"; ?></td>
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
                                        <td style="width: 70%;"><?php echo $row->BRAND ? $row->BRAND : "-"; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Model</td>
                                        <td style="width: 5%;" align="center">:</td>
                                        <td style="width: 70%;"><?php echo $row->MODEL ? $row->MODEL : "-"; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Color</td>
                                        <td style="width: 5%;" align="center">:</td>
                                        <td style="width: 70%;"><?php echo $row->COLOR ? $row->COLOR : "-"; ?></td>
                                    </tr>
                                    <tr>
                                        <td>SPPB Date</td>
                                        <td style="width: 5%;" align="center">:</td>
                                        <td style="width: 70%;"><?php echo $row->SPPB_DATE ? $row->SPPB_DATE : "-"; ?></td>
                                    </tr>
                                    <tr>
                                        <td>SPPB No</td>
                                        <td style="width: 5%;" align="center">:</td>
                                        <td style="width: 70%;"><?php echo $row->SPPB_NO ? $row->SPPB_NO : "-"; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="<?php echo ($no % 4) == 0 ? 'margin-top: 100px;' : 'margin-top: 10px;'; ?>"></div>
    <?php } ?>

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
                <td align="left">Printed: <?php date_default_timezone_set("Asia/Bangkok");
                                            echo date("l d-M-Y h:i:s a"); ?></td>
            </tr>
        </table>
    </div>

</body>

</html>