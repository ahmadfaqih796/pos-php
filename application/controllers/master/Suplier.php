<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Suplier extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url', 'html', 'file'));
        $this->load->library(array('template', 'form_validation', 'unit_test'));
        $this->load->model('master/Suplier_model', 'sm');
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
            'title' => 'Master Suplier'
        ];
        $this->template->load('template', 'master/suplier/index', $data);
    }

    public function tambahSuplier()
    {
        if ($this->auth() == false) {
            redirect('');
        }
        $this->validationSuplier();
        if ($this->form_validation->run() == FALSE) {
            $data = [
                'title' => 'Menu Tambah Suplier'
            ];

            $this->template->load('template', 'master/suplier/add', $data);
        } else {
            if ($this->duplicate_entry() == 0) {
                $name           = trim(htmlspecialchars($this->input->post('name')));
                $name_company   = trim(htmlspecialchars($this->input->post('name_company')));
                $jenis_supplier   = trim(htmlspecialchars($this->input->post('jenis_supplier')));
                $harga_supplier   = trim(htmlspecialchars($this->input->post('harga_supplier')));
                $address        = trim(htmlspecialchars($this->input->post('address')));
                $city           = trim(htmlspecialchars($this->input->post('city')));
                $province       = trim(htmlspecialchars($this->input->post('province')));
                $phone_number   = trim(htmlspecialchars($this->input->post('phone_number')));

                $dataInsert = [
                    'name'          => $name,
                    'name_company'  => $name_company,
                    'jenis_supplier' => $jenis_supplier,
                    'harga_supplier' => $harga_supplier,
                    'address'       => $address,
                    'city'          => $city,
                    'province'      => $province,
                    'phone_number'  => $phone_number,
                    'created_by'    => $this->userId
                ];

                $this->sm->insert('supliers', $dataInsert);

                $this->session->set_flashdata('msg', 'Berhasil menambahkan suplier!');
                redirect('master/suplier');
            } else {
                $this->session->set_flashdata('msg', 'Nama suplier sudah terdaftar di sistem! Tidak boleh sama!');
                redirect('master/suplier/tambahSuplier');
            }
        }
    }

    public function editSuplier($id)
    {
        if ($this->auth() == false) {
            redirect('');
        }
        $this->validationSuplier();
        if ($this->form_validation->run() == FALSE) {
            $data = [
                'title'        => 'Menu Edit Suplier',
                'id'           => $id,
                'rowSuplier'   => $this->sm->getData('supliers', ['id' => $id])->row()
            ];

            $this->template->load('template', 'master/suplier/edit', $data);
        } else {
            if ($this->duplicate_entry() == 0) {
                $name     = trim(htmlspecialchars($this->input->post('name')));
                $name_company   = trim(htmlspecialchars($this->input->post('name_company')));
                $jenis_supplier   = trim(htmlspecialchars($this->input->post('jenis_supplier')));
                $harga_supplier   = trim(htmlspecialchars($this->input->post('harga_supplier')));
                $address  = trim(htmlspecialchars($this->input->post('address')));
                $city     = trim(htmlspecialchars($this->input->post('city')));
                $province = trim(htmlspecialchars($this->input->post('province')));
                $phone_number = trim(htmlspecialchars($this->input->post('phone_number')));

                $dataUpdate = [
                    'name'          => $name,
                    'name_company'  => $name_company,
                    'jenis_supplier' => $jenis_supplier,
                    'harga_supplier' => $harga_supplier,
                    'address'       => $address,
                    'city'          => $city,
                    'province'      => $province,
                    'phone_number'      => $phone_number,
                    'updated_by'    => $this->userId,
                    'updated_at'    => $this->dateNow
                ];

                $this->sm->update('supliers', $dataUpdate, ['id' => $id]);

                $this->session->set_flashdata('msg', 'Berhasil update suplier!');
                redirect('master/suplier');
            } else {
                $this->session->set_flashdata('msg', 'Nama suplier sudah terdaftar di sistem! Tidak boleh sama!');
                redirect('master/suplier/editSuplier/' . $id);
            }
        }
    }

    public function hapussuplier($id)
    {
        if ($this->auth() == false) {
            redirect('');
        }
        $dataUpdate = [
            'is_deleted'    => 1,
            'deleted_by'    => $this->userId,
            'deleted_at'    => $this->dateNow
        ];

        $this->sm->update('supliers', $dataUpdate, ['id' => $id]);
        $this->session->set_flashdata('msg', 'Berhasil menghapus suplier!');
        redirect('master/suplier');
    }


    function getDataSuplier()
    {
        if ($this->auth() == false) {
            redirect('');
        }
        $list = $this->sm->dataSuplier();
        $data = array();
        $no   = $_POST['start'];
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->name_company;
            $row[] = $field->name;
            // $row[] = $field->jenis_supplier;
            // $row[] = $field->harga_supplier;
            $row[] = $field->phone_number;
            $row[] = $field->address;
            // $row[] = $field->city;
            // $row[] = $field->province;
            $row[] = "<a href='" . site_url('master/suplier/hapusSuplier/' . $field->id) . "' onclick='return confirm(`Yakin ingin hapus suplier?`)' class='btn btn-danger btn-icon'><i class='fa fa-trash'></i></a> <a href='" . site_url('master/suplier/editSuplier/' . $field->id) . "' class='btn btn-warning btn-icon'><i class='fa fa-pen'></i></a>";

            $data[] = $row;
        }
        $output = array(
            "draw"         => $_POST['draw'],
            "recordsTotal" => $this->sm->countDataSuplier(),
            "recordsFiltered" => $this->sm->countDataSuplier(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    private function duplicate_entry()
    {
        $id             = $this->input->post('id');
        $nama           = trim(htmlspecialchars($this->input->post('name')));
        $where          = "(name = '$nama' AND is_deleted = 0 AND id != '$id')";
        $query          = $this->sm->getData('supliers', $where);

        if ($query->num_rows() > 0) {
            return '1';
        } else {
            return '0';
        }
    }

    private function validationSuplier()
    {
        $this->form_validation->set_rules('name', 'Nama', 'trim|required', [
            'required' => 'Nama wajib diisi!'
        ]);
        $this->form_validation->set_rules('name_company', 'Nama Perusahaan', 'trim|required', [
            'required' => 'Nama Perusahaan wajib diisi!'
        ]);
        $this->form_validation->set_rules('jenis_supplier', 'Jenis Supplier', 'trim|required', [
            'required' => 'Jenis Supplier wajib diisi!'
        ]);
        $this->form_validation->set_rules('address', 'Alamat', 'trim|required', [
            'required' => 'Alamat wajib diisi!'
        ]);
        // $this->form_validation->set_rules('city', 'Kota', 'trim|required', [
        //     'required' => 'Kota wajib diisi!'
        // ]);
        // $this->form_validation->set_rules('province', 'Provinsi', 'trim|required', [
        //     'required' => 'Provinsi wajib diisi!'
        // ]);
        $this->form_validation->set_rules('phone_number', 'Nomor Telepon', 'trim|required', [
            'required' => 'Nomor Telepon wajib diisi!'
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
