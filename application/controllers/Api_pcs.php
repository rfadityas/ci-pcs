<?php
defined('BASEPATH') or exit('No direct script access allowed');

//Lib

require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

//JWT

require APPPATH . '/libraries/Firebase/JWT/JWT.php';

use \Firebase\JWT\JWT;


//Class

class Api_pcs extends REST_Controller
{

    private $secret_key = "asdhjasndaj86763ih8hsa";

    function __construct()
    {

        parent::__construct();
        $this->load->model('M_admin');
        $this->load->model('M_produk');
        $this->load->model('M_transaksi');
        $this->load->model('M_item_transaksi');
    }

    //Adm Get

    public function admin_get()
    {

        $this->cekToken();

        $result = $this->M_admin->getAdmin();
        echo "admin";

        $data_json = array(
            "success" => true,
            "message" => "Data Found",
            "data" => array(
                "admin" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    //Adm Post

    public function admin_post()
    {

        $this->cekToken();

        //Validasi
        $validation_message = [];

        if ($this->input->post("email") == "") {
            array_push($validation_message, "Email Tidak Boleh Kosong !!!");
        }

        if ($this->input->post("email") != "" && !filter_var($this->input->post("email"), FILTER_VALIDATE_EMAIL)) {
            array_push($validation_message, "Format Email Tidak Valid !!!");
        }

        if ($this->input->post("password") == "") {
            array_push($validation_message, "Password Tidak Boleh Kosong !!!");
        }

        if ($this->input->post("nama") == "") {
            array_push($validation_message, "Nama Tidak Boleh Kosong !!!");
        }

        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data Tidak Valid",
                "data" => $validation_message

            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        //Lolos Validasi

        $data = array(
            "email" => $this->input->post("email"),
            "password" => md5($this->input->post("password")),
            "nama" => $this->input->post("nama")
        );

        $result = $this->M_admin->insertAdmin($data);

        $data_json = array(
            "success" => true,
            "message" => "Insert Berhasil",
            "data" => array(
                "admin" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    //Adm Put

    public function admin_put()
    {

        $this->cekToken();

        //Validasi
        $validation_message = [];

        if ($this->put("email") == "") {
            array_push($validation_message, "Email Tidak Boleh Kosong !!!");
        }

        if ($this->put("email") != "" && !filter_var($this->put("email"), FILTER_VALIDATE_EMAIL)) {
            array_push($validation_message, "Format Email Tidak Valid !!!");
        }

        if ($this->put("password") == "") {
            array_push($validation_message, "Password Tidak Boleh Kosong !!!");
        }

        if ($this->put("nama") == "") {
            array_push($validation_message, "Nama Tidak Boleh Kosong !!!");
        }

        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data Tidak Valid",
                "data" => $validation_message

            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        //Lolos Validasi

        $data = array(
            "email" => $this->put("email"),
            "password" => md5($this->put("password")),
            "nama" => $this->put("nama")
        );

        $id = $this->put("id");

        $result = $this->M_admin->updateAdmin($data, $id);

        $data_json = array(
            "success" => true,
            "message" => "Update Berhasil",
            "data" => array(
                "admin" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    //Adm Del

    public function admin_delete()
    {

        $this->cekToken();

        $id = $this->delete("id");

        $result = $this->M_admin->deleteAdmin($id);

        if (empty($result)) {
            $data_json = array(
                "success" => false,
                "message" => "Data Tidak Valid",
                "data" => null

            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        $data_json = array(
            "success" => true,
            "message" => "Delete Berhasil",
            "data" => array(
                "admin" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    //Login Post

    public function login_post()
    {
        $data = array(
            "email" => $this->input->post("email"),
            "password" => md5($this->input->post("password"))
        );

        $result = $this->M_admin->cekLoginAdmin($data);

        if (empty($result)) {

            $data_json = array(
                "success" => false,
                "message" => "Email dan Password Tidak Valid",
                "error_code" => 1308,
                "data" => null

            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        } else {
            $date = new Datetime();

            $payload["id"] = $result["id"];
            $payload["email"] = $result["email"];
            $payload["iat"] = $date->getTimestamp();
            $payload["exp"] = $date->getTimestamp() + 3600;

            $data_json = array(
                "success" => true,
                "message" => "Autentikasi Berhasil",
                "data" => array(
                    "admin" => $result,
                    "token" => JWT::encode($payload, $this->secret_key)
                )

            );

            $this->response($data_json, REST_Controller::HTTP_OK);
        }
    }

    //Prod Start

    public function produk_get()
    {

        $this->cekToken();

        $result = $this->M_produk->getProduk();
        echo "Produk";

        $data_json = array(
            "success" => true,
            "message" => "Data Found",
            "data" => array(
                "produk" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    //Prod Post

    public function produk_post()
    {

        $this->cekToken();

        //Validasi
        $validation_message = [];

        if ($this->input->post("admin_id") == "") {
            array_push($validation_message, "Admin ID Tidak Boleh Kosong !!!");
        }

        if ($this->input->post("admin_id") != "" && !$this->M_admin->cekAdminExist($this->input->post("admin_id"))) {
            array_push($validation_message, "Admin ID Tidak Ditemukan !!!");
        }

        if ($this->input->post("nama") == "") {
            array_push($validation_message, "Nama Tidak Valid !!!");
        }

        if ($this->input->post("harga") == "") {
            array_push($validation_message, "Harga Tidak Boleh Kosong !!!");
        }

        if ($this->input->post("harga") != "" && !is_numeric($this->input->post("harga"))) {
            array_push($validation_message, "Harga Harus Angka !!!");
        }

        if ($this->input->post("stok") == "") {
            array_push($validation_message, "Stok Tidak Boleh Kosong !!!");
        }

        if ($this->input->post("stok") != "" && !is_numeric($this->input->post("stok"))) {
            array_push($validation_message, "Stok Harus Angka !!!");
        }

        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data Tidak Valid",
                "data" => $validation_message

            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        //Lolos Valid

        $data = array(
            "admin_id" => $this->input->post("admin_id"),
            "nama" => $this->input->post("nama"),
            "harga" => $this->input->post("harga"),
            "stok" => $this->input->post("stok")
        );

        $result = $this->M_produk->insertProduk($data);

        $data_json = array(
            "success" => true,
            "message" => "Insert Berhasil",
            "data" => array(
                "produk" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    //Prod Put

    public function produk_put()
    {

        $this->cekToken();

        //Validasi
        $validation_message = [];

        if ($this->put("id") == "") {
            array_push($validation_message, "ID Tidak Boleh Kosong !!!");
        }

        if ($this->put("admin_id") == "") {
            array_push($validation_message, "Admin ID Tidak Boleh Kosong !!!");
        }

        if ($this->put("admin_id") != "" && !$this->M_admin->cekAdminExist($this->put("admin_id"))) {
            array_push($validation_message, "Admin ID Tidak Ditemukan !!!");
        }

        if ($this->put("nama") == "") {
            array_push($validation_message, "Nama Tidak Valid !!!");
        }

        if ($this->put("harga") == "") {
            array_push($validation_message, "Harga Tidak Boleh Kosong !!!");
        }

        if ($this->put("harga") != "" && !is_numeric($this->put("harga"))) {
            array_push($validation_message, "Harga Harus Angka !!!");
        }

        if ($this->put("stok") == "") {
            array_push($validation_message, "Stok Tidak Boleh Kosong !!!");
        }

        if ($this->put("stok") != "" && !is_numeric($this->put("stok"))) {
            array_push($validation_message, "Stok Harus Angka !!!");
        }

        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data Tidak Valid",
                "data" => $validation_message

            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        //Lolos Validasi

        $data = array(
            "admin_id" => $this->put("admin_id"),
            "nama" => $this->put("nama"),
            "harga" => $this->put("harga"),
            "stok" => $this->put("stok")
        );

        $id = $this->put("id");

        $result = $this->M_produk->updateProduk($data, $id);

        $data_json = array(
            "success" => true,
            "message" => "Update Berhasil",
            "data" => array(
                "produk" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    //Prod Del

    public function produk_delete()
    {

        $this->cekToken();

        $id = $this->delete("id");

        $result = $this->M_produk->deleteProduk($id);

        if (empty($result)) {
            $data_json = array(
                "success" => false,
                "message" => "Data Tidak Valid",
                "data" => null

            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        $data_json = array(
            "success" => true,
            "message" => "Delete Berhasil",
            "data" => array(
                "produk" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    // Prod End



    //Trx Start

    public function transaksi_get()
    {

        $this->cekToken();

        $result = $this->M_transaksi->getTransaksi();

        $data_json = array(
            "success" => true,
            "message" => "Data Found",
            "data" => array(
                "transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function transaksi_bulan_ini_get()
    {

        $this->cekToken();

        $result = $this->M_transaksi->getTransaksiBulanIni();

        $data_json = array(
            "success" => true,
            "message" => "Data Found",
            "data" => array(
                "transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    //TrxPost
    public function transaksi_post()
    {

        $this->cekToken();

        //Validasi
        $validation_message = [];

        if ($this->input->post("admin_id") == "") {
            array_push($validation_message, "Admin ID Tidak Boleh Kosong !!!");
        }

        if ($this->input->post("admin_id") != "" && !$this->M_admin->cekAdminExist($this->input->post("admin_id"))) {
            array_push($validation_message, "Admin ID Tidak Ditemukan !!!");
        }

        if ($this->input->post("total") == "") {
            array_push($validation_message, "Total Tidak Boleh Kosong !!!");
        }

        if ($this->input->post("total") != "" && !is_numeric($this->input->post("total"))) {
            array_push($validation_message, "Total Harus Angka !!!");
        }

        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data Tidak Valid",
                "data" => $validation_message

            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        //Lolos Validasi

        $data = array(
            "admin_id" => $this->input->post("admin_id"),
            "total" => $this->input->post("total"),
            'tanggal' => date("Y-m-d H:i:s")
        );

        $result = $this->M_transaksi->insertTransaksi($data);

        $data_json = array(
            "success" => true,
            "message" => "Insert Berhasil",
            "data" => array(
                "transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    //-Trx Put

    public function transaksi_put()
    {

        $this->cekToken();

        //Validasi
        $validation_message = [];

        if ($this->put("id") == "") {
            array_push($validation_message, "ID Tidak Boleh Kosong !!!");
        }

        if ($this->put("admin_id") == "") {
            array_push($validation_message, "Admin ID Tidak Boleh Kosong !!!");
        }

        if ($this->put("admin_id") != "" && !$this->M_admin->cekAdminExist($this->put("admin_id"))) {
            array_push($validation_message, "Admin ID Tidak Ditemukan !!!");
        }

        if ($this->put("total") == "") {
            array_push($validation_message, "Total Tidak Boleh Kosong !!!");
        }

        if ($this->put("total") != "" && !is_numeric($this->put("total"))) {
            array_push($validation_message, "Total Harus Angka !!!");
        }

        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data Tidak Valid",
                "data" => $validation_message

            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        //Lolos Validasi

        $data = array(
            "admin_id" => $this->put("admin_id"),
            "total" => $this->put("total"),
            'tanggal' => date("Y-m-d H:i:s")
        );

        $id = $this->put("id");

        $result = $this->M_transaksi->updateTransaksi($data, $id);

        $data_json = array(
            "success" => true,
            "message" => "Update Berhasil",
            "data" => array(
                "transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    //TrxDel

    public function transaksi_delete()
    {

        $this->cekToken();

        $id = $this->delete("id");

        $result = $this->M_transaksi->deleteTransaksi($id);

        if (empty($result)) {
            $data_json = array(
                "success" => false,
                "message" => "Data Tidak Valid",
                "data" => null

            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        $data_json = array(
            "success" => true,
            "message" => "Delete Berhasil",
            "data" => array(
                "transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    // Trx End



    //Item Trx Start

    public function item_transaksi_get()
    {

        $this->cekToken();

        $result = $this->M_item_transaksi->getItemTransaksi();

        $data_json = array(
            "success" => true,
            "message" => "Data Found",
            "data" => array(
                "item_transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function item_transaksi_by_transaksi_id_get()
    {

        $this->cekToken();

        $result = $this->M_item_transaksi->getItemTransaksiByTransaksiID($this->input->get('transaksi_id'));

        $data_json = array(
            "success" => true,
            "message" => "Data Found",
            "data" => array(
                "item_transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    //Item Trx Post

    public function item_transaksi_post()
    {

        $this->cekToken();

        //Validasi
        $validation_message = [];

        if ($this->input->post("transaksi_id") == "") {
            array_push($validation_message, "Transaksi ID Tidak Boleh Kosong !!!");
        }

        if ($this->input->post("transaksi_id") != "" && !$this->M_transaksi->cekTransaksiExist($this->input->post("transaksi_id"))) {
            array_push($validation_message, "Transaksi ID Tidak Ditemukan !!!");
        }

        if ($this->input->post("produk_id") == "") {
            array_push($validation_message, "Produk Tidak Boleh Kosong !!!");
        }

        if ($this->input->post("produk_id") != "" && !$this->M_produk->cekProdukExist($this->input->post("produk_id"))) {
            array_push($validation_message, "Produk ID Tidak Ditemukan !!!");
        }

        if ($this->input->post("qty") == "") {
            array_push($validation_message, "Qty Tidak Boleh Kosong !!!");
        }

        if ($this->input->post("qty") != "" && !is_numeric($this->input->post("qty"))) {
            array_push($validation_message, "Qty Harus Angka !!!");
        }

        if ($this->input->post("harga_saat_transaksi") == "") {
            array_push($validation_message, "Harga Tidak Boleh Kosong !!!");
        }

        if ($this->input->post("harga_saat_transaksi") != "" && !is_numeric($this->input->post("harga_saat_transaksi"))) {
            array_push($validation_message, "Harga Harus Angka !!!");
        }

        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data Tidak Valid",
                "data" => $validation_message

            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        // Lolos Validasi

        $data = array(
            "transaksi_id" => $this->input->post("transaksi_id"),
            "produk_id" => $this->input->post("produk_id"),
            "qty" => $this->input->post("qty"),
            "harga_saat_transaksi" => $this->input->post("harga_saat_transaksi"),
            "sub_total" => $this->input->post("qty") * $this->input->post("harga_saat_transaksi")
        );

        $result = $this->M_item_transaksi->insertItemTransaksi($data);

        $data_json = array(
            "success" => true,
            "message" => "Insert Berhasil",
            "data" => array(
                "item_transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    // Item Trx Put

    public function item_transaksi_put()
    {

        $this->cekToken();

        // Validasi
        $validation_message = [];

        if ($this->put("id") == "") {
            array_push($validation_message, "ID Tidak Boleh Kosong !!!");
        }

        if ($this->put("transaksi_id") == "") {
            array_push($validation_message, "Transaksi ID Tidak Boleh Kosong !!!");
        }

        if ($this->put("transaksi_id") != "" && !$this->M_transaksi->cekTransaksiExist($this->put("transaksi_id"))) {
            array_push($validation_message, "Transaksi ID Tidak Ditemukan !!!");
        }

        if ($this->put("produk_id") == "") {
            array_push($validation_message, "Produk Tidak Boleh Kosong !!!");
        }

        if ($this->put("produk_id") != "" && !$this->M_produk->cekProdukExist($this->put("produk_id"))) {
            array_push($validation_message, "Produk ID Tidak Ditemukan !!!");
        }

        if ($this->put("qty") == "") {
            array_push($validation_message, "Qty Tidak Boleh Kosong !!!");
        }

        if ($this->put("qty") != "" && !is_numeric($this->put("qty"))) {
            array_push($validation_message, "Qty Harus Angka !!!");
        }

        if ($this->put("harga_saat_transaksi") == "") {
            array_push($validation_message, "Harga Tidak Boleh Kosong !!!");
        }

        if ($this->put("harga_saat_transaksi") != "" && !is_numeric($this->put("harga_saat_transaksi"))) {
            array_push($validation_message, "Harga Harus Angka !!!");
        }

        if (count($validation_message) > 0) {
            $data_json = array(
                "success" => false,
                "message" => "Data Tidak Valid",
                "data" => $validation_message

            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        // Lolos Validasi

        $data = array(
            "transaksi_id" => $this->put("transaksi_id"),
            "produk_id" => $this->put("produk_id"),
            "qty" => $this->put("qty"),
            "harga_saat_transaksi" => $this->put("harga_saat_transaksi"),
            "sub_total" => $this->put("qty") * $this->put("harga_saat_transaksi")
        );

        $id = $this->put("id");

        $result = $this->M_item_transaksi->updateItemTransaksi($data, $id);

        $data_json = array(
            "success" => true,
            "message" => "Update Berhasil",
            "data" => array(
                "item_transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    // Item Trx Del

    public function item_transaksi_delete()
    {

        $this->cekToken();

        $id = $this->delete("id");

        $result = $this->M_item_transaksi->deleteItemTransaksi($id);

        if (empty($result)) {
            $data_json = array(
                "success" => false,
                "message" => "Data Tidak Valid",
                "data" => null

            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        $data_json = array(
            "success" => true,
            "message" => "Delete Berhasil",
            "data" => array(
                "item_transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    public function item_transaksi_by_transaksi_id_delete()
    {

        $this->cekToken();

        $transaksi_id = $this->delete("transaksi_id");

        $result = $this->M_item_transaksi->deleteItemTransaksiByTransaksiId($transaksi_id);

        if (empty($result)) {
            $data_json = array(
                "success" => false,
                "message" => "Data Tidak Valid",
                "data" => null

            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }

        $data_json = array(
            "success" => true,
            "message" => "Delete Berhasil",
            "data" => array(
                "item_transaksi" => $result
            )

        );

        $this->response($data_json, REST_Controller::HTTP_OK);
    }

    // Item Trx End


    // Cek Token

    public function cekToken()
    {

        try {

            $token = $this->input->get_request_header('Authorization');

            if (!empty($token)) {
                $token = explode(' ', $token)[1];
            }

            $token_decode = JWT::decode($token, $this->secret_key, array('HS256'));
        } catch (Exception $e) {

            $data_json = array(
                "success" => false,
                "message" => "Token Tidak Valid",
                "error_code" => 1204,
                "data" => null

            );

            $this->response($data_json, REST_Controller::HTTP_OK);
            $this->output->_display();
            exit();
        }
    }
}
