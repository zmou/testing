<?php

class statistics extends Controller {

	function statistics()
	{
		parent::Controller();
	}
	function index( $ref = NULL )
	{
		$this->save_view_infomation();

		if( c('statistics_open') )
		{
			if( is_login() )
			{
				$data['uid'] = format_uid();
			}
			else
			{
				$data['uid'] = 0;
			}
			$this->load->database();
			$data['ip'] = $this->input->ip_address();
			$this->db->select('*')->from('u2_statistics')->where('ip',$data['ip'])->where('date',date("Y-m-d") )->limit(1);
			if( !lazy_get_line() )
			{
				$this->add_ip_count( date("Y-m-d") );
			}
			$data['agent'] = $this->input->user_agent();	
			$data['ref'] = base64_decode($ref);
			$data['uri'] = $this->input->server('HTTP_REFERER');
			$data['date'] = date("Y-m-d");
			
			$this->db->insert( 'u2_statistics' , $data );
		}
		$this->feedig_auto_update();
	}
	private function add_ip_count( $date )
	{
		$data = lazy_get_data("select * from `u2_statistics_res`");
		foreach( $data as $v )
		{
			$array[$v['key']] = $v['value'];
		}
		if( isset( $array['ip_date'] ) && $array['ip_date'] == $date )
		{
			if( isset( $array['ip_count'] ) )
			{
				lazy_run_sql("update `u2_statistics_res` set `value` = `value` + 1 where `key` = 'ip_count' ");
			}
			else
			{
				lazy_run_sql("insert into `u2_statistics_res` (`key`,`value`)values('ip_count','1')");
			}
		}
		else
		{
			lazy_run_sql("delete from `u2_statistics_res` where `key` = 'ip_count' OR `key` = 'ip_date' ");
			lazy_run_sql("insert into `u2_statistics_res` (`key`,`value`)values('ip_count','1'),('ip_date','$date')");
		}
	}
	private function save_view_infomation()
	{
		if(is_login())
		{
			$new['time'] = time(); 
			$new['date'] =  date("Y-m-d");
			if( _sess('statistics') )
			{
				$statistics = _sess('statistics');
				$staytime = time() - $statistics['time'];
				$secondes = intval( c('online_seconds') ) < 60 ?300:intval( c('online_seconds') ) ;
				if( $statistics['date'] == date("Y-m-d") && $staytime > 60 && $staytime < $secondes )
				{
					if( _sess('online_date') == $statistics['date'] )
					{
						lazy_run_sql("update `u2_user` set `online_today` = `online_today` + $staytime , `onlinetime` = `onlinetime` + $staytime where `id` = '".format_uid()."' limit 1");
					}
					else
					{
						lazy_run_sql("update `u2_user` set `online_date` = '".date("Y-m-d")."' , `online_today` =  $staytime , `onlinetime` = `onlinetime` + $staytime where `id` = '".format_uid()."' limit 1");
						$res['online_date'] = date("Y-m-d");
					}
				}
				elseif(  $statistics['date'] == date("Y-m-d") && $staytime < 60  )
				{
					$new = $statistics;
				}
			}
			$res['statistics'] = $new; 
			set_sess( $res );
			$this->load->database();
			$uid = format_uid();
			$data['u2_stay_time'] = date("Y-m-d H:i:s");
			$data['u2_stay_location'] = $_SERVER['REQUEST_URI'];
			$data['u2_sid'] = _sess('session_id');
			$data['u2_uid'] = $uid;
			$this->db->select('count(*)')->from('u2_online')->where('u2_uid',$uid);
			if( lazy_get_var() )
			{
				$this->db->where('u2_uid',$uid);
				$this->db->update('u2_online',$data);
			}
			else
			{
				$this->db->insert('u2_online',$data);
			}
		
		}
	}
	
	private function feedig_auto_update()
	{
		$ifeed = lazy_get_line("SELECT * FROM `u2_app` WHERE `aid` = 'ifeedig' LIMIT 1");
		if( $ifeed )
		{
			$ftime = file_get_contents( dirname(__FILE__).'/feed_auto.txt' );
			if( $ftime )
			{
				$time = date( 'Y-m-d H:i:s' , time() - app_config('update_time' , $ifeed['u2_folder']) );
				if( $time > $ftime )
				{
					$info = lazy_get_data("SELECT * FROM `app_feed`");
					foreach( $info as $k => $v )
					{
						$feed = $v['feed'];
						$tid = $v['tid'];
						$id = $v['id'];
						$state = $v['state'];
						$fuid = $v['uid'];
						
						if( !empty($feed) )
						{
							$CI =&get_instance();
							$CI->load->library('simplepie');
							$CI->simplepie->set_feed_url( $feed ); 
							$CI->simplepie->init();
							$items = $CI->simplepie->get_items();

							foreach( $items as $item )
							{
								$title = $item->get_title(); //
								$desp = $item->get_content();//
								$link = $item->get_link();
								$date = date('Y-m-d H:i:s' , strtotime($item->get_date()) );
								$unistring = md5( $link ).$date;
								$itid = lazy_get_var("SELECT `tid` FROM `app_feed_item` WHERE `unistring` = '".$unistring."'");
								if( $itid > 0 )
								{
									//update
									$sql  = "UPDATE `app_feed_item` SET `tid` = '".intval($itid)."',";
									$sql .= " `fid` = '".intval($id)."', `title` = ".s($title).",";
									$sql .= "`desp` = ".s($desp).",`time` = ".s($date).", `link` = ".s($link)."";
									$sql .= "WHERE `unistring` = '".$unistring."' LIMIT 1 ";

									lazy_run_sql( $sql );
								}
								else
								{
									//insert
									$sql  = "INSERT INTO `app_feed_item` (`tid`, `fid`, `title`,";
									$sql .= " `desp`, `time`, `link`, `state`, `unistring`, `admin_uid`)";
									$sql .= "VALUES ('".intval($tid)."', '".intval($id)."', ".s($title).",";
									$sql .= " ".s($desp).", ".s($date)." , ".s($link).",";
									$sql .= "'".$state."', ".s($unistring).", '".intval($fuid)."')";

									lazy_run_sql( $sql );
								}
								
								lazy_run_sql("UPDATE `app_feed` SET `time` = '".date('Y-m-d H:i:s')."' WHERE `id` = '".$id."'");
							}
						}	
					}				
					file_put_contents( dirname(__FILE__).'/feed_auto.txt' , date( 'Y-m-d H:i:s' ) );
				}
			}
			else
			{
				file_put_contents( dirname(__FILE__).'/feed_auto.txt' , date( 'Y-m-d H:i:s' ) );
			}
		}
	}
}