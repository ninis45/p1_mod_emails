<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin Blog Fields
 *
 * Manage custom blogs fields for
 * your blog.
 *
 * @author 		PyroCMS Dev Team
 * @package 	PyroCMS\Core\Modules\Users\Controllers
 */
class Admin_organizaciones extends Admin_Controller {

	protected $section = 'organizaciones';

	// --------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();
        $this->lang->load('email');
        $this->load->model(array('org_m','users/user_m'));
        $this->load->config('groups');
        
        $this->load->library('centros/centro');
    }
    function index()
    {
        $orgs = $this->org_m->get_all();
        
        foreach($orgs as &$org)
        {
            $org->users = Centro::GetList($org->id,'orgs');
            
           
            
        }
        
        
        $this->template->title($this->module_details['name'])
                ->set('orgs',$orgs)
                ->build('admin/orgs/index');
    }
    function add($id)
    {
        $org   = $this->org_m->get($id) OR redirect('admin');
        
        if($_POST)
        {
             Centro::AddUsers($id,'orgs',$this->input->post('users'));
             
             redirect('admin/emails/organizaciones');
        }
        
        
        $users = $this->user_m->where_in('name',$this->config->item('groups'))->get_all();
        
        $user_actives = Centro::GetList($id,'orgs');
        $this->template->title($this->module_details['name'],lang('email:add_org'))
                ->set('users',$users)
                ->set('users_active',$user_actives?$user_actives:array())
                ->set('org',$org)
                ->build('admin/orgs/form');
    }
 }
?>