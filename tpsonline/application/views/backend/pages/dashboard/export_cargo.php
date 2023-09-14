<div>
    <label>&nbsp;</label><br/>
    <label><b>REKAPITULASI DATA CARGO</b></label><br/>
    <label><b>Periode : <?php echo $periode; ?></b></label><br/>
    <label>&nbsp;</label>
</div>
<table border="1px" cellpadding="2">
    <thead>
        <tr align="center">
            <th rowspan="2">No</th>
            <th rowspan="2">Vin</th>
            <th rowspan="2">Status</th>
            <th colspan="3">Waktu</th>
            <th rowspan="2">Actual Position</th>
            <th rowspan="2">Direction</th>
            <th rowspan="2">Maker</th>
            <th rowspan="2">Model</th>
            <th rowspan="2">Jenis</th>
            <th rowspan="2">Consignee</th>
            <th rowspan="2">Asal</th>
            <th rowspan="2">Tujuan Terakhir</th>
            <th rowspan="2">Vessel</th>
            <th rowspan="2">Status Custom</th>
            <th rowspan="2">Visit ID (Vessel)</th>
            <th rowspan="2">Custom Number</th>
            <th rowspan="2">Custom Date</th>
         </tr>
         <tr>
            <th>On Terminal</th>
            <th>Loaded</th>
            <th>Left</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $no = 1;
            if(isset($data) && count($data) > 0) {
                foreach ($data as $obj) {
        ?>
            <tr>
                <td><?php echo $no; ?></td>
                <td><?php echo $obj->VIN; ?></td>
                <td><?php echo $obj->STATUS; ?></td>
                <td><?php echo $obj->DTSONTERMINAL; ?></td>
                <td><?php echo $obj->DTSLOADED; ?></td>
                <td><?php echo $obj->DTSLEFT; ?></td>
                <td><?php echo $obj->ACTUAL_POSITION; ?></td>
                <td><?php echo $obj->DIRECTION; ?></td>
                <td><?php echo $obj->MAKER; ?></td>
                <td><?php echo $obj->MODEL; ?></td>
                <td><?php echo $obj->JENIS; ?></td>
                <td><?php echo $obj->CONSIGNEE; ?></td>
                <td><?php echo $obj->ASAL; ?></td>
                <td><?php echo $obj->FINAL_LOCATION; ?></td>
                <td><?php echo $obj->VESSEL; ?></td>
                <td><?php echo $obj->HOLD_STATUS; ?></td>
                <td><?php echo $obj->VISIT_ID; ?></td>
                <td><?php echo $obj->CUSTOMSNO; ?></td>
                <td><?php echo $obj->CUSTOMSDATE; ?></td>
            </tr>
        <?php
                    $no++;
                }
            }
        ?>
    </tbody>
</table>
