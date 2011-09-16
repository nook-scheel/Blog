<?php
class Updates extends Controller {

	function Updates()
	{
		parent::Controller();
	}
	
	// Добовление только тользователям
	function Add()
	{		
		// Validate form
		$this->form_validation->set_rules('updateContent' , 'text', 'trim|required');
		if($this->form_validation->run() && $this->user_model->Secure(array('userType' => array('admin', 'user'))))
		{
			$updateId = $this->update_model->AddUpdate(array('updateContent' => $this->input->post('updateContent'), 'userId'=>$this->session->userdata('userId')));
			
			if($updateId)
			{
				$this->session->set_flashdata('flashConfirm', 'Запись успешно добавленна.');
				redirect();
			}
			else
			{
				$this->session->set_flashdata('flashError', 'Ошибка');
				redirect();
			}
		}
		else
		{
			redirect();
		}
	}
	
	// Удаление
	
	function delete()
	{
		$data['updates'] = $this->update_model->GetUpdates(array('updateId' => $this->uri->segment(2)));
		print_r($data['updates']);
		if(!$data['updates']) redirect('/');
		if($this->user_model->Secure(array('userType' => 'admin')))
		{
			$this->update_model->UpdateUpdate(array(
				'updateId' => $this->uri->segment(2),
				'updateStatus'=>'deleted'
			));
		
			$this->session->set_flashdata('flashConfirm', 'Пост удален.');
			redirect('/');
		}
		else
		{
			redirect('login');
		}
		
		redirect('/');
	}
	
	// Список
	function Index($str = 1)
	{
	    $perpage = 10;
		
	    $config['base_url'] = base_url() . 'page/';
	    $config['total_rows'] = $this->update_model->GetUpdates(array('count' => true));
	    $config['per_page'] = $perpage;
	    $config['uri_segment'] = $this->uri->slash_segment(2);
		$config['next_link'] = '';
		$config['next_tag_open'] = '<div class="next">';
		$config['next_tag_close'] = '</div>';
		$config['prev_link'] = '';
		$config['prev_tag_open'] = '<div class="previous">';
		$config['prev_tag_close'] = '</div>';
		$config['cur_tag_open'] = '<div class="page_button current">';
		$config['cur_tag_close'] = '</div>';
		$config['num_tag_open'] = '<div class="page_button">';
		$config['num_tag_close'] = '</div>';
		$str = str_replace('/', '', $this->uri->slash_segment(2));
		$str = ($str != '') ? $str : 1;
		
	    $data['pagination'] = $this->update_model->ArrayPages($config);
	    $data['secure'] = $this->user_model->Secure(array('userType' => array('admin', 'user')));
		$data['updates'] = $this->update_model->GetUpdates(array(
															'limit' => $perpage,
															'offset' => ($perpage*$str) - $perpage,
															'join' => array('table' => 'users', 'on' => 'users.userId = updates.userId'),
															'sortBy' => 'updateId',
															'sortDirection' => 'desc'
														));
		$this->load->view('index', $data);
	}
	
	// Вход
	function Login()
	{
		$this->form_validation->set_rules('userName', 'name', 'trim|required|callback__check_login');
		$this->form_validation->set_rules('userPassword', 'password', 'trim|required');
		
		if($this->form_validation->run())
		{
			if($this->user_model->Login(array('userName' => $this->input->post('userName'), 'userPassword' => $this->input->post('userPassword'))))
			{
				redirect('updates');
			} redirect('updates/login');
		}
		$this->load->view('login');
	}
	
	// Выход
	function Logout()
	{
		$this->session->sess_destroy();
		redirect('updates');
	}
	
	// Валидация
	function _check_login($userName)
	{
		if($this->input->post('userPassword'))
		{
			$user = $this->user_model->GetUsers(array('userName' => $userName, 'userPassword' => md5($this->input->post('userPassword'))));
			if($user) return true;
		}
		
		$this->form_validation->set_message('_check_login', 'Your username / password combination is invalid.');
		return false;
	}
}