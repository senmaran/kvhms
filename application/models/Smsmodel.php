<?php
Class Smsmodel extends CI_Model
{

 public function __construct()
  {
      parent::__construct();

  }

   public function getYear()
    {
      $sqlYear = "SELECT * FROM edu_academic_year WHERE NOW() >= from_month AND NOW() <= to_month AND status = 'Active'";
      $year_result = $this->db->query($sqlYear);
      $ress_year = $year_result->result();

      if($year_result->num_rows()==1)
      {
        foreach ($year_result->result() as $rows)
        {
            $year_id = $rows->year_id;
        }
        return $year_id;
      }
    }

  function send_sms_for_teacher_leave($number,$leave_type)
  {
	// http://173.45.76.227/send.aspx?username=kvmhss&pass=kvmhss123&route=trans1&senderid=KVMHSS&numbers=12345&message=WELCOME
     //Thank you for the information. This is to inform you that your leave has been approved.

	$textmessage='Thank you for the information This is to inform you that your '.$leave_type.' has been approved';

	$textmsg =urlencode($textmessage);

	$smsGatewayUrl = 'http://173.45.76.227/send.aspx?';

	$api_element = 'username=kvmhss&pass=kvmhss123&route=trans1&senderid=KVMHSS';

    $api_params = $api_element.'&numbers='.$number.'&message='.$textmsg;

	$smsgatewaydata = $smsGatewayUrl.$api_params;

	$url = $smsgatewaydata;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST, false);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch);
	curl_close($ch);

	if(!$output)
	{
      $output =  file_get_contents($smsgatewaydata);
    }
 }

 function send_sms_for_teacher_substitution($tname,$sub_teacher,$sub_tname,$leave_date,$cls_id,$period_id)
 {

	$sql="SELECT teacher_id,name,phone FROM edu_teachers WHERE teacher_id='$sub_teacher'";
	$resultset=$this->db->query($sql);
	$res=$resultset->result();
	foreach($res as $cell){}
	$num=$cell->phone;

	$sql1="SELECT cm.class_sec_id,cm.class,cm.section,c.class_id,c.class_name,s.sec_id,s.sec_name FROM edu_classmaster AS cm,edu_class AS c,edu_sections AS s WHERE cm.class_sec_id='$cls_id' AND cm.class=c.class_id AND cm.section=s.sec_id ";
	$resultset1=$this->db->query($sql1);
	$res1=$resultset1->result();
	foreach($res1 as $cls){}
	$cname=$cls->class_name;
	$sename=$cls->sec_name;

    $textmessage='This is to inform you that as '.$tname.' is on leave,'.$sub_tname.' will be the substitute teacher to fill in for '.$cname.'-'.$sename.',period ('.$period_id.') on '.$leave_date.' ';

//$textmessage='This is to inform you that as '.$tname.' is on leave, '.$sub_tname.' will be the substitute teacher to fill in for '.$leave_date.' class & section ('.$cname.'-'.$sename.') period ('.$period_id.') day/s.';

	 $textmsg =urlencode($textmessage);

	$smsGatewayUrl = 'http://173.45.76.227/send.aspx?';

	$api_element = 'username=kvmhss&pass=kvmhss123&route=trans1&senderid=KVMHSS';

    $api_params = $api_element.'&numbers='.$num.'&message='.$textmsg;

	$smsgatewaydata = $smsGatewayUrl.$api_params;

	$url = $smsgatewaydata;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST, false);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch);
	curl_close($ch);

	if(!$output)
	{
      $output =  file_get_contents($smsgatewaydata);
    }
}

  function send_circular_via_sms($title,$notes,$tusers_id,$stusers_id,$pusers_id,$users_id)
  {
	 //-----------------------------Teacher----------------------
		   //echo'hi'; print_r($tusers_id);
			 if($tusers_id!='')
			 {
			     $countid=count($tusers_id);
			     //echo $countid;
				 for ($i=0;$i<$countid;$i++)
				 {
					$userid=$tusers_id[$i];
					$cnotes=$notes;
					$ctitle=$title;

					$sql="SELECT u.user_id,u.user_type,u.user_master_id,t.teacher_id,t.name,t.phone FROM edu_users AS u,edu_teachers AS t WHERE u.user_id='$userid' AND u.user_type='2' AND u.user_master_id=t.teacher_id";
					$tcell=$this->db->query($sql);
					$res=$tcell->result();
					foreach($res as $row)
					{ } $number=$row->phone;

						$textmessage=$cnotes;

						$textmsg =urlencode($textmessage);

						$smsGatewayUrl = 'http://173.45.76.227/send.aspx?';

						$api_element = 'username=kvmhss&pass=kvmhss123&route=trans1&senderid=KVMHSS';

						$api_params = $api_element.'&numbers='.$number.'&message='.$textmsg;

						$smsgatewaydata = $smsGatewayUrl.$api_params;

						$url = $smsgatewaydata;

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_POST, false);
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$output = curl_exec($ch);
						curl_close($ch);
                }
				/* if(!$output)
				{
				  $output =  file_get_contents($smsgatewaydata);
				} */
             }
			 //-----------------------------Students----------------------
		     //print_r($stusers_id);
			 if($stusers_id!='')
			 {
			     $scountid=count($stusers_id);
			      //echo $scountid; exit;
				 for ($i=0;$i<$scountid;$i++)
				 {
					$clsid=$stusers_id[$i];
					$cnotes=$notes;
					//$ctitle=$title;

					 $sql1="SELECT e.enroll_id,e.admission_id,e.admisn_no,e.name,e.class_id,a.admission_id,a.admisn_no,a.name,a.mobile FROM edu_enrollment AS e,edu_admission AS a WHERE e.class_id='$clsid' AND e.admission_id=a.admission_id ";
					$scell=$this->db->query($sql1);
					$res1=$scell->result();
					foreach($res1 as $row1)
					{
       					$snumber=$row1->mobile;
					   //echo $snumber;exit;
						$textmessage=$cnotes;

						$textmsg =urlencode($textmessage);

						$smsGatewayUrl = 'http://173.45.76.227/send.aspx?';

						$api_element = 'username=kvmhss&pass=kvmhss123&route=trans1&senderid=KVMHSS';

						$api_params = $api_element.'&numbers='.$snumber.'&message='.$textmsg;

						$smsgatewaydata = $smsGatewayUrl.$api_params;

						$url = $smsgatewaydata;

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_POST, false);
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$output = curl_exec($ch);
						curl_close($ch);
                  }
				 /*  if(!$output)
						{
						  $output =  file_get_contents($smsgatewaydata);
						} */
				}

             }

	 //-----------------------------Parents----------------------
		  print_r($pusers_id);
		  if($pusers_id!='')
		  {
			 $pcountid=count($pusers_id);
			 //echo $pcountid;exit;
			 for ($i=0;$i<$pcountid;$i++)
			 {
				$classid=$pusers_id[$i];
				$cnotes=$notes;
				//$ctitle=$title;

				 $pgid="SELECT e.enroll_id,e.admission_id,e.admisn_no,e.name,e.class_id FROM edu_enrollment AS e WHERE e.class_id='$classid'";
				 $pcell=$this->db->query($pgid);
				 $res2=$pcell->result();
				 foreach($res2 as $row2)
				 {
				     $stuid=$row2->admission_id;
				    $class="SELECT id,mobile,admission_id,primary_flag FROM edu_parents WHERE FIND_IN_SET('$stuid',admission_id) AND primary_flag='Yes'";
    				  $pcell1=$this->db->query($class);
    				  $res3=$pcell1->result();
					foreach($res3 as $row3)
					{
       					$pnumber=$row3->mobile;
					   //echo $pnumber;exit;
						$textmessage=$cnotes;

						$textmsg =urlencode($textmessage);

						$smsGatewayUrl ='http://173.45.76.227/send.aspx?';

						$api_element ='username=kvmhss&pass=kvmhss123&route=trans1&senderid=KVMHSS';

						$api_params = $api_element.'&numbers='.$pnumber.'&message='.$textmsg;

						$smsgatewaydata = $smsGatewayUrl.$api_params;

						$url = $smsgatewaydata;

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_POST, false);
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$output = curl_exec($ch);
						curl_close($ch);
                  }
				 }
				 /*  if(!$output)
						{
						  $output =  file_get_contents($smsgatewaydata);
						} */
				}
		  }


		  //------------------------------Admin-----------------------
			if($users_id!='')
			{
				//------------------------Teacher----------------------
				if($users_id==2)
				{
				 //echo $users_id;
				    $cnotes=$notes;
				//	$ctitle=$title;
					$tsql="SELECT u.user_id,u.user_type,u.user_master_id,t.teacher_id,t.name,t.phone FROM edu_users AS u,edu_teachers AS t  WHERE u.user_type='$users_id' AND u.user_master_id=t.teacher_id AND u.status='Active'";
					$res=$this->db->query($tsql);
					$result1=$res->result();
					foreach($result1 as $rows)
					{
					   $tcell=$rows->phone;

					   //echo $tcell;exit;
						$textmessage=$cnotes;

						$textmsg =urlencode($textmessage);

						$smsGatewayUrl = 'http://173.45.76.227/send.aspx?';

						$api_element = 'username=kvmhss&pass=kvmhss123&route=trans1&senderid=KVMHSS';

						$api_params = $api_element.'&numbers='.$tcell.'&message='.$textmsg;

						$smsgatewaydata = $smsGatewayUrl.$api_params;

						$url = $smsgatewaydata;

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_POST, false);
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$output = curl_exec($ch);
						curl_close($ch);
				  }
				}

				//---------------------------Students----------------------
				if($users_id==3)
				{
				   //echo $users_id;
				    $cnotes=$notes;
					$ctitle=$title;
					$ssql="SELECT u.user_id,u.user_type,u.user_master_id,u.name,a.admission_id,a.name,a.mobile FROM edu_users AS u,edu_admission AS a  WHERE u.user_type='$users_id' AND u.user_master_id=a.admission_id AND u.name=a.name AND u.status='Active'";
					$res2=$this->db->query($ssql);
					$result2=$res2->result();
					foreach($result2 as $rows1)
					{
					   $scell=$rows1->mobile;

					   //echo $tcell;exit;
						$textmessage=$cnotes;

						$textmsg =urlencode($textmessage);

						$smsGatewayUrl = 'http://173.45.76.227/send.aspx?';

						$api_element = 'username=kvmhss&pass=kvmhss123&route=trans1&senderid=KVMHSS';

						$api_params = $api_element.'&numbers='.$scell.'&message='.$textmsg;

						$smsgatewaydata = $smsGatewayUrl.$api_params;

						$url = $smsgatewaydata;

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_POST, false);
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$output = curl_exec($ch);
						curl_close($ch);

				}
				}

					//---------------------------Parents--------------------------------------------
				if($users_id==4)
				{
				   //echo $users_id;
				    $cnotes=$notes;
					$ctitle=$title;
					$psql="SELECT u.user_id,u.user_type,u.user_master_id,u.name,p.id,p.mobile FROM edu_users AS u,edu_parents AS p WHERE u.user_type='$users_id' AND u.user_master_id=p.id AND u.status='Active'";
					$pres2=$this->db->query($psql);
					$presult2=$pres2->result();
					foreach($presult2 as $prows1)
					{
					   $pcell=$prows1->mobile;

					   //echo $tcell;exit;
						$textmessage=$cnotes;

						$textmsg =urlencode($textmessage);

						$smsGatewayUrl = 'http://173.45.76.227/send.aspx?';

						$api_element = 'username=kvmhss&pass=kvmhss123&route=trans1&senderid=KVMHSS';

						$api_params = $api_element.'&numbers='.$pcell.'&message='.$textmsg;

						$smsgatewaydata = $smsGatewayUrl.$api_params;

						$url = $smsgatewaydata;

						$ch = curl_init();
						curl_setopt($ch, CURLOPT_POST, false);
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$output = curl_exec($ch);
						curl_close($ch);

				}
				}

			}


		}

         //DOB Wisher for users_dob_wishes

         function student_dob_wishes($cur_date){
           $query="SELECT ee.name,ea.dob,ea.mobile FROM edu_enrollment AS ee LEFT JOIN edu_admission AS ea ON ee.admission_id=ea.admission_id WHERE ee.status='Active' AND CONCAT(YEAR(CURDATE()),DATE_FORMAT(ea.dob,'-%m-%d')) = '$cur_date' AND ee.status='Active'";
           $result=$this->db->query($query);
           $res=$result->result();
           foreach($res as $rows){
             $name=$rows->name;
             $number=$rows->mobile;
             $textmessage='Wishing you a Birthday filled with joy and a year filled with happiness and good health Happy Birthday '.$name.'';
           	 $textmsg =urlencode($textmessage);
           	 $smsGatewayUrl = 'http://173.45.76.227/send.aspx?';
             $api_element = 'username=kvmhss&pass=kvmhss123&route=trans1&senderid=KVMHSS';
             $api_params = $api_element.'&numbers='.$number.'&message='.$textmsg;
           	 $smsgatewaydata = $smsGatewayUrl.$api_params;
           	 $url = $smsgatewaydata;

           	$ch = curl_init();
           	curl_setopt($ch, CURLOPT_POST, false);
           	curl_setopt($ch, CURLOPT_URL, $url);
           	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           	$output = curl_exec($ch);
           	curl_close($ch);

           	if(!$output)
           	{
                 $output =  file_get_contents($smsgatewaydata);
               }


           }
         }

         //  Teacher DOB WIshes
         function teacher_dob_wishes($cur_date){
          $query1="SELECT name,phone FROM edu_teachers WHERE CONCAT(YEAR(CURDATE()),DATE_FORMAT(dob,'-%m-%d')) = '$cur_date' AND status='Active'";
          $result1=$this->db->query($query1);
          $res=$result1->result();
          foreach($res as $rows){
            $name=$rows->name;
            $number=$rows->phone;
            $textmessage='Wishing you a Birthday filled with joy and a year filled with happiness and good health Happy Birthday '.$name.'';
            $textmsg =urlencode($textmessage);
            $smsGatewayUrl = 'http://173.45.76.227/send.aspx?';
            $api_element = 'username=kvmhss&pass=kvmhss123&route=trans1&senderid=KVMHSS';
            $api_params = $api_element.'&numbers='.$number.'&message='.$textmsg;
            $smsgatewaydata = $smsGatewayUrl.$api_params;
            $url = $smsgatewaydata;

           $ch = curl_init();
           curl_setopt($ch, CURLOPT_POST, false);
           curl_setopt($ch, CURLOPT_URL, $url);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           $output = curl_exec($ch);
           curl_close($ch);

           if(!$output)
           {
                $output =  file_get_contents($smsgatewaydata);
              }


          }

         }


    //
    //     //  Group  SMS
    //     function send_msg($group_id,$notes,$user_id)
		// {
    //      $class="SELECT egm.group_member_id,ep.email,ep.mobile FROM edu_grouping_members AS egm LEFT JOIN edu_users AS eu ON eu.user_id=egm.group_member_id LEFT JOIN edu_admission AS ea ON ea.admission_id=eu.user_master_id LEFT JOIN edu_parents AS ep ON FIND_IN_SET(ea.admission_id,ep.admission_id) WHERE  egm.group_title_id='$group_id' and ep.mobile <>''";
    //
    //     $pcell=$this->db->query($class);
    //       $res2=$pcell->result();
    //       foreach($res2 as $result){
    //          $number=$result->mobile;
    //         $textmessage=$notes;
    //         $textmsg =urlencode($textmessage);
    //         $smsGatewayUrl = 'http://173.45.76.227/send.aspx?';
    //         $api_element = 'username=kvmhss&pass=kvmhss123&route=trans1&senderid=KVMHSS';
    //         $api_params = $api_element.'&numbers='.$number.'&message='.$textmsg;
    //         $smsgatewaydata = $smsGatewayUrl.$api_params;
    //
    //        $url = $smsgatewaydata;
    //
    //
    //        $ch = curl_init();
    //        curl_setopt($ch, CURLOPT_POST, false);
    //        curl_setopt($ch, CURLOPT_URL, $url);
    //        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //        $output = curl_exec($ch);
    //        curl_close($ch);
    //
    //        if(!$output)
    //        {
    //             $output =  file_get_contents($smsgatewaydata);
    //           }
    //       }
    //     }


     function send_msg($group_id,$notes,$user_id){
       //Your authentication key
      $authKey = "191431AStibz285a4f14b4";

      //Multiple mobiles numbers separated by comma
      $class="SELECT egm.group_member_id,ep.email,ep.mobile FROM edu_grouping_members AS egm LEFT JOIN edu_users AS eu ON eu.user_id=egm.group_member_id LEFT JOIN edu_admission AS ea ON ea.admission_id=eu.user_master_id LEFT JOIN edu_parents AS ep ON FIND_IN_SET(ea.admission_id,ep.admission_id) WHERE  egm.group_title_id='$group_id' and ep.mobile <>''";
      $pcell=$this->db->query($class);
      $res2=$pcell->result();
      foreach($res2 as $rows){ $mobile[]=$rows->mobile;}
      //$Phoneno[]=$rows->mobile;
       $mobileNumber=implode(',',$mobile);

      //Sender ID,While using route4 sender id should be 6 characters long.
      $senderId = "HEYLAA";

      //Your message to send, Add URL encoding here.
      $message = urlencode($notes);

      //Define route
      $route = "transactional";

      //Prepare you post parameters
      $postData = array(
          'authkey' => $authKey,
          'mobiles' => $mobileNumber,
          'message' => $message,
          'sender' => $senderId,
          'route' => $route
      );

      //API URL
      $url="https://control.msg91.com/api/sendhttp.php";

      // init the resource
      $ch = curl_init();
      curl_setopt_array($ch, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_POST => true,
          CURLOPT_POSTFIELDS => $postData
          //,CURLOPT_FOLLOWLOCATION => true
      ));


      //Ignore SSL certificate verification
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


      //get response
      $output = curl_exec($ch);

      //Print error if any
      if(curl_errno($ch))
      {
          echo 'error:' . curl_error($ch);
      }

      curl_close($ch);
     }

		// Home Work SMS

		function send_sms_homework($user_id,$user_type,$createdate,$clssid)
		{
		   $year_id=$this->getYear();

		   $pcell="SELECT p.mobile FROM edu_parents AS p,edu_enrollment AS e WHERE e.class_id='$clssid' AND FIND_IN_SET( e.admission_id,p.admission_id) GROUP BY p.name";
		  $pcell1=$this->db->query($pcell);
		  $pcel2=$pcell1->result();
		  foreach($pcel2 as $res)
		  {  $cell[]=$res->mobile;
		     //echo $num=implode(',',$cell); echo"<br>";
			}
		  $sms="SELECT h.title,h.hw_details,h.hw_type,h.test_date,s.subject_name FROM edu_homework AS h,edu_subject AS s WHERE h.class_id='$clssid' AND h.year_id='$year_id' AND DATE_FORMAT(h.created_at,'%Y-%m-%d')='$createdate' AND h.subject_id=s.subject_id";
		  $sms1=$this->db->query($sms);
		  $sms2= $sms1->result();
		  //return $sms2;
		  foreach ($sms2 as $value)
          {
            $hwtitle=$value->title;
		    $hwdetails=$value->hw_details;
			$subname=$value->subject_name;
			$ht=$value->hw_type;
			$tdat=$value->test_date;

			if($ht=='HW'){ $type="Home Work" ; }else{ $type="Class Test" ; }

			$message="Title : " .$hwtitle. ",Type : " .$type. ", Details : " .$hwdetails .", Subject : ".$subname.",";
			$home_work_details[]=$message;
		  }
			//print_r($home_work_details);
		    $hdetails=implode('',$home_work_details);
			$num=implode(',',$cell);
			$count1=count($cell);

				$textmsg =urlencode($hdetails);
				$smsGatewayUrl = 'http://173.45.76.227/send.aspx?';
				$api_element = 'username=kvmhss&pass=kvmhss123&route=trans1&senderid=KVMHSS';
				$api_params = $api_element.'&numbers='.$num.'&message='.$textmsg;
				$smsgatewaydata = $smsGatewayUrl.$api_params;

				$url = $smsgatewaydata;

			   $ch = curl_init();
			   curl_setopt($ch, CURLOPT_POST, false);
			   curl_setopt($ch, CURLOPT_URL, $url);
			   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			   $output = curl_exec($ch);
			   curl_close($ch);

			   if(!$output)
			   {
				  $output =  file_get_contents($smsgatewaydata);
			   }else{  $data= array("status"=>"success");
		      return $data; }
	}



      function send_sms_attendance($attend_id){
         $query="SELECT ee.name,ep.mobile,ee.admission_id,eah.abs_date,eah.student_id,eah.a_status,eah.attend_period,
         CASE WHEN attend_period = 0 THEN 'MORNING'  ELSE 'AFTERNOON' END  AS a_session,CASE WHEN a_status = 'L' THEN 'Leave' WHEN a_status = 'A' THEN 'Absent' ELSE 'OnDuty' END  AS abs_atatus FROM edu_attendance_history AS eah LEFT JOIN edu_enrollment AS ee ON ee.enroll_id=eah.student_id LEFT JOIN edu_parents AS ep ON ee.admission_id=ep.admission_id WHERE
          eah.attend_id='$attend_id' AND ep.primary_flag='Yes'";

        $result=$this->db->query($query);
        $res=$result->result();
        foreach($res as $rows){
           $st_name=$rows->name;
           $parents_num=$rows->mobile;
           $at_ses=$rows->a_session;
           $abs_date=$rows->abs_date;
           $abs_status=$rows->abs_atatus;

           $textmessage='Your child '.$st_name.' was marked '.$abs_status.' ON '.$abs_date.'. To Known more details login into http://bit.ly/2wLwdRQ';

          $textmsg =urlencode($textmessage);
          $smsGatewayUrl = 'http://173.45.76.227/send.aspx?';
          $api_element = 'username=kvmhss&pass=kvmhss123&route=trans1&senderid=KVMHSS';
          $api_params = $api_element.'&numbers='.$parents_num.'&message='.$textmsg;
          $smsgatewaydata = $smsGatewayUrl.$api_params;
          $url = $smsgatewaydata;

         $ch = curl_init();
         curl_setopt($ch, CURLOPT_POST, false);
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         $output = curl_exec($ch);
         curl_close($ch);

         if(!$output)
         {
              $output =  file_get_contents($smsgatewaydata);
            }

        }
      }


}
	  ?>
