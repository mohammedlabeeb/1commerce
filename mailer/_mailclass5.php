<?php
/*
Author: Stuart Cochrane
URL: www.freecontactform.com
Email: stuartc1@gmail.com
Date: April 2011
Version: 1.6 Professional

License:

Copyright (c) 2011 Stuart Cochrane <stuartc1@gmail.com>

Permission is hereby granted, to any person legally purchasing a copy
of this software and associated documentation files (the "Software"), to deal
in the Software with little restriction, including the rights to use, copy,
modify, merge and publish the Software, subject to the following conditions:

A. The copyright, permission and conditional notices shall be included in
   all copies or substantial portions of the Software.

B. Single license holder can use this Software on a single Licensed Domain only.
	 This includes sub-domains (of Licensed Domain).

C. You may not convey/distribute this software to any third party without
   express permission from the Copyright Holder/Author.

D. You can surrender you license to a third party providing you also surrender
   the licensed Domain name to the same party.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/
class createTheMail {
	public $iso = 'iso-8859-1';
	public $linebreak = "\r\n";
	public $from = '';
	public $from_name = '';
	public $reply_to = '';
	public $to = '';
	public $subject = '';
	public $message = '';
	public $bcc = '';
	public $cc = '';
	private $uid = '';
	private $attachment_data = '';
	private $additional_param = '';

	public function __construct($ap) {
		$this->uid = md5(uniqid(time()));
		$this->additional_param = $ap;
	}

	public function setBcc($name,$email) {
		if(strlen(trim($this->bcc)) == 0) {
			$this->bcc = $email;
		} else {
			$this->bcc += ','.$email;
		}
	}

	public function setCc($name,$email) {
		if(strlen(trim($this->cc)) == 0) {
			$this->cc = $email;
		} else {
			$this->cc += ','.$email;
		}
	}

	public function addAttachment($file, $filename, $type) {
		if($type == "") { $type = "application/octet-stream"; }
		$filename_actual = basename($file);
		$filename_new = empty($filename) ? basename($file) : $filename;
		$path = dirname($file);
		$file = $path.'/'.$filename_actual;
		$file_size = filesize($file);
		$handle = fopen($file, "r");
		$content = fread($handle, $file_size);
		fclose($handle);
		$content = chunk_split(base64_encode($content));
		$this->attachment_data .= "--".$this->uid."\r\n";
		$this->attachment_data .= "Content-Type: $type; name=\"".$filename_new."\"".$this->linebreak;
		$this->attachment_data .= "Content-Transfer-Encoding: base64".$this->linebreak;
		$this->attachment_data .= "Content-Disposition: attachment; filename=\"".$filename_new."\"".$this->linebreak.$this->linebreak;
		$this->attachment_data .= $content.$this->linebreak.$this->linebreak;
	}

	public function mail() {
		if (strlen(trim($this->attachment_data)) > 0) {
			$header = "From: ".$this->from_name." <".$this->from.">".$this->linebreak;
			$header .= "Reply-To: ".$this->reply_to.$this->linebreak;
			if(strlen(trim($this->cc)) > 5) {
				$header .= "CC: ".$this->cc.$this->linebreak;
			}
			if(strlen(trim($this->bcc)) > 5) {
				$header .= "BCC: ".$this->bcc.$this->linebreak;
			}
			$header .= "X-Mailer: createTheMail".$this->linebreak;
			$header .= "MIME-Version: 1.0".$this->linebreak;
			$header .= "Content-Type: multipart/mixed; boundary=\"".$this->uid."\"".$this->linebreak.$this->linebreak;
			$header .= "This is a multi-part message in MIME format.".$this->linebreak;
			$header .= "--".$this->uid.$this->linebreak;
			$header .= "Content-type:text/plain; charset=".$this->iso.$this->linebreak;
			$header .= "Content-Transfer-Encoding: 7bit".$this->linebreak.$this->linebreak;
			$header .= stripcslashes($this->message).$this->linebreak.$this->linebreak;
			$header .= $this->attachment_data;
			$header .= "--".$this->uid."--";
			if (@mail($this->to, $this->subject, "", $header, $this->additional_param)) {
				return true;
			} else {
				return false;
			}
			
			
		} else {

			$header = "From: ".($this->from_name)." <".($this->from).">".$this->linebreak;
			$header .= "Reply-To: ".($this->reply_to).$this->linebreak;
			if(strlen(trim($this->cc)) > 5) {
				$header .= "CC: ".$this->cc.$this->linebreak;
			}
			if(strlen(trim($this->bcc)) > 5) {
				$header .= "BCC: ".$this->bcc.$this->linebreak;
			}
			$header .= "X-Mailer: createTheMail".$this->linebreak;
			if (@mail($this->to, $this->subject, stripcslashes($this->message), $header, $this->additional_param)) {
				return true;
			} else {
				return false;
			}

		}
	}

}
?>