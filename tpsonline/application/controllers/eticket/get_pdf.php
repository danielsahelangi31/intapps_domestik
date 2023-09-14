<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Get_Pdf extends CI_Controller
{
    private $local_db;

    public function __construct()
    {
        parent::__construct();
    }

    private function get_ctos()
    {
        if (!$this->local_db) {
            $this->local_db = $this->load->database(ILCS_CTOS_QAS, TRUE);
        }

        return $this->local_db;
    }

    public function visit($TRKVisitID)
    {
        $this->load->library('ftp');
        $this->load->library('M_pdf');
        $mod = model('etickets');

        $config['hostname'] = '172.16.254.219';
        $config['username'] = 'eticketikt';
        $config['password'] = 'IKT@X7f6';
        $config['port'] = 21;
        $config['debug'] = TRUE;
        $this->ftp->connect($config);

//        $byDocTransferID = $mod->getByTransferID($docTransferID);

        try {
            $res = $mod->getAsosiasiByTruckVisitID($TRKVisitID);

            $import = [];
            $export = [];

            $imp = $mod->getImport($TRKVisitID);

            if ($imp) {
                foreach ($imp as $items) {
                    array_push($import, $items);
                }
            }

            if ($res) {
                foreach ($res as $item) {
                    if ($item->DIRECTION != '1') {
                        array_push($export, $item);
                    }
                }
            }

            $datas = $mod->getEntryTicketInfo($TRKVisitID);

            if ($datas) {
                $data = array(
                    'inform' => $datas ? $datas[0] : null,
                    'title' => $TRKVisitID,
                    'imports' => $import,
                    'exports' => $export
                );

                ini_set('memory_limit', '256M');
                $html = $this->load->view('backend/reports/getPdf/entry_ticket', $data, true);
                $this->m_pdf->pdf->WriteHTML($html);
                $output = $datas[0]->TNR . '.pdf';
                $this->m_pdf->pdf->Output(FILE_PATH . $output, "F");

                $this->ftp->upload(FILE_PATH . $output, GEN_PDF_FTP . $output, 'ascii', 0775);
                $this->load->helper("file");
                unlink(FILE_PATH . $output);
            }

            $this->ftp->close();
            echo json_encode(array(
                'statusCode' => 1,
                'message' => "OK"
            ));

        } catch (Exception $e) {
            echo json_encode(array(
                'statusCode' => 0,
                'message' => $e->getMessage()
            ));
        }

    }

}
