<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Produk_Supplier extends MY_Controller
{
   function __construct()
   {
      parent::__construct();
      $this->load->helper(array('form', 'url', 'html', 'file'));
      $this->load->library(array('template', 'form_validation', 'unit_test', 'Ciqrcode'));
      $this->load->model('master/Produk_Supplier_model', 'ps');
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
         'title' => 'Master Produk Supplier'
      ];
      $this->template->load('template', 'master/produk_supplier/index', $data);
   }

   public function tambahProduk()
   {
      if ($this->auth() == false) {
         redirect('');
      }
      $this->validationProduk();
      if ($this->form_validation->run() == FALSE) {
         $data_suplier = $this->ps->getDataAll('supliers')->result();
         $data_product = $this->ps->getDataAll('product')->result();

         $data = [
            'title' => 'Menu Tambah Produk Supplier',
            'suplier' => $data_suplier,
            'product' => $data_product
         ];

         $this->template->load('template', 'master/produk_supplier/add', $data);
      } else {
         if ($this->duplicate_entry() == 0) {
            $suplier_id     = trim(htmlspecialchars($this->input->post('suplier_id')));
            $product_name     = trim(htmlspecialchars($this->input->post('product_name')));
            $hargaProduk    = trim(htmlspecialchars($this->input->post('harga')));
            $satuanProduk     = trim(htmlspecialchars($this->input->post('satuan')));

            $dataInsert = [
               'product_name'      => $product_name,
               'suplier_id'      => $suplier_id,
               'harga'           => $hargaProduk,
               'satuan'          => $satuanProduk,
            ];

            $this->ps->insert('product_supliers', $dataInsert);

            $this->session->set_flashdata('msg', 'Berhasil menambahkan produk supplier!');
            redirect('master/produk_supplier');
         } else {
            $this->session->set_flashdata('msg', 'Nama produk sudah terdaftar di sistem! Tidak boleh sama!');
            redirect('master/produk_supplier/tambahProduk');
         }
      }
   }

   public function editProduk($id)
   {
      if ($this->auth() == false) {
         redirect('');
      }
      $this->validationProduk();
      if ($this->form_validation->run() == FALSE) {

         $data_suplier = $this->ps->getDataAll('supliers')->result();
         $data_product = $this->ps->getDataAll('product')->result();

         $data = [
            'title'        => 'Menu Edit Produk Supplier',
            'id_table'           => $id,
            'rowProdukSupplier'   => $this->ps->getData($id)->row(),
            'suplier' => $data_suplier,
            'product' => $data_product
         ];

         $this->template->load('template', 'master/produk_supplier/edit', $data);
      } else {
         if ($this->duplicate_entry() == 0) {
            $suplier_id     = trim(htmlspecialchars($this->input->post('suplier_id')));
            $product_name     = trim(htmlspecialchars($this->input->post('product_name')));
            $hargaProduk    = trim(htmlspecialchars($this->input->post('harga')));
            $satuanProduk     = trim(htmlspecialchars($this->input->post('satuan')));


            $dataUpdate = [
               'product_name'      => $product_name,
               'suplier_id'      => $suplier_id,
               'harga'           => $hargaProduk,
               'satuan'          => $satuanProduk,
            ];

            $this->ps->update('product_supliers', $dataUpdate, ['id' => $id]);

            $this->session->set_flashdata('msg', 'Berhasil update produk!');
            redirect('master/produk_supplier');
         } else {
            $this->session->set_flashdata('msg', 'Nama produk sudah terdaftar di sistem! Tidak boleh sama!');
            redirect('master/produk_supplier/editProduk/' . $id);
         }
      }
   }

   public function hapusProduk($id)
   {
      if ($this->auth() == false) {
         redirect('');
      }
      $dataUpdate = [
         'is_deleted'    => 1,
      ];

      $this->ps->update('product_supliers', $dataUpdate, ['id' => $id]);
      $this->session->set_flashdata('msg', 'Berhasil menghapus produk supplier!');
      redirect('master/produk_supplier');
   }


   function getDataProduk()
   {
      if ($this->auth() == false) {
         redirect('');
      }
      $list = $this->ps->dataProduk();
      $data = array();
      $no   = $_POST['start'];
      // $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
      foreach ($list as $field) {
         $no++;
         $row = array();
         $row[] = $no;
         $row[] = $field->nama_supplier;
         $row[] = $field->product_name;
         $row[] = $field->satuan;
         $row[] = "Rp. " . number_format($field->harga, 0, '.', '.');
         $row[] = "<a href='" . site_url('master/produk_supplier/hapusProduk/' . $field->id) . "' onclick='return confirm(`Yakin ingin hapus produk supplier?`)' class='btn btn-danger btn-icon'><i class='fa fa-trash'></i></a> <a href='" . site_url('master/produk_supplier/editProduk/' . $field->id) . "' class='btn btn-warning btn-icon'><i class='fa fa-pen'></i></a>";

         $data[] = $row;
      }
      $output = array(
         "draw"         => $_POST['draw'],
         "recordsTotal" => $this->ps->countDataProduk(),
         "recordsFiltered" => $this->ps->countDataProduk(),
         "data" => $data,
      );
      echo json_encode($output);
   }

   private function duplicate_entry()
   {
      if ($this->auth() == false) {
         redirect('');
      }
      $id             = $this->input->post('id');
      $namaProduk     = trim(htmlspecialchars($this->input->post('product_name')));
      $where          = "(product_name = '$namaProduk' AND is_deleted = 0 AND id != '$id')";
      $query          = $this->ps->getData('product', $where);

      if ($query->num_rows() > 0) {
         return '1';
      } else {
         return '0';
      }
   }

   private function validationProduk()
   {

      $this->form_validation->set_rules('suplier_id', 'Nama Supplier', 'trim|required', [
         'required' => 'Nama Supplier wajib diisi!'
      ]);
      $this->form_validation->set_rules('product_name', 'Nama Produk', 'trim|required', [
         'required' => 'Nama Produk wajib diisi!'
      ]);
      $this->form_validation->set_rules('harga', 'Harga', 'trim|required|numeric', [
         'required' => 'Harga wajib diisi!',
         'numeric'  => 'Inputan wajib angka'
      ]);
   }

   function generate_random_string($length = 8)
   {
      // Karakter yang digunakan untuk menghasilkan string acak
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

      // Panjang karakter
      $characters_length = strlen($characters);

      // Variabel untuk menyimpan string acak
      $random_string = '';

      // Loop untuk menghasilkan string acak
      for ($i = 0; $i < $length; $i++) {
         // Pilih karakter acak dari daftar karakter
         $random_character = $characters[rand(0, $characters_length - 1)];

         // Tambahkan karakter acak ke string
         $random_string .= $random_character;
      }

      // Kembalikan string acak
      return $random_string;
   }

   function generateQr($string)
   {
      QRCode::png(
         $string,
         $outfile = false,
         $level = QR_ECLEVEL_H,
         $size = 5,
         $margin = 2
      );
   }

   function auth()
   {
      $role = $this->session->userdata('role');
      if (in_array($role, [1, 2])) {
         return true;
      } else {
         return false;
      }
   }
}
