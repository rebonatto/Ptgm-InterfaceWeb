<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modelo extends MY_Controller {

	/**
	* 2015
	* Desenvolvido por: Mateus Perego
	* Email: mateusperego@yahoo.com.br
	* Projeto de conclusão de curso
	* UPF - Ciência da Computação
	*/	

	public function __construct()
	{
		parent::__construct();
		$this->load->model('modelo_model');
	}  

	public function index($offset=0)
	{
		$link=$this->input->get('link');
		if ($link=='cadastro') {
			$data['modelo']=$link;
			$this->load->view('modelo_cadastro',$data);
		}else{
			//paginacao
    		$config = array();
    		$config['base_url']= base_url()."index.php/modelo/index";
    		$config['total_rows']= $this->db->get('modelo')->num_rows();
    		$config['per_page']= LIMITE;
    		$config['uri_segment']= 3;    		
    		$this->pagination->initialize($config);
    		$data['paginacao'] = $this->pagination->create_links();    		
    		$data['modelo']= $this->modelo_model->get_all_modelo(LIMITE, $offset);

			$this->load->view('modelo_consulta',$data);				
		}		
	}

	// create modelo
	function create_modelo()
	{
		//teste o nível do usuário
		if ($this->session->userdata('nivel') == '1' || $this->session->userdata('nivel') == '3') {
			$data=array(
				'codModelo'=>$this->input->post('codModelo'),
				'desc'=>$this->input->post('desc')
				);
			if($this->modelo_model->add_modelo($data)){
				$this->session->set_flashdata('msg',$this->lang->line('msg_insert'));
				redirect('index.php/modelo?link=cadastro');
			}
		} else {
			$this->session->set_flashdata('msg',$this->lang->line('msg_permission_insert'));
			redirect('index.php/modelo?link=cadastro');
		}
	}

	//delete modelo
	function apagar_modelo(){
		//testa o nível do usuário
		if ($this->session->userdata('nivel') == '1' || $this->session->userdata('nivel') == '3') {
			if ($this->modelo_model->delete_modelo()) {
				$this->session->set_flashdata('msg',$this->lang->line('msg_delete'));
				redirect('index.php/modelo');
			}else{
				die($this->lang->line('msg_error_delete'));
			}
		}else{
			$this->session->set_flashdata('msg',$this->lang->line('msg_permission_delete'));
			redirect('index.php/modelo');
		}
	}

	//update modelo
	function editar_modelo($codModelo){
		//testa o nível do usuário
		if ($this->session->userdata('nivel') == '1' || $this->session->userdata('nivel') == '3') {
			$data['modelo']=$this->modelo_model->get_by_id_modelo($codModelo);
			$this->form_validation->set_rules('desc','desc','trim|required');

			if ($this->form_validation->run()) {
				$_POST['codModelo']=$codModelo;
				if($this->modelo_model->update_modelo($_POST)){
					$this->session->set_flashdata('msg',$this->lang->line('msg_update'));
					redirect('index.php/modelo?link=cadastro');
				}
			}
			$this->load->view('modelo_cadastro',$data);
		}else{
			$this->session->set_flashdata('msg',$this->lang->line('msg_permission_update'));
			redirect('index.php/modelo');
		}
	}

}

/* End of file */