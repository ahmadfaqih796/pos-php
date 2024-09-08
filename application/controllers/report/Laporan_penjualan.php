<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Laporan_penjualan extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'html', 'file', 'tanggal'));
        $this->load->library(array('template', 'form_validation', 'unit_test'));
        // $this->load->model('report/Report_model', 'rm');
        date_default_timezone_set('Asia/Jakarta');

        $this->name     = $this->session->userdata('name');
        $this->userId   = $this->session->userdata('user_id');
        $this->dateNow  = date('Y-m-d H:i:s');
    }

    public function index()
    {
        if ($this->auth() == false) {
            redirect('');
        }
        if ($this->session->userdata('filter')) {
            $this->convertDateFormat($this->session->userdata('tgl1'));
            $this->convertDateFormat($this->session->userdata('tgl2'));
        } else {
            $this->session->set_userdata('tgl1', date('Y-m-01'));
            $this->session->set_userdata('tgl2', date('Y-m-d'));
        }

        $data = [
            'title' => 'Laporan Penjualan',
            'query' => $this->db->select('*')->where('Is_Paid', 1)->where('DATE(Created_Date) >=', $this->session->userdata('tgl1'))->where('DATE(Created_Date) <=', $this->session->userdata('tgl2'))->get('tbl_transaksi'),
            'tgl1'  => $this->convertDateFormat($this->session->userdata('tgl1')),
            'tgl2'  => $this->convertDateFormat($this->session->userdata('tgl2')),
            'totalPendapatan' => $this->db->select('SUM(Bill) as pendapatan')->where('Is_Paid', 1)->where('DATE(Created_Date) >=', $this->session->userdata('tgl1'))->where('DATE(Created_Date) <=', $this->session->userdata('tgl2'))->get('tbl_transaksi')->row()->pendapatan
        ];


        $this->template->load('template', 'report/index', $data);
    }

    function filter1()
    {
        if ($this->auth() == false) {
            redirect('');
        }
        $GDMonth = $this->input->post('GDMonth');
        $GDYear = $this->input->post('GDYear');

        $tglinput1 = $GDYear . '-' . $GDMonth . '-01';
        $tglinput2 = $GDYear . '-' . $GDMonth . '-' . $this->getLastDayOfMonth($GDYear . '-' . $GDMonth);
        $this->session->set_userdata('filter', true);
        $this->session->set_userdata('tgl1', $tglinput1);
        $this->session->set_userdata('tgl2', $tglinput2);

        redirect('report/laporan_penjualan');
    }

    function filter2()
    {
        if ($this->auth() == false) {
            redirect('');
        }
        $date1 = $this->input->post('date1');
        $date2 = $this->input->post('date2');

        $this->session->set_userdata('filter', true);
        $this->session->set_userdata('tgl1', $date1);
        $this->session->set_userdata('tgl2', $date2);

        redirect('report/laporan_penjualan');
    }

    function reset_filter()
    {
        if ($this->auth() == false) {
            redirect('');
        }
        $this->session->unset_userdata('filter');
        $this->session->unset_userdata('tgl1');
        $this->session->unset_userdata('tgl2');

        redirect('report/laporan_penjualan');
    }

    public function get_data_detail_penjualan()
    {
        if ($this->auth() == false) {
            redirect('');
        }
        $id_transaksi = $this->input->get('id_transaksi');

        $data['detail_penjualan'] = $this->db->select('*')->from('tbl_penjualan')->where('ID_Transaksi', $id_transaksi)->get()->result();

        echo json_encode($data['detail_penjualan']);
    }

    public function export()
    {
        if ($this->auth() == false) {
            redirect('');
        }
        if ($this->session->userdata('filter')) {
            $this->convertDateFormat($this->session->userdata('tgl1'));
            $this->convertDateFormat($this->session->userdata('tgl2'));
        } else {
            $this->session->set_userdata('tgl1', date('Y-m-01'));
            $this->session->set_userdata('tgl2', date('Y-m-d'));
        }

        $data = [
            'title' => 'Laporan Penjualan',
            'query' => $this->db->select('*')->where('Is_Paid', 1)->where('DATE(Created_Date) >=', $this->session->userdata('tgl1'))->where('DATE(Created_Date) <=', $this->session->userdata('tgl2'))->get('tbl_transaksi'),
            'tgl1'  => $this->convertDateFormat($this->session->userdata('tgl1')),
            'tgl2'  => $this->convertDateFormat($this->session->userdata('tgl2')),
            'totalPendapatan' => $this->db->select('SUM(Bill) as pendapatan')->where('Is_Paid', 1)->where('DATE(Created_Date) >=', $this->session->userdata('tgl1'))->where('DATE(Created_Date) <=', $this->session->userdata('tgl2'))->get('tbl_transaksi')->row()->pendapatan
        ];


        $this->load->view('report/export', $data);
    }

    function convertDateFormat($dateString)
    {
        $date = new DateTime($dateString);

        $formattedDate = $date->format('j M Y');

        return $formattedDate;
    }

    function getLastDayOfMonth($dateString)
    {
        $date = new DateTime($dateString);

        $month = $date->format('m');
        $year = $date->format('Y');

        $lastDayOfMonth = date("t", strtotime("$year-$month-01"));

        return $lastDayOfMonth;
    }

    function grafik()
    {
        if ($this->auth() == false) {
            redirect('');
        }
        $data['query'] = $this->db->query('SELECT Period_Month as BulanPembayaran, SUM(Bill) as jumlahPembayaran FROM `tbl_transaksi` where Is_Paid = 1 GROUP BY Period_Month');

        $this->template->set('title', 'Laporan Penjualan Grafik');
        $this->template->load('template', 'report/grafik', $data);
    }

    function cetakTransaksi($id)
    {
        if ($this->auth() == false) {
            redirect('');
        }

        // $data['q1'] = $this->db->select('*')->from('tbl_transaksi')->where('ID_Transaksi', $id)->get()->row();
        $data['q1'] = $this->db->select('*')
            ->from('tbl_transaksi')
            ->where('ID_Transaksi', $id)
            ->join('users', 'users.id = tbl_transaksi.Created_By', 'left')
            ->get()->row();
        $data['q2'] = $this->db->get_where('tbl_penjualan', ['ID_Transaksi' => $id]);
        $this->load->view('report/cetak', $data);
    }

    function auth()
    {
        $role = $this->session->userdata('role');
        if ($role == 1 || $role == 2) {
            return true;
        } else {
            return false;
        }
    }
}
