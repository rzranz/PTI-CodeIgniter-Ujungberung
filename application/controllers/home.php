<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Load models
        $this->load->model("Item_model");
        $this->load->model('usermodel');
        $this->load->library('auth');
        $this->load->library('menu');
    }

    // Default controller
    public function index()
    {
        // Cek apakah user sudah login
        if ($this->auth->is_logged_in() == false) {
            // Jika belum login, arahkan ke form login
            $this->signin();
        } else {
            // Jika sudah login, tampilkan halaman dashboard
            $this->menu->tampil_sidebar();

            // Ambil data pengguna dan produk
            $data['user1'] = $this->usermodel->select_all(1);
            $data['user2'] = $this->usermodel->select_all(2);
            $data['products'] = $this->Item_model->select_all()->result();

            // Inisialisasi data chart
            $data['chart'] = [
                'label' => [],
                'data' => [],
            ];

            // Isi data chart jika produk tersedia
            if ($data['products']) {
                foreach ($data['products'] as $product) {
                    // Cek apakah properti 'nama_model' dan 'jml_produk' ada
                    $data['chart']['label'][] = isset($product->nama_model) ? $product->nama_model : 'Unknown';
                    $data['chart']['data'][] = isset($product->jml_produk) ? $product->jml_produk : 0;
                }
            }

            // Load view utama
            $this->load->view('main_page', $data);
        }
    }

    public function signin()
    {
        $this->load->view('login_form');
    }

    public function login()
    {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $success = $this->auth->do_login($username, $password);

        if ($success) {
            // Redirect ke halaman dashboard
            redirect(site_url('dashboard'));
        } else {
            // Tampilkan pesan kesalahan di form login
            $data['login_info'] = "Username atau password salah. Silahkan masukkan kombinasi yang benar!";
            $this->load->view('login_form', $data);
        }
    }

    public function logout()
    {
        if ($this->auth->is_logged_in() == true) {
            // Logout jika sudah login
            $this->auth->do_logout();
        }

        // Redirect ke halaman login
        redirect('login');
    }
}
