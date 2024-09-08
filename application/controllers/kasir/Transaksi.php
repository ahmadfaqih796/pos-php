<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Transaksi extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'html', 'file'));
        $this->load->library(array('template', 'form_validation', 'unit_test'));
        $this->load->model('kasir/Transaksi_model', 'tm');
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
        $data = [
            'title' => 'Menu Transaksi'
        ];
        $this->template->load('template', 'kasir/transaksi/index', $data);
    }

    public function tambahTransaksi()
    {
        if ($this->auth() == false) {
            redirect('');
        }
        $dataInsert = [
            'Nomor_Invoice'     => $this->generateNomorInvoice(),
            'Created_By'        => $this->userId,
            'Period_Month'      => $this->bulanBahasa(date('n')),
            'Is_Paid'           => 2,
            'Period_Year'       => date('Y')
        ];

        $this->db->insert('tbl_transaksi', $dataInsert);
        $id = $this->db->insert_id();

        redirect('kasir/transaksi/inputBelanja/' . $id);
    }

    function bulanBahasa($bulan)
    {
        $nama_bulan = array(
            1 => "Januari",
            2 => "Februari",
            3 => "Maret",
            4 => "April",
            5 => "Mei",
            6 => "Juni",
            7 => "Juli",
            8 => "Agustus",
            9 => "September",
            10 => "Oktober",
            11 => "November",
            12 => "Desember"
        );

        return $nama_bulan[$bulan];
    }

    public function generateNomorInvoice()
    {
        $lastInvoiceNumber = $this->db->select('Nomor_Invoice')->order_by('ID_Transaksi', 'DESC')->limit(1)->get('tbl_transaksi')->row('Nomor_Invoice');

        $lastInvoiceSequence = intval(substr($lastInvoiceNumber, 5));

        $newInvoiceNumber = 'TRINV' . str_pad($lastInvoiceSequence + 1, 4, '0', STR_PAD_LEFT);

        return $newInvoiceNumber;
    }

    public function inputBelanja($id)
    {
        if ($this->auth() == false) {
            redirect('');
        }
        $data = [
            'id'            => $id,
            'title'         => 'Input Belanja',
            'barangJual'    => $this->db->query("SELECT * FROM product WHERE product_name NOT IN (SELECT Nama_Barang FROM tbl_penjualan WHERE ID_Transaksi = $id) AND is_deleted = 0"),
            'barangTemp'    => $this->db->where('ID_Transaksi', $id)->get('tbl_penjualan'),
            'totalBelanja'  => $this->db->query("SELECT SUM(Jumlah_Transaksi_Barang) as jtb FROM tbl_penjualan WHERE ID_Transaksi = $id")->row()->jtb
        ];

        $this->template->load('template', 'kasir/transaksi/edit', $data);
    }

    public function inputBarangTemp($id, $productName, $price)
    {
        if ($this->auth() == false) {
            redirect('');
        }
        $productName_ = str_replace('%20', ' ', $productName);
        $query = $this->db->get_where('tbl_penjualan', ['Nama_Barang' => $productName_, 'ID_Transaksi' => $id]);
        if (!empty($query->result())) {
            echo json_encode(false);
        } else {

            $dataInsert = [
                'ID_Transaksi'              => $id,
                'Nama_Barang'               => $productName_,
                'Harga_Barang'              => $price,
                'Jumlah_Barang'             => 1,
                'Jumlah_Transaksi_Barang'   => 1 * $price,
                'Created_By'                => $this->userId
            ];

            $this->db->insert('tbl_penjualan', $dataInsert);
            echo json_encode(true);
        }
        // $this->session->set_flashdata('msg', 'Berhasil menambahkan ke keranjang!');
        // redirect('kasir/transaksi/inputBelanja/' . $id);
    }

    public function hapusBarangTemp($id_penjualan, $id)
    {
        if ($this->auth() == false) {
            redirect('');
        }
        $this->db->delete('tbl_penjualan', ['ID_Penjualan' => $id_penjualan]);
        $this->session->set_flashdata('msg', 'Berhasil menghapus barang!');
        redirect('kasir/transaksi/inputBelanja/' . $id);
    }

    public function hapusProduk($id)
    {
        if ($this->auth() == false) {
            redirect('');
        }
        $dataUpdate = [
            'is_deleted'    => 1,
            'deleted_by'    => $this->userId,
            'deleted_at'    => $this->dateNow
        ];

        $this->tm->update('product', $dataUpdate, ['id' => $id]);
        $this->session->set_flashdata('msg', 'Berhasil menghapus produk!');
        redirect('master/produk');
    }

    public function kalkulasi($id, $id_penjualan, $val)
    {
        if ($this->auth() == false) {
            redirect('');
        }
        $hargaDB = $this->db->select('*')->from('tbl_penjualan')->where('ID_Penjualan', $id_penjualan)->get()->row();
        $stock = $this->db->select('stock')->from('product')->where('product_name', $hargaDB->Nama_Barang)->get()->row()->stock;
        if ($val > $stock || $val == 0) {
            echo json_encode([
                'data' => false
            ]);
        } else {
            $hargaDB_ = $hargaDB->Harga_Barang * $val;
            $dataUpdate = [
                'Jumlah_Barang' => $val,
                'Jumlah_Transaksi_Barang' => $hargaDB_,
            ];
            $this->db->update('tbl_penjualan', $dataUpdate, ['ID_Penjualan' => $id_penjualan]);
            echo json_encode([
                'data' => true
            ]);
        }
        // redirect('kasir/transaksi/inputBelanja/' . $id);
    }

    public function inputPembayaran($id)
    {
        if ($this->auth() == false) {
            redirect('');
        }
        $bill = $this->input->post('Bill');
        $paid = $this->input->post('Paid');
        $paidType = $this->input->post('paidType');

        // Update stok produk
        $this->updateProductStock($id);

        if ($paidType == "Transfer") {
            // Validasi file bukti transfer
            $config['upload_path']   = './upload/'; // Direktori untuk menyimpan file
            $config['allowed_types'] = 'jpg|jpeg|png'; // Ekstensi file yang diperbolehkan
            $config['max_size']      = 10048; // Ukuran maksimum file (dalam kilobita)
            $config['file_name']     = 'bukti_transfer_' . time(); // Nama file

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('buktiTf')) {
                // Jika upload gagal
                // $error = $this->upload->display_errors();
                // $this->session->set_flashdata('msg', 'Upload bukti transfer gagal: ' . $error);
                // redirect('kasir/transaksi/inputBelanja/' . $id);

                $buktiTransfer = "no_image.jpg";

                // Simpan data pembayaran
                $dataUpdate = [
                    'Bill' => $bill,
                    'Paid' => $bill,
                    'Bukti_Transfer' => $buktiTransfer, // Simpan nama file bukti transfer
                    'Is_Paid' => 1,
                    'Paid_Type' => $paidType
                ];

                $this->db->update('tbl_transaksi', $dataUpdate, ['ID_Transaksi' => $id]);
                $this->session->set_flashdata('msg', 'Pembayaran berhasil!');
                redirect('kasir/transaksi/');
            } else {
                // Jika upload berhasil
                $uploadData = $this->upload->data();
                $buktiTransfer = $uploadData['file_name'];

                // Simpan data pembayaran
                $dataUpdate = [
                    'Bill' => $bill,
                    'Paid' => $bill,
                    'Bukti_Transfer' => $buktiTransfer, // Simpan nama file bukti transfer
                    'Is_Paid' => 1,
                    'Paid_Type' => $paidType
                ];

                $this->db->update('tbl_transaksi', $dataUpdate, ['ID_Transaksi' => $id]);
                $this->session->set_flashdata('msg', 'Pembayaran berhasil!');
                redirect('kasir/transaksi/');
            }
        } else {

            if ($paid < $bill) {
                $this->session->set_flashdata('msg', 'Pemabayaran Gagal!');
                redirect('kasir/transaksi/inputBelanja/' . $id);
            } else {
                $dataUpdate = [
                    'Bill' => $bill,
                    'Paid' => $paid,
                    'Is_Paid' => 1,
                    'Paid_Type' => $paidType
                ];

                $this->db->update('tbl_transaksi', $dataUpdate, ['ID_Transaksi' => $id]);
                $this->session->set_flashdata('msg', 'Pembayaran Berhasil!');
                redirect('kasir/transaksi/');
            }
        }
    }

    private function updateProductStock($id_transaksi)
    {
        // Ambil barang-barang yang terkait dengan transaksi
        $barangTransaksi = $this->db->select('Nama_Barang, Jumlah_Barang')
            ->from('tbl_penjualan')
            ->where('ID_Transaksi', $id_transaksi)
            ->get()
            ->result();

        // Kurangi stok barang di tabel produk
        foreach ($barangTransaksi as $barang) {
            // $productName_ = str_replace('%20', ' ', $barang->Nama_Barang);
            $this->db->set('stock', 'stock - ' . (int) $barang->Jumlah_Barang, FALSE)
                ->where('product_name', $barang->Nama_Barang)
                ->update('product');
        }
    }

    function cetakTransaksi($id)
    {
        if ($this->auth() == false) {
            redirect('');
        }

        $data['q1'] = $this->db->select('*')
            ->from('tbl_transaksi')
            ->where('ID_Transaksi', $id)
            ->join('users', 'users.id = tbl_transaksi.Created_By', 'left')
            ->get()->row();
        $data['q2'] = $this->db->get_where('tbl_penjualan', ['ID_Transaksi' => $id]);
        $this->load->view('kasir/transaksi/cetak', $data);
    }


    function getDataTransaksi()
    {
        if ($this->auth() == false) {
            redirect('');
        }
        $list = $this->tm->dataTransaksi();
        $data = array();
        $no   = $_POST['start'];
        foreach ($list as $field) {
            // $is_paid = ($field->Paid_Type == null) ? '<p class="badge badge-warning">Menunggu Konfirmasi</p>' : ($field->Is_Paid == 0) ? '<p class="badge badge-danger">Belum Bayar</p>' : '<p class="badge badge-success">Sudah Bayar</p>';
            $is_paid = $this->getPaymentStatus($field->Is_Paid);
            // $is_paid2 = ($field->Is_Paid == 0 || $field->Is_Paid == 2) ? "<a href='" . site_url('kasir/transaksi/inputBelanja/' . $field->ID_Transaksi) . "' class='btn btn-warning btn-icon'><i class='fa fa-pen'></i></a>" : "<a href='" . site_url('kasir/transaksi/cetakTransaksi/' . $field->ID_Transaksi) . "' class='btn btn-success btn-icon'><i class='fa fa-print'></i></a>";
            $is_paid2 = ($field->Is_Paid == 0 || $field->Is_Paid == 2)
                ? "<a href='#' class='btn btn-warning btn-icon' data-toggle='modal' data-target='#confirmModal' data-id='" . $field->ID_Transaksi . "'><i class='fa fa-pen'></i></a>"
                : "<a href='" . site_url('kasir/transaksi/cetakTransaksi/' . $field->ID_Transaksi) . "' class='btn btn-success btn-icon'><i class='fa fa-print'></i></a>";
            $no++;
            $row = array();

            $row[] = $no;
            $row[] = $field->Nomor_Invoice;
            $row[] = $is_paid;
            $row[] = $field->Created_Date;
            $row[] = $is_paid2;

            $data[] = $row;
        }
        $output = array(
            "draw"         => $_POST['draw'],
            "recordsTotal" => $this->tm->countDataTransaksi(),
            "recordsFiltered" => $this->tm->countDataTransaksi(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    private function getPaymentStatus($isPaid)
    {
        if ($isPaid == 2) {
            return '';
        } elseif ($isPaid == 1) {
            return '<p class="badge badge-success">Sudah Bayar</p>';
        } else {
            return '<p class="badge badge-danger">Belum Bayar</p>';
        }
    }

    public function deleteTransaction($id)
    {
        // return print_r($id);
        $this->db->where('ID_Transaksi', $id)->delete('tbl_penjualan');
        $this->db->where('ID_Transaksi', $id)->update('tbl_transaksi', ['Is_Deleted' => 1]);
        $this->session->set_flashdata('msg', 'Berhasil menghapus transaksi!');
        redirect('kasir/transaksi');
    }



    public function cariDetailBarang($barcode_id)
    {
        $query = $this->db->get_where('product', ['barcode_id' => $barcode_id])->row();
        echo json_encode($query);
    }

    public function cariDetailBarangBySearch($name = null)
    {
        $name = str_replace('%20', ' ', $name);
        $query = $this->db->like('product_name', $name)->get('product')->row();
        echo json_encode($query);
    }

    function auth()
    {
        $role = $this->session->userdata('role');
        if ($role == "2") {
            return true;
        } else {
            return false;
        }
    }
}
