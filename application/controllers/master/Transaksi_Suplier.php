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

   public function viewTransaksi_Suplier($transaksi_id = null)
   {
      $product_supliers = $this->sm->getData('tr_product_supliers', ['transaksi_id' => $transaksi_id])->result();
      $transaksi_supliers = $this->sm->getData('tr_new_supliers', ['id_transaksi' => $transaksi_id])->row();
      if ($this->auth() == false) {
         redirect('');
      }
      $data = [
         'title' => 'Master View Transaksi Suplier',
         'product' => $product_supliers,
         'transaksi' => $transaksi_supliers
      ];
      $this->template->load('template', 'master/transaksi_suplier/view', $data);
   }

   function cetakTransaksi($id_transaksi = null)
   {
      if ($this->auth() == false) {
         redirect('');
      }
      $data['q1'] = $this->db->get_where('tr_supliers', ['id' => $id_transaksi])->row();
      $this->load->view('master/transaksi_suplier/cetak', $data);
   }

   // public function tambahTransaksi_Suplier()
   // {
   //    if ($this->auth() == false) {
   //       redirect('');
   //    }
   //    $this->validationTransaksi_Suplier();
   //    $data_suplier = $this->sm->getDataAll('supliers')->result();
   //    $data_product = $this->sm->getDataAll('product_supliers')->result();
   //    if ($this->form_validation->run() == FALSE) {
   //       $data = [
   //          'title' => 'Menu Tambah Transaksi Suplier',
   //          'suplier' => $data_suplier,
   //          'product' => $data_product
   //       ];

   //       $this->template->load('template', 'master/Transaksi_suplier/add', $data);
   //    } else {
   //       $product_name           = trim(htmlspecialchars($this->input->post('product_name')));
   //       $n_suplier   = trim(htmlspecialchars($this->input->post('n_suplier')));
   //       $h_product        = trim(htmlspecialchars($this->input->post('h_product')));
   //       $qty           = trim(htmlspecialchars($this->input->post('qty')));
   //       $t_harga       = trim(htmlspecialchars($this->input->post('t_harga')));
   //       $bayar         = trim(htmlspecialchars($this->input->post('bayar')));
   //       $kembalian     = trim(htmlspecialchars($this->input->post('kembalian')));

   //       $dataInsert = [
   //          'id_transaksi'  => $this->generate_id_transaksi(),
   //          'n_barang'  => $product_name,
   //          'n_suplier'    => $n_suplier,
   //          'harga'    => $h_product,
   //          'qty'          => $qty,
   //          'total'      => $t_harga,
   //          'bayar'      => $bayar,
   //          'kembalian'    => $kembalian,
   //          'created_by'    => $this->userId
   //       ];

   //       $getProduct = $this->pm->getData('product', ['product_name' => $product_name])->row();
   //       $dataUpdate = [
   //          'stock' => $getProduct->stock + $qty
   //       ];

   //       $this->sm->insert('tr_supliers', $dataInsert);
   //       $this->pm->update('product', $dataUpdate, ['id' => $getProduct->id]);

   //       $this->session->set_flashdata('msg', 'Berhasil menambahkan Transaksi Suplier!');
   //       redirect('master/Transaksi_suplier');
   //    }
   // }

   public function tambahTransaksi_Suplier()
   {
      if ($this->auth() == false) {
         redirect('');
      }

      $this->validationTransaksi_Suplier();

      if ($this->form_validation->run() == FALSE) {
         $data = [
            'title' => 'Menu Tambah Transaksi Suplier',
            'suplier' => $this->sm->getDataAll('supliers')->result(),
         ];

         $this->template->load('template', 'master/Transaksi_suplier/add', $data);
      } else {
         $suplier_id  = trim(htmlspecialchars($this->input->post('suplier_id')));
         $t_harga     = trim(htmlspecialchars($this->input->post('t_harga')));
         $bayar       = trim(htmlspecialchars($this->input->post('bayar')));
         $kembalian   = trim(htmlspecialchars($this->input->post('kembalian')));

         $transaksi_id = $this->generate_id_transaksi();

         // Process each product from the table
         $product_data = $this->input->post('products');
         // return print_r($product_data);

         // Loop through the products array and decode JSON
         foreach ($product_data as $product_json) {
            $product = json_decode($product_json, true);
            $product_name = $product['product_name'];
            $satuan = $product['satuan'];
            $qty = $product['qty'];
            $harga = $product['harga'];
            $total = $product['total'];
            // return var_dump($product_data);

            if ($qty > 0) {
               $this->sm->insert('tr_product_supliers', [
                  'transaksi_id' => $transaksi_id,
                  'name_product' => $product_name,
                  'satuan'       => $satuan,
                  'qty'          => $qty,
                  'harga'        => $harga,
                  'total'        => $total
               ]);

               $getProduct = $this->pm->getData('product', ['product_name' => $product_name])->row();
               if ($getProduct) {
                  $this->pm->update('product', ['stock' => $getProduct->stock + $qty], ['id' => $getProduct->id]);
               }
            }
         }

         // Insert to `tr_new_supliers`
         $this->sm->insert('tr_new_supliers', [
            'id_transaksi' => $transaksi_id,
            'suplier_id'   => $suplier_id,
            'total'        => $t_harga,
            'bayar'        => $bayar,
            'kembalian'    => $kembalian
         ]);

         $this->session->set_flashdata('msg', 'Transaksi berhasil ditambahkan!');
         redirect('master/transaksi_suplier');
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
         $row[] = $field->created_at;
         $row[] = $field->nama_supplier;
         $row[] = $field->id_transaksi;
         $row[] = $field->total;
         $row[] = "<a href='" . site_url('master/transaksi_suplier/viewTransaksi_Suplier/' . $field->id_transaksi) . "' class='btn btn-primary btn-icon'><i class='fa fa-eye'></i></a> <a href='" . site_url('master/transaksi_suplier/cetakTransaksi/' . $field->id_transaksi) . "' class='btn btn-success btn-icon'><i class='fa fa-print'></i></a>";

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
      $product = $this->sm->getData('product_supliers', ['product_name' => $product_name])->row();

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

   public function getProductSupplierDetail()
   {
      $suplier_id = trim(htmlspecialchars($this->input->post('suplier_id')));
      $result = $this->sm->getData('product_supliers', ['suplier_id' => $suplier_id])->result();

      if ($result) {
         echo json_encode(['status' => 'success', 'data' => $result]);
      } else {
         echo json_encode(['status' => 'error', 'message' => 'Produk tidak ditemukan untuk supplier ini']);
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
      $this->form_validation->set_rules('suplier_id', 'Nama Suplier', 'trim|required', [
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
