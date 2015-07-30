<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {

	/**
	* 2015
	* Desenvolvido por: Mateus Perego
	* Email: mateusperego@yahoo.com.br
	* Projeto de conclusão de curso
	* UPF - Ciência da Computação
	*/	

	//Como este controller estende o MY_Controller, que é onde está a verificação de login e senha, então aqui você não precisa
	//fazer nenhuma verifição se o usuário está logado.
	//Lembre-se: os controllers que você deseja proteger, devem estender o MY_Controller. 
	//O controller Login não pode estender o MY_Controller, caso contrário o código entra em loop, e também não tem sentido proteger
	//a tela de login. :)

	public function __construct()
	{
		parent::__construct();
	} 

	//carrega a home Página de boas vindas
	public function index()
	{
		$this->load->view('v_home');
	}

}