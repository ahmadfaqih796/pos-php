<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Transaksi_Suplier extends MY_Controller
{
   function __construct()
   {
      parent::__construct();
      $this->load->helper(array('form', 'url', 'html', 'file'));
      $this->load->library(array('template', 'form_validation', 'unit_test'));
      $this->load->model('master/Transaksi_Suplier_model', 'sm');
      $this->load->model('master/Produk_model', 'pm');
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
         'title' => 'Master Transaksi Suplier'
      ];
      $this->template->load('template', 'master/transaksi_suplier/index', $data);
   }

   function cetakTransaksi($id)
   {
      if ($this->auth() == false) {
         redirect('');
      }
      $data['q1'] = $this->db->get_where('tr_supliers', ['id' => $id])->row();
      $this->load->view('master/transaksi_suplier/cetak', $data);
   }

   public function tambahTransaksi_Suplier()
   {
      if ($this->auth() == false) {
         redirect('');
      }
      $this->validationTransaksi_Suplier();
      $data_suplier = $this->sm->getDataAll('supliers')->result();
      $data_product = $this->sm->getDataAll('product')->result();
      if ($this->form_validation->run() == FALSE) {
         $data = [
            'title' => 'Menu Tambah Transaksi Suplier',
            'suplier' => $data_suplier,
            'product' => $data_product
         ];

         $this->template->load('template', 'master/Transaksi_suplier/add', $data);
      } else {
         $product_name           = trim(htmlspecialchars($this->input->post('jenis_produk')));
         $n_suplier   = trim(htmlspecialchars($this->input->post('n_suplier')));
         $h_product        = trim(htmlspecialchars($this->input->post('h_product')));
         $qty           = trim(htmlspecialchars($this->input->post('qty')));
         $t_harga       = trim(htmlspecialchars($this->input->post('t_harga')));
         $bayar         = trim(htmlspecialchars($this->input->post('bayar')));
         $kembalian     = trim(htmlspecialchars($this->input->post('kembalian')));

         $dataInsert = [
            'id_transaksi'  => $this->generate_id_transaksi(),
            'n_barang'  => $product_name,
            'n_suplier'    => $n_suplier,
            'harga'    => $h_product,
            'qty'          => $qty,
            'total'      => $t_harga,
            'bayar'      => $bayar,
            'kembalian'    => $kembalian,
            'created_by'    => $this->userId
         ];

         $getProduct = $this->pm->getData('product', ['product_name' => $product_name])->row();
         $dataUpdate = [
            'stock' => $getProduct->stock + $qty
         ];

         $this->sm->insert('tr_supliers', $dataInsert);
         $this->pm->update('product', $dataUpdate, ['id' => $getProduct->id]);

         $this->session->set_flashdata('msg', 'Berhasil menambahkan Transaksi Suplier!');
         redirect('master/Transaksi_suplier');
      }
   }

   private function generate_id_transaksi()
   {
      $now = new DateTime();
      $dateString = $now->format('ymdHis'); // 240707180634
      $last_id = $this->sm->get_last_id(); // Mendapatkan ID terakhir dari database

      $new_id = $last_id ? intval($last_id) + 1 : 1; // Menentukan ID baru

      return 'TS' . $dateString; // Menggabungkan semua komponen menjadi satu ID
   }

   public function editTransaksi_Suplier($id)
   {
      if ($this->auth() == false) {
         redirect('');
      }
      $this->validationTransaksi_Suplier();
      if ($this->form_validation->run() == FALSE) {
         $data = [
            'title'        => 'Menu Edit Transaksi Suplier',
            'id'           => $id,
            'rowTransaksi_Suplier'   => $this->sm->getData('Transaksi_supliers', ['id' => $id])->row()
         ];

         $this->template->load('template', 'master/Transaksi_suplier/edit', $data);
      } else {
         if ($this->duplicate_entry() == 0) {
            $name     = trim(htmlspecialchars($this->input->post('name')));
            $id_transaksi   = trim(htmlspecialchars($this->input->post('id_transaksi')));
            $n_suplier   = trim(htmlspecialchars($this->input->post('n_suplier')));
            $harga  = trim(htmlspecialchars($this->input->post('harga')));
            $city     = trim(htmlspecialchars($this->input->post('city')));
            $province = trim(htmlspecialchars($this->input->post('province')));
            $n_barang = trim(htmlspecialchars($this->input->post('n_barang')));

            $dataUpdate = [
               'name'          => $name,
               'id_transaksi'  => $id_transaksi,
               'n_suplier' => $n_suplier,
               'harga'       => $harga,
               'city'          => $city,
               'province'      => $province,
               'n_barang'      => $n_barang,
               'updated_by'    => $this->userId,
               'updated_at'    => $this->dateNow
            ];

            $this->sm->update('Transaksi_supliers', $dataUpdate, ['id' => $id]);

            $this->session->set_flashdata('msg', 'Berhasil update Transaksi_suplier!');
            redirect('master/Transaksi_suplier');
         } else {
            $this->session->set_flashdata('msg', 'Nama Transaksi_suplier sudah terdaftar di sistem! Tidak boleh sama!');
            redirect('master/Transaksi_suplier/editTransaksi_Suplier/' . $id);
         }
      }
   }

   public function hapusTransaksi_suplier($id)
   {
      if ($this->auth() == false) {
         redirect('');
      }
      $dataUpdate = [
         'is_deleted'    => 1,
         'deleted_by'    => $this->userId,
         'deleted_at'    => $this->dateNow
      ];

      $this->sm->update('tr_supliers', $dataUpdate, ['id' => $id]);
      $this->session->set_flashdata('msg', 'Berhasil menghapus Transaksi_suplier!');
      redirect('master/Transaksi_suplier');
   }


   function getDataTransaksiSuplier()
   {
      if ($this->auth() == false) {
         redirect('');
      }
      $list = $this->sm->dataTransaksiSuplier();
      $data = array();
      $no   = $_POST['start'];

      foreach ($list as $field) {
         $no++;
         $row = array();
         $row[] = $no;
         $row[] = $field->n_suplier;
         $row[] = $field->id_transaksi;
         $row[] = $field->n_barang;
         $row[] = $field->harga;
         $row[] = $field->qty;
         $row[] = $field->total;
         $row[] = "<a href='" . site_url('master/transaksi_suplier/hapusTransaksi_suplier/' . $field->id) . "' onclick='return confirm(`Yakin ingin hapus Transaksi_suplier?`)' class='btn btn-danger btn-icon'><i class='fa fa-trash'></i></a> <a href='" . site_url('master/transaksi_suplier/cetakTransaksi/' . $field->id) . "' class='btn btn-success btn-icon'><i class='fa fa-print'></i></a>";

         $data[] = $row;
      }
      $output = array(
         "draw"         => $_POST['draw'],
         "recordsTotal" => $this->sm->countTransaksiSuplier(),
         "recordsFiltered" => $this->sm->countTransaksiSuplier(),
         "data" => $data,
      );
      echo json_encode($output);
   }

   public function getProductDetail()
   {
      $product_name = trim(htmlspecialchars($this->input->post('product_name')));
      $product = $this->sm->getData('product', ['product_name' => $product_name])->row();

      if ($product) {
         echo json_encode(['status' => 'success', 'data' => $product]);
      } else {
         echo json_encode(['status' => 'error']);
      }
   }

   public function getSupplierDetail()
   {
      $n_suplier = trim(htmlspecialchars($this->input->post('n_suplier')));
      $result = $this->sm->getData('supliers', ['name' => $n_suplier])->row();

      if ($result) {
         echo json_encode(['status' => 'success', 'data' => $result]);
      } else {
         echo json_encode(['status' => 'error']);
      }
   }


   private function duplicate_entry()
   {
      $id             = $this->input->post('id');
      $nama           = trim(htmlspecialchars($this->input->post('name')));
      $where          = "(name = '$nama' AND is_deleted = 0 AND id != '$id')";
      $query          = $this->sm->getData('tr_supliers', $where);

      if ($query->num_rows() > 0) {
         return '1';
      } else {
         return '0';
      }
   }

   private function validationTransaksi_Suplier()
   {
      $this->form_validation->set_rules('n_suplier', 'Nama Suplier', 'trim|required', [
         'required' => 'Nama Suplier wajib diisi!'
      ]);
   }

   function auth()
   {
      $role = $this->session->userdata('role');
      if ($role == "1") {
         return true;
      } else {
         return false;
      }
   }
}
