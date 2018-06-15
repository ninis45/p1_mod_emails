<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * The public controller for the Pages module.
 *
 * @author		PyroCMS Dev Team
 * @package		PyroCMS\Core\Modules\Pages\Controllers
 */
class Emails extends Public_Controller
{

	/**
	 * Constructor method
	 */
	public function __construct()
	{
		parent::__construct();
    }
    public function index()
    {
         $email = null;

        $total_asignados =  $this->db->select('org_path, COUNT(*) AS numrows')
          ->join('chromebooks','chromebooks.email=emails.email')
          ->group_by('org_path')
          ->get('emails')
          ->result();
      //  print_r($total_asignados);

                    


    $asignado =  $this->db                                             
                         ->join('chromebooks','chromebooks.email=emails.email')
                         ->count_all_results('emails');

    $chromebook =  $this->db->count_all_results('chromebooks');

    $disponibles = $chromebook - $asignado;


           $query1 =  $this->db->select('org_path, COUNT(*) AS numrows')
          ->group_by('org_path')
          ->get('emails')
          ->result();
          // print_r($query1);

            
          $data;


          foreach($query1 as $elemento)
          {
            
            $org_path =  $elemento->org_path;
            $total = $elemento->numrows;
           

            foreach ($total_asignados as $alumnos) 
            {

              if ($org_path == $alumnos->org_path) 
              {

                  $alumnos->alumnos=   $total;
              
              } 
             
            
              

            }



              
 
          }



 $this->template->set_layout(false)
            ->enable_parser(true)
            ->set('total_asignados',$total_asignados)
            //->set('asignados',$asignados)
           // ->set('data',$data)
            ->set('disponibles',$disponibles)
            ->build('admin/index_mail');   
    }

    public function remover()
    {
        $result   = false;
        $message  = '';      
        $asignacion   = false; 
      
         $message = '<div class="alert alert-warning">01</div>';
        if($_POST)
        {
        

           $asignacion =  $this->db->where(array(
                    'id_chromebook' => $this->input->post('serial'),
                    'removido IS NULL'      => null
           ))->get('chromebook_asignacion')->row();
           

           if(!$asignacion)
           {
               $message = '<div class="alert alert-warning">No hay registro de la CHROMEBOOK o aún no ha sido asignada</div>';
           }
           else{
                
              
               $this->db->where(array('id'=>$asignacion->id))->set(array('removido'=>date('Y-m-d H:i:s')))->update('chromebook_asignacion');
               $message = '<div class="alert alert-success">La CHROMEBOOK con No. de Serie '.$asignacion->id_chromebook.' ha sido removida</div>';    
              
               /*if($chromebook->email)
               {
                 
                    $asignado = $this->db->where('emails.email',$chromebook->email)
                                    ->join('chromebooks','chromebooks.email=emails.email')
                                    ->get('emails')->row();

                  
                   $baja = array('email' => null,'updated_on'=>now());
                   
                   $this->db->where('serial',$chromebook->serial)
                                            ->set($baja)
                                            ->update('chromebooks');
                            
                    $message = '<div class="alert alert-success">La CHROMEBOOK con No. de Serie '.$chromebook->serial.' ha sido removida</div>';        
                   

               }
               else
               {
                     $message = '<div class="alert alert-info">La CHROMEBOOK con No. de Serie '.$chromebook->serial.' anteriormente ya habia sido removida.</div>';        
               }*/

           }
           
            
        
                     
           
        }


        $this->template->set_layout(false)
            ->enable_parser(true)
            ->set('asignado',$asignacion)
            ->set('message',$message)
            ->build('admin/form_remover');   
    }

    public function agregar ()
    {


        //$this->load->helper('cookie');
        
        $result   = false;
        $message  = '';
        $org_path = $this->input->post('org_path');
        
        $asignado   = false; 
        $total_asignados = 0;
        
        
        
        
        if($_POST && $org_path)
        {
            
            
            
            
                
                
             
            
           
           $chromebook =  $this->db->where('serial',$this->input->post('serial'))->get('chromebooks')->row();
           
           $list =  $this->db->select('serial,full_name,org_path,emails.email AS email')
                    ->where(array(
                         'org_path'=>$this->input->post('org_path')
                       
                     ))->order_by('grado,grupo')
                     ->join('alumnos','alumnos.idalum=emails.table_id')
                     ->join('chromebooks','chromebooks.email=emails.email','LEFT')
                     ->get('emails')->result();
                     
            
           
           if(!$chromebook)
           {
               $message = '<div class="alert alert-warning">No hay registro de la CHROMEBOOK</div>';
           }
           else{
               $inc = 0;
               
               if($chromebook->email)
               {
                    $asignado = $this->db->where('emails.email',$chromebook->email)
                                    ->join('chromebooks','chromebooks.email=emails.email')
                                    ->get('emails')->row();
                                    
                    if(!$asignado)
                    {
                        $message = '<div class="alert alert-danger"> CHROMEBOOK asignada pero no existe el beneficiado: '.$chromebook->email.'</div>';
                    }
                    else
                    {
                        $message = '<div class="alert alert-info">CHROMEBOOK asignada : '.$chromebook->serial.'  / '.$chromebook->email.'</div>';
                    }
                   
               }
               else{
                   while(!$asignado)
                   {
                        
                        foreach($list as $alum)
                        {
                            
                            if(!$alum->serial)
                            {
                                
                               
                                
                                
                                $asignado = array('email' => $alum->email,'updated_on'=>now());
                                
                                if($this->db->where('serial',$chromebook->serial)
                                            ->set($asignado)
                                            ->update('chromebooks'))
                                {
                                   
                                    $asignado['serial']    = $chromebook->serial;
                                    $asignado['full_name'] = $alum->full_name;
                                    $asignado['org_path']  = $alum->org_path;
                                    $asignado = (Object)$asignado;
                                    
                                    break;
                                }
                            }
                        }
                        if(!$asignado)
                        {
                             $message = '<div class="alert alert-danger"> Se hay llegado al limite de registros</div>';
                             $asignado = true;
                        }
                        
                       
                        
                        
                   }
               }
           }
           
            
              $total_asignados =  $this->db
                        ->where(array(
                             'org_path'=>$this->input->post('org_path')
                           
                         ))
                         
                         ->join('chromebooks','chromebooks.email=emails.email')
                         ->count_all_results('emails');
           
                     
           
        }
        
         $orgs = $this->db->group_by('org_path')->get('emails')->result();
         $this->template->set_layout(false)
            ->enable_parser(true)
            ->set('orgs',array_for_select($orgs,'org_path','org_path'))
            ->set('total_alumnos',isset($list)?count($list):0)
            ->set('total_asignados',$total_asignados)
            ->set('asignado',$asignado)
            ->set('message',$message)
            ->build('admin/form_lectura');        
    
    

    }
     public function consulta()
    {
        $result   = false;
        $message  = '';      
        $asignado   = false; 
      
         $message = '<div class="alert alert-success">01</div>';
        if($_POST)
        {
            if(strlen($this->input->post('serial'))!= 10)
            {
               $message = '<div class="alert alert-danger">Numero de Serie Incorrecto</div>';
            }
            else{
          $chromebook_levantamiento =  $this->db->where('chromebook',$this->input->post('serial'))->get('chromebook_levantamiento')->row();
          if($chromebook_levantamiento)
          {
            $message = '<div class="alert alert-warning">La CHROMEBOOK con No. de Serie '.$chromebook_levantamiento->chromebook.' ya habia sido escaneada</div>';
          }
          else{
           $chromebook =  $this->db->where('serial',$this->input->post('serial'))->get('chromebooks')->row();
           
           if(!$chromebook)
           {
                $data = array( 'chromebook' => $this->input->post('serial'),
                                'status' =>   0  );
                if($this->db->insert('default_chromebook_levantamiento',$data))
                {
                   $message = '<div class="alert alert-info">La CHROMEBOOK con No. de Serie '.$this->input->post('serial').' No se encontraba en la Base de Datos</div>';
                }
           }
           else
           {
            $data = array( 'chromebook' => $this->input->post('serial'),
                                'status' =>   1 );
                if($this->db->insert('default_chromebook_levantamiento',$data))
                {
                   $message = '<div class="alert alert-success">La CHROMEBOOK con No. de Serie '.$chromebook->serial.' Se encontraba en la Base de Datos</div>';
                }
                    
               
           }   
           } 
           }      
          
         }
        $this->template->set_layout(false)
            ->enable_parser(true)
            ->set('message',$message)
            ->build('form_consulta');   
    }
    
}