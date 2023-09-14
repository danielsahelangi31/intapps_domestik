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
            border: 3px solid dodgerblue;
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
                <h3>Entry Ticket</h3>
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
                    <img src="assets/img/ipc_logo.png"
                         style="width: 40mm; height: 20mm; margin: 0;"/>
                </div>
            </td>
        </tr>
    </table>
</div>


<br/>

<div class="bord">
    <div style="width: 100%">
        <div align="center">
            <barcode size="1" height="1" code="<?php echo $inform->TNR; ?>" type="C128A"/>
        </div>
        <div align="center">
            <?php echo $inform->TNR; ?>
        </div>
    </div>
    <table width="100%">
        <tr>
            <td align="left">
                <table align="left">
                    <tr>
                        <td>Carrier information</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Carrier</td>
                        <td>:</td>
                        <td><?php echo $inform->CARRIER ? $inform->CARRIER : 'No Carrier'; ?></td>
                    </tr>
                    <tr>
                        <td>Transport Mode</td>
                        <td>:</td>
                        <td>TRUCK</td>
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
                        <td><?php echo $inform->DRIVER? $inform->DRIVER : 'No Driver'; ?></td>
                    </tr>
                    <tr>
                        <td>License Plate</td>
                        <td>:</td>
                        <td><?php echo $inform->TRANSPORTMEANNAME ? $inform->TRANSPORTMEANNAME : 'No License Plate' ; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <br/>
    <br/>
    <table width="100%">
        <tr>
            <? if($exports && $imports) { ?>
            <td align="left" style="vertical-align: top">
                <table align="left" width="100%">
                    <tr>
                        <th></th>
                        <th width="40%" align="left"><?php echo $inform->DESCRIPTION == 'SELFDRIVE' ? 'LIST VIN' : 'List BL' ?></th>
                        <?php
                        if($imports){
                            if($inform->DESCRIPTION == 'SELFDRIVE'){
                                ?>
                                <th width="35%" align="right"><?php echo $imports ? 'Vessel' : ''; ?></th>
                                <th width="10%" align="right"><?php echo $imports ? 'IN' : ''; ?></th>
                                <th width="10%" align="right"><?php echo $imports ? 'OUT' : ''; ?></th>
                                <?php
                            }else{
                                ?>
                                <th width="35%" align="left"></th>
                                <th width="10%" align="left"></th>
                                <th width="10%" align="left"></th>
                                <?php
                            }
                        }
                        ?>

                    </tr>
                    <?php
                    if($imports){
                        foreach ($imports as $index=>$item){
                            ?>
                            <tr>
                                <td><input type="checkbox" name="import_checkbox<?php echo ($index+1); ?>" /></td>
                                <td>
                                    <? echo $inform->DESCRIPTION == 'SELFDRIVE' ? $item->VIN : $item->BL_NUMBER; ?> 
                                    <?php echo ' - ' . $item->DOC_TYPE ?>
                                </td>
                                <td><?  $item->TRANSPORTMEANNAME ? $item->TRANSPORTMEANNAME : '-'; ?></td>
                                <td><?  $item->EXTERNALREFERENCEIN ? $item->EXTERNALREFERENCEIN : '-'; ?></td>
                                <td><?  $item->EXTERNALREFERENCEOUT ? $item->EXTERNALREFERENCEOUT : '-'; ?></td>
                            </tr>
                            <?php
                        }
                    }else{
                        ?>
                        <tr>
                            <td><?  ?></td>
                            <td><? echo 'Tidak ada BL'; ?></td>
                            <td><?  ?></td>
                            <td><?  ?></td>
                            <td><?  ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </td>
            <td align="right" style="vertical-align: top">
                <table align="right" width="100%">
                    <tr >
                        <th width="10%" align="center"><?php echo $exports ? 'OUT' : ''; ?></th>
                        <th width="10%" align="center"><?php echo $exports ? 'IN' : ''; ?></th>
                        <th width="35%" align="center"><?php echo $exports ? 'Vessel' : ''; ?></th>
                        <th width="40%" align="center">List VIN EXPORT</th>
                        <th></th>
                    </tr>
                    <?php
                    if($exports){
                        foreach ($exports as $index=>$item){
                            ?>
                            <tr>
                                <td align="center"><? echo $item->EXTERNALREFERENCEOUT ? $item->EXTERNALREFERENCEOUT : '-'; ?></td>
                                <td align="center"><? echo $item->EXTERNALREFERENCEIN ? $item->EXTERNALREFERENCEIN : '-'; ?></td>
                                <td align="center"><? echo $item->TRANSPORTMEANNAME ? $item->TRANSPORTMEANNAME : '-'; ?></td>
                                <td align="center">
                                    <? echo $item->VIN; ?>
                                    <? if ($item->MAKE === 'HONDA' || $item->MAKE == 'TOYOC' || $item->MAKE == 'TOYOTA' || $item->MAKE == 'TOYOT' || $item->MAKE == 'TOYOH' || $item->MAKE == 'TOYOSP' || $item->MAKE == 'OTHER' || $item->MAKE == 'HONDC' || $item->MAKE == 'WULING') {
                                        echo ' - NPE';
                                    } else {
                                        echo ' - Auto NPE';
                                    } ?>
                                </td>
                                <td align="center"><input type="checkbox" name="export_checkbox<?php echo ($index+1); ?>" /></td>
                            </tr>
                            <?php
                        }
                    }else{
                        ?>
                        <tr>
                            <td><?  ?></td>
                            <td><?  ?></td>
                            <td><?  ?></td>
                            <td><? echo 'Tidak ada VIN'; ?></td>
                            <td><?  ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </td>
            <? } else if(!$exports) { ?>
            <td align="left" style="vertical-align: top">
                <table align="left" width="100%">
                    <tr>
                        <th></th>
                        <th align="left"><?php echo $inform->DESCRIPTION == 'SELFDRIVE' ? 'LIST VIN' : 'List BL' ?></th>
                        <?php
                        if($imports){
                            if($inform->DESCRIPTION == 'SELFDRIVE'){
                                ?>
                                <th align="left"><?php echo $imports ? 'Vessel' : ''; ?></th>
                                <th align="left"><?php echo $imports ? 'IN' : ''; ?></th>
                                <th align="left"><?php echo $imports ? 'OUT' : ''; ?></th>
                                <?php
                            }else{
                                ?>
                                <th align="left"></th>
                                <th align="left"></th>
                                <th align="left"></th>
                                <?php
                            }
                        }
                        ?>
                        <th align="center">List Vin Export</th>
                    </tr>
                    <?php
                    if($imports){
                        foreach ($imports as $index=>$item){
                            ?>
                            <tr>
                                <td align="left"><input type="checkbox" name="import_checkbox<?php echo ($index+1); ?>" /></td>
                                <td align="left">
                                    <? echo $inform->DESCRIPTION == 'SELFDRIVE' ? $item->VIN : $item->BL_NUMBER; ?> 
                                    <? if ($item->MAKE === 'HONDA' || $item->MAKE == 'TOYOC' || $item->MAKE == 'TOYOTA' || $item->MAKE == 'TOYOT' || $item->MAKE == 'TOYOH' || $item->MAKE == 'TOYOSP' || $item->MAKE == 'OTHER' || $item->MAKE == 'HONDC' || $item->MAKE == 'WULING') {
                                        echo '';
                                    } else {
                                        echo '';
                                    } ?>
                                </td>
                                    <td align="left"><? echo $item->TRANSPORTMEANNAME ? $item->TRANSPORTMEANNAME : ''; ?></td>
                                    <td align="left"><? echo $item->EXTERNALREFERENCEIN ? $item->EXTERNALREFERENCEIN : ''; ?></td>
                                    <td align="left"><? echo $item->EXTERNALREFERENCEOUT ? $item->EXTERNALREFERENCEOUT : ''; ?></td>
                                    <td align="center">Tidak Ada VIN</td>
                            </tr>
                            <?php
                        }
                    }else{
                        ?>
                        <tr>
                            <td><?  ?></td>
                            <td><? echo 'Tidak ada BL'; ?></td>
                            <td><?  ?></td>
                            <td><?  ?></td>
                            <td><?  ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </td>
            <? } else { ?>
            <td align="right" style="vertical-align: top">
                <table align="right" width="100%">
                    <tr >
                        <th align="left">List BL</th>
                        <th align="center"><?php echo $exports ? 'OUT' : ''; ?></th>
                        <th align="center"><?php echo $exports ? 'IN' : ''; ?></th>
                        <th align="center"><?php echo $exports ? 'Vessel' : ''; ?></th>
                        <th width="40%" align="center">List VIN EXPORT</th>
                        <th></th>
                    </tr>
                    <?php
                    if($exports){
                        foreach ($exports as $index=>$item){
                            ?>
                            <tr>
                                <td align="left">Tidak ada BL</td>
                                <td align="center"><? echo $item->EXTERNALREFERENCEOUT ? $item->EXTERNALREFERENCEOUT : '-'; ?></td>
                                <td align="center"><? echo $item->EXTERNALREFERENCEIN ? $item->EXTERNALREFERENCEIN : '-'; ?></td>
                                <td align="center"><? echo $item->TRANSPORTMEANNAME ? $item->TRANSPORTMEANNAME : '-'; ?></td>
                                <td width="40%" align="center">
                                    <? echo $item->VIN; ?>
                                    <? if ($item->MAKE === 'HONDA' || $item->MAKE == 'TOYOC' || $item->MAKE == 'TOYOTA' || $item->MAKE == 'TOYOT' || $item->MAKE == 'TOYOH' || $item->MAKE == 'TOYOSP' || $item->MAKE == 'OTHER' || $item->MAKE == 'HONDC' || $item->MAKE == 'WULING') {
                                        echo ' - NPE';
                                    } else {
                                        echo ' - Auto NPE';
                                    } ?>
                                </td>
                                <td align="left"><input type="checkbox" name="export_checkbox<?php echo ($index+1); ?>" /></td>
                            </tr>
                            <?php
                        }
                    }else{
                        ?>
                        <tr>
                            <td><?  ?></td>
                            <td><?  ?></td>
                            <td><?  ?></td>
                            <td><? echo 'Tidak ada VIN'; ?></td>
                            <td><?  ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </td>
            <? } ?>
        </tr>
    </table>
</div>

</body>
</html>
