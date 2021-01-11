<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

require_once __DIR__ . '/API_V1_Controller.php';

use EA\Engine\Notifications\Email as EmailClient;
use EA\Engine\Types\Email;
use EA\Engine\Types\NonEmptyText;
use EA\Engine\Api\V1\Request;
use EA\Engine\Api\V1\Response;

/**
 * Sms Controller
 *
 * @package Controllers
 */
class SmsCode extends API_V1_Controller {


    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('sms_code_model');
		// Set the default language.
        $this->lang->load('translations', $this->config->item('language')); // default
    }

    /**
     * post API Method
     *
     * send verification code
     */
    public function sendCode()
    {
       try
        {
            $request = new Request();
            $param = $request->get_body();
            $eamilAdress=$param["email"];
            if (!$eamilAdress)
            {
                throw new Exception('You must enter a valid  email address in '
                    . 'order to get a verification code!');
            }

            $this->load->model('settings_model');

            $verification_code = $this->sms_code_model->add($eamilAdress);

            $this->config->load('email');

            $email = new EmailClient($this, $this->config->config);

            $company_settings = [
                    'company_name' => $this->settings_model->get_setting('company_name'),
                    'company_link' => $this->settings_model->get_setting('company_link'),
                    'company_email' => $this->settings_model->get_setting('company_email')
             ];
           $email->send_sms_code(new NonEmptyText($verification_code), new Email($eamilAdress),$company_settings);
           $response = [
                'states'=>AJAX_SUCCESS
            ];
        }
        catch (Exception $exception)
        {
            $response = [
                'states'=>AJAX_FAILURE,
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * get API Method
     *
     * validate Code
     */
    public function validateCode()
    {
        try
        {
            $email =$this->input->get('email');
            $code = $this->input->get('code');

            $message="";
            $state=AJAX_SUCCESS;
            $module="";

            if(empty($email)){
                $module="emaiEmpty";
                throw new Exception('mailbox cannot be empty');
            }
            if(empty($code)){
               $module="codeEmpty";
                throw new Exception('Verification code cannot be empty');
            }

            $conditions = [
               'email'=>$email,
               'validate_code'=>$code
            ];

            $sms_code_List = $this->sms_code_model->get_batch($conditions,"create_date desc",1);
            $sms_code=$sms_code_List[0];

            if($sms_code!=null){

               $utcNow= date("Y-m-d H:i:s", time() - date("Z"));
               $senddate= $sms_code["create_date"];
               $minute=(strtotime($utcNow)-strtotime($senddate))%86400%3600/60;

               if($sms_code["is_used"]||$minute>5)
               {
                  $module='codeExpired';
                  $state=AJAX_FAILURE;
                  $message="Verification code invalid";
               }
               else
               {
                  $sms_code["is_used"]=1;
                  $this->sms_code_model->update($sms_code);
                }

            }
            else
            {
              $module='codeError';
              $state=AJAX_FAILURE;
              $message="Verification code error";
            }

            $response = [
                'module'=>$module,
                'states' =>$state,
                'message' =>$message
            ];
        }
        catch (Exception $exception)
        {
           $response = [
                'module'=>$module,
                'states' =>AJAX_FAILURE,
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }

        $this->output
             ->set_content_type('application/json')
             ->set_output(json_encode($response));
            
    }

}
