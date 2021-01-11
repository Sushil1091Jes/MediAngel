<?php defined('BASEPATH') or exit('No direct script access allowed');

/* ----------------------------------------------------------------------------
 * MediAngel - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

require_once __DIR__ . '/API_V1_Controller.php';

use EA\Engine\Api\V1\Request;
use EA\Engine\Api\V1\Response;
use EA\Engine\Types\NonEmptyText;

/**
 * Appointments Controller
 *
 * @package Controllers
 */
class Appointments extends API_V1_Controller {
    /**
     * Appointments Resource Parser
     *
     * @var \EA\Engine\Api\V1\Parsers\Appointments
     */
    protected $parser;

    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('appointments_model');
        $this->load->model('services_model');
        $this->load->model('providers_model');
        $this->load->model('customers_model');
        $this->load->model('settings_model');
        $this->load->library('synchronization');
        $this->load->library('notifications');
        $this->parser = new \EA\Engine\Api\V1\Parsers\Appointments;
        // Set the default language.
        $this->lang->load('translations', $this->config->item('language'));
    }

    /**
     * GET API Method
     *
     * @param int $id Optional (null), the record ID to be returned.
     */
    public function get($id = NULL)
    {
        try
        {
            $conditions = [
                'is_unavailable' => FALSE,
                'status'=>1
            ];

            if ($id !== NULL)
            {
                $conditions['id'] = $id;
            }

            $appointments = $this->appointments_model->get_batch($conditions, NULL, NULL, NULL, array_key_exists('aggregates', $_GET));

            if ($id !== NULL && count($appointments) === 0)
            {
                $this->throw_record_not_found();
            }

            $response = new Response($appointments);

            $response->encode($this->parser)
                ->search()
                ->sort()
                ->paginate()
                ->minimize()
                ->singleEntry($id)
                ->output();

        }
        catch (Exception $exception)
        {
            exit($this->handle_exception($exception));
        }
    }

    /**
     * POST API Method
     */
    public function post()
    {
        try
        {
            // Insert the appointment to the database.
            $request = new Request();
            $appointment = $request->get_body();
            $this->parser->decode($appointment);

            if (isset($appointment['id']))
            {
                unset($appointment['id']);
            }

            // Generate end_datetime based on service duration if this field is not defined
            if ( ! isset($appointment['end_datetime']))
            {
                $service = $this->services_model->get_row($appointment['id_services']);

                if (isset($service['duration']))
                {
                    $end_datetime = new DateTime($appointment['start_datetime']);
                    $end_datetime->add(new DateInterval('PT' . $service['duration'] . 'M'));
                    $appointment['end_datetime'] = $end_datetime->format('Y-m-d H:i:s');
                }
            }

            $service = $this->services_model->get_row($appointment['id_services']);
            if($service==null){
                $response = [
                    'module'=>"service",
                    'states' =>AJAX_FAILURE,
                    'message' => "Medical field does not exist, please choose again"
                ];
               $this->output->set_content_type('application/json') ->set_output(json_encode($response));
               return;
            }

            $provider = $this->providers_model->get_row($appointment['id_users_provider']);
            if($provider==null){
               $response = [
                    'module'=>"provider",
                    'states' =>AJAX_FAILURE,
                    'message' => "Doctor does not exist, please choose again"
               ];
               $this->output->set_content_type('application/json') ->set_output(json_encode($response));
               return;
            }

            $customer = $this->customers_model->get_row($appointment['id_users_customer']);
            if($customer==null){
               $response = [
                    'module'=>"customer",
                    'states' =>AJAX_FAILURE,
                    'message' => "The patient does not exist, please re select or create"
               ];
               $this->output->set_content_type('application/json') ->set_output(json_encode($response));
               return;
            }

            //Verify that the date has been reserved
            $this->appointments_model->verify_date_is_reserved($appointment['id_users_provider'],$appointment['start_datetime'],$appointment['end_datetime']);
            
            //Verify Working Plan
            $this->verify_is_work_time($appointment['id_users_provider'],$appointment['start_datetime'],$appointment['end_datetime']);

            $id = $this->appointments_model->add($appointment);

            $settings = [
                'company_name' => $this->settings_model->get_setting('company_name'),
                'company_email' => $this->settings_model->get_setting('company_email'),
                'company_link' => $this->settings_model->get_setting('company_link'),
                'date_format' => $this->settings_model->get_setting('date_format'),
                'time_format' => $this->settings_model->get_setting('time_format')
            ];

            $this->synchronization->sync_appointment_saved($appointment, $service, $provider, $customer, $settings, FALSE);
            $this->notifications->notify_appointment_saved($appointment, $service, $provider, $customer, $settings, FALSE);

            // Fetch the new object from the database and return it to the client.
            $batch = $this->appointments_model->get_batch('id = ' . $id);
            $response = [
                'states' =>AJAX_SUCCESS,
                'message' => ""
            ];
        }
        catch (Exception $exception)
        {
           $response = [
                'states' =>AJAX_FAILURE,
                'message' => $exception->getMessage(),
                'trace' => config('debug') ? $exception->getTrace() : []
            ];
        }
         $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * PUT API Method
     *
     * @param int $id The record ID to be updated.
     */
    public function put($id)
    {
        try
        {
            // Update the appointment record.
            $batch = $this->appointments_model->get_batch('id = ' . $id);

            if ($id !== NULL && count($batch) === 0)
            {
                $this->throw_record_not_found();
            }

            $request = new Request();
            $updated_appointment = $request->get_body();
            $base_appointment = $batch[0];
            $this->parser->decode($updated_appointment, $base_appointment);
            $updated_appointment['id'] = $id;
            $id = $this->appointments_model->add($updated_appointment);

            $service = $this->services_model->get_row($updated_appointment['id_services']);
            $provider = $this->providers_model->get_row($updated_appointment['id_users_provider']);
            $customer = $this->customers_model->get_row($updated_appointment['id_users_customer']);
            $settings = [
                'company_name' => $this->settings_model->get_setting('company_name'),
                'company_email' => $this->settings_model->get_setting('company_email'),
                'company_link' => $this->settings_model->get_setting('company_link'),
                'date_format' => $this->settings_model->get_setting('date_format'),
                'time_format' => $this->settings_model->get_setting('time_format')
            ];

            $this->synchronization->sync_appointment_saved($updated_appointment, $service, $provider, $customer, $settings, TRUE);
            $this->notifications->notify_appointment_saved($updated_appointment, $service, $provider, $customer, $settings, TRUE);


            // Fetch the updated object from the database and return it to the client.
            $batch = $this->appointments_model->get_batch('id = ' . $id);
            $response = new Response($batch);
            $response->encode($this->parser)->singleEntry($id)->output();
        }
        catch (Exception $exception)
        {
            exit($this->handle_exception($exception));
        }
    }

    /**
     * DELETE API Method
     *
     * @param int $id The record ID to be deleted.
     */
    public function delete($id)
    {
        try
        {
            $appointment = $this->appointments_model->get_row($id);
            $service = $this->services_model->get_row($appointment['id_services']);
            $provider = $this->providers_model->get_row($appointment['id_users_provider']);
            $customer = $this->customers_model->get_row($appointment['id_users_customer']);
            $settings = [
                'company_name' => $this->settings_model->get_setting('company_name'),
                'company_email' => $this->settings_model->get_setting('company_email'),
                'company_link' => $this->settings_model->get_setting('company_link'),
                'date_format' => $this->settings_model->get_setting('date_format'),
                'time_format' => $this->settings_model->get_setting('time_format')
            ];

            $this->appointments_model->delete($id);

            $this->synchronization->sync_appointment_deleted($appointment, $provider);
            $this->notifications->notify_appointment_deleted($appointment, $service, $provider, $customer, $settings);

            $response = new Response([
                'code' => 200,
                'message' => 'Record was deleted successfully!'
            ]);

            $response->output();
        }
        catch (Exception $exception)
        {
            exit($this->handle_exception($exception));
        }
    }

    /**
     * Cancel an existing appointment.
     *
     * This method removes an appointment from the company's schedule. In order for the appointment to be deleted, the
     * hash string must be provided. The customer can only cancel the appointment if the edit time period is not over
     * yet.
     *
     * @param string $appointment_hash This appointment hash identifier.
     */
    public function cancel()
    {
        try
        {
            $request = new Request();
            $cancelModel = $request->get_body();

            $appointment_hash=$cancelModel['Hash'];
            $appointment_cancel_reasons=$cancelModel['Reasons'];

            // Check whether the appointment hash exists in the database.
            $appointments = $this->appointments_model->get_batch(['hash' => $appointment_hash]);

            if (empty($appointments))
            {
                throw new Exception('No record matches the provided hash.');
            }

            $cancel_appointment = $appointments[0];
            
            $provider = $this->providers_model->get_row($cancel_appointment['id_users_provider']);
            $customer = $this->customers_model->get_row($cancel_appointment['id_users_customer']);
            $service = $this->services_model->get_row($cancel_appointment['id_services']);

            $settings = [
                'company_name' => $this->settings_model->get_setting('company_name'),
                'company_email' => $this->settings_model->get_setting('company_email'),
                'company_link' => $this->settings_model->get_setting('company_link'),
                'date_format' => $this->settings_model->get_setting('date_format'),
                'time_format' => $this->settings_model->get_setting('time_format')
            ];

            $cancel_appointment['status']=-1;
            $cancel_appointment['cancelreasons']=$appointment_cancel_reasons;
            $this->appointments_model->add($cancel_appointment);

            $this->synchronization->sync_appointment_deleted($cancel_appointment, $provider);
            $this->notifications->notify_appointment_deleted($cancel_appointment, $service, $provider, $customer, $settings);

            $response = new Response([
                'code' => 200,
                'message' => 'Record canceled successfully!'
            ]);

            $response->output();
        }
        catch (Exception $exception)
        {
            exit($this->handle_exception($exception));
        }
    }

        /**
     * Query Appointments By Email API Method
     *
     *
     */
    public function QueryAppointmentsByEmail()
    {
        try
        {
			$request = new Request();
            $query = $request->get_body();

            $email=$query['email'];

            $appointments=$this->appointments_model->get_appointments_by_email($email);

            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode($appointments));
        }
        catch (Exception $exception)
        {
            exit($this->handle_exception($exception));
        }
    }
	


    /**
     * Query Appointments API Method
     *
     *
     */
    public function QueryAppointments()
    {
        try
        {
			$request = new Request();
            $query = $request->get_body();

            $slot_start=$query['start'];
            $slot_end=$query['end'];

            $number = $this->appointments_model->get_appointments_number_for_period($slot_start,$slot_end);
			$groupNumber = $this->appointments_model->get_appointments_group_number_for_period($slot_start,$slot_end);
            
			$allGroup=$this->appointments_model->generate_group_data_for_period($slot_start,$slot_end);			
			
			foreach($allGroup as $key=>$valueAll)
			{
				foreach($groupNumber as $valueHasData)
				{
					if($valueAll['AppointmentDate']==$valueHasData['AppointmentDate'])
					{
						$valueAll['Number']=$valueHasData['Number'];
						$allGroup[$key]=$valueAll;
					}
				}
			}			
						
            $data=array(
				'TotalAppointments'=>$number,
				'GroupNumber'=>$allGroup
			);

            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode($data));
        }
        catch (Exception $exception)
        {
            exit($this->handle_exception($exception));
        }
    }
	
	/**
     * Query New Patients API Method
     *
     *
     */
    public function QueryNewPatients()
    {
        try
        {
			$request = new Request();
            $query = $request->get_body();

            $slot_start=$query['start'];
            $slot_end=$query['end'];

            $number = $this->appointments_model->get_newpatients_number_for_period($slot_start,$slot_end);
			$groupNumber = $this->appointments_model->get_newpatients_group_number_for_period($slot_start,$slot_end);
            
			$allGroup=$this->appointments_model->generate_group_data_for_period($slot_start,$slot_end);			
			
			foreach($allGroup as $key=>$valueAll)
			{
				foreach($groupNumber as $valueHasData)
				{
					if($valueAll['AppointmentDate']==$valueHasData['AppointmentDate'])
					{
						$valueAll['Number']=$valueHasData['Number'];
						$allGroup[$key]=$valueAll;
					}
				}
			}			
			
            $data=array(
				'TotalNewPatients'=>$number,
				'GroupNumber'=>$allGroup
			);

            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode($data));
        }
        catch (Exception $exception)
        {
            exit($this->handle_exception($exception));
        }
    }
	
		
	/**
     * Query Today Appointments API Method
     *
     *
     */
    public function QueryTodayAppointments()
    {
        try
        {
			$number = $this->appointments_model->get_todayappointments_number();			
            
            $data=array(
				'TodayAppointments'=>$number
			);

            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode($data));
        }
        catch (Exception $exception)
        {
            exit($this->handle_exception($exception));
        }
    }
	
	/**
     * Query Canceled Appointments API Method
     *
     *
     */
    public function QueryCanceledAppointments()
    {
        try
        {
			$request = new Request();
            $query = $request->get_body();

            $slot_start=$query['start'];
            $slot_end=$query['end'];

            $number = $this->appointments_model->get_canceled_appointments_number_for_period($slot_start,$slot_end);
			$groupNumber = $this->appointments_model->get_canceled_appointments_group_number_for_period($slot_start,$slot_end);
            
            $allGroup=$this->appointments_model->generate_group_data_for_period($slot_start,$slot_end);			
			
			foreach($allGroup as $key=>$valueAll)
			{
				foreach($groupNumber as $valueHasData)
				{
					if($valueAll['AppointmentDate']==$valueHasData['AppointmentDate'])
					{
						$valueAll['Number']=$valueHasData['Number'];
						$allGroup[$key]=$valueAll;
					}
				}
			}			
						
            $data=array(
				'TotalCanceledAppointments'=>$number,
				'GroupNumber'=>$allGroup
			);

            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode($data));
        }
        catch (Exception $exception)
        {
            exit($this->handle_exception($exception));
        }
    }
	
	/**
     * Query Latest Appointment API Method
     *
     *
     */
    public function QueryLatestAppointment()
    {
        try
        {
            $latestAppointment = $this->appointments_model->get_latest_appointments_user();			
            
            $data=array(
				'LatestAppointment'=>$latestAppointment
			);

            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode($data));
        }
        catch (Exception $exception)
        {
            exit($this->handle_exception($exception));
        }
    }

	/**
     * Patients Statistics For Last Year API Method
     *
     *
     */
    public function PatientsStatisticsForLastYear()
    {
        try
        {
			$groupTotalPatients = $this->appointments_model->get_group_total_patients_number_for_last_year();			            
			$allGroupTotalPatients=$this->appointments_model->generate_group_data_for_last_year();			
			
			foreach($allGroupTotalPatients as $key=>$valueAll)
			{
				foreach($groupTotalPatients as $valueHasData)
				{
					if($valueAll['YearMonth']==$valueHasData['YearMonth'])
					{
						$valueAll['Number']=$valueHasData['Number'];
						$allGroupTotalPatients[$key]=$valueAll;
					}
				}
			}

			$groupNewPatients = $this->appointments_model->get_group_new_patients_number_for_last_year();
			$allGroupNewPatients=$this->appointments_model->generate_group_data_for_last_year();			
			
			foreach($allGroupNewPatients as $key=>$valueAll)
			{
				foreach($groupNewPatients as $valueHasData)
				{
					if($valueAll['YearMonth']==$valueHasData['YearMonth'])
					{
						$valueAll['Number']=$valueHasData['Number'];
						$allGroupNewPatients[$key]=$valueAll;
					}
				}
			}
			
			$groupRepeatPatients = $this->appointments_model->get_group_repeat_patients_number_for_last_year();
			$allGroupRepeatPatients=$this->appointments_model->generate_group_data_for_last_year();
			
			foreach($allGroupRepeatPatients as $key=>$valueAll)
			{
				foreach($groupRepeatPatients as $valueHasData)
				{
					if($valueAll['YearMonth']==$valueHasData['YearMonth'])
					{
						$valueAll['Number']=$valueHasData['Number'];
						$allGroupRepeatPatients[$key]=$valueAll;
					}
				}
			}
			
            $data=array(
				'TotalPatients'=>$allGroupTotalPatients,
				'NewPatients'=>$allGroupNewPatients,
				'RepeatPatients'=>$allGroupRepeatPatients
			);

            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode($data));
        }
        catch (Exception $exception)
        {
            exit($this->handle_exception($exception));
        }
    }
	
	/**
     * Patients Statistics By Gender API Method
     *
     *
     */
    public function PatientsStatisticsByGender()
    {
        try
        {
			$request = new Request();
            $query = $request->get_body();

            $slot_start=$query['start'];
            $slot_end=$query['end'];
			
			$groupPatientsByGender = $this->appointments_model->get_group_number_patients_by_gender_for_period($slot_start,$slot_end);
			
			$total=0;
			$male=0;
			$female=0;
			$child=0;
			
			foreach($groupPatientsByGender as $value)
			{
				$total=$total+$value['Number'];
				
				if($value['GenderType']==1)
				{
					$male=$male+$value['Number'];
				}
				else if($value['GenderType']==2)
				{
					$female=$female+$value['Number'];
				}
				else
				{
					$child=$child+$value['Number'];
				}
			}
            			
            $data=array(
				'Male'=>(number_format($male/$total,4)*100).'%',
				'Female'=>(number_format($female/$total,4)*100).'%',
				'Child'=>((100-(number_format($male/$total,4)*100+number_format($female/$total,4)*100))).'%'
			);

            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode($data));
        }
        catch (Exception $exception)
        {
            exit($this->handle_exception($exception));
        }
    }

   protected function verify_is_work_time($provider_id,$start_datetime,$end_datetime)
   {
        $this->load->model('providers_model');

        $selected_date = date('Y-m-d', strtotime($start_datetime));
        $start = strtotime($start_datetime);
        $end =strtotime($end_datetime);

        // Get the provider's working plan 
        $working_plan = json_decode($this->providers_model->get_setting('working_plan', $provider_id), TRUE);

       
        // Find the empty spaces on the plan. The first split between the plan is due to a break (if exist). After that
        // every reserved appointment is considered to be a taken space in the plan.
       $selected_date_working_plan = $working_plan[strtolower(date('l', strtotime($selected_date)))];

       //working_plan->Verify that it is working time
       if($selected_date_working_plan==null){
          throw new Exception('The doctor has taken a rest, Please re select the appointment time');
       }
       $working_start = strtotime($selected_date . ' ' . $selected_date_working_plan['start']);
       $working_end = strtotime($selected_date . ' ' . $selected_date_working_plan['end']);
       if(!($start>=$working_start&&$end<=$working_end)) 
       {
          throw new Exception('The doctor has taken a rest, Please re select the appointment time');
       }

       //working_plan -> lunch break
       if (isset($selected_date_working_plan['breaks']))
       {
           // Split the working plan to available time periods that do not contain the breaks in them.
           foreach ($selected_date_working_plan['breaks'] as $index => $break)
           {
                $break_start = new DateTime($break['start']);
                $break_end = new DateTime($break['end']);
                if($start<$break_end &&$end>$break_start)
                {
                  throw new Exception('The doctor has taken a rest, Please re select the appointment time');
                }
            }
        }

        // Get the provider's working plan exceptions.
        $working_plan_exceptions = json_decode($this->providers_model->get_setting('working_plan_exceptions', $provider_id), TRUE);
        if (isset($working_plan_exceptions[$selected_date]))
        {
           $selected_date_working_plan = $working_plan_exceptions[$selected_date];
           if (isset($selected_date_working_plan['breaks']))
           {
                foreach ($selected_date_working_plan['breaks'] as $index => $break)
                {
                    $break_start = new DateTime($break['start']);
                    $break_end = new DateTime($break['end']);
                    if($start<$break_end &&$end>$break_start)
                    {
                      throw new Exception('The doctor has taken a rest, Please re select the appointment time');
                    }
                }
           }
        }
    }
}
