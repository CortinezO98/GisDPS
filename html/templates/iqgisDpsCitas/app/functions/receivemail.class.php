<?php 
// Main ReciveMail Class File - Version 1.2 (10-07-2015) 
/* 
 * File: receivemail.class.php 
 * Description: Reciving mail With Attechment 
 * Version: 1.2 
 * Created: 01-03-2006 
 * Modified: 10-07-2015 
 * Author: Mitul Koradia 
 * Email: mitulkoradia@gmail.com 
 * Cell : +91 9825273322 
 */ 
  
/***************** Changes *********************** 
* 
* 01-03-2006 - Added feature to retrive embedded attachment - Changes provided by. Antti <anttiantti83@gmail.com> 
* 02-06-2009 - Added SSL Supported mailbox. 
* 10-07-2015 - Converted and optimised to PHP 5 standard.
* 
**************************************************/ 

class ReceiveMail { 
    private $protocol= 'imap'; 
	private $hostname= 'imap.gmail.com';
	private $port = 993;
	private $username= 'your-email@gmail.com';
	private $password= 'your-password';
	private $ssl = true;
	private $novalidate = false;
    private $buzon= '';
	
    protected $marubox=''; 
    protected $is_connected = false;
	protected $error_msg = array();
	
	public function __construct($host=null,$user=null,$pass=null,$protocol=null,$port=null,$ssl=null,$novalidate=null,$bandeja=null) {
		$this->hostname = (is_null($host) ? $this->hostname : $host);
		$this->username = (is_null($user) ? $this->username : $user);
		$this->password = (is_null($pass) ? $this->password : $pass);
		$this->protocol = (is_null($protocol) ? $this->protocol : $protocol);
		$this->port = (is_null($port) ? $this->port : $port);
		$this->ssl = (is_null($ssl) ? $this->ssl : $ssl);
		$this->novalidate = (is_null($novalidate) ? $this->novalidate : $novalidate);
        $this->bandeja = (is_null($bandeja) ? $this->buzon : $bandeja);
	}
     
    public function connect() {//Connect To the Mail Box 
		$con = '{'.$this->hostname.':'.$this->port.'/'.$this->protocol.($this->ssl?'/ssl':'').($this->novalidate?'/novalidate-cert':'').'}'.$this->bandeja;
        $this->marubox=@imap_open($con,$this->username,$this->password) or die('Can not connect to '.$this->hostname.' on port '.$this->port.': '.@imap_last_error()); 
        
        if($this->marubox) { 
			$this->is_connected = true;
			return true;
		}
		return false;
    }

	public function is_connected() {
		return $this->is_connected;
	}

    public function get_email_header($mid=null) {// Get Header info 
        if(!$this->is_connected || is_null($mid)) 
            return false; 

        $mail_header=imap_headerinfo($this->marubox,$mid);
		//echo imap_fetchheader($this->marubox,$mid);
    
        $sender=$mail_header->from[0]; 
        $sender_replyto=$mail_header->reply_to[0]; 
        $sendercc=$mail_header->cc; 
		$mail_details = array();
        if(strtolower($sender->mailbox)!='mailer-daemon' && strtolower($sender->mailbox)!='postmaster') 
        { 
            $sendercc=array();
            $cc_final="";
            for ($i=0; $i < count($sendercc); $i++) { 
                $cc_final.=$sendercc[$i]->mailbox."@".$sendercc[$i]->host.";";
            }
            $mail_details=array( 
				'datetime'=>date("Y-m-d H:i:s",$mail_header->udate),
				'from'=>strtolower($sender->mailbox).'@'.$sender->host, 
				'fromName'=>@$sender->personal, 
				'replyTo'=>strtolower($sender_replyto->mailbox).'@'.$sender_replyto->host, 
				'replyToName'=>@$sender_replyto->personal, 
				'subject'=>iconv_mime_decode($mail_header->subject,0, "utf-8"),
                'to'=>strtolower($mail_header->toaddress),
				'cc'=>strtolower($cc_final),
                'msgid'=>$mail_header->Msgno,
                'msguid'=>imap_uid($this->marubox, $mid)
				// 'full_header'=>serialize($mail_header)
			); 
        } 
        return $mail_details; 
    } 
	public function get_unread_emails() {
		if(strtolower($this->protocol) != 'imap') {
			echo "Warning: The function get_unread_emails will not work on '".$this->protocol."' Protocol";
			return false;
		}
		if(!$this->is_connected) 
            return false;
		
		$result = imap_search($this->marubox, 'UNSEEN');
		return $result;
	}
	public function get_total_emails() { 
        if(!$this->is_connected) 
            return false; 

		return imap_num_msg($this->marubox); 
    } 

    public function get_email_body($mid=null,$format='html') { 
        if(!$this->is_connected || is_null($mid)) 
            return false; 
		
		$body = "";
		
		if(strtolower($format) == 'html')
			$body = $this->get_part($this->marubox, $mid, "TEXT/HTML"); 
        
		if ($body == "") 
            $body = $this->get_part($this->marubox, $mid, "TEXT/PLAIN"); 
        if ($body == "") { 
            return ""; 
        } 
        return $body; 
    } 

    public function move_email($mid=null, $folder=null) { 
        if(!$this->is_connected || is_null($mid) || $folder=="") 
            return false; 
        $msuid=imap_uid($this->marubox, $mid);
        $respuesta=imap_mail_move($this->marubox, $msuid, $folder, CP_UID);
        imap_expunge($this->marubox);
        
    } 

	public function get_attachments_body($mid=null) { 
        if(!$this->is_connected || is_null($mid)) 
            return false; 

        $structure = imap_fetchstructure($this->marubox,$mid); 
        $attachments_body = array();
                            
        if(isset($structure->parts[0]->parts) && count($structure->parts[0]->parts)>0) {
            for($j = 0; $j < count($structure->parts[0]->parts); $j++) {
               $attachments_body[$j] = array(
                  'is_attachment' => false,
                  'filename' => '',
                  'name' => '',
                  'attachment' => '');

               if($structure->parts[0]->parts[$j]->ifdparameters) {
                 foreach($structure->parts[0]->parts[$j]->dparameters as $object) {
                   if(strtolower($object->attribute) == 'filename') {
                     $attachments_body[$j]['is_attachment'] = true;
                     $attachments_body[$j]['filename'] = imap_utf8($object->value);

                   }
                 }
               }

               if($structure->parts[0]->parts[$j]->ifparameters) {
                 foreach($structure->parts[0]->parts[$j]->parameters as $object) {
                   if(strtolower($object->attribute) == 'name') {
                     $attachments_body[$j]['is_attachment'] = true;
                     $attachments_body[$j]['name'] = imap_utf8($object->value);
                   }
                 }
               }
               
               if($attachments_body[$j]['is_attachment']) {
                 $id_imagen="1.".($j+1);
                 $attachments_body[$j]['attachment'] = imap_fetchbody($this->marubox, $mid, $id_imagen);
                 if($structure->parts[0]->parts[$j]->encoding == 3) { // 3 = BASE64
                   $attachments_body[$j]['attachment'] = base64_decode($attachments_body[$j]['attachment']);
                 }
                 elseif($structure->parts[0]->parts[$j]->encoding == 4) { // 4 = QUOTED-PRINTABLE
                   $attachments_body[$j]['attachment'] = quoted_printable_decode($attachments_body[$j]['attachment']);
                 }
               } 
            }
        } 
        return $attachments_body; 
    }
    public function get_attachments($mid=null) { 
        if(!$this->is_connected || is_null($mid)) 
            return false; 

        $structure = imap_fetchstructure($this->marubox,$mid); 
        $attachments = array();
        if(isset($structure->parts) && count($structure->parts)) {
             for($j = 0; $j < count($structure->parts); $j++) {
               $attachments[$j] = array(
                  'is_attachment' => false,
                  'filename' => '',
                  'name' => '',
                  'attachment' => '');

               if($structure->parts[$j]->ifdparameters) {
                 foreach($structure->parts[$j]->dparameters as $object) {
                   if(strtolower($object->attribute) == 'filename') {
                     $attachments[$j]['is_attachment'] = true;
                     $attachments[$j]['filename'] = imap_utf8($object->value);

                   }
                 }
               }

               if($structure->parts[$j]->ifparameters) {
                 foreach($structure->parts[$j]->parameters as $object) {
                   if(strtolower($object->attribute) == 'name') {
                     $attachments[$j]['is_attachment'] = true;
                     $attachments[$j]['name'] = imap_utf8($object->value);
                   }
                 }
               }
               
               if($attachments[$j]['is_attachment']) {
                 $attachments[$j]['attachment'] = imap_fetchbody($this->marubox, $mid, $j+1);
                 if($structure->parts[$j]->encoding == 3) { // 3 = BASE64
                   $attachments[$j]['attachment'] = base64_decode($attachments[$j]['attachment']);
                 }
                 elseif($structure->parts[$j]->encoding == 4) { // 4 = QUOTED-PRINTABLE
                   $attachments[$j]['attachment'] = quoted_printable_decode($attachments[$j]['attachment']);
                 }
               } 
             }
        }
                            
         
        return $attachments; 
    } 
    public function delete_email($mid=null) { 
        if(!$this->is_connected || is_null($mid)) 
            return false; 
    
        imap_delete($this->marubox,$mid); 
    } 
	public function markas_read_email($mid=null) {
		if(strtolower($this->protocol) != 'imap') {
			echo "Warning: The function markas_read_email will not work on '".$this->protocol."' Protocol";
		}
		if(!$this->is_connected || is_null($mid)) 
            return false; 
		
		$status = imap_setflag_full($this->marubox, $mid, "\Seen");
		return $status;
	}
    public function close_mailbox() { 
        if(!$this->is_connected) 
            return false; 
		
		imap_expunge($this->marubox);
        imap_close($this->marubox,CL_EXPUNGE); 
    } 
    private function get_mime_type(&$structure) { //Get Mime type Internal Private Use 
        $primary_mime_type = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER"); 
        
        if($structure->subtype) { 
            return $primary_mime_type[(int) $structure->type] . '/' . $structure->subtype; 
        } 
        return "TEXT/PLAIN"; 
    } 
    private function get_part($stream, $msg_number, $mime_type, $structure = false, $part_number = false) //Get Part Of Message Internal Private Use 
    { 
        if(!$structure) { 
            $structure = imap_fetchstructure($stream, $msg_number); 
        } 
        if($structure) { 
            if($mime_type == $this->get_mime_type($structure)) 
            { 
                if(!$part_number) 
                { 
                    $part_number = "1"; 
                } 
                $text = imap_fetchbody($stream, $msg_number, $part_number); 
				
				if($structure->encoding == 1) {
					return imap_utf8($text);
				}
                else if($structure->encoding == 3) 
                { 
                    return imap_base64($text); 
                } 
                else if($structure->encoding == 4) 
                { 
                    return imap_qprint($text); 
                } 
                else 
                { 
                    return $text; 
                } 
            } 
            if($structure->type == 1) /* multipart */ 
            { 

                // echo "<pre>";
                // print_r($structure);
                // print_r($structure->parts);
                // echo "</pre>";
                
                foreach($structure->parts as $index => $sub_structure) {

                // while(list($index, $sub_structure) = each($structure->parts)) 
                 
					$prefix='';
                    if($part_number) 
                    { 
                        $prefix = $part_number . '.'; 
                    } 
                    $data = $this->get_part($stream, $msg_number, $mime_type, $sub_structure, $prefix . ($index + 1)); 
                    if($data) 
                    { 
                        return $data; 
                    } 
                } 
            } 
        } 
        return false; 
    } 
} 
?>